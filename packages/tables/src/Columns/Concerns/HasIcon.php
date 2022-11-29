<?php

namespace Filament\Tables\Columns\Concerns;

use BackedEnum;
use Closure;
use Filament\Support\Contracts\HasIcon as IconInterface;
use Filament\Tables\Columns\Column;

trait HasIcon
{
    protected string | Closure | null $icon = null;

    protected string | Closure | null $iconPosition = null;

    public function icon(string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param  array<mixed> | Closure  $icons
     */
    public function icons(array | Closure $icons): static
    {
        $this->icon(function (Column $column, $state) use ($icons) {
            $icons = $column->evaluate($icons);

            $icon = null;

            foreach ($icons as $conditionalIcon => $condition) {
                if (is_numeric($conditionalIcon)) {
                    $icon = $condition;
                } elseif ($condition instanceof Closure && $column->evaluate($condition)) {
                    $icon = $conditionalIcon;
                } elseif ($condition === $state) {
                    $icon = $conditionalIcon;
                }
            }

            return $icon;
        });

        return $this;
    }

    public function iconPosition(string | Closure | null $iconPosition): static
    {
        $this->iconPosition = $iconPosition;

        return $this;
    }

    public function getIcon(): ?string
    {
        $icon = $this->evaluate($this->icon);

        $enum = $icon ?? $this->enum;
        if (
            is_string($enum) &&
            function_exists('enum_exists') &&
            enum_exists($enum) &&
            is_a($enum, BackedEnum::class, allow_string: true) &&
            is_a($enum, IconInterface::class, allow_string: true)
        ) {
            return $enum::tryFrom($this->getState())?->getIcon();
        }

        return $icon;
    }

    public function getIconPosition(): string
    {
        return $this->evaluate($this->iconPosition) ?? 'before';
    }
}
