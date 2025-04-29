<?php
require_once __DIR__ . '/vendor/autoload.php';

use Voila\Core\WordPressToJoomla;
use Voila\Utils\Logger;

$logger = new Logger();
$migrator = new WordPressToJoomla($logger);
$migrator->migrate();
