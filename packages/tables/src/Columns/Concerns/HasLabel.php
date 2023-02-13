<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;

trait HasLabel
{
    protected string | Closure | null $label = null;

    protected bool $shouldTranslateLabel = false;

    public function label(string | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function translateLabel(bool $shouldTranslateLabel = true): static
    {
        $this->shouldTranslateLabel = $shouldTranslateLabel;

        return $this;
    }

    public function getLabel(): string | Htmlable
    {
        $label = $this->evaluate($this->label) ?? (string) Str::of($this->getName())
            ->beforeLast('.')
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return $this->shouldTranslateLabel ? __($label) : $label;
    }
}
