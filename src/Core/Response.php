<?php

namespace Kelvinwongg\Yapi\Core;

use Kelvinwongg\Yapi\Core\ResponseInterface;

class Response implements ResponseInterface
{
	public function __construct()
	{
		echo 'Response construct';
	}
}
