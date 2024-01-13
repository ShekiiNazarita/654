<?php

namespace Filament\Infolists\Components;

use Filament\Support\Concerns\CanBeCopied;

class ColorEntry extends Entry
{
    use CanBeCopied;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.color-entry';

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match($parameterName) {
            'colorEntry', 'entry', 'component' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
