@php
    use Filament\Tables\Actions\Position as ActionsPosition;
    use Filament\Tables\Filters\Layout as FiltersLayout;

    $actions = $getActions();
    $actionsAlignment = $getActionsAlignment();
    $actionsPosition = $getActionsPosition();
    $actionsColumnLabel = $getActionsColumnLabel();
    $allRecordsCount = $getAllRecordsCount();
    $columns = $getVisibleColumns();
    $collapsibleColumnsLayout = $getCollapsibleColumnsLayout();
    $content = $getContent();
    $contentGrid = $getContentGrid();
    $contentFooter = $getContentFooter();
    $filterIndicators = collect($getFilters())
        ->mapWithKeys(fn (\Filament\Tables\Filters\BaseFilter $filter): array => [$filter->getName() => $filter->getIndicators()])
        ->filter(fn (array $indicators): bool => count($indicators))
        ->all();
    $hasColumnsLayout = $hasColumnsLayout();
    $hasSummary = $hasSummary();
    $header = $getHeader();
    $headerActions = $getHeaderActions();
    $headerActionsPosition = $getHeaderActionsPosition();
    $heading = $getHeading();
    $group = $getGrouping();
    $groupedBulkActions = $getGroupedBulkActions();
    $groups = $getGroups();
    $description = $getDescription();
    $isGroupsOnly = $isGroupsOnly() && $group;
    $isReorderable = $isReorderable();
    $isReordering = $isReordering();
    $isColumnSearchVisible = $isSearchableByColumn();
    $isGlobalSearchVisible = $isSearchable();
    $isSelectionEnabled = $isSelectionEnabled();
    $isStriped = $isStriped();
    $hasFilters = $isFilterable();
    $hasFiltersDropdown = $hasFilters && ($getFiltersLayout() === FiltersLayout::Dropdown);
    $hasFiltersAboveContent = $hasFilters && ($getFiltersLayout() === FiltersLayout::AboveContent);
    $hasFiltersAfterContent = $hasFilters && ($getFiltersLayout() === FiltersLayout::BelowContent);
    $isColumnToggleFormVisible = $hasToggleableColumns();
    $pluralModelLabel = $getPluralModelLabel();
    $records = $getRecords();

    $columnsCount = count($columns);
    if (count($actions) && (! $isReordering)) $columnsCount++;
    if ($isSelectionEnabled || $isReordering) $columnsCount++;

    $getHiddenClasses = function (\Filament\Tables\Columns\Column $column): ?string {
        if ($breakpoint = $column->getHiddenFrom()) {
            return match ($breakpoint) {
                'sm' => 'sm:hidden',
                'md' => 'md:hidden',
                'lg' => 'lg:hidden',
                'xl' => 'xl:hidden',
                '2xl' => '2xl:hidden',
            };
        }

        if ($breakpoint = $column->getVisibleFrom()) {
            return match ($breakpoint) {
                'sm' => 'hidden sm:table-cell',
                'md' => 'hidden md:table-cell',
                'lg' => 'hidden lg:table-cell',
                'xl' => 'hidden xl:table-cell',
                '2xl' => 'hidden 2xl:table-cell',
            };
        }

        return null;
    };
@endphp

<div
    x-data="{

        hasHeader: true,

        isLoading: false,

        selectedRecords: [],

        shouldCheckUniqueSelection: true,

        init: function () {
            $wire.on('deselectAllTableRecords', () => this.deselectAllRecords())

            $watch('selectedRecords', () => {
                if (! this.shouldCheckUniqueSelection) {
                    this.shouldCheckUniqueSelection = true

                    return
                }

                this.selectedRecords = [...new Set(this.selectedRecords)]

                this.shouldCheckUniqueSelection = false
            })
        },

        mountBulkAction: function (name) {
            $wire.mountTableBulkAction(name, this.selectedRecords)
        },

        toggleSelectRecordsOnPage: function () {
            let keys = this.getRecordsOnPage()

            if (this.areRecordsSelected(keys)) {
                this.deselectRecords(keys)

                return
            }

            this.selectRecords(keys)
        },

        getRecordsOnPage: function () {
            let keys = []

            for (checkbox of $el.getElementsByClassName('filament-tables-record-checkbox')) {
                keys.push(checkbox.value)
            }

            return keys
        },

        selectRecords: function (keys) {
            for (key of keys) {
                if (this.isRecordSelected(key)) {
                    continue
                }

                this.selectedRecords.push(key)
            }
        },

        deselectRecords: function (keys) {
            for (key of keys) {
                let index = this.selectedRecords.indexOf(key)

                if (index === -1) {
                    continue
                }

                this.selectedRecords.splice(index, 1)
            }
        },

        selectAllRecords: async function () {
            this.isLoading = true

            this.selectedRecords = await $wire.getAllTableRecordKeys()

            this.isLoading = false
        },

        deselectAllRecords: function () {
            this.selectedRecords = []
        },

        isRecordSelected: function (key) {
            return this.selectedRecords.includes(key)
        },

        areRecordsSelected: function (keys) {
            return keys.every(key => this.isRecordSelected(key))
        },

    }"
    class="filament-tables-component"
