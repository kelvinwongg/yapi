<?php

namespace Yapi\Core;

use Yapi\Core\DatabaseInterface;

class Database implements DatabaseInterface
{
	public function __construct(array $settings = [])
	{
		echo 'Database construct';
	}

	public function connect(array $settings)
	{
		$host = (array_key_exists('host', $settings)) ? $settings['host'] : 'localhost';
		$port = (array_key_exists('port', $settings)) ? $settings['port'] : '3306';
		$dbname = (array_key_exists('dbname', $settings)) ? $settings['dbname'] : 'yapi';
		$username = (array_key_exists('username', $settings)) ? $settings['username'] : 'root';
		$password = (array_key_exists('password', $settings)) ? $settings['password'] : '';
		$options = [];
		$dsn = "mysql:host={$host};port={$port};dbname={$dbname}";

		try {
			$conn = new \PDO($dsn, $username, $password, $options);
			// set the PDO error mode to exception
			$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			return $conn;
		} catch (\PDOException $e) {
			throw $e;
		}
	}
}
