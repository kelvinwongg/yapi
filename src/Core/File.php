<?php

namespace Kelvinwongg\Yapi\Core;

use function Kelvinwongg\Yapi\Util\{xd};

class File
{
	public $filepath;

	public function __construct(string $filepath)
	{
		$this->filepath = $filepath;
	}

	private static function getYamlDir($dir = NULL): ?string
	{
		if (!file_exists($dir) || !is_dir($dir)) {
			// Fallback to default location ./endpoints
			$dir = pathinfo(end(debug_backtrace())['file'])['dirname'] . '/endpoints';
		}

		// Ensure trailling slash
		return rtrim($dir, '/') . '/';
	}

	public static function getYamlFromDir($dir = NULL): ?array
	{
		return array_map(
			function ($filepath) {
				return new self($filepath);
			},
			glob(self::getYamlDir($dir) . '*\.y*ml')
		);
	}
}
