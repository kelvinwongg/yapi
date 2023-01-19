<?php

namespace Kelvinwongg\Yapi\Core;

/**
 * Hook
 * 
 * This class is the base of hooks that are called in YAPI.
 * BeforeHook
 * AfterHook
 * CrudHook
 * 
 * - fromRequest
 * - callCrud
 * - callBefore
 * - callAfter
 */
interface HookInterface
{
	public static function fromRequest(RequestInterface $request, array $config): self;
	public function callCrud(RequestInterface $request, ResponseInterface $response, FileInterface $file, HookInterface $hook): bool;
	public function callBefore(RequestInterface $request, ResponseInterface $response, FileInterface $file, HookInterface $hook): bool;
	public function callAfter(RequestInterface $request, ResponseInterface $response, FileInterface $file, HookInterface $hook): bool;
}
