<?php

namespace Filament\Resources\Pages\ListRecords\Concerns;

use Filament\Resources\Pages\Concerns\HasActiveLocaleSwitcher;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait Translatable
{
    use HasActiveLocaleSwitcher;

    public function mount(): void
    {
        static::authorizeResourceAccess();

        $this->setActiveLocale();
    }

    public function getActiveTableLocale(): ?string
    {
        return $this->activeLocale;
    }

    protected function setActiveLocale(): void
    {
        $this->activeLocale = static::getResource()::getDefaultTranslatableLocale();
    }

    public function getTableRecords(): Collection | Paginator
    {
        parent::getTableRecords();

        $this->records->transform(fn (Model $record) => $record->setLocale($this->activeLocale));

        return $this->records;
    }
}
