<?php

/**
 * Arcane: Intuitive Web Prototyping
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

function cache($path, $content = null) {
  if(is_array($path)) {
    list($path, $access) = [$path[0], $path[1] ?? false];
  }

  $path = path($path, true);
  $access = $access ?? false == true ? fileatime($path) : filemtime($path);
  $file = path(DIR['CACHES'], true) . crc32($path) . '.' . $access . '.php';

  if(is_bool($content)) {
    return (file_exists($file) == $content);
  } else if(is_null($content)) {
    if(file_exists($file)) {
      $content = file_get_contents($file);
    }

    return $content;
  } else {
    array_map('unlink', glob(strtok($file, '.') . '.*.php'));

    file_put_contents($file, $content);
  }
}

function path($locator = null, $actual = false) {
  if(is_null($locator)) {
    return str_replace('//', '/', '/' . implode(URI, '/') . '/');
  } else if(is_int($locator)) {
    return URI[$locator] ?? null;
  } else {
    $prepend = $actual ? APP['DIR'] : APP['ROOT'];

    if(is_array($locator)) {
      list($define, $locator) = [$locator[0], $locator[1] ?? null];

      if(!is_null($define)) {
        $define = DIR[strtoupper($define)];

        if(isset($define) && !empty($define)) {
          $prepend .= $define;
        }
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

    $locator = $prepend . '/' . $locator;
    $locator = preg_replace('#(^|[^:])//+#', '\\1/', $locator);

    return $locator;
  }
}

function relay($define, $content) {
  if(is_callable($content)) {
    ob_start();
      $content();
    $content = ob_get_clean();
  }

  define(strtoupper($define), $content);
}

function scribe($string) {
  if(defined('LOCALE')) {
    if(array_key_exists($string, LOCALE['TRANSCRIPT'])) {
      $string = LOCALE['TRANSCRIPT'][$string];
    }
  }

  return $string;
}

(function() {
  define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);
  define('APP', [
    'DIR' => __DIR__,
    'ROOT' => substr(__DIR__ . '/', strlen(realpath(__ROOT__))),
    'URI' => $_SERVER['REQUEST_URI']
  ]);

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
  $locales = cache([DIR['LOCALES'], true]) ?? [];

  if(!empty($locales)) {
    $locales = unserialize($locales);
  } else {
    $directory = rtrim(path(DIR['LOCALES'], true), '/');

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
          $file = json_decode(file_get_contents($file), true) ?? [];
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

    if(!empty($locales)) {
      cache([DIR['LOCALES'], true], serialize($locales));
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
      $filter = str_replace([
        "\r\n", "\r", "\n", "\t"
      ], '', $filter);
    }

    return $filter;
  });
    if(defined('LAYOUTFILE')) {
      require_once LAYOUTFILE;
    } else {
      echo CONTENT;
    }
  ob_get_flush();
})();

?>