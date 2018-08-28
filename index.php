<?php

/**
 * Arcane: Intuitive PHP Boilerplate
 * Copyright 2017-2018 Joshua Britt
 * https://github.com/capachow/arcane/
 * Released under the MIT License
**/

define('DIR', [
  'CACHES' => '/caches/',
  'IMAGES' => '/images/',
  'LAYOUTS' => '/layouts/',
  'LOCALES' => '/locales/',
  'PAGES' => '/pages/',
  'SCRIPTS' => '/scripts/',
  'STYLES' => '/styles/'
]);

define('SET', [
  'ERRORS' => false,
  'INDEX' => 'index',
  'LAYOUT' => null,
  'LOCALE' => null,
  'MINIFY' => true
]);

function cache($path, $data = null) {
  if(is_array($path)) {
    list($path, $access) = $path;
  }

  $path = path($path, true);
  $name = crc32($path);
  $time = $access ?? false ? fileatime($path) : filemtime($path);
  $file = path(DIR['CACHES'], true) . implode('.', [$name, $time]) . '.php';

  if(is_bool($data)) {
    return (file_exists($file) == $data);
  } else if(is_null($data)) {
    if(file_exists($file)) {
      $return = file_get_contents($file);
    }

    return $return ?? $data;
  } else {
    array_map('unlink', glob($name . '.*.php'));

    file_put_contents($file, $data);
  }
}

function path($locator = null, $actual = false) {
  if(is_null($locator)) {
    return str_replace('//', '/', '/' . implode(@URI, '/') . '/');
  } else if(is_int($locator)) {
    return @URI[$locator];
  } else {
    $return = $actual ? APP['DIR'] : APP['ROOT'];

    if(is_array($locator)) {
      list($define, $locator) = $locator;

      $define = DIR[strtoupper($define)];

      if(isset($define) && !empty($define)) {
        $return .= $define;
      }
    }

    if(!strpos($locator, '.')) {
      if(defined('LOCALE') && !$actual) {
        $locator = LOCALE['URI'] . '/' . $locator;
      }

      if(!strpos($locator, '?')) {
        $locator .= '/';
      }
    }

    return preg_replace('#(^|[^:])//+#', '\\1/', $return . '/' . $locator);
  }
}

function relay($define, $function) {
  ob_start();
    $function();
  define(strtoupper($define), ob_get_clean());
}

function scribe($string) {
  if(defined('LOCALE') && @LOCALE['TRANSCRIPT'][$string]) {
    $return = LOCALE['TRANSCRIPT'][$string];
  }

  return $return ?? $string;
}

(function() {
  define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);
  define('APP', [
    'DIR' => __DIR__,
    'ROOT' => substr(__DIR__ . '/', strlen(realpath(__ROOT__))),
    'URI' => $_SERVER['REQUEST_URI']
  ]);

  if(isset(DIR['CACHES']) && !empty(DIR['CACHES'])) {
    define('CACHE', path(DIR['CACHES'], true) . '%d.%d.php');
  }

  if(!file_exists('.htaccess')) {
    $htaccess = implode("\n", [
      '<IfModule mod_rewrite.c>',
      '  RewriteEngine On',
      '  RewriteCond %{REQUEST_URI} !(/$|\.|^$)',
      '  RewriteRule ^(.*)$ %{REQUEST_URI}/ [R=301,L]',
      '  RewriteCond %{REQUEST_FILENAME} !-f',
      '  RewriteRule . index.php [L]',
      '  RewriteCond %{REQUEST_FILENAME} -d',
      '  RewriteRule . index.php [L]',
      '</IfModule>'
    ]);

    file_put_contents('.htaccess', $htaccess);
  }

  foreach(DIR as $directory => $path) {
    $path = trim($path, '/') . '/';

    if(!is_dir($path) && !empty($path)) {
      mkdir($path, 0777, true);

      if($directory === 'PAGES') {
        $html = implode("\n", [
          '<html>',
          '  <body>',
          '    <h1>Hello, world!</h1>',
          '  </body>',
          '</html>'
        ]);

        file_put_contents($path . SET['INDEX'] . '.php', $html);
      }
    }
  }
})();

