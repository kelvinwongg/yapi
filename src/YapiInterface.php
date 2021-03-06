<?php

namespace Kelvinwongg\Yapi;

use Kelvinwongg\Yapi\Core\DatabaseInterface;
use Kelvinwongg\Yapi\Core\RequestInterface;
use Kelvinwongg\Yapi\Core\ResponseInterface;
use Kelvinwongg\Yapi\Core\FileInterface;
use Kelvinwongg\Yapi\Core\ParserInterface;

interface YapiInterface
{
	/**
	 * exec
	 * 
	 * Run at __construct function. ?? Or manually call this method ??
	 * Process the YAPI normal flow if a YAML file presents.
	 *
	 * @param FileInterface $file
	 * @return ResponseInterface
	 */
	public function exec(FileInterface $file): ResponseInterface;

	/**
	 * handleRequest
	 * 
	 * Handle inbound request.
	 * 
	 * @param  RequestInterface $request
	 * @return ResponseInterface
	 */
	public function handleRequest(RequestInterface $request): ResponseInterface;

	/**
	 * checkYaml
	 * 
	 * Check the integrity of the YAML file.
	 * Without any actual change to the database (dry run).
	 * Return boolean on success/failure.
	 * ?? Is it necessary to check the YAML file ??
	 * ?? Should it be let failed and throw errors when the YAML data is incorrect ??
	 *
	 * @return boolean
	 */
	public static function checkYaml(FileInterface $file, ParserInterface $parser): bool;

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
	 * execCrud
	 * 
	 * Do the CRUD operation
	 * Do the before/after hooks if exists.
	 *
	 * @param  FileInterface $file The YAML file.
	 * @return ResponseInterface Return a response of normal flow.
	 */
	public function execCrud(FileInterface $file): ResponseInterface;

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
