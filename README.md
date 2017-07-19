# Arcane PHP

Arcane is unconventional, but beautifully intuitive. Arcane PHP aims to make simple website developing/prototyping even faster by automating the features you want while making it easier to manage the ones you need, all with just one initial file.

### Highlights

- Clean URL paths
- Unique page routing
- HTML minification
- Clear file structure
- Simple localization
- Page layout templates
- Native PHP code

### Structure (Example)

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
│   ├── gb/
│   │   └── en+gb.json
│   ├── us/
│   │   ├── es-us.json
│   │   ├── en+us.json
│   │   └── us.json
│   └── en.json
├── pages/
│   └── index.php
├── scripts/
│   └── selectors.css
├── styles/
│   └── functions.js
└── index.php
```

### Support

- Requires Apache & PHP >= 7.0.0

[Creating an issue](https://github.com/capachow/arcane-php/issues/) on GitHub for reporting bugs is always appreciated.

### License

Copyright 2017 [Joshua Britt](https://github.com/capachow/) under the [MIT](LICENSE.md).