>
    <x-filament-tables::container>
        <div
            class="filament-tables-header-container"
            x-show="hasHeader = (@js($renderHeader = ($header || $heading || ($headerActions && (! $isReordering)) || $isReorderable || count($groups) || $isGlobalSearchVisible || $hasFilters || $isColumnToggleFormVisible)) || (selectedRecords.length && @js(count($groupedBulkActions))))"
            @if (! $renderHeader) x-cloak @endif
        >
            @if ($header)
                {{ $header }}
            @elseif ($heading || $headerActions)
                <div @class([
                    'px-2 pt-2',
                    'hidden' => ! $heading && $isReordering,
                ])>
                    <x-filament-tables::header :actions="$isReordering ? [] : $headerActions" :actions-position="$headerActionsPosition" class="mb-2">
                        <x-slot name="heading">
                            {{ $heading }}
                        </x-slot>

                        <x-slot name="description">
                            {{ $description }}
                        </x-slot>
                    </x-filament-tables::header>

                    <x-filament::hr
                        :x-show="\Illuminate\Support\Js::from($isReorderable || count($groups) || $isGlobalSearchVisible || $hasFilters || $isColumnToggleFormVisible) . ' || (selectedRecords.length && ' . \Illuminate\Support\Js::from(count($groupedBulkActions)) . ')'"/>
                </div>
            @endif

            @if ($hasFiltersAboveContent)
                <div class="px-2 pt-2">
                    <div class="p-4 mb-2">
                        <x-filament-tables::filters :form="$getFiltersForm()"/>
                    </div>

                    <x-filament::hr
                        :x-show="\Illuminate\Support\Js::from($isReorderable || count($groups) || $isGlobalSearchVisible || $isColumnToggleFormVisible) . ' || (selectedRecords.length && ' . \Illuminate\Support\Js::from(count($groupedBulkActions)) . ')'"/>
                </div>
            @endif

            <div
                x-show="@js($shouldRenderHeaderDiv = ($isReorderable || count($groups) || $isGlobalSearchVisible || $hasFiltersDropdown || $isColumnToggleFormVisible)) || (selectedRecords.length && @js(count($groupedBulkActions)))"
                @if (! $shouldRenderHeaderDiv) x-cloak @endif
                class="filament-tables-header-toolbar flex items-center justify-between py-2 px-3 h-14"
                x-bind:class="{
                    'gap-3': @js($isReorderable) || @js(count($groups)) || (selectedRecords.length && @js(count($groupedBulkActions))),
                }"
            >
                <div class="flex-shrink-0 flex items-center sm:gap-3">
                    @if ($isReorderable)
                        <x-filament-tables::reorder.trigger
                            :enabled="$isReordering"
                        />
                    @endif

                    @if (count($groups))
                        <x-filament-tables::groups :groups="$groups"/>
                    @endif

                    @if ((! $isReordering) && count($groupedBulkActions))
                        <x-filament-tables::bulk-actions
                            x-show="selectedRecords.length"
                            x-cloak
                            :actions="$groupedBulkActions"
                        />
                    @endif
                </div>

                @if ($isGlobalSearchVisible || $hasFiltersDropdown || $isColumnToggleFormVisible)
                    <div class="flex-1 flex items-center justify-end gap-3 md:max-w-md">
                        @if ($isGlobalSearchVisible)
                            <div class="filament-tables-search-container flex items-center justify-end flex-1">
                                <x-filament-tables::search-input/>
                            </div>
                        @endif

                        <div class="flex">
                            @if ($hasFiltersDropdown)
                                <x-filament-tables::filters.dropdown
                                    :form="$getFiltersForm()"
                                    :width="$getFiltersFormWidth()"
                                    :indicators-count="count(\Illuminate\Support\Arr::flatten($filterIndicators))"
                                    class="shrink-0"
                                />
                            @endif

                            @if ($isColumnToggleFormVisible)
                                <x-filament-tables::toggleable
                                    :form="$getColumnToggleForm()"
                                    :width="$getColumnToggleFormWidth()"
                                    class="shrink-0"
                                />
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if ($isReordering)
            <x-filament-tables::reorder.indicator
                :colspan="$columnsCount"
                class="border-t dark:border-gray-700"
            />
        @elseif ($isSelectionEnabled)
            <x-filament-tables::selection-indicator
                :all-records-count="$allRecordsCount"
                :colspan="$columnsCount"
                x-show="selectedRecords.length"
                class="border-t dark:border-gray-700"
            >
                <x-slot name="selectedRecordsCount">
                    <span x-text="selectedRecords.length"></span>
                </x-slot>
            </x-filament-tables::selection-indicator>
        @endif

        <x-filament-tables::filters.indicators
            :indicators="$filterIndicators"
            class="border-t dark:border-gray-700"
        />

        <div
            @if ($pollingInterval = $getPollingInterval())
                wire:poll.{{ $pollingInterval }}
            @endif
            @class([
                'filament-tables-table-container overflow-x-auto dark:border-gray-700',
                'overflow-x-auto' => $content || $hasColumnsLayout,
                'rounded-t-xl' => ! $renderHeader,
                'border-t' => $renderHeader,
            ])
            x-bind:class="{
                'rounded-t-xl': ! hasHeader,
                'border-t': hasHeader,
            }"
        >
            @if ($content || $hasColumnsLayout)
                @if (count($records))
                    @if (($content || $hasColumnsLayout) && (! $isReordering))
                        <div class="bg-gray-500/5 flex items-center gap-4 px-4 border-b dark:border-gray-700">
                            @if ($isSelectionEnabled)
                                <x-filament-tables::checkbox
                                    x-on:click="toggleSelectRecordsOnPage"
                                    x-bind:checked="
                                        let recordsOnPage = getRecordsOnPage()

                                        if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                            $el.checked = true

                                            return 'checked'
                                        }

                                        $el.checked = false

                                        return null
                                    "
                                    @class(['hidden' => $isReordering])
                                />
                            @endif

                            @php
                                $sortableColumns = array_filter(
                                    $columns,
                                    fn (\Filament\Tables\Columns\Column $column): bool => $column->isSortable(),
                                );
                            @endphp

                            @if (count($sortableColumns))
                                <div
                                    x-data="{
                                        column: $wire.entangle('tableSortColumn'),
                                        direction: $wire.entangle('tableSortDirection'),
                                    }"
                                    x-init="
                                        $watch('column', function (newColumn, oldColumn) {
                                            if (! newColumn) {
                                                direction = null

                                                return
                                            }

                                            if (oldColumn) {
                                                return
                                            }

                                            direction = 'asc'
                                        })
                                    "
                                    class="flex flex-wrap items-center gap-1 py-1 text-xs sm:text-sm"
                                >
                                    <label>
                                        <span class="mr-1 font-medium">
                                            {{ __('filament-tables::table.sorting.fields.column.label') }}
                                        </span>

                                        <select
                                            x-model="column"
                                            style="background-position: right 0.2rem center"
                                            class="text-xs pl-2 pr-6 py-1 font-medium border-0 bg-gray-500/5 rounded-lg border-gray-300 sm:text-sm focus:ring-0 focus:border-primary-500 focus:ring-primary-500 dark:text-white dark:bg-gray-700 dark:border-gray-600 dark:focus:border-primary-500"
                                        >
                                            <option value="">-</option>
                                            @foreach ($sortableColumns as $column)
                                                <option
                                                    value="{{ $column->getName() }}">{{ $column->getLabel() }}</option>
                                            @endforeach
                                        </select>
                                    </label>

                                    <label>
                                        <span class="sr-only">
                                            {{ __('filament-tables::table.sorting.fields.direction.label') }}
                                        </span>

                                        <select
                                            x-show="column"
                                            x-cloak
                                            x-model="direction"
                                            style="background-position: right 0.2rem center"
                                            class="text-xs pl-2 pr-6 py-1 font-medium border-0 bg-gray-500/5 rounded-lg border-gray-300 sm:text-sm focus:ring-0 focus:border-primary-500 focus:ring-primary-500 dark:text-white dark:bg-gray-700 dark:border-gray-600 dark:focus:border-primary-500"
                                        >
                                            <option value="asc">{{ __('filament-tables::table.sorting.fields.direction.options.asc') }}</option>
                                            <option value="desc">{{ __('filament-tables::table.sorting.fields.direction.options.desc') }}</option>
                                        </select>
                                    </label>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($content)
                        {{ $content->with(['records' => $records]) }}
                    @else
                        <x-filament::grid
                            x-sortable
                            x-on:end.stop="$wire.reorderTable($event.target.sortable.toArray())"
                            :default="$contentGrid['default'] ?? 1"
                            :sm="$contentGrid['sm'] ?? null"
                            :md="$contentGrid['md'] ?? null"
                            :lg="$contentGrid['lg'] ?? null"
                            :xl="$contentGrid['xl'] ?? null"
                            :two-xl="$contentGrid['2xl'] ?? null"
                            @class([
                                'dark:divide-gray-700',
                                'divide-y' => ! $contentGrid,
                                'p-2 gap-2' => $contentGrid,
                            ])
                        >
                            @php
                                $previousRecord = null;
                                $previousGroupTitle = null;
                            @endphp

                            @foreach ($records as $record)
                                @php
                                    $recordAction = $getRecordAction($record);
                                    $recordKey = $getRecordKey($record);
                                    $recordUrl = $getRecordUrl($record);
                                    $recordGroupTitle = $group?->getGroupTitleFromRecord($record);

                                    $collapsibleColumnsLayout?->record($record);
                                    $hasCollapsibleColumnsLayout = $collapsibleColumnsLayout && (! $collapsibleColumnsLayout->isHidden());
                                @endphp

                                @if ($recordGroupTitle !== $previousGroupTitle)
                                    @if ($hasSummary && (! $isReordering) && filled($previousGroupTitle))
                                        <x-filament-tables::table class="col-span-full">
                                            <x-filament-tables::summary.row
                                                :columns="$columns"
                                                :heading="__('filament-tables::table.summary.subheadings.group', ['group' => $previousGroupTitle, 'label' => $pluralModelLabel])"
                                                :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                                                extra-heading-column
                                                :placeholder-columns="false"
                                            />
                                        </x-filament-tables::table>
                                    @endif

                                    <div @class([
                                        'col-span-full bg-gray-500/5 flex items-center w-full px-4 py-2 whitespace-nowrap space-x-1 rtl:space-x-reverse font-medium text-base text-gray-600 dark:text-gray-300',
                                        'rounded-xl shadow-sm' => $contentGrid,
                                    ])>
                                        {{ $group->getLabel() }}: {{ $recordGroupTitle }}
                                    </div>
                                @endif

                                <div
                                    @if ($hasCollapsibleColumnsLayout)
                                        x-data="{ isCollapsed: true }"
                                    x-init="$dispatch('collapsible-table-row-initialized')"
                                    x-on:expand-all-table-rows.window="isCollapsed = false"
                                    x-on:collapse-all-table-rows.window="isCollapsed = true"
                                    @endif
                                    wire:key="{{ $this->id }}.table.records.{{ $recordKey }}"
                                    @if ($isReordering)
                                        x-sortable-item="{{ $recordKey }}"
                                    x-sortable-handle
                                    @endif
                                >
                                    <div
                                        x-bind:class="{
                                            'bg-gray-50 dark:bg-gray-500/10': isRecordSelected('{{ $recordKey }}'),
                                        }"
                                        @class(array_merge(
                                            [
                                                'h-full relative px-4 transition',
                                                'hover:bg-gray-50 dark:hover:bg-gray-500/10' => $recordUrl || $recordAction,
                                                'dark:border-gray-600' => ! $contentGrid,
                                                'group' => $isReordering,
                                                'rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 dark:bg-gray-700/40' => $contentGrid,
                                            ],
                                            $getRecordClasses($record),
                                        ))
                                    >
                                        <div @class([
                                            'items-center gap-4 md:flex md:mr-0 rtl:md:ml-0' => (! $contentGrid),
                                            'mr-6 rtl:mr-0 rtl:ml-6' => $isSelectionEnabled || $hasCollapsibleColumnsLayout || $isReordering,
                                        ])>
                                            <x-filament-tables::reorder.handle @class([
                                                'absolute top-3 right-3 rtl:right-auto rtl:left-3',
                                                'md:relative md:top-0 md:right-0 rtl:md:left-0' => ! $contentGrid,
                                                'hidden' => ! $isReordering,
                                            ]) />

                                            @if ($isSelectionEnabled)
                                                <x-filament-tables::checkbox
                                                    x-model="selectedRecords"
                                                    :value="$recordKey"
                                                    @class([
                                                        'filament-tables-record-checkbox absolute top-3 right-3 rtl:right-auto rtl:left-3',
                                                        'md:relative md:top-0 md:right-0 rtl:md:left-0' => ! $contentGrid,
                                                        'hidden' => $isReordering,
                                                    ])
                                                />
                                            @endif

                                            @if ($hasCollapsibleColumnsLayout)
                                                <div @class([
                                                    'absolute right-1 rtl:right-auto rtl:left-1',
                                                    'top-10' => $isSelectionEnabled,
                                                    'top-1' => ! $isSelectionEnabled,
                                                    'md:relative md:top-0 md:right-0 rtl:md:left-0' => ! $contentGrid,
                                                    'hidden' => $isReordering,
                                                ])>
                                                    <x-filament::icon-button
                                                        icon="heroicon-m-chevron-down"
                                                        icon-alias="tables::collapsible-column.trigger"
                                                        color="gray"
                                                        size="sm"
                                                        x-on:click="isCollapsed = ! isCollapsed"
                                                        x-bind:class="isCollapsed || '-rotate-180'"
                                                        class="transition"
                                                    />
                                                </div>
                                            @endif

                                            @if ($recordUrl)
                                                <a
                                                    href="{{ $recordUrl }}"
                                                    class="filament-tables-record-url-link flex-1 block py-3"
                                                >
                                                    <x-filament-tables::columns.layout
                                                        :components="$getColumnsLayout()"
                                                        :record="$record"
                                                        :record-key="$recordKey"
                                                        :row-loop="$loop"
                                                    />
                                                </a>
                                            @elseif ($recordAction)
                                                @php
                                                    if ($getAction($recordAction)) {
                                                        $recordWireClickAction = "mountTableAction('{$recordAction}', '{$recordKey}')";
                                                    } else {
                                                        $recordWireClickAction = "{$recordAction}('{$recordKey}')";
                                                    }
                                                @endphp

                                                <button
                                                    wire:click="{{ $recordWireClickAction }}"
                                                    wire:target="{{ $recordWireClickAction }}"
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="cursor-wait opacity-70"
                                                    type="button"
                                                    class="filament-tables-record-action-button flex-1 block py-3"
                                                >
                                                    <x-filament-tables::columns.layout
                                                        :components="$getColumnsLayout()"
                                                        :record="$record"
                                                        :record-key="$recordKey"
                                                        :row-loop="$loop"
                                                    />
                                                </button>
                                            @else
                                                <div class="flex-1 py-3">
                                                    <x-filament-tables::columns.layout
                                                        :components="$getColumnsLayout()"
                                                        :record="$record"
                                                        :record-key="$recordKey"
                                                        :row-loop="$loop"
                                                    />
                                                </div>
                                            @endif

                                            @if (count($actions))
                                                <x-filament-tables::actions
                                                    :actions="$actions"
                                                    :alignment="$actionsPosition === ActionsPosition::AfterContent ? 'start' : 'start md:end'"
                                                    :record="$record"
                                                    wrap="-md"
                                                    @class([
                                                        'absolute bottom-1 right-1 rtl:right-auto rtl:left-1' => $actionsPosition === ActionsPosition::BottomCorner,
                                                        'md:relative md:bottom-0 md:right-0 rtl:md:left-0' => $actionsPosition === ActionsPosition::BottomCorner && (! $contentGrid),
                                                        'mb-3' => $actionsPosition === ActionsPosition::AfterContent,
                                                        'md:mb-0' => $actionsPosition === ActionsPosition::AfterContent && (! $contentGrid),
                                                        'hidden' => $isReordering,
                                                    ])
                                                />
                                            @endif
                                        </div>

                                        @if ($hasCollapsibleColumnsLayout)
                                            <div
                                                x-show="! isCollapsed"
                                                x-collapse
                                                @class([
                                                    'pb-2 -mx-2',
                                                    'md:pl-20 rtl:md:pl-0 rtl:md:pr-20' => (! $contentGrid) && $isSelectionEnabled,
                                                    'md:pl-12 rtl:md:pl-0 rtl:md:pr-12' => (! $contentGrid) && (! $isSelectionEnabled),
                                                    'hidden' => $isReordering,
                                                ])
                                            >
                                                {{ $collapsibleColumnsLayout->viewData(['recordKey' => $recordKey]) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @php
                                    $previousGroupTitle = $recordGroupTitle;
                                    $previousRecord = $record;
                                @endphp
                            @endforeach

                            @if ($hasSummary && (! $isReordering) && filled($previousGroupTitle) && ((! $records instanceof \Illuminate\Contracts\Pagination\Paginator) || (! $records->hasMorePages())))
                                <x-filament-tables::table class="col-span-full">
                                    <x-filament-tables::summary.row
                                        :columns="$columns"
                                        :heading="__('filament-tables::table.summary.subheadings.group', ['group' => $previousGroupTitle, 'label' => $pluralModelLabel])"
                                        :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                                        extra-heading-column
                                        :placeholder-columns="false"
                                    />
                                </x-filament-tables::table>
                            @endif
                        </x-filament::grid>
                    @endif

                    @if (($content || $hasColumnsLayout) && $contentFooter)
                        {{ $contentFooter->with(['columns' => $columns, 'records' => $records]) }}
                    @endif

                    @if ($hasSummary && (! $isReordering))
                        <x-filament-tables::table class="border-t dark:border-gray-700">
                            <x-filament-tables::summary
                                :columns="$columns"
                                :plural-model-label="$pluralModelLabel"
                                :records="$records"
                                extra-heading-column
                                :placeholder-columns="false"
                            />
                        </x-filament-tables::table>
                    @endif
                @else
                    @if ($emptyState = $getEmptyState())
                        {{ $emptyState }}
                    @else
                        <div class="flex items-center justify-center p-4">
                            <x-filament-tables::empty-state :icon="$getEmptyStateIcon()"
                                                            :actions="$getEmptyStateActions()">
                                <x-slot name="heading">
                                    {{ $getEmptyStateHeading() }}
                                </x-slot>

                                <x-slot name="description">
                                    {{ $getEmptyStateDescription() }}
                                </x-slot>
                            </x-filament-tables::empty-state>
                        </div>
                    @endif
                @endif
            @else
                <x-filament-tables::table>
                    <x-slot name="header">
                        @if ($isReordering)
                            <th></th>
                        @else
                            @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells)
                                @if ($actionsColumnLabel)
                                    <x-filament-tables::header-cell>
                                        {{ $actionsColumnLabel }}
                                    </x-filament-tables::header-cell>
                                @else
                                    <th class="w-5"></th>
                                @endif
                            @endif

                            @if ($isSelectionEnabled)
                                <x-filament-tables::checkbox.cell>
                                    <x-filament-tables::checkbox
                                        x-on:click="toggleSelectRecordsOnPage"
                                        x-bind:checked="
                                            let recordsOnPage = getRecordsOnPage()

                                            if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                $el.checked = true

                                                return 'checked'
                                            }

                                            $el.checked = false

                                            return null
                                        "
                                    />
                                </x-filament-tables::checkbox.cell>
                            @endif

                            @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns)
                                @if ($actionsColumnLabel)
                                    <x-filament-tables::header-cell>
                                        {{ $actionsColumnLabel }}
                                    </x-filament-tables::header-cell>
                                @else
                                    <th class="w-5"></th>
                                @endif
                            @endif
                        @endif

                        @if ($isGroupsOnly)
                            <th class="filament-tables-header-cell px-4 py-2 whitespace-nowrap font-medium text-sm text-gray-600 dark:text-gray-300">
                                {{ $group->getLabel() }}
                            </th>
                        @endif

                        @foreach ($columns as $column)
                            <x-filament-tables::header-cell
                                :actively-sorted="$getSortColumn() === $column->getName()"
                                :name="$column->getName()"
                                :alignment="$column->getAlignment()"
                                :sortable="$column->isSortable() && (! $isReordering)"
                                :sort-direction="$getSortDirection()"
                                class="filament-table-header-cell-{{ \Illuminate\Support\Str::of($column->getName())->camel()->kebab() }} {{ $getHiddenClasses($column) }}"
                                :attributes="$column->getExtraHeaderAttributeBag()"
                                :wrap="$column->isHeaderWrapped()"
                            >
                                {{ $column->getLabel() }}
                            </x-filament-tables::header-cell>
                        @endforeach

                        @if (count($actions) && (! $isReordering) && $actionsPosition === ActionsPosition::AfterCells)
                            @if ($actionsColumnLabel)
                                <x-filament-tables::header-cell alignment="end">
                                    {{ $actionsColumnLabel }}
                                </x-filament-tables::header-cell>
                            @else
                                <th class="w-5"></th>
                            @endif
                        @endif
                    </x-slot>

                    @if ($isColumnSearchVisible)
                        <x-filament-tables::row>
                            @if ($isReordering)
                                <td></td>
                            @else
                                @if (count($actions) && in_array($actionsPosition, [ActionsPosition::BeforeCells, ActionsPosition::BeforeColumns]))
                                    <td></td>
                                @endif

                                @if ($isSelectionEnabled)
                                    <td></td>
                                @endif
                            @endif

                            @foreach ($columns as $column)
                                <x-filament-tables::cell class="filament-table-individual-search-cell-{{ \Illuminate\Support\Str::of($column->getName())->camel()->kebab() }} px-4 py-1">
                                    @if ($column->isIndividuallySearchable())
                                        <x-filament-tables::search-input
                                            wire-model="tableColumnSearches.{{ $column->getName() }}"/>
                                    @endif
                                </x-filament-tables::cell>
                            @endforeach

                            @if (count($actions) && (! $isReordering) && $actionsPosition === ActionsPosition::AfterCells)
                                <td></td>
                            @endif
                        </x-filament-tables::row>
                    @endif

                    @if (count($records))
                        @php
                            $previousRecord = null;
                            $previousGroupTitle = null;
                        @endphp

                        @foreach ($records as $record)
                            @php
                                $recordAction = $getRecordAction($record);
                                $recordKey = $getRecordKey($record);
                                $recordUrl = $getRecordUrl($record);
                                $recordGroupTitle = $group?->getGroupTitleFromRecord($record);
                            @endphp

                            @if ($recordGroupTitle !== $previousGroupTitle)
                                @if ($hasSummary && (! $isReordering) && filled($previousGroupTitle))
                                    <x-filament-tables::summary.row
                                        :actions="count($actions)"
                                        :actions-position="$actionsPosition"
                                        :columns="$columns"
                                        :heading="$isGroupsOnly ? $previousGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousGroupTitle, 'label' => $pluralModelLabel])"
                                        :groups-only="$isGroupsOnly"
                                        :selection-enabled="$isSelectionEnabled"
                                        :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                                    />
                                @endif

                                @if (! $isGroupsOnly)
                                    <x-filament-tables::row class="filament-tables-group-header-row bg-gray-500/5">
                                        @if (count($actions) && in_array($actionsPosition, [ActionsPosition::BeforeCells, ActionsPosition::BeforeColumns]))
                                            <td></td>
                                        @endif

                                        @if ($isSelectionEnabled)
                                            <td></td>
                                        @endif

                                        @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns)
                                            <td></td>
                                        @endif

                                        <td
                                            colspan="{{ count($columns) }}"
                                            class="px-4 py-2 whitespace-nowrap font-medium text-base text-gray-600 dark:text-gray-300"
                                        >
                                            {{ $group->getLabel() }}: {{ $recordGroupTitle }}
                                        </td>

                                        @if (count($actions) && $actionsPosition === ActionsPosition::AfterCells)
                                            <td></td>
                                        @endif
                                    </x-filament-tables::row>
                                @endif
                            @endif

                            @if (! $isGroupsOnly)
                                <x-filament-tables::row
                                    :record-action="$recordAction"
                                    :record-url="$recordUrl"
                                    :wire:key="$this->id . '.table.records.' . $recordKey"
                                    :x-sortable-item="$isReordering ? $recordKey : null"
                                    :x-sortable-handle="$isReordering"
                                    :striped="$isStriped"
                                    x-bind:class="{
                                        'bg-gray-50 dark:bg-gray-500/10': isRecordSelected('{{ $recordKey }}'),
                                    }"
                                    @class(array_merge(
                                        [
                                            'group cursor-move' => $isReordering,
                                        ],
                                        $getRecordClasses($record),
                                    ))
                                >
                                    <x-filament-tables::reorder.cell @class([
                                        'hidden' => ! $isReordering,
                                    ])>
                                        @if ($isReordering)
                                            <x-filament-tables::reorder.handle/>
                                        @endif
                                    </x-filament-tables::reorder.cell>

                                    @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells)
                                        <x-filament-tables::actions.cell @class([
                                            'hidden' => $isReordering,
                                        ])>
                                            <x-filament-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsAlignment ?? 'start'"
                                                :record="$record"
                                            />
                                        </x-filament-tables::actions.cell>
                                    @endif

                                    @if ($isSelectionEnabled)
                                        <x-filament-tables::checkbox.cell @class([
                                            'hidden' => $isReordering,
                                        ])>
                                            <x-filament-tables::checkbox
                                                x-model="selectedRecords"
                                                :value="$recordKey"
                                                class="filament-tables-record-checkbox"
                                            />
                                        </x-filament-tables::checkbox.cell>
                                    @endif

                                    @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns)
                                        <x-filament-tables::actions.cell @class([
                                            'hidden' => $isReordering,
                                        ])>
                                            <x-filament-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsAlignment ?? 'start'"
                                                :record="$record"
                                            />
                                        </x-filament-tables::actions.cell>
                                    @endif

                                    @foreach ($columns as $column)
                                        @php
                                            $column->record($record);
                                            $column->rowLoop($loop->parent);
                                        @endphp

                                        <x-filament-tables::cell
                                            class="filament-table-cell-{{ \Illuminate\Support\Str::of($column->getName())->camel()->kebab() }} {{ $getHiddenClasses($column) }}"
                                            wire:key="{{ $this->id }}.table.record.{{ $recordKey }}.column.{{ $column->getName() }}"
                                            wire:loading.remove.delay
                                            wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                        >
                                            <x-filament-tables::columns.column
                                                :column="$column"
                                                :record="$record"
                                                :record-action="$recordAction"
                                                :record-key="$recordKey"
                                                :record-url="$recordUrl"
                                                :is-click-disabled="$column->isClickDisabled() || $isReordering"
                                            />
                                        </x-filament-tables::cell>
                                    @endforeach

                                    @if (count($actions) && $actionsPosition === ActionsPosition::AfterCells)
                                        <x-filament-tables::actions.cell @class([
                                            'hidden' => $isReordering,
                                        ])>
                                            <x-filament-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsAlignment ?? 'end'"
                                                :record="$record"
                                            />
                                        </x-filament-tables::actions.cell>
                                    @endif

                                    <x-filament-tables::loading-cell
                                        :colspan="$columnsCount"
                                        wire:loading.class.remove.delay="hidden"
                                        class="hidden"
                                        :wire:key="$this->id . '.table.records.' . $recordKey . '.loading-cell'"
                                        wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                    />
                                </x-filament-tables::row>
                            @endif

                            @php
                                $previousGroupTitle = $recordGroupTitle;
                                $previousRecord = $record;
                            @endphp
                        @endforeach

                        @if ($hasSummary && (! $isReordering) && filled($previousGroupTitle) && ((! $records instanceof \Illuminate\Contracts\Pagination\Paginator) || (! $records->hasMorePages())))
                            <x-filament-tables::summary.row
                                :actions="count($actions)"
                                :actions-position="$actionsPosition"
                                :columns="$columns"
                                :heading="$isGroupsOnly ? $previousGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousGroupTitle, 'label' => $pluralModelLabel])"
                                :groups-only="$isGroupsOnly"
                                :selection-enabled="$isSelectionEnabled"
                                :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                            />
                        @endif

                        @if ($contentFooter)
                            <x-slot name="footer">
                                {{ $contentFooter->with(['columns' => $columns, 'records' => $records]) }}
                            </x-slot>
                        @endif

                        @if ($hasSummary && (! $isReordering))
                            <x-filament-tables::summary
                                :actions="count($actions)"
                                :actions-position="$actionsPosition"
                                :columns="$columns"
                                :groups-only="$isGroupsOnly"
                                :selection-enabled="$isSelectionEnabled"
                                :plural-model-label="$pluralModelLabel"
                                :records="$records"
                            />
                        @endif
                    @else
                        @if ($emptyState = $getEmptyState())
                            {{ $emptyState }}
                        @else
                            <tr>
                                <td colspan="{{ $columnsCount }}">
                                    <div class="flex items-center justify-center w-full p-4">
                                        <x-filament-tables::empty-state :icon="$getEmptyStateIcon()"
                                                                        :actions="$getEmptyStateActions()">
                                            <x-slot name="heading">
                                                {{ $getEmptyStateHeading() }}
                                            </x-slot>

                                            <x-slot name="description">
                                                {{ $getEmptyStateDescription() }}
                                            </x-slot>
                                        </x-filament-tables::empty-state>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endif
                </x-filament-tables::table>
            @endif
        </div>

        @if (
            $records instanceof \Illuminate\Contracts\Pagination\Paginator &&
            ((! $records instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) || $records->total())
        )
            <div class="filament-tables-pagination-container p-2 border-t dark:border-gray-700">
                <x-filament-tables::pagination
                    :paginator="$records"
                    :page-options="$getPaginationPageOptions()"
                />
            </div>
        @endif

        @if ($hasFiltersAfterContent)
            <div class="px-2 pb-2">
                <x-filament::hr/>

                <div class="p-4 mt-2">
                    <x-filament-tables::filters :form="$getFiltersForm()"/>
                </div>
            </div>
        @endif
    </x-filament-tables::container>

    <x-filament-actions::modals />
</div>
