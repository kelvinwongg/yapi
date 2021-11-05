<?php

namespace Kelvinwongg\Yapi\Core;

use function Kelvinwongg\Yapi\Util\{xd};
use Kelvinwongg\Yapi\Core\File;

class Parser
{
	public function __construct($path = NULL)
	{
		// $endpointPath = File::findEndpointDirectory($path);
		$endpointPath = File::findEndpointDirectory();
	}
}
