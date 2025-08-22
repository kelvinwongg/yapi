<?php

namespace Yapi\Core;

/**
 * ResponseInterface
 * 
 * This class represent the outbound http response,
 * and all the functions to prepare it to be run in YAPI.
 * 
 * - setContent
 * - getContent
 * - setStatusCode
 * - addHeader
 * - send
 * - redirect
 * - setContentType
 */
interface ResponseInterface
{
	public function setContent($content): static;
	public function getContent(): mixed;
	public function setStatusCode($statusCode): static;
	public function addHeader($header): static;
	public function send(): bool;
	public function redirect($location): bool;
}
