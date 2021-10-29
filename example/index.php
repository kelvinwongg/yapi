<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Kelvinwongg\Yapi\Core\Parser;
use function Kelvinwongg\Yapi\Util\{xd};

// Absolute path
// $parser = new Parser('/var/www/html/example');

// Relative path
$parser = new Parser('./endpoints');
