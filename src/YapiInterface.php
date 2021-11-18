<?php

namespace Kelvinwongg\Yapi;

use Kelvinwongg\Yapi\Core\RequestInterface;
use Kelvinwongg\Yapi\Core\ResponseInterface;
use Kelvinwongg\Yapi\Core\File;

interface YapiInterface
{
	/**
	 * init
	 * run at __construct, process the normal flow,
	 * if user provide a yaml file
	 *
	 * @param  File $file
	 * @return self
	 */
	public function init(File $file): self;

	/**
	 * handleRequest
	 * 
	 * @param  mixed $request
	 * @return ResponseInterface
	 */
	public function handleRequest(RequestInterface $request): ResponseInterface;
	public function createDatabase(): bool;
	public function checkDatabase(): bool;
	public function checkYaml(): bool;
}
