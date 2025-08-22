<?php

namespace Yapi\Core;

/**
 * RequestInterface
 * 
 * This class represent the inbound http request,
 * and all the functions to prepare it to be run in YAPI.
 * - Create itself from $SERVER global variables
 */
interface RequestInterface
{
	public static function fromGlobal(): self;
}