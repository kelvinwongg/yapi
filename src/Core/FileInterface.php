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
	public function getFilepath(): string;
	public function getYamlString(): string;
	public function getYamlArray(): array;
}
