<?php

/**
 * Arcane: A Simple & Intuitive Web Architecture
 * Copyright 2017 Joshua Britt
 * https://github.com/capachow/arcane/
 * Released under the MIT License
**/

/* App Settings */

define('DIR', [
  'IMAGES' => '/images/',
  'LAYOUTS' => '/layouts/',
  'LOCALES' => '/locales/',
  'SCRIPTS' => '/scripts/',
  'STYLES' => '/styles/',
  'VIEWS' => '/views/'
]);

define('DEV', [
  'ERRORS' => false,
  'MINIFY' => true
]);

define('SET', [
  'INDEX' => 'index',
  'LOCALE' => null,
  'LAYOUT' => null
]);

/* App Functions */

function path($filter, $actual = false) {
  if(is_bool($filter)) {
    $return = str_replace('//', '/', '/' . implode(@PATH, '/') . '/');
  } else if(is_int($filter)) {
    $return = @PATH[$filter];
  } else {
    $return = $actual ? APP['DIR'] : APP['ROOT'];

    if(preg_match('/\.(jpe?g|.png|.gif|.svg)$/', $filter) && !empty(DIR['IMAGES'])) {
      $return .= DIR['IMAGES'];
    } else if(preg_match('/\.js$/', $filter) && !empty(DIR['SCRIPTS'])) {
      $return .= DIR['SCRIPTS'];
    } else if(preg_match("/\.css$/", $filter) && !empty(DIR['STYLES'])) {
      $return .= DIR['STYLES'];
    } else if(!$actual && defined('LOCALE') && !strpos($filter, '.')) {
      $filter = LOCALE['URL'] . '/' . $filter;
    }

    if(!strpos($filter, '.') && !strpos($filter, '?')) {
      $filter .= '/';
    }

    $return = preg_replace('#(^|[^:])//+#', '\\1/', $return . '/' . $filter);
  }

  return $return;
}

function relay($define, $filter) {
  ob_start();
    $filter();
  define(strtoupper($define), ob_get_clean());
}

function scribe($filter) {
  if(defined('TRANSCRIPT') && @TRANSCRIPT[$filter]) {
    $return = TRANSCRIPT[$filter];
  } else {
    $return = $filter;
  }

  return $return;
}

/* App Information */

define('APP', [
  'DIR' => __DIR__,
  'ROOT' => substr(__DIR__ . '/', strlen(realpath($_SERVER['DOCUMENT_ROOT']))),
  'URI' => $_SERVER['REQUEST_URI']
]);

/* Create Rewrite File */

