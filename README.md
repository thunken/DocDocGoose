Generates a clean HTML'd API documentation. Based on your routes declarations and filtered patterns.

# Install

```bash
composer require thunken/doc-doc-goose
```

Add
```php
Thunken\DocDocGoose\DocDocGooseProvider::class,
```
in *config/app.php* providers array

## Publish files (if you need some tweaks on config and views)
```bash
php artisan vendor:publish --provider="Thunken\DocDocGoose\DocDocGooseProvider"
```

## Config changes
```php
return [
    'routes' => [
        'patterns' => [ 'api.*' ],
    ],
    'rules' => [
        'headers' => [
            'Authorization' => '<Your API Key>'
        ]
    ]
];
```

## Facade 

Already declared in provider so this should be useless. Still, to do so, add
```php
'Extractor' => Thunken\DocDocGoose\Facades\Extractor::class,
```
in *config/app.php* facades array


# Usage

## In views
As simple as injecting 
```php
{!! \Extractor::renderMenu()  !!}
```
and / or
```php
{!! \Extractor::renderContent()  !!}
```
in your templates.

## Use the raw extractor output
```php
$extractor = app(Extractor::class);
$extractor->extract();

/** @var array $docsAsArray */
$docAsArray = $extractor->toArray(); // returns an array

/** @var Collection $docsAsGroups */
$docsAsGroups = $extractor->toRaw(); // returns a Group Collection

```

# @TODOs
- Goals description
- More documentation

# References and thanks
* Widely and wisely using https://github.com/mpociot/laravel-apidoc-generator library (Thanks)
