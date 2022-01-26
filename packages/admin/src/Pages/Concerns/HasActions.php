<?php

namespace Filament\Pages\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Pages\Actions\Action;

/**
 * @property ComponentContainer $mountedActionForm
 */
trait HasActions
{
    public $mountedAction = null;

    public $mountedActionData = [];

    protected ?array $cachedActions = null;

    public function callMountedAction()
    {
        $action = $this->getMountedAction();

        if (! $action) {
            return;
        }

        if ($action->isHidden()) {
            return;
        }

        $data = $this->getMountedActionForm()->getState();

        try {
            return $action->call($data);
        } finally {
            $this->dispatchBrowserEvent('close-modal', [
                'id' => 'page-action',
            ]);
        }
    }

    public function mountAction(string $name)
    {
        $this->mountedAction = $name;

        $action = $this->getMountedAction();

        if (! $action) {
            return;
        }

        if ($action->isHidden()) {
            return;
        }

        $this->cacheForm('mountedActionForm');

        app()->call($action->getMountUsing(), [
            'action' => $action,
            'form' => $this->getMountedActionForm(),
        ]);

        if (! $action->shouldOpenModal()) {
            return $this->callMountedAction();
        }

        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'page-action',
        ]);
    }

    protected function getCachedActions(): array
    {
        if ($this->cachedActions === null) {
            $this->cacheActions();
        }

        return $this->cachedActions;
    }

    protected function cacheActions(): void
    {
        $this->cachedActions = collect($this->getActions())
            ->mapWithKeys(function (Action $action): array {
                $action->livewire($this);

                return [$action->getName() => $action];
            })
            ->toArray();
    }

    public function getMountedAction(): ?Action
    {
        if (! $this->mountedAction) {
            return null;
        }

        return $this->getCachedAction($this->mountedAction);
    }

    public function getMountedActionForm(): ComponentContainer
    {
        return $this->mountedActionForm;
    }

    protected function getCachedAction(string $name): ?Action
    {
        $action = $this->getCachedActions()[$name] ?? null;

        return $action;
    }

    protected function getActions(): array
    {
        return [];
    }
}
