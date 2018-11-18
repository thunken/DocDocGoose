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
        'v1' => [
            'patterns' => [ 'api.v1.*' ],
            'rules' => [
                'headers' => [
                    'Authorization' => '<Your API Key>'
                ]
            ]
        ]
    ],
    'cache' => [
        'enabled' => true,
        'store' => 'file'
    ]
];
```

Here you can manage versions, headers by version and caching feature.  
Cache is enable by default and using the file store, you can disable it by putting 'enable' to false.  

File cache is the best cache method if your cache files are not persisted across deployment. It's a good way to ensure your documentation is up to date with the current version of your API code at each deployment.
You can reproduce this behavior by resetting the _Extractor::cacheName_ key in your choosen cache store.  

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
- Tests
- Tests
- And Tests

# References and thanks
* Widely and wisely using https://github.com/mpociot/laravel-apidoc-generator library (Thanks)
