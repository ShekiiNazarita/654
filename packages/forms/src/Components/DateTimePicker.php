<?php

namespace Filament\Forms\Components;

use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Closure;
use DateTime;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Support\Carbon;
use Illuminate\View\ComponentAttributeBag;

class DateTimePicker extends Field
{
    use Concerns\HasPlaceholder;
    use Concerns\HasAffixes;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.date-time-picker';

    protected string | Closure | null $displayFormat = null;

    /**
     * @var array<mixed> | Closure
     */
    protected array | Closure $extraTriggerAttributes = [];

    protected int | null $firstDayOfWeek = null;

    protected string | Closure | null $format = null;

    protected bool | Closure $isWithoutDate = false;

    protected bool | Closure $isWithoutSeconds = false;

    protected bool | Closure $isWithoutTime = false;

    protected bool | Closure $shouldCloseOnDateSelection = false;

    protected CarbonInterface | string | Closure | null $maxDate = null;

    protected CarbonInterface | string | Closure | null $minDate = null;

    protected string | Closure | null $timezone = null;

    /**
     * @var array<DateTime | string> | Closure
     */
    protected array | Closure $disabledDates = [];

    public static int $defaultFirstDayOfWeek = 1;

    public static string $defaultDateDisplayFormat = 'M j, Y';

    public static string $defaultDateTimeDisplayFormat = 'M j, Y H:i';

    public static string $defaultDateTimeWithSecondsDisplayFormat = 'M j, Y H:i:s';

    public static string $defaultTimeDisplayFormat = 'H:i';

    public static string $defaultTimeWithSecondsDisplayFormat = 'H:i:s';

    protected int | Closure | null $hoursStep = null;

    protected int | Closure | null $minutesStep = null;

    protected int | Closure | null $secondsStep = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(static function (DateTimePicker $component, $state): void {
            if (blank($state)) {
                return;
            }

            if (! $state instanceof CarbonInterface) {
                try {
                    $state = Carbon::createFromFormat($component->getFormat(), $state);
                } catch (InvalidFormatException $exception) {
                    $state = Carbon::parse($state);
                }
            }

            $state->setTimezone($component->getTimezone());

            $component->state((string) $state);
        });

        $this->dehydrateStateUsing(static function (DateTimePicker $component, $state) {
            if (blank($state)) {
                return null;
            }

            if (! $state instanceof CarbonInterface) {
                $state = Carbon::parse($state);
            }

            $state->shiftTimezone($component->getTimezone());
            $state->setTimezone(config('app.timezone'));

            return $state->format($component->getFormat());
        });

