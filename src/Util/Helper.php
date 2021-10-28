<?php

namespace Kelvinwongg\Yapi\Util;

if (!function_exists(__NAMESPACE__ . '\xd')) {
	function xd($var)
	{
		echo '<pre>';
		var_dump($var);
		echo '</pre>';
	}
}
