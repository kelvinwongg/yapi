<?php

namespace Kelvinwongg\Yapi;

use function Kelvinwongg\Yapi\Util\{xd};
use Kelvinwongg\Yapi\YapiInterface;
use Kelvinwongg\Yapi\Core\File;
use Kelvinwongg\Yapi\Core\FileInterface;
use Kelvinwongg\Yapi\Core\Parser;
use Kelvinwongg\Yapi\Core\ParserInterface;
use Kelvinwongg\Yapi\Core\Request;
use Kelvinwongg\Yapi\Core\RequestInterface;
use Kelvinwongg\Yapi\Core\Response;
use Kelvinwongg\Yapi\Core\ResponseInterface;
use Kelvinwongg\Yapi\Core\Database;
use Kelvinwongg\Yapi\Core\DatabaseInterface;

class Yapi implements YapiInterface
{
	private File $file;

	public function __construct(string $pathORstring)
	{
		$this->file = new File($pathORstring);
		xd($this->file);

		/**
		 * 1. Handle the request
		 */

		/**
		 * 2. Check YAML file
		 */

		/**
		 * 3. Check and create database against YAML file
		 */

		/**
		 * 4. Before hook, CRUD operations, After hook
		 */

		/**
		 * 5. Handle the response
		 */
	}

	public function exec(FileInterface $file): ResponseInterface
	{
		return new Response();
	}

	public function handleRequest(RequestInterface $request): ResponseInterface
	{
		return new Response();
	}

	public static function checkYaml(FileInterface $file, ParserInterface $parser): bool
	{
		if ($parser === NULL) {
			$parser = new Parser();
		}

		/**
		 * Parse and check YAML document
		 */
		$parser = new Parser($file);
		$parser->parseYaml($file);

		return true;
	}

	public static function checkDatabase(FileInterface $file, DatabaseInterface $database): bool
	{
		return true;
	}

	public function createDatabase(FileInterface $file): DatabaseInterface
	{
		return new Database();
	}

	public function execCrud(FileInterface $file): ResponseInterface
	{
		return new Response();
	}

	public function handleResponse(ResponseInterface $response): ResponseInterface
	{
		return $response;
	}
}
