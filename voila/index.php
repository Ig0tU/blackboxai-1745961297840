<?php
require_once __DIR__ . '/vendor/autoload.php';

use Voila\Core\WordPressToJoomla;
use Voila\Utils\Logger;

$logger = new Logger();

$wpDbConfig = [
    'host' => 'your_wordpress_db_host',
    'dbname' => 'your_wordpress_db_name',
    'username' => 'your_wordpress_db_user',
    'password' => 'your_wordpress_db_password'
];

$joomlaDbConfig = [
    'host' => 'your_joomla_db_host',
    'dbname' => 'your_joomla_db_name',
    'username' => 'your_joomla_db_user',
    'password' => 'your_joomla_db_password'
];

$migrator = new WordPressToJoomla($logger, $wpDbConfig, $joomlaDbConfig);
$migrator->migrate();
