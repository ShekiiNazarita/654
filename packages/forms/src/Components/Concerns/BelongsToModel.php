<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait BelongsToModel
{
    protected $model = null;

    protected ?Closure $saveRelationshipsUsing = null;

    public function model(Model | string | callable | null $model = null): static
    {
        $this->model = $model;

        return $this;
    }

    public function saveRelationships(): void
    {
        $callback = $this->saveRelationshipsUsing;

        if (! $callback) {
            return;
        }

        $this->evaluate($callback);
    }

    public function saveRelationshipsUsing(?Closure $callback): static
    {
        $this->saveRelationshipsUsing = $callback;

        return $this;
    }

    public function getModel(): Model | string | null
    {
        if ($model = $this->evaluate($this->model)) {
            return $model;
        }

        return $this->getContainer()->getModel();
    }

    public function getModelClass(): string | null
    {
        $model = $this->getModel();

        if (! $model) {
            return null;
        }

        if (is_string($model)) {
            return $model;
        }

        return $model::class;
    }
}
