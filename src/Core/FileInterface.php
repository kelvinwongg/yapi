<?php

namespace Kelvinwongg\Yapi\Core;

/**
 * FileInterface
 * 
 * - Sole representation of a YAML File.
 * - This is the source of truth of the whole system.
 * - No logics and checking is done
 *   against the content of this File class.
 * - No amendment to the file content after initialization.
 */
interface FileInterface
{
	public function getFilepath(): string;
	public function getYamlString(): string;
	public function getYamlArray(): array;

	/**
	 * Convert each paths in YAML into regex to match path in request
	 * Reference: https://codeigniter.com/user_guide/incoming/routing.html#auto-routing-improved
	 * 
	 * Before: /employees
	 * After: /^(?<_1>\/employees)\/?$/
	 * 
	 * Before: /employees/{id}
	 * After: /^(?<_1>\/employees)\/(?<id>[^\/]+)\/?$/
	 */
	public function convertPathRegexp(): array;
}
