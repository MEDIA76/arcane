<?php

/**
* Arcane PHP: A Simple & Intuitive Web Structure
* Copyright 2017 Joshua Britt
* https://github.com/capachow/arcane-php/
* Released under the MIT License
**/

require_once 'includes/settings.php';
require_once 'includes/functions.php';

define('APP', [
	'DIR' => __DIR__,
	'ROOT' => str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ . '/'),
	'URI' => $_SERVER['REQUEST_URI']
]);

if(!file_exists('.htaccess') && file_exists('includes/php.htaccess')) {
	copy('includes/php.htaccess', '.htaccess');
}

if(DEV['ERRORS']) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
} else {
	error_reporting(E_ALL & ~(E_NOTICE|E_DEPRECATED));
	ini_set('display_errors', 0);
}

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

$path = explode('/', strtok(APP['URI'], '?'));
$path = array_filter(array_diff($path, explode('/', APP['ROOT'])));
if(!empty($path)) {
	$path = array_combine(range(1, count($path)), $path);
	if(array_key_exists($path[1], LOCALES)) {
		if(isset($path[2]) && array_key_exists($path[2], LOCALES[$path[1]])) {
			$locale = LOCALES[$path[1]][$path[2]];
			array_shift($path); array_shift($path);
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

do {
	$page = path(DIR['PAGES'] . '/' . implode('/', $path) . '.php', true);
	if(!is_file($page) && is_dir(substr($page, 0, -4) . '/')) {
		$page = rtrim(str_replace('.php', '', $page), '/');
		$page = $page . '/' . SET['INDEX'] . '.php';
	}
	if(is_file($page)) {
		ob_start();
			require_once $page;
			unset($page);
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

ob_start('minify');
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