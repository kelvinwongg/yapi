<?php

namespace Kelvinwongg\Yapi\Util;

function xd($var)
{
	if ($var) {
		echo '<pre>';
		var_dump($var);
		echo '</pre>';
	}
}