if(!file_exists('.htaccess')) {
  $htaccess = <<<HTACCESS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !(/$|\.|^$)
    RewriteRule ^(.*)$ %{REQUEST_URI}/ [R=301,L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule . index.php [L]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule . index.php [L]
</IfModule>
HTACCESS;

  file_put_contents('.htaccess', $htaccess, LOCK_EX);
}

/* Create Directories */

foreach(DIR as $directory) {
  $directory = ltrim($directory, '/');

  if(!is_dir($directory) && !empty($directory)) {
    mkdir($directory);
  }

  unset($directory);
}

/* Development Errors */

if(DEV['ERRORS']) {
  error_reporting(E_ALL);

  ini_set('display_errors', 1);
} else {
  error_reporting(E_ALL & ~(E_NOTICE|E_DEPRECATED));

  ini_set('display_errors', 0);
}

/* Generate and Define Locales */

$locales = [];

foreach(glob(rtrim(path(DIR['LOCALES'], true), '/') . '/*/*[-+]*.json') as $locale) {
  $major = basename(dirname($locale));
  $minor = trim(preg_replace('/' . $major . '/', '', basename($locale, '.json'), 1), '+-');
  $files = [
    trim(DIR['LOCALES'], '/') . '/' . $minor . '.json',
    dirname($locale) . '/' . $major . '.json',
    $locale
  ];
  $language = substr(basename($locale, '.json'), 3) === $major ? $minor : $major;
  $country = substr(basename($locale, '.json'), 3) === $major ? $major : $minor;
  $url = '/' . $major . '/';

  if(strpos($locale, '+')) {
    $minor = null;
  } else {
    $url .= $minor . '/';
  }

  $locales[$major][$minor] = [
    'LANGUAGE' => $language,
    'COUNTRY' => $country,
    'CODE' => $language . '-' . $country,
    'URL' => $url,
    'FILES' => $files
  ];

  unset($locale, $major, $minor, $language, $country, $url, $files);
}

define('LOCALES', $locales);

unset($locales);

/* Define Path and Locale from URL */

$path = explode('/', strtok(APP['URI'], '?'));
$path = array_filter(array_diff($path, explode('/', APP['ROOT'])));

if(!empty($path)) {
  $path = array_combine(range(1, count($path)), $path);

  if(array_key_exists($path[1], LOCALES)) {
    if(isset($path[2]) && array_key_exists($path[2], LOCALES[$path[1]])) {
      $locale = LOCALES[$path[1]][$path[2]];

      array_shift($path);
      array_shift($path);
    } else if(array_key_exists(null, LOCALES[$path[1]])) {
      $locale = LOCALES[$path[1]][null];

      array_shift($path);
    }
  }

  if(isset($locale)) {
    define('LOCALE', $locale);

    unset($locale);
  }

  if(!empty($path)) {
    $path = array_combine(range(1, count($path)), $path);
  }
}

define('PATH', $path);

/* Load Locale or Locale Redirect */

if(defined('LOCALE')) {
  $transcripts = [];

  foreach(LOCALE['FILES'] as $file) {
    if(file_exists($file)) {
      $transcripts = json_decode(file_get_contents($file), true) + $transcripts;
    }
  }

  define('TRANSCRIPT', $transcripts);

  unset($transcripts, $file);
} else if(!empty(SET['LOCALE'])) {
  preg_match_all('/[a-z]{2}-[a-z]{2}/', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']), $request, PREG_PATTERN_ORDER);

  foreach(reset($request) as $locale) {
    foreach(LOCALES as $locales) {
      if(in_array($locale, array_column($locales, 'CODE'))) {
        header('Location: ' . path(reset($locales)['URL']));

        exit;
      }
    }
  }

  header('Location: ' . path(SET['LOCALE']));

  exit;
}

/* Define View and Evaluate Routes */

do {
  $view = path(DIR['VIEWS'] . '/' . implode('/', $path) . '.php', true);

  if(!is_file($view) && is_dir(substr($view, 0, -4) . '/')) {
    $view = rtrim(str_replace('.php', '', $view), '/');
    $view = $view . '/' . SET['INDEX'] . '.php';
  }

  if(is_file($view)) {
    ob_start();
      require_once $view;

      unset($view);
    define('CONTENT', ob_get_clean());

    if(defined('ROUTE')) {
      $pseudo = array_diff_assoc(PATH, $path);

      foreach(ROUTE as $route) {
        if(count($route) === count($pseudo)) {
          foreach(array_values($pseudo) as $increment => $segment) {
            if(is_array($route[$increment])) {
              if(!in_array($segment, $route[$increment])) {
                break;
              }
            } else if($route[$increment] !== $segment) {
              break;
            }

            if(end($pseudo) === $segment) {
              $path = $path + $pseudo;

              unset($pseudo, $route, $increment, $segment);

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

/* Minify and Build View */

ob_start(function($filter) {
  if(DEV['MINIFY']) {
    $return = str_replace(["\r\n", "\r", "\n", "\t", '  '], '', $filter);

    return $return;
  } else {
    return $filter;
  }
});
  if(array_diff(PATH, $path)) {
    header('Location: ' . path(implode('/', $path)));

    exit;
  } else if(defined('REDIRECT')) {
    header('Location: ' . path(REDIRECT));

    exit;
  } else {
    unset($path);

    if((defined('LAYOUT') && !empty(LAYOUT)) || !empty(SET['LAYOUT'])) {
      $layout = defined('LAYOUT') ? LAYOUT : SET['LAYOUT'];
      $layout = path(DIR['LAYOUTS'] . '/' . $layout . '.php', true);
    }

    if(isset($layout) && file_exists($layout)) {
      require_once $layout;

      unset($layout);
    } else {
      echo CONTENT;
    }
  }
ob_get_flush();

?>