<?php

namespace Kelvinwongg\Yapi;

use Kelvinwongg\Yapi\Core\RequestInterface;
use Kelvinwongg\Yapi\Core\ResponseInterface;
use Kelvinwongg\Yapi\Core\File;

interface YapiInterface
{
	/**
	 * init
	 * 
	 * Run at __construct function.
	 * Process the YAPI normal flow if a YAML document presents.
	 *
	 * @param  File $file The YAML document.
	 * @return self Return itself for manual flow.
	 */
	public function init(File $file): self;

	/**
	 * handleRequest
	 * 
	 * Handle inbound request.
	 * Do the CRUD operation and/or before/after hooks if exists.
	 * 
	 * @param  mixed $request
	 * @return ResponseInterface
	 */
	public function handleRequest(RequestInterface $request): ResponseInterface;
	
	/**
	 * createDatabase
	 * 
	 * Create the database schema based on the YAML document.
	 * Return boolean on success/failure.
	 *
	 * @return boolean
	 */
	public function createDatabase(): bool;

	/**
	 * checkDatabase
	 * 
	 * Check the database schema based on the YAML document.
	 * Without any actual change to the database (dry run).
	 * Return boolean on success/failure.
	 *
	 * @return boolean
	 */
	public function checkDatabase(): bool;

	/**
	 * checkYaml
	 * 
	 * Check the integrity of the YAML document.
	 * Without any actual change to the database (dry run).
	 * Return boolean on success/failure.
	 *
	 * @return boolean
	 */
	public function checkYaml(): bool;
}
