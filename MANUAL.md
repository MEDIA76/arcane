# Manual

### Settings

> `'ERRORS' => boolean`: Toggles the display of PHP errors. <nobr />  
> `'INDEX' => 'filename'`: Sets the default page for directories. <nobr />  
> `'LAYOUT' => 'filename'`: Sets the global layout for all pages. Page level overrules global. <nobr />  
> `'LOCALE' => 'BCP 47'`: Sets the default locale for site. This must be set for automated local switching. <nobr />  
> `'MINIFY' => boolean`: Toggles the minification of source code.

### Functions

###### `Path`

> `path(null)`: Returns the current url path `string`. <nobr />  
> `path(['constant', '/path/'])`: Returns the reconstructed url path `string` under assigned DIR `constant`. <nobr />  
> `path(integer)`: Returns the requested path segment `string`. Returns `null` if unset. <nobr />  
> `path('/path/', boolean)`: Returns the reconstructed url path `string`. Pass `true` parameter to use real path.

``` php
<?= path(); ?>

<?= path(['IMAGES', '/logo.svg']); ?>

<?= path(2); ?>

<?= path('/about/'); ?>

<?= path('/styles/selectors.css', true); ?>
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

### Layouts

> `CONTENT`: Returns the page content. <nobr />  

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
├── co/
│   ├── la-co.json
│   └── co.json
└── la.json

locales/
├── la/
│   ├── la-co.json
│   └── la.json
└── co.json
```

- Uses case-insentative IETF language tags with `ISO 639-1` (language/la) and `ISO 3166-1 Alpha-2` (country/co).
- Folders dictate locale priority and singular ISO files (la/co) are shared resources.

### Pages

> `define('REDIRECT', '/path/')`: Redirects page. <nobr />  
> `define('LAYOUT', 'filename')`: Sets the page layout. <nobr />  
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

- Within the `/about/` page, the `/about/news/` or `/about/news/history/` routes are allowed.
- Within the `/blog/` page, the `/post/*/` route is allowed (`*` equals a key within the `$posts` array).