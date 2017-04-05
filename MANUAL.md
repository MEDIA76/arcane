# Manual

### Settings

> `INDEX`: Sets the default page for directories. `/example/` = `/example/index.php`. <nobr />  
> `LANGUAGE`: Sets the default locale for site. *This must be set for automated local switching.* <nobr />  
> `LAYOUT`: Sets the global layout for all pages. *Page level overrule global.*

### Functions

> `scribe('string')`: Returns the equivalent translation `string` from JSON file. Returns itself if unset.

``` php
<?= scribe('example'); ?>
```

> `path(integer)`: Returns the requested path segment `string`. Returns `null` if unset.

``` php
<?php path(2); ?>
```

> `path('/path/', boolean)`: Returns the reconstructed url path `string`. Pass `true` parameter to use real path. *Does not localize paths with file extensions.*

``` php
<?= path('/example/'); ?>

<?= path('/example.css'); ?>

<?= path('/example/', true); ?>
```

> `relay('DEFINE', function)`: Creates a `constant` that yields content into layout pages.

```php
<?php relay('EXAMPLE', function() { ?>
	<p>Example</p>
<?php }); ?>
```

### Pages

> `define('TITLE', 'string')`: Sets the page title. <nobr />  
> `define('REDIRECT', '/path/')`: Redirects page. <nobr />  
> `define('LAYOUT', 'filename')`: Sets the page layout. <nobr />  
> `define('ROUTE', array)`: Sets acceptable page routes.

``` php
<?php define('ROUTE', [
	[
		'path-one-example-one'
	],
	[
		'path-one-example-two',
		'path-two-example-two'
	],
	[
		'path-one-example-three',
		['path-two-example-three', 'path-two-example-three']
	]
]); ?>
```

### Layouts

> `TITLE`: Returns the page title. <nobr />  
> `CONTENT`: Returns the page content.

``` html
<html>
	<head>
		<title><?= TITLE; ?></title>
	</head>
	<body>
		<main><?= CONTENT; ?></main>
	</body>
</html>
```