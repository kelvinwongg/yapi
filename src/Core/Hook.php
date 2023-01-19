<?php

namespace Kelvinwongg\Yapi\Core;

use function Kelvinwongg\Yapi\Util\{xd};
use Kelvinwongg\Yapi\Core\HookInterface;

class Hook implements HookInterface
{
	public $filepath;
	public $classname;

	public function __construct($filepath)
	{
		$this->filepath = $filepath;

		// Find CRUD Hook classname
		$this->classname = $this->findHookFileClassname($this->filepath);
	}

	public static function fromRequest(RequestInterface $request, array $config): self
	{
		// Figure out the path to the file as stated in the YAML
		$path = self::findHookFilePath($request, $config);
		return new self($path);
	}
	public function callCrud(): bool
	{
		return TRUE;
	}
	public function callBefore(): bool{
		return TRUE;
	}
	public function callAfter(): bool{
		return TRUE;
	}

	private static function findHookFilePath(RequestInterface $request, array $config): String
	{
		// Basepath for 'paths' directory
		$filepath = getcwd() . $config['paths'];
		if (!@file_exists($filepath)) {
			throw new \Exception(
				sprintf(
					'Execution file directory not found: %s',
					$filepath
				),
				500
			);
		}

		/**
		 * Find hook file path from specific to general
		 * 
		 * For example:
		 * 
		 * Request path: /employees/12
		 * Match path: /employees/{id}
		 * 
		 * 1. /paths/employees/12.php::get()
		 * 2. /paths/employees/id.php::get()
		 * 3. /paths/employees/id/index.php::get()
		 * 4. /paths/employees/index.php::id() (Todo: To be implemented)
		 * 5. /paths/employees.php::id() (Todo: To be implemented)
		 */

		$found = FALSE;
		$match_path = $request->match['path'];
		$trial[] = $filepath . rtrim($request->path, "/") . '.php';
		$trial[] = $filepath . str_replace(['{', '}'], '', $match_path) . '.php';
		$trial[] = $filepath . str_replace(['{', '}'], '', $match_path) . '/index.php';

		foreach ($trial as $this_trial) {
			if (@file_exists($this_trial)) {
				$filepath = $this_trial;
				$found = TRUE;
				break;
			}
		}

		if (!$found) {
			throw new \Exception(
				sprintf(
					'Execution file for request not found: %s',
					$match_path
				),
				500
			);
		}

		return $filepath;
	}

	private static function findHookFileClassname(String $hookFilePath): String
	{
		$declared_classes_before = get_declared_classes();
		require $hookFilePath;
		$classname = array_values(array_diff(get_declared_classes(), $declared_classes_before));

		if (count($classname) === 0) {
			throw new \Exception(
				sprintf(
					'No class is defined in the Hook file: %s',
					$hookFilePath
				),
				500
			);
		}
		if (count($classname) > 1) {
			throw new \Exception(
				sprintf(
					'Only one class could be defined in the Hook file: %s',
					$hookFilePath
				),
				500
			);
		}

		return $classname[0];
	}
}
