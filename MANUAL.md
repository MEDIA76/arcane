## Summary

#### [Settings](#settings), [Functions](#functions) ([Env](#env), [Path](#path), [Relay](#relay), [Scribe](#scribe)), [Helpers](#helpers), [Layouts](#layouts), [Pages](#pages), [Constants](#constants)

## Settings

> `'ERRORS'` => `boolean` = `false`: Toggles the display of PHP errors. <nobr />  
> `'INDEX'` => `'filename'` = `'index'`: Sets the default page for directories. <nobr />  
> `'LAYOUT'` => `'filename'` = `null`: Sets the global layout for all pages. Page level overrules global. <nobr />  
> `'LOCALE'` => `'BCP 47'` = `null`: Sets the default locale for site. This must be set for automated local switching. <nobr />  
> `'MINIFY'` => `boolean` = `true`: Toggles the minification of HTML code.

- Settings can be defined within `.env` via `SET_` prefix.
- Like settings, directories can also be defined via `DIR_` prefix.

## Functions

#### `Env`

> env(`'KEY'`, `'string'`|`boolean` = `null`): Returns corresponding environment variable `value`. Pass second parameter for fallback value.

``` php
<?php $secret = $env('SECRET_KEY'); ?>

<?php $mode = $env('APP_MODE', 'local'); ?>
```

#### `Path`

> path(`null`): Returns the current url path `string`. <nobr />  
> path(`integer`): Returns the requested path segment `string`. Returns `null` if unset. <nobr />  
> path(`'/path/'`|`['CONSTANT', '/path/']`, `boolean` = `false`): Returns the reconstructed url path `string` or `string` under assigned DIR `constant`. Pass second parameter to use real path.

``` php
<?= path(); ?>

<?= path(2); ?>

<?= path('/about/'); ?>

<?= path('/layouts/header.php', true); ?>

<?= path(['IMAGES', 'logo.svg']); ?>
```

- Does not localize paths with file extensions.

#### `Relay`

> relay(`'DEFINE'`, `function`|`*`): Creates a `constant` that yields content into layout pages.

```php
<?php relay('SIDEBAR', function() { ?>
  <h2>Heading</h2>
  <p>Paragraph</p>
<?php }); ?>

<?php relay('TITLE', 'Home'); ?>
```

#### `Scribe`

> scribe(`'text'`|`['text', *]`, `array` = `[]`): Returns the equivalent translation `string` or `string` with fallback value. Pass second parameter to preform key/value replacements.

``` php
<?= scribe('Welcome'); ?>

<?= scribe('Farewell :name', [
  ':name' => 'John'
]); ?>

<?= scribe(['variable.name', false]); ?>
```

## Helpers

> `filename`.php: Creates a `variable` from filename that can be used within pages.

``` php
<?php return [
  'slug' => [
      'title' => 'Hello, World!'
  ]
]; ?>

<?php return function($array, $keys) {
  $keys = explode('.', $keys);

  foreach($keys as $key) {
    $array = $array[$key];
  }

  return $array;
}; ?>
```

- Page specific helpers are collected by creating matching `/filename/` directory.
- Each file's returned code is automatically loaded and traverses each directory upward.

## Layouts

> `CONTENT`: Returns page content. <nobr />  
> `SCRIPTS`: Returns javascript tags. <nobr />  
> `STYLES`: Returns stylesheet tags.

``` html
<html>
  <head>
    <?= STYLES; ?>
  </head>
  <body>
    <?= CONTENT; ?>
    <?= SCRIPTS; ?>
  </body>
</html>
```

## Locales

> `la-co`.json: Creates `/**/**/`. <nobr />  
> `la+co`.json: Creates `/**/`.

``` html
locales/
├─ ca/
│  ├─ en-ca.json
│  └─ fr-ca.json
├─ mx/
│  └─ es-mx.json
├─ us/
│  ├─ en-us.json
│  ├─ es-us.json
│  └─ us.json
├─ es.json
└─ en.json

locales/
├─ en/
│  ├─ en-ca.json
│  ├─ en+us.json
│  └─ en.json
├─ es/
│  ├─ es+mx.json
│  ├─ es-us.json
│  └─ es.json
├─ fr/
│  └─ fr+ca.json
├─ ca.json
└─ us.json
```

- Supports both country or language localization (dictated by folder name).
- Uses IETF language tags with `ISO 639-1` (language/la) and `ISO 3166-1 Alpha-2` (country/co).
- Singular `ISO` files (la/co) are defaulted and shared locale resources.

## Pages

> `filename`.php: Creates `/filename/` url segment. <nobr />  
> define(`'LAYOUT'`, `'filename'`): Sets the page layout. <nobr />  
> define(`'REDIRECT'`, `'/path/'`): Redirects page. <nobr />  
> define(`'ROUTES'`, `array`): Sets acceptable page routes.

``` php
<?php define('ROUTES', [
  ['news'],
  ['news', 'history']
]); ?>

<?php define('ROUTES', [
  ['post', array_keys($posts)]
]); ?>
```

- Routes `array` values are for multiple route options.
- Route option `string` values are converted to `array` by `/`.
- Route option `array` values `['*', '*']` match `/*/*/` url segments.
- Route option `array` values can be either `string` or `array`.

## Constants

> `CONTENT`: Contains output of current page. <nobr />  
> `LOCALE`: Contains `array` of current locale data from `LOCALES`. <nobr />  
> `LOCALES`: Contains `array` of available locales data. <nobr />  
> `PATH`: Contains `array` of current file segments. <nobr />  
> `URI`: Contains `array` of current url segments.

- Each `LOCALE` contains `CODE`, `COUNTRY`, `FILES`, `LANGUAGE`, and `URL` keys.
- Other constants include `__ROOT__`, `APP`, `DIR`, `LAYOUTFILE`, `PAGEFILE`, `PATHS`, `SET`, `TRANSCRIPT`.