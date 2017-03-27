# Manual

### Settings

> `INDEX`: Sets defaulted page for directories. `/example/` = `/example/index.php`. <nobr />  
> `LANGUAGE`: Sets defaulted locale for your site. *This must be set for automatic local switching.* <nobr />  
> `LAYOUT`: Sets defaulted global layout for all your pages. *Page level defines overrule this.* <nobr />  
> `404`: Sets defaulted 'page not found' landing page. *Disables closest page redirects.*

### Functions

> `scribe('string')`: Returns equivalent translation `string` from JSON file. Returns passed string if unset.

``` php
scribe('example');
```

> `path(integer)`: Returns requested path segment `string`. Returns `null` if unset.

``` php
path(2);
```

> `path('/path/', boolean)`: Returns reconstructed url path `string`. Pass `true` parameter to use real path. *Does not localize passed strings with a file extension.*

``` php
path('/example/');

path('/example.css');

path('/example/', true);
```

> `relay('DEFINE', function)`: Creates a `constant` that yields content into layout pages.

```php
relay('EXAMPLE', function() {
	<p>Example</p>
});
```

### Pages

> `define('TITLE', 'string')`: Sets page title. <nobr />  
> `define('REDIRECT', '/path/')`: Redirects page. <nobr />  
> `define('LAYOUT', 'filename')`: Sets page layout. <nobr />  
> `define('ROUTE', array)`: Sets acceptable page routes.

``` php
define('ROUTE', [
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
]);
```

### Layouts

> `TITLE`: Returns page title. <nobr />  
> `CONTENT`: Returns page content.

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