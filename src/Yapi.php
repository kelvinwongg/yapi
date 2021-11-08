<?php

namespace Kelvinwongg\Yapi;

use function Kelvinwongg\Yapi\Util\{xd};
use Kelvinwongg\Yapi\Core\File;
use Kelvinwongg\Yapi\Core\Parser;

class Yapi
{
	public function __construct($dir = NULL)
	{
		$files = File::findYAMLFile($dir);
		foreach ($files as $key => $file) {
			xd($file);
			xd(file_exists($file));
		}
	}
}
