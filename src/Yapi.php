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
	public File $file;
	public Request $request;
	public Response $response;

	public function __construct(string|bool $pathORYamlStrORFile = false)
	{
		/**
		 * Composer autoload for external packages and tools
		 */
		require_once __DIR__ . '/../vendor/autoload.php';
		
		/**
		 * 1. Init Response
		 */
		$this->initResponse();

		/**
		 * 1. Load the YAML
		 */
		if (!$pathORYamlStrORFile) throw new \Exception("File path is Missing");
		$this->loadYaml($pathORYamlStrORFile);
		// xd($this->file);

		/**
		 * 2. Handle the request
		 */
		$this->loadRequest();
		// xd($this->request);

		/**
		 * 3. Check YAML file
		 */

		/**
		 * 4. Check and create database against YAML file
		 */

		/**
		 * 5. Before hook, CRUD operations, After hook
		 */
		$this->execCrud($this->file, $this->request, $this->response);

		/**
		 * 6. Handle the response
		 */
	}

	public function initResponse(ResponseInterface|bool $response = FALSE): ResponseInterface
	{
		if (!$response) { $response = new Response(); }
		return $this->response = $response;
	}

	public function loadYaml(FileInterface|string|bool $pathORYamlStrORFile = FALSE): FileInterface
	{
		return $this->file = new File($pathORYamlStrORFile);
	}

	public function loadRequest(RequestInterface|bool $request = FALSE): RequestInterface
	{
		return $this->request = Request::fromGlobal();
	}

	public static function checkYaml(FileInterface $file, ParserInterface $parser): bool
	{
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

	public function execCrud(FileInterface $file, RequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		if ( !isset($file->getYamlArray()['paths'][$request->path]))
			throw new \Exception("Request path does not exist");
		if ( !isset($file->getYamlArray()['paths'][$request->path][$request->method]) )
			throw new \Exception("Request method does not exist");
		
		return $response;
	}

	public function handleResponse(ResponseInterface $response): ResponseInterface
	{
		return $response;
	}
}
