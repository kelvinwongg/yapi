<?php

namespace Kelvinwongg\Yapi;

use Kelvinwongg\Yapi\Core\RequestInterface;
use Kelvinwongg\Yapi\Core\ResponseInterface;
use Kelvinwongg\Yapi\Core\ParserInterface;
use Kelvinwongg\Yapi\Core\SystemCheckerInterface;

interface YapiInterface
{
	public function init();
	public function handleRequest(): RequestInterface;
	public function parseYaml(ParserInterface $parser): bool;
	// public function systemCheck(SystemCheckerInterface $checker): bool;
	public function beforeHook(): ResponseInterface;
	public function crudOperation(): ResponseInterface;
	public function afterHook(): ResponseInterface;
	public function handleResponse(): ResponseInterface;
}
