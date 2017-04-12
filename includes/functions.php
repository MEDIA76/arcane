<?php

	function minify($filter) {
		$return = str_replace(["\r\n", "\r", "\n", "\t", '  '], '', $filter);
		return $return;
	}

	function scribe($filter) {
		if(defined('TRANSCRIPT') && TRANSCRIPT[$filter]) {
			$return = TRANSCRIPT[$filter];
		} else {
			$return = $filter;
		}
		return $return;
	}

	function path($filter, $actual = false) {
		if(is_bool($filter)) {
			$return = str_replace('//', '/', '/' . implode(@PATH, '/') . '/');
		} else if(is_int($filter)) {
			$return = @PATH[$filter];
		} else {
			if($actual) {
				$return = $_SERVER['DOCUMENT_ROOT'];
			} else {
				if(!strpos(DIR['ROOT'], '://')) {
					$return = '/';
				}
				$return = DIR['ROOT'];
			}
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

?>
