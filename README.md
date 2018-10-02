## Arcane

Arcane is unconventional, but beautifully intuitive. Arcane aims to make simple web prototyping/developing even faster by automating the features you want while making it easier to apply the ones you need, all with just one initial file (8 KB).

Arcane was designed to keep things as easy and minimal as possible. This is not a full featured framework and instead was created to provide a fast and flexible solution for building small web projects with little to zero setup time. Simply upload Arcane's `index.php` within your root [or sub] directory and visit the file via your web browser.

## Download

#### [http://getarcane.com/download/](http://getarcane.com/download/)

``` shell
curl -fsLO copy.getarcane.com/index.php
```

## Highlights

- Clean URL Paths
- Unique Routing
- Flexible Structure
- Autoload Helpers
- Simple Localization
- Layout Templates
- Native PHP Code
- Zero Dependency

## Example

``` txt
arcane/
├─ helpers/
│  ├─ blog/
│  │  └─ posts.php
│  └─ truncate.php
├─ images/
│  └─ logo.svg
├─ layouts/
│  ├─ partials/
│  │  ├─ footer.php
│  │  └─ header.php
│  └─ default.php
├─ locales/
│  ├─ en/
│  │  └─ en+us.json
│  ├─ es/
│  │  ├─ es+mx.json
│  │  └─ es-us.json
│  └─ us.php
├─ pages/
│  ├─ about.php
│  ├─ blog/
│  │  └─ index.php
│  ├── contact.php
│  ├── index.php
│  └── services.php
├─ scripts/
│  └─ functions.js
├─ styles/
│  └─ selectors.css
└─ index.php
```

## Support

- Requires Apache & PHP >= 7.0.0
- `AllowOverride All` is required for subdirectory use.
- [Examples and Documentation](MANUAL.md)

[Creating an issue](https://github.com/capachow/arcane/issues/) on GitHub for reporting bugs is always appreciated.

## License

Copyright 2017-2018 [Joshua Britt](https://github.com/capachow/) under the [MIT](LICENSE.md).