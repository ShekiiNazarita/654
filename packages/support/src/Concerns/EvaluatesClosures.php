<?php

namespace Filament\Support\Concerns;

use Closure;

trait EvaluatesClosures
{
    protected string $evaluationIdentifier;

    /**
     * @var array<string>
     */
    protected array $evaluationParametersToRemove = [];

    /**
     * @param  mixed  $value
     * @param  array<string, mixed>  $parameters
     * @param  array<string>  $exceptParameters
     * @return mixed
     */
    public function evaluate($value, array $parameters = [], array $exceptParameters = [])
    {
        $this->evaluationParametersToRemove = $exceptParameters;

        if ($value instanceof Closure) {
            return app()->call(
                $value,
                array_merge(
                    isset($this->evaluationIdentifier) ? [$this->evaluationIdentifier => $this] : [],
                    $this->getDefaultEvaluationParameters(),
                    $parameters,
                ),
            );
        }

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultEvaluationParameters(): array
    {
        return [];
    }

    /**
     * @return mixed
     */
    protected function resolveEvaluationParameter(string $parameter, Closure $value)
    {
        if ($this->isEvaluationParameterRemoved($parameter)) {
            return null;
        }

        return $value();
    }

    protected function isEvaluationParameterRemoved(string $parameter): bool
    {
        return in_array($parameter, $this->evaluationParametersToRemove);
    }
}
