<?php

use Ini\Parser;

define('ROOT_DIR', __DIR__ . '/..');

$overrideConfigFile = ROOT_DIR . '/.config.override.ini';
$baseConfigFile = ROOT_DIR . '/.config.ini';

// Load the original configuration
$originalConfig = file_get_contents($baseConfigFile);

// Append the override if available
if (file_exists($overrideConfigFile)) {
    $originalConfig = $originalConfig . PHP_EOL . file_get_contents($overrideConfigFile);
}

$parser                          = new Parser();
$parser->use_array_object        = false;
$parser->ini_parse_option        = INI_SCANNER_TYPED;
$parser->array_literals_behavior = Parser::PARSE_JSON;
$parser->treat_ini_string        = true;

$parsedConfig = $parser->parse($originalConfig);

$env = $parsedConfig['ENV'] ?? 'local';
if (!isset($parsedConfig[$env])) {
    throw new RuntimeException("Configuration group `${env}` missing in the configuration file");
}

return $parsedConfig[$env];