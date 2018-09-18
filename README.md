# Arcane

Arcane is unconventional, but beautifully intuitive. Arcane aims to make simple web prototyping/developing even faster by automating the features you want while making it easier to apply the ones you need, all with just one initial file (10 KB).

### Usage

- [http://getarcane.com/download/](http://getarcane.com/download/)

``` shell
curl -fsLO copy.getarcane.com/index.php
```

### Highlights

- Clean URL Paths
- Unique Routing
- Flexible Structure
- Autoload Helpers
- Simple Localization
- Layout Templates
- Native PHP Code
- Zero Dependency

### Example

``` txt
arcane/
├─ helpers/
│  └─ posts.php
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

### Support

- Requires Apache & PHP >= 7.0.0
- [Documentation](MANUAL.md)

[Creating an issue](https://github.com/capachow/arcane/issues/) on GitHub for reporting bugs is always appreciated.

### License

Copyright 2017-2018 [Joshua Britt](https://github.com/capachow/) under the [MIT](LICENSE.md).