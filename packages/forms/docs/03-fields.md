---
title: Fields
---

Field classes can be found in the `Filament\Form\Components` namespace.

They reside within the schema of your form, alongside any [layout components](layout):

```php
protected function getFormSchema(): array
{
    return [
        // ...
    ];
}
```

Fields may be created using the static `make()` method, passing its name. The name of the field should correspond to a property on your Livewire component. You may use [Livewire's "dot syntax"](https://laravel-livewire.com/docs/properties#binding-nested-data) to bind fields to nested properties such as arrays and Eloquent models.

```php
Field::make('name')
```

By default, the label of the field will be automatically determined based on its name. To override the field's label, you may use the `label()` method. Customizing the label in this way is useful if you wish to use a [translation string for localization](https://laravel.com/docs/localization#retrieving-translation-strings):

```php
Field::make('name')->label(__('fields.name'))
```

In the same way as labels, field IDs are also automatically determined based on their names. To override a field ID, use the `id()` method:

```php
Field::make('name')->id('name-field')
```

When fields fail validation, their label is used in the error message. To customize the label used in field error messages, use the `validationAttribute()` method:

```php
Field::make('name')->validationAttribute('full name')
```

Fields may have a default value. This will be filled if the [form's `fill()` method](building-forms#default-data) is called without any arguments. To define a default value, use the `default()` method:

```php
Field::make('name')->default('John')
```

Sometimes, you may wish to provide extra information for the user of the form. For this purpose, you may use helper messages and hints.

Help messages are displayed below the field. The `helpMessage()` method supports Markdown formatting:

```php
Field::make('name')->helpMessage('Your full name here, including any middle names.')
```

Hints can be used to display text adjacent to its label:

```php
Field::make('password')->hint('[Forgotten your password?](forgotten-password)')
```

The HTML of fields can be customized even further, by passing an array of `extraAttributes()`:

```php
Field::make('name')->extraAttributes(['step' => 10])
```

You may disable a field to prevent it from being edited:

```php
Field::make('name')->disabled()
```

Most fields will be autofocusable. Ideally, you should aim for the first significant field in your form to be autofocused for the best user experience.

```php
Field::make('name')->autofocus()
```

Many fields will also include a placeholder value for when it has no value. You may customize this using the `placeholder()` method:

```php
Field::make('name')->placeholder('John Doe')
```

If your field is in a grid layout, you may specify the number of columns it spans at any breakpoint:

```php
use Filament\Forms\Components\Grid;use Filament\Forms\Components\TextInput;

Grid::make([
    'default' => 1,
    'sm' => 3,
    'xl' => 6,
    '2xl' => 8,
])
    ->schema([
        TextInput::make('name')
            ->columnSpan([
                'sm' => 2,
                'xl' => 3,
                '2xl' => 4,
            ]),
        // ...
    ])
```

> More information about grids is available in the [layout documentation](layout#grid).

## Builder

Similar to a [repeater](#repeater), the builder component allows you to output a JSON array of repeated form components. Unlike the repeater, which only defines one form schema to repeat, the builder allows you to define different schema "blocks", which you can repeat in any order. This makes it useful for building more advanced array structures.

The primary use of the builder component is to build web page content using predefined blocks. The example below defines multiple blocks for different elements in the page content. On the frontend of your website, you could loop through each block in the JSON and format it how you wish.

```php
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

Builder::make('content')
    ->blocks([
        Builder\Block::make('heading')
            ->schema([
                TextInput::make('content')->required(),
                Select::make('level')
                    ->options([
                        'h1' => 'Heading 1',
                        'h2' => 'Heading 2',
                        'h3' => 'Heading 3',
                        'h4' => 'Heading 4',
                        'h5' => 'Heading 5',
                        'h6' => 'Heading 6',
                    ])
                    ->required(),
            ]),
        Builder\Block::make('paragraph')
            ->schema([
                MarkdownEditor::make('content')->required(),
            ]),
        Builder\Block::make('image')
            ->schema([
                FileUpload::make('url')
                    ->image()
                    ->required(),
                TextInput::make('alt')
                    ->label('Alt text')
                    ->required(),
            ]),
    ])
```

As evident in the above example, blocks can be defined within the `blocks()` method of the component. Blocks are `Builder\Block` objects, and require a unique name, and a component schema:

```php
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;

Builder::make('content')
    ->blocks([
        Builder\Block::make('heading')
            ->schema([
                TextInput::make('content')->required(),
                // ...
            ]),
        // ...
    ])
```

By default, the label of the block will be automatically determined based on its name. To override the block's label, you may use the `label()` method. Customizing the label in this way is useful if you wish to use a [translation string for localization](https://laravel.com/docs/localization#retrieving-translation-strings):

```php
use Filament\Forms\Components\Builder;

Builder\Block::make('heading')->label(__('blocks.heading'))
```

Blocks may also have an icon, which is displayed next to the label. The `icon()` method accepts the name of any Blade icon component:

```php
use Filament\Forms\Components\Builder;

Builder\Block::make('heading')->icon('heroicon-o-archive')
```

## Checkbox

The checkbox component, similar to a [toggle](#toggle), allows you to interact a boolean value.

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin'),
```

Checkbox fields have two layout modes, inline and stacked. By default, they are inline.

When the checkbox is inline, its label is adjacent to it:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin')->inline()
```

When the checkbox is stacked, its label is above it:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin')->stacked()
```

If you're saving the boolean value using Eloquent, you should be sure to add a `boolean` [cast](https://laravel.com/docs/eloquent-mutators#attribute-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $casts = [
        'is_admin' => 'boolean',
    ];
    
    // ...
}
```

## Date-time picker

The date-time picker provides an interactive interface for selecting a date and a time.

```php
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;

DateTimePicker::make('published_at')
DatePicker::make('date_of_birth')
TimePicker::make('alarm_at')
```

You may restrict the minimum and maximum date that can be selected with the picker. The `minDate()` and `maxDate()` methods accept a `DateTime` instance (e.g. Carbon), or a string:

```php
use Filament\Forms\Components\DatePicker;

DatePicker::make('date_of_birth')
    ->minDate(now()->subYears(150))
    ->maxDate(now())
```

You may customize the format of the field when it is saved in your database, using the `format()` method. This accepts a string date format, using [PHP date formatting tokens](https://www.php.net/manual/en/datetime.format.php):

```php
use Filament\Forms\Components\DatePicker;

DatePicker::make('date_of_birth')->format('d/m/Y')
```

You may also customize the display format of the field, separately from the format used when it is saved in your database. For this, use the `displayFormat()` method, which also accepts a string date format, using [PHP date formatting tokens](https://www.php.net/manual/en/datetime.format.php):

```php
use Filament\Forms\Components\DatePicker;

DatePicker::make('date_of_birth')->displayFormat('d/m/Y')
```

When using the time picker, you may disable the seconds input using the `withoutSeconds()` method:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('published_at')->withoutSeconds()
```

In some countries, the first day of the week is not Monday. To customize the first day of the week in the date picker, use the `forms.components.date_time_picker.first_day_of_week` config option, or the `firstDayOfWeek()` method on the component. 0 to 7 are accepted values, with Monday as 1 and Sunday as 7 or 0:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('published_at')->firstDayOfWeek(7)
```

There are additionally convenient helper methods to set the first day of the week more semantically:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('published_at')->weekStartsOnMonday()
DateTimePicker::make('published_at')->weekStartsOnSunday()
```

## File upload

The file upload field is based on [Filepond](https://pqina.nl/filepond).

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment'),
```

By default, files will be uploaded publicly to your default storage disk.

To change the disk and directory that files are saved in, and their visibility, use the `disk()`, `directory()` and `visibility` methods:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->disk('s3')
    ->directory('form-attachments')
    ->visibility('private')
```

> Please note, it is the responsibility of the developer to delete these files from the disk if they are removed, as Filament is unaware if they are depended on elsewhere. One way to do this automatically is observing a [model event](https://laravel.com/docs/eloquent#events).

You may restrict the types of files that may be uploaded using the `acceptedFileTypes()` method, and passing an array of MIME types. You may also use the `image()` method as shorthand to allow all image MIME types.

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('document')->acceptedFileTypes(['application/pdf'])
FileUpload::make('image')->image()
```

You may also restrict the size of uploaded files, in kilobytes:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->minSize(512)
    ->maxSize(1024)
```

> To customize Livewire's default file upload validation rules, please refer to its [documentation](https://laravel-livewire.com/docs/file-uploads#global-validation).

Filepond allows you to crop and resize images before they are uploaded. You can customize this behaviour using the `imageCropAspectRatio()`, `imageResizeTargetHeight()` and `imageResizeTargetWidth()` methods.

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('image')
    ->image()
    ->imageCropAspectRatio('16:9'),
    ->imageResizeTargetWidth('1920'),
    ->imageResizeTargetHeight('1080')
```

You may also alter the general appearance of the Filepond component. Available options for these methods are available on the [Filepond website](https://pqina.nl/filepond/docs/api/instance/properties/#styles).

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->imagePreviewHeight('250')
    ->loadingIndicatorPosition('left')
    ->panelAspectRatio('2:1')
    ->panelLayout('integrated')
    ->removeUploadedFileButtonPosition('right')
    ->uploadButtonPosition('left')
    ->uploadProgressIndicatorPosition('left')
```

You may also upload multiple files, using the multiple file upload component. This stores URLs in JSON, but you may customize it to write to a relationship instead.

```php
use Filament\Forms\Components\MultipleFileUpload;

MultipleFileUpload::make('attachments')
```

You can customize the underlying file upload component by passing an instance of it to `uploadComponent()`. It must have the name `file`, but you can customize its label, ID and validation attribute to hide this if you wish:

```php
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MultipleFileUpload;

MultipleFileUpload::make('attachments')
    ->uploadComponent(
        FileUpload::make('file')->image(),
    )
```

> Filament also supports [`spatie/laravel-medialibrary`](https://github.com/spatie/laravel-medialibrary). See our [plugin documentation](/docs/spatie-laravel-media-library-plugin) for more information.

## Hidden

The hidden component allows you to create a hidden field in your form that holds a value.

```php
use Filament\Forms\Components\Hidden;

Hidden::make('token')
```

## Markdown editor

The markdown editor allows you to edit and preview markdown content, as well as upload images.

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
```

You may enable / disable toolbar buttons using a range of convenient methods:

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
    ->toolbarButtons([
        'attachFiles',
        'bold',
        'bulletList',
        'codeBlock',
        'edit',
        'italic',
        'link',
        'orderedList',
        'preview',
        'strike',
    ])
MarkdownEditor::make('content')
    ->disableToolbarButtons([
        'attachFiles',
        'codeBlock',
    ])
MarkdownEditor::make('content')
    ->disableAllToolbarButtons()
    ->enableToolbarButtons([
        'bold',
        'bulletList',
        'edit',
        'italic',
        'preview',
        'strike',
    ])
```

You may customise how images are uploaded using configuration methods:

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
    ->fileAttachmentsDisk('s3')
    ->fileAttachmentsDirectory('attachments')
    ->fileAttachmentsVisibility('private')
```

## Multi-select

The multi-select component allows you to select multiple values from a list of predefined options:

```php
use Filament\Forms\Components\MultiSelect;

MultiSelect::make('technologies')
    ->options([
        'tailwind' => 'TailwindCSS',
        'alpine' => 'Alpine.js',
        'laravel' => 'Laravel',
        'livewire' => 'Laravel Livewire',
    ])
```

These options are returned in JSON format. If you're saving them using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    protected $casts = [
        'technologies' => 'array',
    ];
    
    // ...
}
```

### Populating automatically from a `belongsToMany` relationship

You may employ the `relationship()` method of the `BelongsToManyMultiSelect` to configure a relationship to automatically retrieve and save options from:

```php
use App\Models\App;
use Filament\Forms\Components\BelongsToManyMultiSelect;

BelongsToManyMultiSelect::make('technologies')
    ->relationship('technologies', 'name')
```

> To set this functionality up, **you must also follow the instructions set out in the [field relationships](building-forms#field-relationships) section**.

You may customise the database query that retrieves options using the third parameter of the `relationship()` method:

```php
use Filament\Forms\Components\BelongsToManyMultiSelect;
use Illuminate\Database\Eloquent\Builder;

BelongsToManyMultiSelect::make('technologies')
    ->relationship('technologies', 'name', fn (Builder $query) => $query->withTrashed())
```

## Repeater

The repeater component allows you to output a JSON array of repeated form components.

```php
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

Repeater::make('members')
    ->schema([
        TextInput::make('name')->required(),
        Select::make('role')
            ->options([
                'member' => 'Member',
                'administrator' => 'Administrator',
                'owner' => 'Owner',
            ])
            ->required(),
    ])
```

As evident in the above example, the component schema can be defined within the `schema()` method of the component:

```php
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

Repeater::make('members')
    ->schema([
        TextInput::make('name')->required(),
        // ...
    ])
```

If you wish to define a repeater with multiple schema blocks that can be repeated in any order, please use the [builder](#builder).

## Rich editor

The rich editor allows you to edit and preview HTML content, as well as upload images.

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
```

You may enable / disable toolbar buttons using a range of convenient methods:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->toolbarButtons([
        'attachFiles',
        'blockquote',
        'bold',
        'bulletList',
        'codeBlock',
        'h2',
        'h3',
        'italic',
        'link',
        'orderedList',
        'redo',
        'strike',
        'undo',
    ])
RichEditor::make('content')
    ->disableToolbarButtons([
        'attachFiles',
        'codeBlock',
    ])
RichEditor::make('content')
    ->disableAllToolbarButtons()
    ->enableToolbarButtons([
        'bold',
        'bulletList',
        'italic',
        'strike',
    ])
```

You may customise how images are uploaded using configuration methods:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->fileAttachmentsDisk('s3')
    ->fileAttachmentsDirectory('attachments')
    ->fileAttachmentsVisibility('private')
```

## Select

The select component allows you to select from a list of predefined options:

```php
use Filament\Forms\Components\Select;

Select::make('status')
    ->options([
        'draft' => 'Draft',
        'review' => 'In review',
        'published' => 'Published',
    ])
```

You may enable a search input to allow easier access to many options, using the `searchable()` method:

```php
use App\Models\User;
use Filament\Forms\Components\Select;

Select::make('authorId')
    ->options(User::all()->pluck('name', 'id'))
    ->searchable()
```

### Dependant selects

Commonly, you may desire "dependant" select inputs, which populate their options based on the state of another.

<iframe width="560" height="315" src="https://www.youtube.com/embed/W_eNyimRi3w" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

Some of the techniques described in the [advanced forms](advanced-forms) section are required to create dependant selects. These techniques can be applied across all form components for many dynamic customisation possibilities.

### Populating automatically from a `belongsTo` relationship

You may employ the `relationship()` method of the `BelongsToSelect` to configure a relationship to automatically retrieve and save options from:

```php
use App\Models\Post;
use Filament\Forms\Components\BelongsToSelect;

BelongsToSelect::make('authorId')
    ->relationship('author', 'name')
```

> To set this functionality up, **you must also follow the instructions set out in the [field relationships](building-forms#field-relationships) section**.

You may customise the database query that retrieves options using the third parameter of the `relationship()` method:

```php
use App\Models\App;
use Filament\Forms\Components\BelongsToSelect;
use Illuminate\Database\Eloquent\Builder;

BelongsToSelect::make('authorId')
    ->relationship('author', 'name', fn (Builder $query) => $query->withTrashed())
```

## Tags input

The tags input component allows you to interact with a list of tags.

By default, tags are stored in JSON:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')
```

If you're saving the JSON tags using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $casts = [
        'tags' => 'array',
    ];
    
    // ...
}
```

You may allow the tags to be stored in a separated string, instead of JSON. To set this up, pass the separating character to the `separator()` method:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')->separator(',')
```

> Filament also supports [`spatie/laravel-tags`](https://github.com/spatie/laravel-tags). See our [plugin documentation](/docs/spatie-laravel-tags-plugin) for more information.

## Text input

The text input allows you to interact with a string:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
```

You may set the type of string using a set of methods. Some, such as `email()`, also provide validation:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('text')
    ->email()
    ->numeric()
    ->password()
    ->tel()
    ->url()
```

You may instead use the `type()` method to pass another [HTML input type](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#input_types):

```php
use Filament\Forms\Components\TextInput;

TextInput::make('backgroundColor')->type('color')
```

You may place text before and after the input using the `prefix()` and `postfix()` methods:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('domain')
    ->url()
    ->prefix('https://')
    ->postfix('.com')
```

You may limit the length of the string by setting the `minLength()` and `maxLength()` methods. These methods add both frontend and backend validation:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->minLength(2)
    ->maxLength(255)
```

In addition, you may validate the minimum and maximum value of the input by setting the `minValue()` and `maxValue()` methods:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('age')
    ->numeric()
    ->minValue(1)
    ->maxValue(100)
```

### Input masking

Input masking is the practice of defining a format that the input value must conform to.

In Filament, you may interact with the `Mask` object in the `mask()` method to configure your mask:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('phone')
    ->mask(fn (TextInput\Mask $mask) => $mask->pattern('+{7}(000)000-00-00'))
```

Under the hood, masking is powered by [`imaskjs`](https://imask.js.org). The vast majority of its masking features are also available in Filament. Reading their [guide](https://imask.js.org/guide.html) first, and then approaching the same task using Filament is probably the easiest option.

You may define and configure a [numeric mask](https://imask.js.org/guide.html#masked-number) to deal with numbers:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('number')
    ->numeric()
    ->mask(fn (TextInput\Mask $mask) => $mask
        ->numeric()
        ->decimalPlaces(2) // Set the number of digits after the decimal point.
        ->decimalSeparator(',') // Add a separator for decimal numbers.
        ->integer() // Disallow decimal numbers.
        ->mapToDecimalSeparator([',']) // Map additional characters to the decimal separator.
        ->minValue(1) // Set a minimum and maximum value that the number can be.
        ->minValue(100) // Set a minimum and maximum value that the number can be.
        ->normalizeZeros() // Append or remove zeros at the end of the number.
        ->padFractionalZeros() // Pad zeros at the end of the number to always maintain the maximum number of decimal places.
        ->thousandsSeparator(','), // Add a separator for thousands.
    )
```

[Enum masks](https://imask.js.org/guide.html#enum) limit the options that the user can input:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('code')->mask(fn (TextInput\Mask $mask) => $mask->enum(['F1', 'G2', 'H3']))
```

[Range masks](https://imask.js.org/guide.html#masked-range) can be used to restrict input to a number range:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('code')->mask(fn (TextInput\Mask $mask) => $mask
    ->range()
    ->from(1) // Set the lower limit.
    ->to(100) // Set the upper limit.
    ->maxValue(100), // Pad zeros at the start of smaller numbers.
)
```

In addition to simple pattens, you may also define multiple [pattern blocks](https://imask.js.org/guide.html#masked-pattern):

```php
use Filament\Forms\Components\TextInput;

TextInput::make('cost')->mask(fn (TextInput\Mask $mask) => $mask
    ->patternBlocks([
        'money' => fn (Mask $mask) => $mask
            ->numeric()
            ->thousandsSeparator(',')
            ->decimalPlaces('.'),
    ])
    ->pattern('$money'),
)
```

There is also a `money()` method that is able to define easier formatting for currency inputs. This example, the symbol prefix is `$`, there is a `,` thousands separator, and two decimal places:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('cost')->mask(fn (TextInput\Mask $mask) => $mask->money('$', ',', 2))
```

### Datalists

You may specify [datalist](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/datalist) options for a text input using the `datalist()` method:

```php
TextInput::make('manufacturer')
    ->datalist([
        'BWM',
        'Ford',
        'Mercedes-Benz',
        'Porsche',
        'Toyota',
        'Tesla',
        'Volkswagen',
    ])
```

Datalists provide autocomplete options to users when they use a text input. However, these are purely recommendations, and the user is still able to type any value into the input. If you're looking for strictly predefined options, check out [select fields](#select).

## Textarea

The textarea allows you to interact with a multi-line string:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
```

You may change the size of the textarea by defining the `rows()` and `cols()` methods:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->rows(10)
    ->cols(20)
```

You may limit the length of the string by setting the `minLength()` and `maxLength()` methods. These methods add both frontend and backend validation:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->minLength(50)
    ->maxLength(500)
```

## Toggle

The toggle component, similar to a [checkbox](#checkbox), allows you to interact a boolean value.

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
```

Toggle fields have two layout modes, inline and stacked. By default, they are inline.

When the toggle is inline, its label is adjacent to it:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')->inline()
```

When the toggle is stacked, its label is above it:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')->stacked()
```

Toggles may also use an "on icon" and an "off icon". These are displayed on its handle and could provide a greater indication to what your field represents. The parameter to each method must contain the name of a Blade icon component:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
    ->onIcon('heroicon-s-lightning-bolt'),
    ->offIcon('heroicon-s-user')
```

If you're saving the boolean value using Eloquent, you should be sure to add a `boolean` [cast](https://laravel.com/docs/eloquent-mutators#attribute-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $casts = [
        'is_admin' => 'boolean',
    ];
    
    // ...
}
```

## View

Aside from [building custom fields](#building-custom-fields), you may create "view" fields which allow you to create custom fields without extra PHP classes.

```php
use Filament\Forms\Components\ViewField;

ViewField::make('notifications')->view('filament.forms.components.checkbox-list')
```

Inside your view, you may interact with the state of the form component using Livewire and Alpine.js.

The `$getStatePath()` callable may be used by the view to retrieve the Livewire property path of the field. You could use this to [`wire:model`](https://laravel-livewire.com/docs/properties#data-binding) a value, or [`$wire.entangle`](https://laravel-livewire.com/docs/alpine-js) it with Alpine.js:

```blade
<div x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }">
    <!-- Interact with the `state` property in Alpine.js -->
</div>
```

## Building custom fields

You may create your own custom field classes and views, which you can reuse across your project, and even release as a plugin to the community.

> If you're just creating a simple custom field to use once, you could instead use a [view field](#view) to render any custom Blade file.

Extend the `Filament\Forms\Components\Field` class, and define the `$view` path of the custom field:

```php
use Filament\Forms\Components\Field;

class CheckboxList extends Field
{
    protected string $view = 'filament.forms.components.checkbox-list';
}
```

Inside your view, you may interact with the state of the form component using Livewire and Alpine.js.

The `$getStatePath()` callable may be used by the view to retrieve the Livewire property path of the field. You could use this to [`wire:model`](https://laravel-livewire.com/docs/properties#data-binding) a value, or [`$wire.entangle`](https://laravel-livewire.com/docs/alpine-js) it with Alpine.js:

```blade
<div x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }">
    <!-- Interact with the `state` property in Alpine.js -->
</div>
```
