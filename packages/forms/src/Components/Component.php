<?php

namespace Filament\Forms\Components;

use Filament\Forms\Concerns\HasColumns;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\View\Component as ViewComponent;

class Component extends ViewComponent implements Htmlable
{
    use Concerns\BelongsToContainer;
    use Concerns\BelongsToModel;
    use Concerns\CanBeConcealed;
    use Concerns\CanBeConditionallyModified;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanSpanColumns;
    use Concerns\Cloneable;
    use Concerns\EvaluatesCallbacks;
    use Concerns\HasChildComponents;
    use Concerns\HasExtraAttributes;
    use Concerns\HasId;
    use Concerns\HasLabel;
    use Concerns\HasMeta;
    use Concerns\HasState;
    use Concerns\HasView;
    use Concerns\ListensToEvents;
    use HasColumns;
    use Macroable;
    use Tappable;

    protected function setUp(): void
    {
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view($this->getView(), array_merge($this->data(), [
            'component' => $this,
        ]));
    }
}
