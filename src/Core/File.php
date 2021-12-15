<?php

namespace Kelvinwongg\Yapi\Core;

use function Kelvinwongg\Yapi\Util\{xd};

class File implements FileInterface
{
	private $filepath;
	private $yamlString;
	private $yamlArray;

	public function __construct(string $pathORstring)
	{
		$this->filepath = $this->findPath($pathORstring);
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

	protected function findPath($pathORstring): string
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
				# yamlstring
				break;
		}
		if (!@file_exists($path)) {
			throw new \Exception('Invalid file path');
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
