<?php

namespace Kelvinwongg\Yapi\Core;

/**
 * FileInterface
 * 
 * Sole representation of a YAML File.
 * No logics and checking is done
 * against the content of this File class.
 */
interface FileInterface
{
	public static function fromYamlString(string $yaml): self;
	public static function fromPath(string $filepath): self;
	public function setYamlArray(array $yamlArray): void;
	public function getYamlArray(): array;
	public function getYamlString(): string;
	public function isParsed(): bool;
}
