# Manual

### Settings

> `'ERRORS' => boolean`: Toggles the display of PHP errors. <nobr />  
> `'INDEX' => 'filename'`: Sets the default page for directories. <nobr />  
> `'LAYOUT' => 'filename'`: Sets the global layout for all pages. Page level overrules global. <nobr />  
> `'LOCALE' => 'BCP 47'`: Sets the default locale for site. This must be set for automated local switching.

### Constants

> `LOCALE`: Contains `array` of current locale data form `LOCALES`. <nobr />  
> `LOCALES`: Contains `array` of available locales data. <nobr />  
> `PATH`: Contains `array` of current file segments. <nobr />  
> `URI`: Contains `array` of current url segments.

- Each `LOCALE` contains `CODE`, `COUNTRY`, `TRANSCRIPT`, `LANGUAGE`, and `URI` keys.

### Functions

###### `Path`

> `path(null)`: Returns the current url path `string`. <nobr />  
> `path('/path/', boolean)`: Returns the reconstructed url path `string`. Pass `true` parameter to use real path. <nobr />  
> `path(integer)`: Returns the requested path segment `string`. Returns `null` if unset. <nobr />  
> `path(['constant', '/path/'])`: Returns the reconstructed url path `string` under assigned DIR `constant`.

``` php
<?= path(); ?>

<?= path('/about/'); ?>

<?= path('/styles/selectors.css', true); ?>

<?= path(2); ?>

<?= path(['IMAGES', '/logo.svg']); ?>
```

- Does not localize paths with file extensions.

###### `Relay`

> `relay('DEFINE', function)`: Creates a `constant` that yields content into layout pages.

```php
<?php relay('SIDEBAR', function() { ?>
  <h2>Heading</h2>
  <p>Paragraph</p>
<?php }); ?>
```

###### `Scribe`

> `scribe('text')`: Returns the equivalent translation `string` from JSON file. Returns itself if unset.

``` php
<?= scribe('Welcome'); ?>
```

### Helpers

> `filename.php`: Creates `variable` from filename that can be used within pages/layouts.

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

- Each file's returned code is automatically loaded.

### Layouts

> `CONTENT`: Returns the page content.

``` html
<html>
  <body>
    <?= CONTENT; ?>
  </body>
</html>
```

### Locales

> `la-co.json`: Creates `/**/**/`. <nobr />  
> `la+co.json`: Creates `/**/`.

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

### Pages

> `define('LAYOUT', 'filename')`: Sets the page layout. <nobr />  
> `define('REDIRECT', '/path/')`: Redirects page. <nobr />  
> `define('ROUTE', [array])`: Sets acceptable page routes.

``` php
<?php define('ROUTE', [
  ['news'],
  ['news', 'history']
]); ?>

<?php define('ROUTE', [
  ['post', array_keys($posts)]
]); ?>
```

- Each route array are for multiple route options.
- Option values `['*', '*']` match `/*/*/` url segments that follow its page segment.
- Option values can be either `string` or `array`.