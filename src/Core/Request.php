<?php

namespace Kelvinwongg\Yapi\Core;

use function Kelvinwongg\Yapi\Util\{xd};
use Kelvinwongg\Yapi\Core\RequestInterface;

class Request implements RequestInterface
{
	public $url;
	public $basepath;
	public $path;
	public $method;

	public function __construct($args)
	{
		$this->url = $args['url'];
		$this->basepath = $args['basepath'];
		$this->path = $args['path'];
		$this->method = $args['method'];
	}

	public static function fromGlobal(): RequestInterface
	{
		$dirname = pathinfo($_SERVER['SCRIPT_NAME'])['dirname'];
		$global['url'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		$global['basepath'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $dirname;
		$global['path'] = str_replace($dirname, '', $_SERVER['REQUEST_URI']);
		$global['method'] = strtolower($_SERVER['REQUEST_METHOD']);
		return new self($global);
	}
}
