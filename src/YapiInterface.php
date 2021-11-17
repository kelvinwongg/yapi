<?php

namespace Kelvinwongg\Yapi;

use Kelvinwongg\Yapi\Core\ResponseInterface;
use Kelvinwongg\Yapi\Core\File;

interface YapiInterface
{
	/**
	 * What does YAPI do?
	 */	

	/**
	 * init, run at __construct, process the normal flow,
	 * if user provide a yaml file
	 *
	 * @param  File $file
	 * @return self
	 */
	public function init(File $file): self;

	public function handleRequest(): ResponseInterface;
	public function createDatabase(): bool;
	public function checkDatabase(): bool;
	public function checkYaml(): bool;
}
