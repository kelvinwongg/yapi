<?php

namespace Kelvinwongg\Yapi\Core;

class File implements FileInterface
{
	private $filepath;
	private $yamlArray;
	private $yamlString;

	private function __construct(string $yamlString)
	{
		$this->yamlString = $yamlString;
	}

	public static function fromYamlString(string $yamlString): self
	{
		return new self($yamlString);
	}

	public static function fromPath(string $filepath): self
	{
		$yamlString = file_get_contents($filepath);
		$self = new self($yamlString);
		$self->filepath = $filepath;
		return $self;
	}

	public function setYamlArray(array $yamlArray): void
	{
		$this->yamlArray = $yamlArray;
	}

	public function getYamlArray(): array
	{
		return $this->yamlArray;
	}

	public function getYamlString(): string
	{
		return $this->yamlString;
	}

	public function isParsed(): bool
	{
		return gettype($this->yamlArray) === 'Array';
	}

	/**
	 * Implement some magic methods
	 */
	public function __toString(): string
	{
		return $this->yamlString;
	}
}
