<?php

namespace Yapi\Core;

use function Yapi\Util\{xd};
use Yapi\Core\RequestInterface;

class Request implements RequestInterface
{
	public $url;
	public $basepath;
	public $path;
	public $query;
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
		if ($uri['query'] ?? FALSE) {
			parse_str($uri['query'], $global['query']);
		}
		$global['method'] = strtolower($_SERVER['REQUEST_METHOD']);

		return new self($global);
	}

	public static function is_integer(string $value): bool
	{
		$castedValue = (int) $value;
		return (strval($castedValue) === $value);
	}

	public static function is_float(string $value): bool
	{
		$castedValue = (float) $value;
		return (strval($castedValue) === $value);
	}

	public static function is_boolean(string $value): bool
	{
		return filter_var($value, FILTER_VALIDATE_BOOLEAN);
	}
}
