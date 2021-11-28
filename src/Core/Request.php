<?php

namespace Kelvinwongg\Yapi\Core;

use Kelvinwongg\Yapi\Core\RequestInterface;

class Request implements RequestInterface
{
	public function __construct()
	{
		echo 'Request construct';
	}
}