        $this->rule(
            'date',
            static fn (DateTimePicker $component): bool => $component->hasDate(),
        );
    }

    public function displayFormat(string | Closure | null $format): static
    {
        $this->displayFormat = $format;

        return $this;
    }

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraTriggerAttributes(array | Closure $attributes): static
    {
        $this->extraTriggerAttributes = $attributes;

        return $this;
    }

    public function firstDayOfWeek(int | null $day): static
    {
        if ($day < 0 || $day > 7) {
            $day = null;
        }

        $this->firstDayOfWeek = $day;

        return $this;
    }

    public function format(string | Closure | null $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function maxDate(CarbonInterface | string | Closure | null $date): static
    {
        $this->maxDate = $date;

        $this->rule(static function (DateTimePicker $component) {
            return "before_or_equal:{$component->getMaxDate()}";
        }, static fn (DateTimePicker $component): bool => (bool) $component->getMaxDate());

        return $this;
    }

    public function minDate(CarbonInterface | string | Closure | null $date): static
    {
        $this->minDate = $date;

        $this->rule(static function (DateTimePicker $component) {
            return "after_or_equal:{$component->getMinDate()}";
        }, static fn (DateTimePicker $component): bool => (bool) $component->getMinDate());

        return $this;
    }

    /**
     * @param  array<DateTime | string> | Closure  $dates
     */
    public function disabledDates(array | Closure $dates): static
    {
        $this->disabledDates = $dates;

        return $this;
    }

    public function resetFirstDayOfWeek(): static
    {
        $this->firstDayOfWeek(null);

        return $this;
    }

    public function hoursStep(int | Closure | null $hoursStep): static
    {
        $this->hoursStep = $hoursStep;

        return $this;
    }

    public function minutesStep(int | Closure | null $minutesStep): static
    {
        $this->minutesStep = $minutesStep;

        return $this;
    }

    public function secondsStep(int | Closure | null $secondsStep): static
    {
        $this->secondsStep = $secondsStep;

        return $this;
    }

    public function timezone(string | Closure | null $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function weekStartsOnMonday(): static
    {
        $this->firstDayOfWeek(1);

        return $this;
    }

    public function weekStartsOnSunday(): static
    {
        $this->firstDayOfWeek(7);

        return $this;
    }

    public function withoutDate(bool | Closure $condition = true): static
    {
        $this->isWithoutDate = $condition;

        return $this;
    }

    public function withoutSeconds(bool | Closure $condition = true): static
    {
        $this->isWithoutSeconds = $condition;

        return $this;
    }

    public function withoutTime(bool | Closure $condition = true): static
    {
        $this->isWithoutTime = $condition;

        return $this;
    }

    public function closeOnDateSelection(bool | Closure $condition = true): static
    {
        $this->shouldCloseOnDateSelection = $condition;

        return $this;
    }

    public function getDisplayFormat(): string
    {
        $format = $this->evaluate($this->displayFormat);

        if ($format) {
            return $format;
        }

        if (! $this->hasTime()) {
            return static::$defaultDateDisplayFormat;
        }

        if (! $this->hasDate()) {
            return $this->hasSeconds() ?
                static::$defaultTimeWithSecondsDisplayFormat :
                static::$defaultTimeDisplayFormat;
        }

        return $this->hasSeconds() ?
            static::$defaultDateTimeWithSecondsDisplayFormat :
            static::$defaultDateTimeDisplayFormat;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraTriggerAttributes(): array
    {
        return $this->evaluate($this->extraTriggerAttributes);
    }

    public function getExtraTriggerAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraTriggerAttributes());
    }

    public function getFirstDayOfWeek(): int
    {
        return $this->firstDayOfWeek ?? static::$defaultFirstDayOfWeek;
    }

    public function getFormat(): string
    {
        $format = $this->evaluate($this->format);

        if ($format) {
            return $format;
        }

        $format = $this->hasDate() ? 'Y-m-d' : '';

        if (! $this->hasTime()) {
            return $format;
        }

        $format = $format ? "{$format} H:i" : 'H:i';

        if (! $this->hasSeconds()) {
            return $format;
        }

        return "{$format}:s";
    }

    public function getMaxDate(): ?string
    {
        return $this->evaluate($this->maxDate);
    }

    public function getMinDate(): ?string
    {
        return $this->evaluate($this->minDate);
    }

    /**
     * @return array<DateTime | string>
     */
    public function getDisabledDates(): array
    {
        return $this->evaluate($this->disabledDates);
    }

    public function getTimezone(): string
    {
        return $this->evaluate($this->timezone) ?? config('app.timezone');
    }

    public function hasDate(): bool
    {
        return ! $this->isWithoutDate;
    }

    public function hasSeconds(): bool
    {
        return ! $this->isWithoutSeconds;
    }

    public function hasTime(): bool
    {
        return ! $this->isWithoutTime;
    }

    public function getHoursStep(): int
    {
        return $this->evaluate($this->hoursStep) ?? 1;
    }

    public function getMinutesStep(): int
    {
        return $this->evaluate($this->minutesStep) ?? 1;
    }

    public function getSecondsStep(): int
    {
        return $this->evaluate($this->secondsStep) ?? 1;
    }

    public function shouldCloseOnDateSelection(): bool
    {
        return $this->evaluate($this->shouldCloseOnDateSelection);
    }
}
