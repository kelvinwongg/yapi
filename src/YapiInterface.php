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
	 * Run at __construct function.
	 * Process the YAPI normal flow if a YAML document presents.
	 *
	 * @param  FileInterface $file The YAML document.
	 * @return ResponseInterface Return a response of normal flow.
	 */
	public function exec(FileInterface $file): ResponseInterface;

	/**
	 * handleRequest
	 * 
	 * Handle inbound request.
	 * Do the CRUD operation and/or before/after hooks if exists.
	 * 
	 * @param  RequestInterface $request
	 * @return ResponseInterface
	 */
	public function handleRequest(RequestInterface $request): ResponseInterface;

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
	 * checkDatabase
	 * 
	 * Check the database schema based on the YAML file.
	 * Without any actual change to the database (dry run).
	 * Return boolean on success/failure.
	 *
	 * @return boolean
	 */
	public static function checkDatabase(FileInterface $file): bool;

	/**
	 * checkYaml
	 * 
	 * Check the integrity of the YAML file.
	 * Without any actual change to the database (dry run).
	 * Return boolean on success/failure.
	 *
	 * @return boolean
	 */
	public static function checkYaml(FileInterface $file, ParserInterface $parser): bool;
}