(function() {
  $directory = rtrim(path(DIR['LOCALES'], true), '/');
  $locales = [];

  if(defined('CACHE')) {
    $cache = sprintf(CACHE, crc32('LOCALES'), fileatime($directory));

    if(file_exists($cache)) {
      $locales = unserialize(file_get_contents($cache));
    }
  }

  if(empty($locales)) {
    foreach(glob($directory . '/*/*[-+]*.json') as $locale) {
      $filename = basename($locale, '.json');
      $major = basename(dirname($locale));
      $minor = trim(preg_replace('/' . $major . '/', '', $filename, 1), '+-');
      $uri = '/' . $major . '/';
      $transcript = [];

      foreach([
        trim(DIR['LOCALES'], '/') . '/' . $minor . '.json',
        dirname($locale) . '/' . $major . '.json',
        $locale
      ] as $file) {
        if(file_exists($file)) {
          $file = json_decode(file_get_contents($file), true);
          $transcript = $file + $transcript;
        }
      }

      switch(substr($filename, 3)) {
        case $major:
          list($language, $country) = [$minor, $major];
        break;

        case $minor:
          list($language, $country) = [$major, $minor];
        break;
      }

      if(strpos($locale, '+')) {
        $minor = null;
      } else {
        $uri .= $minor . '/';
      }

      $locales[$major][$minor] = [
        'CODE' => $language . '-' . $country,
        'COUNTRY' => $country,
        'TRANSCRIPT' => $transcript,
        'LANGUAGE' => $language,
        'URI' => $uri,
      ];
    }

    if(defined('CACHE')) {
      array_map('unlink', glob(strtok($cache, '.') . '.*.php'));

      file_put_contents($cache, serialize($locales));
    }
  }

  define('LOCALES', $locales);
})();

(function() {
  $uri = explode('/', strtok(APP['URI'], '?'));
  $uri = array_filter(array_diff($uri, explode('/', APP['ROOT'])));

  if(!empty($uri)) {
    $uri = array_combine(range(1, count($uri)), $uri);

    if(array_key_exists($uri[1], LOCALES)) {
      if(isset($uri[2]) && array_key_exists($uri[2], LOCALES[$uri[1]])) {
        $locale = LOCALES[$uri[1]][$uri[2]];

        array_shift($uri);
        array_shift($uri);
      } else if(array_key_exists(null, LOCALES[$uri[1]])) {
        $locale = LOCALES[$uri[1]][null];

        array_shift($uri);
      }
    }

    if(isset($locale)) {
      define('LOCALE', $locale);
    }

    if(!empty($uri)) {
      $uri = array_combine(range(1, count($uri)), $uri);
    }
  }

  define('URI', $uri);

  if(!defined('LOCALE') && !empty(SET['LOCALE'])) {
    $pattern = '/[a-z]{2}-[a-z]{2}/';
    $language = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    $default = str_replace('+', '-', SET['LOCALE']);
    $uri = implode(URI, '/');

    preg_match_all($pattern, $language, $request, PREG_PATTERN_ORDER);

    foreach(array_merge(reset($request), [$default]) as $locale) {
      foreach(LOCALES as $locales) {
        if(in_array($locale, array_column($locales, 'CODE'))) {
          header('Location: ' . path(reset($locales)['URI'] . $uri));

          exit;
        }
      }
    }
  }
})();

(function() {
  $path = URI;

  ini_set('display_errors', SET['ERRORS'] ? 1 : 0);

  if(SET['ERRORS']) {
    error_reporting(E_ALL);
  } else {
    error_reporting(E_ALL & ~(E_NOTICE|E_DEPRECATED));
  }

  do {
    $page = path(DIR['PAGES'] . '/' . implode('/', $path) . '.php', true);

    if(!is_file($page) && is_dir(substr($page, 0, -4) . '/')) {
      $page = rtrim(str_replace('.php', '', $page), '/');
      $page = $page . '/' . SET['INDEX'] . '.php';
    }

    if(is_file($page)) {
      define('PATH', $path);
      define('PAGEFILE', $page);

      ob_start();
        unset($path, $page);

        require_once PAGEFILE;

        $path = PATH;
      define('CONTENT', ob_get_clean());

      if(defined('REDIRECT')) {
        header('Location: ' . path(REDIRECT));

        exit;
      }

      if((defined('LAYOUT') && !empty(LAYOUT)) || !empty(SET['LAYOUT'])) {
        $layout = defined('LAYOUT') ? LAYOUT : SET['LAYOUT'];
        $layout = path(DIR['LAYOUTS'] . '/' . $layout . '.php', true);

        if(file_exists($layout)) {
          define('LAYOUTFILE', $layout);
        }
      }

      if(defined('ROUTE')) {
        $facade = array_diff_assoc(URI, $path);

        foreach(ROUTE as $route) {
          if(count($route) === count($facade)) {
            foreach(array_values($facade) as $increment => $segment) {
              if(is_array($route[$increment])) {
                if(!in_array($segment, $route[$increment])) {
                  break;
                }
              } else if($route[$increment] !== $segment) {
                break;
              }

              if(end($facade) === $segment) {
                $path = $path + $facade;

                break 2;
              }
            }
          }
        }
      }

      if(end($path) !== SET['INDEX']) {
        break;
      }
    } else if(empty($path)) {
      return false;
    }

    array_pop($path);
  } while(true);

  if(array_diff(URI, $path)) {
    header('Location: ' . path(implode('/', $path)));

    exit;
  }
})();

(function() {
  ob_start(function($filter) {
    if(SET['MINIFY']) {
      $return = str_replace([
        "\r\n", "\r", "\n", "\t"
      ], '', $filter);
    }

    return $return ?? $filter;
  });
    if(defined('LAYOUTFILE')) {
      require_once LAYOUTFILE;
    } else {
      echo CONTENT;
    }
  ob_get_flush();
})();

?>