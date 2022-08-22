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
	private array $requestMethodFound;

	public function __construct(string|bool $pathORYamlStrORFile = false)
	{
		/**
		 * Composer autoload for external packages and tools
		 */
		require_once __DIR__ . '/../vendor/autoload.php';

		try {
			/**
			 * 1. Init Response
			 */
			$this->initResponse();
			// xd($this->response);

			/**
			 * 2. Load the YAML
			 */
			if (!$pathORYamlStrORFile) throw new \Exception("File path is Missing");
			$this->loadYaml($pathORYamlStrORFile);
			// xd($this->file);

			/**
			 * 3. Handle the request
			 */
			$this->loadRequest();
			// xd($this->request);

			/**
			 * 4. Check request against YAML file
			 */
			$this->checkRequest($this->file, $this->request);

			/**
			 * 5. Check and create database against YAML file
			 */

			/**
			 * 6. Before hook, CRUD operations, After hook
			 */
			$this->execCrud($this->file, $this->request, $this->response);
		} catch (\Exception $e) {
			$this->response
				->setContent($e->getMessage())
				->setStatusCode($e->getCode());
		}

		/**
		 * 7. Handle the response
		 */
		$this->handleResponse($this->response);
	}

	public function initResponse(ResponseInterface|bool $response = FALSE): ResponseInterface
	{
		if (!$response)
			$response = new Response();
		return $this->response = $response;
	}

	public function loadYaml(FileInterface|string|bool $pathORYamlStrORFile = FALSE): FileInterface
	{
		return $this->file = new File($pathORYamlStrORFile);
	}

	public function loadRequest(RequestInterface|bool $request = FALSE): RequestInterface
	{
		if (!$request)
			$request = Request::fromGlobal();
		return $this->request = $request;
	}

	public static function checkRequest(FileInterface $file, RequestInterface $request): bool
	{
		try {
			// 
			// Validate the inbound request with YAML
			// 

			// Check path
			if (!isset($file->getYamlArray()['paths'][$request->path]))
				throw new \Exception(
					sprintf(
						"Request path does not exist: %s.",
						$request->path
					),
					404
				);

			// Check method
			if (!isset($file->getYamlArray()['paths'][$request->path][$request->method]))
				throw new \Exception(
					sprintf(
						"Request method does not exist: %s.",
						$request->method
					),
					405
				);

			// Found request method (revamp for complex path with placeholder)
			$requestMethodFound = $file->getYamlArray()['paths'][$request->path][$request->method];
			// xd($requestMethodFound);

			foreach ($requestMethodFound['parameters'] as $thisParameter) {
				// Check if required parameters exists
				if (array_key_exists('required', $thisParameter)) {
					if (!$request->query || !array_key_exists($thisParameter['name'], $request->query)) {
						throw new \Exception(
							sprintf(
								"Required parameter is missing: %s.",
								$thisParameter['name']
							),
							400
						);
					}
				}
				// Check parameters schema (type, minimum, maximum)
				if ($thisParameter['schema'] ?? FALSE) {
					if ($thisParameter['schema']['type'] ?? FALSE) {
						switch ($thisParameter['schema']['type']) {
							case 'integer':
								if (isset($request->query[$thisParameter['name']])) {
									if (!Request::is_integer($request->query[$thisParameter['name']])) {
										throw new \Exception(
											sprintf(
												"Request parameter (%s) type mismatched (%s).",
												$thisParameter['name'],
												'integer'
											),
											400
										);
									}
								} else { /* Do nothing if parameter not found in $request */
								}
								break;
							case 'float':
								if (isset($request->query[$thisParameter['name']])) {
									if (!Request::is_float($request->query[$thisParameter['name']])) {
										throw new \Exception(
											sprintf(
												"Request parameter (%s) type mismatched (%s).",
												$thisParameter['name'],
												'float'
											),
											400
										);
									}
								} else { /* Do nothing if parameter not found in $request */
								}
								break;
							case 'boolean':
								if (isset($request->query[$thisParameter['name']])) {
									if (!Request::is_boolean($request->query[$thisParameter['name']])) {
										throw new \Exception(
											sprintf(
												"Request parameter (%s) type mismatched (%s).",
												$thisParameter['name'],
												'boolean'
											),
											400
										);
									}
								} else { /* Do nothing if parameter not found in $request */
								}
								break;
							case 'string':
								// Do nothing for string parameter schema type
								break;
							default:
								throw new \Exception(
									sprintf(
										"Invalid parameter schema type: %s.",
										$thisParameter['name']
									),
									501
								);
								break;
						}
					} else {
						throw new \Exception(
							sprintf(
								"Parameter schema type is not defined: %s.",
								$thisParameter['name']
							),
							501
						);
					}
				} else {
					throw new \Exception(
						sprintf(
							"Parameter schema is not defined: %s.",
							$thisParameter['name']
						),
						501
					);
				}
			}
		} catch (\Exception $e) {
			throw $e;
		}
		return TRUE;
	}

	public static function checkDatabase(FileInterface $file, DatabaseInterface $database): bool
	{
		// To be implemented
		return TRUE;
	}

	public function createDatabase(FileInterface $file): DatabaseInterface
	{
		// To be implemented
		return new Database();
	}

	public function execCrud(FileInterface $file, RequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		return $response;
	}

	public function handleResponse(ResponseInterface $response): ResponseInterface
	{
		$response->send();
		return $response;
	}
}
