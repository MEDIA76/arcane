<?php

/**
* Arcane PHP: A Simple & Intuitive Web Structure
* Copyright 2017 Joshua Britt
* https://github.com/capachow/arcane-php/
* Released under the MIT License
**/

require_once 'includes/settings.php';
require_once 'includes/functions.php';

if(!file_exists('.htaccess') && file_exists('includes/php.htaccess')) {
	copy('includes/php.htaccess', '.htaccess');
}

foreach(glob(trim(DIR['LOCALES'], '/') . '/*.json') as $transcript) {
	$locales[basename($transcript, '.json')] = $transcript;
	unset($transcript);
}

$path = explode('/', strtok($_SERVER['REQUEST_URI'], '?'));
$path = array_filter(array_diff($path, explode('/', DIR['ROOT'])));
if(!empty($path)) {
	if(array_key_exists($path[1], $locales) || $path[1] == SET['LOCALE']) {
		define('LOCALE', $path[1]);
		define('TRANSCRIPT', json_decode(file_get_contents($locales[LOCALE]), true));
		array_shift($path);
	}
	if(!empty($path)) {
		$path = array_combine(range(1, count($path)), $path);
	}
}
define('PATH', $path);

if(!defined('LOCALE') && !empty(SET['LOCALE'])) {
	$request = explode(',', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']))[0];
	if(!array_key_exists($request, $locales)) {
		$request = SET['LOCALE'];
	}
	header('Location: ' . path($request));
	exit;
} else {
	unset($locales);
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