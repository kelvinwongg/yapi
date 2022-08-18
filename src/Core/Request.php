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
		$this->url = $args['url'] ?? NULL;
		$this->basepath = $args['basepath'] ?? NULL;
		$this->path = $args['path'] ?? NULL;
		$this->query = $args['query'] ?? NULL;
		$this->method = $args['method'] ?? NULL;
	}

	public static function fromGlobal(): RequestInterface
	{
		$dirname = pathinfo($_SERVER['SCRIPT_NAME'])['dirname'];
		$uri = parse_url($_SERVER['REQUEST_URI']);

		$global['url'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		$global['basepath'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $dirname;
		$global['path'] = str_replace($dirname, '', $uri['path']);
		parse_str($uri['query'], $global['query']);
		$global['method'] = strtolower($_SERVER['REQUEST_METHOD']);

		return new self($global);
	}
}
