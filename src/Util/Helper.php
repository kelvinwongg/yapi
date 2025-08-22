<?php

namespace Yapi\Util;

if (!function_exists(__NAMESPACE__ . '\xd')) {
	function xd(...$var)
	{
		echo '<pre>';
		foreach ($var as $this_var) {
			var_dump($this_var);
		}
		echo '</pre>';
	}
}
