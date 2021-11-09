<?php

namespace Kelvinwongg\Yapi;

use function Kelvinwongg\Yapi\Util\{xd};
use Kelvinwongg\Yapi\Core\File;
use Kelvinwongg\Yapi\Core\Parser;

class Yapi
{
	public function __construct($dir = NULL)
	{
		/**
		 * 1. Handle the request
		 */

		/**
		 * 2. Parse and check system with YAML document
		 */
		foreach (File::getYamlFromDir($dir) as $file) {
			$this->parse($file);
		}

		/**
		 * 3. Before hook, CRUD operations, After hook
		 */

		/**
		 * 4. Handle the response
		 */
	}

	public function parse($file)
	{
		xd(yaml_parse_file($file->filepath));
	}
}