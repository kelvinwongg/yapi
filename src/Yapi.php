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
	// Todo: Create Core\HookInterface
	protected $crudHook;
	protected $beforeHook;
	protected $afterHook;
	protected $config;

	public function __construct(string|bool $pathORYamlStrORFile = false, array $config = [])
	{
		/**
		 * Composer autoload for external packages and tools
		 */
		require_once __DIR__ . '/../vendor/autoload.php';

		// Merge $config with default values
		$this->config = array_merge(array(
			'paths' => '/paths',
		), $config);

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
			// xd($this->request);

			/**
			 * 5. Check and create database against YAML file
			 */

			/**
			 * 6. Before hook, CRUD operations, After hook
			 */
			$this->crudHook = new \stdClass();
			$this->execCrud($this->file, $this->request, $this->response);
			// xd($this->request);
			// xd($this->response);
			// xd($this->crudHook);
			// xd($this->file);
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

	public static function checkRequest(FileInterface $file, RequestInterface $request): bool
	{
		try {
			// 
			// Validate the inbound request with YAML file
			// 

			// Match path
			self::matchPath($file, $request);

			// Check operation
			self::checkOperation($file, $request);

			// Check parameters
			// Todo: Handle header, and cookie parameter
			self::checkParameters($file, $request);
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
		$this->crudHook->filepath = $this->findHookFile($request);

		// Find CRUD Hook classname
		$declared_classes_before = get_declared_classes();
		require $this->crudHook->filepath;
		$classname = array_values(array_diff(get_declared_classes(), $declared_classes_before));

		if (count($classname) === 0) {
			throw new \Exception(
				sprintf(
					'No class is defined in the request Execution file: %s',
					$this->crudHook->filepath
				),
				500
			);
		}
		if (count($classname) > 1) {
			throw new \Exception(
				sprintf(
					'Only one class could be defined in the request Execution file: %s',
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

	/**
	 * This function match Parameter Types for Path and Query
	 * https://swagger.io/docs/specification/describing-parameters/#path-parameters
	 * https://swagger.io/docs/specification/describing-parameters/#query-parameters
	 */
	private static function matchPath(FileInterface $file, RequestInterface &$request)
	{
		// Convert and match $request with paths in $file
		$path_match = FALSE;
		foreach (self::convertPathRegexp($file) as $this_path => $this_regexp) {
			if (preg_match($this_regexp, $request->path, $matches)) {
				$path_match = [
					'path' => $this_path,
					'path_parameters' => array_filter($matches, fn ($key) => (is_string($key) && substr($key, 0, 1) !== "_"), ARRAY_FILTER_USE_KEY)
				];
				break;
			}
		}
		if (!$path_match) {
			throw new \Exception(
				sprintf(
					"Request path does not exist: %s.",
					$request->path
				),
				404
			);
		}
		$request->match = $path_match;
		return $path_match;
	}

	/**
	 * Convert each paths in YAML into regex to match path in request
	 * Reference: https://codeigniter.com/user_guide/incoming/routing.html#auto-routing-improved
	 * 
	 * Before: /employees
	 * After: /^(?<_1>\/employees)\/?$/
	 * 
	 * Before: /employees/{id}
	 * After: /^(?<_1>\/employees)\/(?<id>[^\/]+)\/?$/
	 */
	private static function convertPathRegexp(FileInterface $file)
	{
		$ret = [];
		foreach (array_keys($file->getYamlArray()['paths']) as $this_path) {
			// Convert parts not in curly brace
			$not_in_curly = '/(^\/[^{}\/]+(?![^{]*})|[^{}\/]+(?![^{]*}))/';
			$regexp = preg_replace_callback($not_in_curly, function ($match) {
				static $count = 0;
				$count++;
				return "(?<_{$count}>{$match[0]})";
			}, $this_path);
			// xd($regexp);

			// Convert slashes
			$slash = '/(\/)/';
			$regexp = preg_replace_callback($slash, function ($match) {
				return "\/";
			}, $regexp);
			// xd($regexp);

			// Convert curly brace parts
			$curly_brace = '/{(.+)}/';
			$regexp = preg_replace_callback($curly_brace, function ($match) {
				return "(?<{$match[1]}>[^\/]+)";
			}, $regexp);
			// xd($regexp);

			$ret[$this_path] = '/^' . $regexp . '\/?$/';
		}
		return $ret;
	}

	private static function checkOperation(FileInterface $file, RequestInterface $request)
	{
		$match_path = $file->getYamlArray()['paths'][$request->match['path']];
		$match_operation = array_key_exists($request->method, $match_path) ? $match_path[$request->method] : FALSE;
		if (!$match_operation)
			throw new \Exception(
				sprintf(
					"Request method does not exist: %s.",
					$request->method
				),
				405
			);
	}

	private static function checkParameters(FileInterface $file, RequestInterface $request)
	{
		$match_path = $file->getYamlArray()['paths'][$request->match['path']];
		$match_operation = array_key_exists($request->method, $match_path) ? $match_path[$request->method] : FALSE;
		foreach ($match_operation['parameters'] as $this_parameter) {
			// Get Parameter Value from Request
			switch ($this_parameter['in']) {
				case 'query':
					$req_param_value = ($request->query && array_key_exists($this_parameter['name'], $request->query)) ? $request->query[$this_parameter['name']] : NULL;
					break;
				case 'path':
					$req_param_value = $request->match['path_parameters'][$this_parameter['name']];
					break;
				default:
					$req_param_value = NULL;
					break;
			}

			// Check required parameter
			if (array_key_exists('required', $this_parameter) && $this_parameter['required']) {
				if (!isset($req_param_value)) {
					throw new \Exception(
						sprintf(
							"Required parameter is missing: %s.",
							$this_parameter['name']
						),
						400
					);
				}
			}
			// xd($request);
			// xd($this_parameter);
			// xd($req_param_value);

			// Only check if this parameter exists in request
			if (isset($req_param_value)) {
				// Check if parameter schema exists
				if (!isset($this_parameter['schema'])) {
					throw new \Exception(
						sprintf(
							"Parameter schema is not defined: %s.",
							$this_parameter['name']
						),
						501
					);
				}

				// Check if parameter type exists
				if (!isset($this_parameter['schema']['type'])) {
					throw new \Exception(
						sprintf(
							"Parameter schema type is not defined: %s.",
							$this_parameter['name']
						),
						501
					);
				}

				$this_param_type = $this_parameter['schema']['type'];

				// Check this parameter type
				switch ($this_param_type) {
					case 'integer':
					case 'float':
					case 'boolean':
						if (!call_user_func(
							[
								__NAMESPACE__ . '\Core\Request',
								'is_' . $this_param_type
							],
							$req_param_value
						)) {
							throw new \Exception(
								sprintf(
									"Request parameter (%s) type mismatched (%s).",
									$this_parameter['name'],
									$this_param_type
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
								$this_parameter['name'],
								$this_param_type
							),
							501
						);
						break;
				}

				// Check parameter min/max for integer/float
				if ($this_param_type == 'integer' || $this_param_type == 'float') {
					$cast_req_param_value = ($this_param_type == 'integer') ? intval($req_param_value) : floatval($req_param_value);
					if (isset($this_parameter['schema']['minimum'])) {
						if ($cast_req_param_value < $this_parameter['schema']['minimum']) {
							throw new \Exception(
								sprintf(
									"Parameter (%s) is lower than minimum (%s).",
									$this_parameter['name'],
									$this_parameter['schema']['minimum']
								),
								400
							);
						}
					}
					if (isset($this_parameter['schema']['maximum'])) {
						if ($cast_req_param_value > $this_parameter['schema']['maximum']) {
							throw new \Exception(
								sprintf(
									"Parameter (%s) is higher than maximum (%s).",
									$this_parameter['name'],
									$this_parameter['schema']['maximum']
								),
								400
							);
						}
					}
				}
			}
		}
	}

	private function findHookFile(RequestInterface $request): String
	{
		// Basepath for 'paths' directory
		$filepath = getcwd() . $this->config['paths'];
		if (!@file_exists($filepath)) {
			throw new \Exception(
				sprintf(
					'Execution file directory not found: %s',
					$filepath
				),
				500
			);
		}

		// Todo: Handle multiple segments, path template uri, and trailing slash
		xd($filepath . $request->path . '.php');
		xd($filepath . $request->path . '/index.php');
		xd($request);
		xd($this->crudHook);
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

		return $filepath;
	}
}
