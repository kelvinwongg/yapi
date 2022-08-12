<?php

namespace Kelvinwongg\Yapi\Core;

use function Kelvinwongg\Yapi\Util\{xd};
use Kelvinwongg\Yapi\Core\RequestInterface;

class Request implements RequestInterface
{
	private $scriptDirectory;
	protected $uri;
	protected $url;

	public function __construct()
	{
		$this->scriptDirectory = pathinfo($_SERVER['SCRIPT_NAME'])['dirname'];
		$this->uri = str_replace($this->scriptDirectory, '', $_SERVER['REQUEST_URI']);
		$this->url = parse_url($this->uri);
		xd($this->scriptDirectory);
		xd($this->uri);
		xd($this->url);
	}

	public static function fromGlobal(): RequestInterface
	{
		return new self();
	}
}
