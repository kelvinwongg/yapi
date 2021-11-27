<?php

namespace Kelvinwongg\Yapi\Core;

use function Kelvinwongg\Yapi\Util\{xd};

class Parser
{
	public function __construct()
	{
	}

	public function parseYaml(File $file)
	{
		yaml_parse_file($file->filepath);
	}
}
