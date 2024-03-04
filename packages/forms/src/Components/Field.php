<?php

namespace Filament\Forms\Components;

use Exception;

class Field extends Component implements Contracts\HasHintActions, Contracts\HasValidationRules
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeMarkedAsRequired;
    use Concerns\CanBeValidated;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;

    protected string $viewIdentifier = 'field';

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(?string $name = null): static
    {
        $fieldClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new Exception("Column of class [$fieldClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function getId(): string
    {
        return parent::getId() ?? $this->getStatePath();
    }

    public function getKey(): string
    {
        return parent::getKey() ?? $this->getStatePath();
    }
}
