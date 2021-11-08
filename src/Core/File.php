<?php

namespace Kelvinwongg\Yapi\Core;

use function Kelvinwongg\Yapi\Util\{xd};

class File
{
	public function __construct($dir = NULL)
	{
	}

	public static function findYAMLDir($dir = NULL): ?string
	{
		/**
		 * Find YAML directory
		 */

		// check if user provide a directory path
		// if not exists or not a directory,
		// fallback to the default ./endpoints directory

		if (file_exists($dir) && is_dir($dir)) {
			// Find by user provide $dir
			$dir;
		} else {
			// Default location ./endpoints
			$dir = pathinfo(end(debug_backtrace())['file'])['dirname'] . '/endpoints';
		}

		// Ensure trailling slash
		return rtrim($dir, '/') . '/';
	}

	public static function findYAMLFile($dir = NULL): ?array
	{
		return glob(self::findYAMLDir($dir) . '*\.y*ml');
	}
}
