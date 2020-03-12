## Arcane

Arcane is unconventional, but beautifully intuitive. It aims to make simple web prototyping or developing even faster by automating the features you want while making it easier to apply the ones you need, all with just one tiny file of ~11kb.

Arcane was designed to keep things as easy and minimal as possible, making it perfect for beginners and designers. This is not a full featured framework and instead was created to provide a fast and flexible solution for building small web projects with little to zero setup time. Simply upload Arcane's `index.php` within your root [or sub] directory and visit the file via your web browser.

## Download

#### [https://arcane.dev/download](https://arcane.dev/download)

``` shell
curl -fsLO copy.arcane.dev/index.php
```

## Highlights

- Clean URL Paths
- Unique Routing
- Flexible Structure
- Autoload Helpers
- Simple Localization
- Layout Templates
- Environment File
- HTML Minification
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
│  ├─ contact.php
│  ├─ index.php
│  └─ services.php
├─ scripts/
│  ├─ pages/
│  │  └─ contact.js
│  └─ pages.js
├─ styles/
│  └─ default.css
├─ .env
├─ .htaccess
└─ index.php
```

## Support

- Requires Apache & PHP >= 7.0
- `AllowOverride All` directive is required.
- [Examples and Documentation](MANUAL.md)
- [Collection of Helpers](https://github.com/MEDIA76/helpers)
- [Simple Markdown Blog Example](https://github.com/capachow/arcane-blog)

[Creating an issue](https://github.com/MEDIA76/arcane/issues) on GitHub for reporting bugs is always appreciated.

## License

Copyright 2017-2020 [Joshua Britt](https://github.com/capachow) under the [MIT](LICENSE.md).
