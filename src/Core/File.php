<?php

namespace Yapi\Core;

use function Yapi\Util\{xd};

class File implements FileInterface
{
	private $filepath;
	private $yamlString;
	private $yamlArray;

	public function __construct(string $pathORstring)
	{
		$this->filepath = $this->filepathFromString($pathORstring);
		if ($this->filepath) {
			$this->yamlString = file_get_contents($this->filepath);
		} else {
			$this->yamlString = $pathORstring;
		}
		$this->yamlArray = \yaml_parse($this->yamlString);
	}

	public function getFilepath(): string
	{
		return $this->filepath;
	}

	public function getYamlString(): string
	{
		return $this->yamlString;
	}

	public function getYamlArray(): array
	{
		return $this->yamlArray;
	}

	public function convertPathRegexp(): array
	{
		$ret = [];
		foreach (array_keys($this->getYamlArray()['paths']) as $this_path) {
			// Convert parts not in curly brace
			$not_in_curly = '/(^\/[^{}\/]+(?![^{]*})|[^{}\/]+(?![^{]*}))/';
			$regexp = preg_replace_callback($not_in_curly, function ($match) {
				static $count = 0;
				$count++;
				return "(?<_{$count}>{$match[0]})";
			}, $this_path);
			// xd($regexp);

			// Convert slashes
			$slash = '/(\/)/';
			$regexp = preg_replace_callback($slash, function ($match) {
				return "\/";
			}, $regexp);
			// xd($regexp);

			// Convert curly brace parts
			$curly_brace = '/{(.+)}/';
			$regexp = preg_replace_callback($curly_brace, function ($match) {
				return "(?<{$match[1]}>[^\/]+)";
			}, $regexp);
			// xd($regexp);

			$ret[$this_path] = '/^' . $regexp . '\/?$/';
		}
		return $ret;
	}

	protected function filepathFromString($pathORstring): string
	{
		$path = FALSE;
		switch (substr($pathORstring, 0, 2)) {
			case '':
				# empty string
				break;
			case './':
				# relative path
				$path = getcwd() . '/' . substr($pathORstring, 2, strlen($pathORstring) - 2);
				break;
			case '..':
				# relative path
				$path = getcwd() . '/' . $pathORstring;
				break;
			case (preg_match('/^\//', $pathORstring) ? true : false):
				# absolute path
				$path = $pathORstring;
				break;
			default:
				# yamlstring or path start without . and /
				$path = getcwd() . '/' . $pathORstring;
				break;
		}
		if (!@file_exists($path)) {
			throw new \Exception(
				sprintf(
					'File path do not exists: %s',
					$path
				),
				500
			);
		}
		return $path;
	}

	/**
	 * Implement some magic methods
	 */
	public function __toString(): string
	{
		return $this->yamlString;
	}
}
