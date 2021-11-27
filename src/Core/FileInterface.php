<?php

namespace Kelvinwongg\Yapi\Core;

interface FileInterface
{
	public static function fromYamlString(string $yaml): self;
	public static function fromPath(string $filepath): self;
	public function getYamlArray(): array;
}
