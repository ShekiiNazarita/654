<?php

namespace Filament\Actions\Exports;

use Carbon\CarbonInterface;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\Component;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Middleware\WithoutOverlapping;

abstract class Exporter
{
    /** @var array<ExportColumn> */
    protected array $cachedColumns;

    protected ?Model $record;

    protected static ?string $model = null;

    public static string $defaultCurrency = 'usd';

    public static string $defaultDateDisplayFormat = 'M j, Y';

    public static string $defaultDateTimeDisplayFormat = 'M j, Y H:i:s';

    public static string $defaultTimeDisplayFormat = 'H:i:s';

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        readonly protected Export $export,
        readonly protected array $columnMap,
        readonly protected array $options,
    ) {
    }

    /**
     * @return array<mixed>
     */
    public function __invoke(Model $record): array
    {
        $this->record = $record;

        $columns = $this->getCachedColumns();

        $data = [];

        foreach (array_keys($this->columnMap) as $column) {
            $data[] = $columns[$column]->getState();
        }

        return $data;
    }

    /**
     * @return array<ExportColumn>
     */
    abstract public static function getColumns(): array;

    /**
     * @return array<Component>
     */
    public static function getOptionsFormComponents(): array
    {
        return [];
    }

    /**
     * @return class-string<Model>
     */
    public static function getModel(): string
    {
        return static::$model ?? (string) str(class_basename(static::class))
            ->beforeLast('Exporter')
            ->prepend('App\\Models\\');
    }

    abstract public static function getCompletedNotificationBody(Export $export): string;

    /**
     * @return array<int, object>
     */
    public function getJobMiddleware(): array
    {
        return [
            (new WithoutOverlapping("export{$this->export->id}"))->expireAfter(600),
        ];
    }

    public function getJobRetryUntil(): CarbonInterface
    {
        return now()->addDay();
    }

    /**
     * @return array<int, string>
     */
    public function getJobTags(): array
    {
        return ["export{$this->export->id}"];
    }

    /**
     * @return array<ExportColumn>
     */
    public function getCachedColumns(): array
    {
        return $this->cachedColumns ?? array_reduce(static::getColumns(), function (array $carry, ExportColumn $column): array {
            $carry[$column->getName()] = $column->exporter($this);

            return $carry;
        }, []);
    }

    public function getRecord(): ?Model
    {
        return $this->record;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    protected function callHook(string $hook): void
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }

    public static function getFileDisk(): string
    {
        return config('filament.default_filesystem_disk');
    }

    public static function getFileName(Export $export): string
    {
        return __('filament-actions::export.file_name', [
            'export_id' => $export->getKey(),
            'model' => (string) str(static::getModel())
                ->classBasename()
                ->beforeLast('Exporter')
                ->kebab()
                ->replace('-', ' ')
                ->plural()
                ->replace(' ', '-'),
        ]);
    }

    public static function getCsvDelimiter(): string
    {
        return ',';
    }
}