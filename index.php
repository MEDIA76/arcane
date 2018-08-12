<?php

/**
 * Arcane: Intuitive Web Application
 * Copyright 2017-2018 Joshua Britt
 * https://github.com/capachow/arcane/
 * Released under the MIT License
**/

/* Application Settings */

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

/* Application Functions */

function path($filter = null, $actual = false) {
  if(is_null($filter)) {
    $return = str_replace('//', '/', '/' . implode(@URI, '/') . '/');
  } else if(is_int($filter)) {
    $return = @URI[$filter];
  } else {
    $return = $actual ? APP['DIR'] : APP['ROOT'];

    if(is_array($filter)) {
      list($define, $filter) = $filter;

      $define = DIR[strtoupper($define)];

      if(isset($define) && !empty($define)) {
        $return .= $define;
      }
    }

    if(!strpos($filter, '.')) {
      if(defined('LOCALE') && !$actual) {
        $filter = LOCALE['URI'] . '/' . $filter;
      }

      if(!strpos($filter, '?')) {
        $filter .= '/';
      }
    }

    $return = $return . '/' . $filter;
    $return = preg_replace('#(^|[^:])//+#', '\\1/', $return);
  }

  return $return;
}

function relay($define, $filter) {
  ob_start();
    $filter();
  define(strtoupper($define), ob_get_clean());
}

function scribe($filter) {
  if(defined('LOCALE') && @LOCALE['TRANSCRIPT'][$filter]) {
    $return = LOCALE['TRANSCRIPT'][$filter];
  } else {
    $return = $filter;
  }

  return $return;
}

/* Application Constants */

(function() {
  define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);
  define('APP', [
    'DIR' => __DIR__,
    'ROOT' => substr(__DIR__ . '/', strlen(realpath(__ROOT__))),
    'URI' => $_SERVER['REQUEST_URI']
  ]);
})();

/* Define CACHE */

(function() {
  if(isset(DIR['CACHES']) && !empty(DIR['CACHES'])) {
    define('CACHE', path(DIR['CACHES'], true) . '%d.%d.php');
  }
})();

/* Create Files and Directories */

(function() {
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

/* Define LOCALES */

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

/* Define LOCALE and URI */

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
})();

/* Define TRANSCRIPT or Redirect */

(function() {
  if(!empty(SET['LOCALE'])) {
    $pattern = '/[a-z]{2}-[a-z]{2}/';
    $language = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);

    preg_match_all($pattern, $language, $request, PREG_PATTERN_ORDER);

    foreach(reset($request) as $locale) {
      foreach(LOCALES as $locales) {
        if(in_array($locale, array_column($locales, 'CODE'))) {
          header('Location: ' . path(reset($locales)['URI']));

          exit;
        }
      }
    }

    header('Location: ' . path(SET['LOCALE']));

    exit;
  }
})();

/* Define CONTENT and Evaluate ROUTE */

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
      ob_start();
        define('REALPATH', $path);
        define('PAGEFILE', $page);

        unset($path, $page);

        require_once PAGEFILE;

        $path = REALPATH;
      define('CONTENT', ob_get_clean());

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

  define('PATH', $path);
})();

/* Redirect or Render Page */

(function() {
  ob_start(function($filter) {
    if(SET['MINIFY']) {
      $return = str_replace([
        "\r\n", "\r", "\n", "\t"
      ], '', $filter);

      return $return;
    } else {
      return $filter;
    }
  });
    if(array_diff(URI, PATH)) {
      header('Location: ' . path(implode('/', PATH)));

      exit;
    } else if(defined('REDIRECT')) {
      header('Location: ' . path(REDIRECT));

      exit;
    } else {
      if((defined('LAYOUT') && !empty(LAYOUT)) || !empty(SET['LAYOUT'])) {
        $layout = defined('LAYOUT') ? LAYOUT : SET['LAYOUT'];
        $layout = path(DIR['LAYOUTS'] . '/' . $layout . '.php', true);
      }

      if(isset($layout) && file_exists($layout)) {
        define('LAYOUTFILE', $layout);

        unset($layout);

        require_once LAYOUTFILE;
      } else {
        echo CONTENT;
      }
    }
  ob_get_flush();
})();

?>