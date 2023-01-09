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
	private $crudHook;
	private $beforeHook;
	private $afterHook;

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
			$this->crudHook = new \stdClass();
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
		// Is a File object
		if (gettype($pathORYamlStrORFile) === 'object' && get_class($pathORYamlStrORFile) === __NAMESPACE__ . 'Core\File') {
			return $this->file = $pathORYamlStrORFile;
		}
		// Is a Path or YAML String
		return $this->file = new File($pathORYamlStrORFile);
	}

	public function loadRequest(RequestInterface|bool $request = FALSE): RequestInterface
	{
		if (!$request)
			$request = Request::fromGlobal();
		return $this->request = $request;
	}

	// Todo: Refactor Yapi::checkRequest into several functions
	public static function checkRequest(FileInterface $file, RequestInterface $request): bool
	{
		try {
			// 
			// Validate the inbound request with YAML file
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

			// Find request method
			// Todo: Handle path parameters
			$requestMethodFound = $file->getYamlArray()['paths'][$request->path][$request->method];
			// xd($requestMethodFound);

			// Todo: Handle path, header, and cookie parameter
			foreach ($requestMethodFound['parameters'] as $thisParameter) {
				$thisQueryValue = $request->query[$thisParameter['name']] ?? NULL;

				// Check required parameter
				if (array_key_exists('required', $thisParameter)) {
					if (!$request->query || !isset($thisQueryValue)) {
						throw new \Exception(
							sprintf(
								"Required parameter is missing: %s.",
								$thisParameter['name']
							),
							400
						);
					}
				}

				// Only check if this parameter exists in request
				if (isset($thisQueryValue)) {
					// Check parameter schema
					if (!isset($thisParameter['schema'])) {
						throw new \Exception(
							sprintf(
								"Parameter schema is not defined: %s.",
								$thisParameter['name']
							),
							501
						);
					}
					if (!isset($thisParameter['schema']['type'])) {
						throw new \Exception(
							sprintf(
								"Parameter schema type is not defined: %s.",
								$thisParameter['name']
							),
							501
						);
					}

					// Check parameter type
					switch ($thisParameter['schema']['type']) {
						case 'integer':
						case 'float':
						case 'boolean':
							if (!call_user_func(
								[
									__NAMESPACE__ . '\Core\Request',
									'is_' . $thisParameter['schema']['type']
								],
								$thisQueryValue
							)) {
								throw new \Exception(
									sprintf(
										"Request parameter (%s) type mismatched (%s).",
										$thisParameter['name'],
										$thisParameter['schema']['type']
									),
									400
								);
							}
							break;
						case 'string':
							// Check nothing for string parameter schema type
							break;
						default:
							throw new \Exception(
								sprintf(
									"Invalid parameter (%s) schema type (%s).",
									$thisParameter['name'],
									$thisParameter['schema']['type']
								),
								501
							);
							break;
					}

					// Check parameter minimum and maximum
					if ($thisParameter['schema']['type'] == 'integer' || $thisParameter['schema']['type'] == 'float') {
						$castQueryValue = ($thisParameter['schema']['type'] == 'integer') ? intval($thisQueryValue) : floatval($thisQueryValue);
						if (isset($thisParameter['schema']['minimum'])) {
							if ($castQueryValue < $thisParameter['schema']['minimum']) {
								throw new \Exception(
									sprintf(
										"Parameter (%s) is lower than minimum (%s).",
										$thisParameter['name'],
										$thisParameter['schema']['minimum']
									),
									400
								);
							}
						}
						if (isset($thisParameter['schema']['maximum'])) {
							if ($castQueryValue > $thisParameter['schema']['maximum']) {
								throw new \Exception(
									sprintf(
										"Parameter (%s) is higher than maximum (%s).",
										$thisParameter['name'],
										$thisParameter['schema']['maximum']
									),
									400
								);
							}
						}
					}
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

	public function execCrud(FileInterface $file, RequestInterface $request, ResponseInterface $response): bool
	{
		// Figure out the path to the file as stated in the YAML
		$filepath = getcwd() . '/paths';
		if (!@file_exists($filepath)) {
			throw new \Exception(
				sprintf(
					'Execution file not found: %s',
					$filepath
				),
				500
			);
		}
		if (@file_exists($filepath . $request->path . '.php')) {
			$filepath = $filepath . $request->path . '.php';
		} elseif (@file_exists($filepath . $request->path . '/index.php')) {
			$filepath = $filepath . $request->path . '/index.php';
		} else {
			throw new \Exception(
				sprintf(
					'Execution file for request not found: %s',
					$request->path
				),
				500
			);
		}
		$this->crudHook->filepath = $filepath;

		// Find CRUD Hook classname
		$declared_classes_before = get_declared_classes();
		require $this->crudHook->filepath;
		$classname = array_values(array_diff(get_declared_classes(), $declared_classes_before));

		if (count($classname) !== 1) {
			throw new \Exception(
				sprintf(
					'Only one class be can defined in the request Execution file: %s',
					$this->crudHook->filepath
				),
				500
			);
		}
		$this->crudHook->classname = $classname[0];

		// Init CRUD Hook object
		$this->crudHook->instance = new $this->crudHook->classname($file, $request, $response, $this->crudHook);

		// Call request method in the CRUD Hook object
		$this->crudHook->instance->{$this->request->method}($file, $request, $response, $this->crudHook);

		return TRUE;
	}

	public function handleResponse(ResponseInterface $response): ResponseInterface
	{
		$response->send();
		return $response;
	}
}
