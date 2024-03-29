<?php

namespace Kelvinwongg\Yapi;

use Kelvinwongg\Yapi\Core\DatabaseInterface;
use Kelvinwongg\Yapi\Core\RequestInterface;
use Kelvinwongg\Yapi\Core\ResponseInterface;
use Kelvinwongg\Yapi\Core\FileInterface;
use Kelvinwongg\Yapi\Core\HookInterface;

interface YapiInterface
{
	/**
	 * initResponse
	 *
	 * Initialize the response object
	 * 
	 * @param ResponseInterface $response (optional) Create empty response if not exists
	 * @return ResopnseInterface
	 */
	public function initResponse(ResponseInterface $response): ResponseInterface;

	/**
	 * loadYaml
	 * 
	 * Load YAML file into YAPI.
	 *
	 * @param FileInterface $file
	 * @return FileInterface
	 */
	public function loadYaml(FileInterface $file): FileInterface;

	/**
	 * loadRequest
	 * 
	 * Load inbound request into YAPI.
	 * 
	 * @param  RequestInterface $request
	 * @return ResponseInterface
	 */
	public function loadRequest(RequestInterface $request): RequestInterface;

	/**
	 * checkRequest
	 * 
	 * Check the integrity of inbound request against the YAML file.
	 * Without any actual change to the database (dry run).
	 * Return TRUE on success.
	 * Throw Exception on failure.
	 * ?? Is it necessary to check the YAML file ??
	 * ?? Should it be let failed and throw errors when the YAML data is incorrect ??
	 *
	 * @return boolean
	 */
	public static function checkRequest(FileInterface $file, RequestInterface $request): bool;

	/**
	 * checkDatabase
	 * 
	 * Check the database schema based on the YAML file.
	 * Without any actual change to the database (dry run).
	 * Return boolean on success/failure.
	 *
	 * @return boolean
	 */
	public static function checkDatabase(FileInterface $file, DatabaseInterface $database): bool;

	/**
	 * createDatabase
	 * 
	 * Create the database schema based on the YAML file.
	 * Return DatabaseInterface on success, NULL on failure
	 *
	 * @param  FileInterface $file
	 * @return DatabaseInterface
	 */
	public function createDatabase(FileInterface $file): ?DatabaseInterface;

	/**
	 * loadHook
	 * 
	 * Load Hook file from file path
	 * Return the Hook instance
	 * 
	 * @param String $hookpath
	 * @return HookInterface
	 */
	public function loadHook(string $hookpath): HookInterface;

	/**
	 * execCrud
	 * 
	 * Do the CRUD operation
	 * Do the before/after hooks if exists.
	 *
	 * @param  FileInterface $file The YAML file.
	 * @return bool TRUE on success.
	 */
	public function execCrud(RequestInterface $request, ResponseInterface $response, FileInterface $file, HookInterface $hook): bool;

	/**
	 * handleResponse
	 * 
	 * Handle http headers.
	 * Sent the response in JSON or HTML.
	 *
	 * @param  ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function handleResponse(ResponseInterface $response): ResponseInterface;
}
