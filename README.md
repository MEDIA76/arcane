# Arcane

Arcane is unconventional, but beautifully intuitive. Arcane aims to make simple web developing or prototyping even faster by automating the features you want while making it easier to apply the ones you need, all with just one initial file (~8KB).

### Highlights

- Clean URL Paths
- Unique Routing
- HTML Minification
- Clear File Structure
- Simple Localization
- Layout Templates
- Native PHP Code
- Zero Dependency

### Structure Example

``` txt
arcane/
├── images/
│   └── logo.svg
├── layouts/
│   ├── partials/
│   │   ├── footer.php
│   │   └── header.php
│   └── default.php
├── locales/
│   ├── es/
│   │   └── es+us.json
│   ├── en/
│   │   ├── en-gb.json
│   │   ├── en+us.json
│   │   └── en.json
│   └── us.json
├── pages/
│   ├── about.php
│   ├── blog/
│   │   ├── entry.php
│   │   └── index.php
│   ├── contact.php
│   ├── index.php
│   └── services.php
├── scripts/
│   └── functions.js
├── styles/
│   └── selectors.css
└── index.php
```

### Support

- Requires Apache & PHP >= 7.0.0
- [Documentation](MANUAL.md)

[Creating an issue](https://github.com/capachow/arcane/issues/) on GitHub for reporting bugs is always appreciated.

### License

Copyright 2017-2018 [Joshua Britt](https://github.com/capachow/) under the [MIT](LICENSE.md).