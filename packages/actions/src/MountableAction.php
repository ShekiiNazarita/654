<?php

namespace Filament\Actions;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Livewire\Component;

abstract class MountableAction extends StaticAction
{
    use Concerns\CanBeDisabled;
    use Concerns\CanBeMounted;
    use Concerns\CanBeOutlined;
    use Concerns\CanNotify;
    use Concerns\CanOpenModal;
    use Concerns\CanOpenUrl;
    use Concerns\CanRedirect;
    use Concerns\CanRequireConfirmation;
    use Concerns\HasAction;
    use Concerns\HasArguments;
    use Concerns\HasForm;
    use Concerns\HasGroupedIcon;
    use Concerns\HasKeyBindings;
    use Concerns\HasLifecycleHooks;
    use Concerns\HasTooltip;
    use Concerns\HasWizard;

    protected string $view = 'filament-actions::button-action';

    protected function setUp(): void
    {
        parent::setUp();

        $this->failureNotification(fn (Notification $notification): Notification => $notification);
        $this->successNotification(fn (Notification $notification): Notification => $notification);
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return mixed
     */
    public function call(array $parameters = [])
    {
        return $this->evaluate($this->getAction(), $parameters);
    }

    public function cancel(): void
    {
        throw new Cancel();
    }

    public function halt(): void
    {
        throw new Halt();
    }

    /**
     * @deprecated Use `->halt()` instead.
     */
    public function hold(): void
    {
        $this->halt();
    }

    public function success(): void
    {
        $this->sendSuccessNotification();
        $this->dispatchSuccessRedirect();
    }

    public function failure(): void
    {
        $this->sendFailureNotification();
        $this->dispatchFailureRedirect();
    }

    public function button(): static
    {
        $this->view('filament-actions::button-action');

        return $this;
    }

    public function grouped(): static
    {
        $this->view('filament-actions::grouped-action');

        return $this;
    }

    public function iconButton(): static
    {
        $this->view('filament-actions::icon-button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('filament-actions::link-action');

        return $this;
    }

    /**
     * @return Component
     */
    abstract public function getLivewire();

    public function getLivewireMountAction(): ?string
    {
        return null;
    }

    public function getAlpineMountAction(): ?string
    {
        return null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'arguments' => $this->getArguments(),
            'data' => $this->getFormData(),
            'livewire' => $this->getLivewire(),
        ]);
    }
}
