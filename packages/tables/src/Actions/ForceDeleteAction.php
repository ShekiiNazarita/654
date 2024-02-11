<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Concerns\CanDeleteRecords;

class ForceDeleteAction extends Action
{
    use CanCustomizeProcess;
    use CanDeleteRecords;

    protected bool $recordIsDeletable = true;

    public static function getDefaultName(): ?string
    {
        return 'forceDelete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::force-delete.single.label'));

        $this->modalHidden(fn(): bool => ! ($this->recordIsDeletable = $this->isDeletable()));

        $this->modalHeading(fn (): string => __('filament-actions::force-delete.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitActionLabel(__('filament-actions::force-delete.single.modal.actions.delete.label'));

        $this->successNotificationTitle(__('filament-actions::force-delete.single.notifications.deleted.title'));

        $this->color('danger');

        $this->icon(FilamentIcon::resolve('actions::force-delete-action') ?? 'heroicon-m-trash');

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve('actions::force-delete-action.modal') ?? 'heroicon-o-trash');

        $this->action(function (): void {
            if (! $this->recordIsDeletable) {
                $this->sendNotDeletableNotification();

                return;
            }

            $result = $this->process(static fn (Model $record) => $record->forceDelete());

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });

        $this->visible(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });
    }
}
