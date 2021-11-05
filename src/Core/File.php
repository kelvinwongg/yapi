<?php

namespace Kelvinwongg\Yapi\Core;

use function Kelvinwongg\Yapi\Util\{xd};

class File
{
	public function __construct($path = NULL)
	{
	}

	public static function findEndpointDirectory($path = NULL): ?string
	{
		/**
		 * Find YAML directory
		 */

		if (!$path) {
			// Find by default location ./endpoints
			$path = pathinfo(end(debug_backtrace())['file'])['dirname'] . '/endpoints';
		}

		// Find by user provide $path
		if (file_exists($path) && is_dir($path)) {
			return $path;
		}
		return NULL;
	}
}
