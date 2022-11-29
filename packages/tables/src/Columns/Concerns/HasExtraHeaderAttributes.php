<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraHeaderAttributes
{
    /**
     * @var array<mixed> | Closure
     */
    protected array | Closure $extraHeaderAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraHeaderAttributes(array | Closure $attributes): static
    {
        $this->extraHeaderAttributes = array_merge($this->extraHeaderAttributes, $attributes);

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraHeaderAttributes(): array
    {
        return $this->evaluate($this->extraHeaderAttributes);
    }

    public function getExtraHeaderAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraHeaderAttributes());
    }
}
