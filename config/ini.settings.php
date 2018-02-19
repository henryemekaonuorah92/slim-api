<?php

define('ROOT_DIR', __DIR__ . '/..');

if(file_exists(ROOT_DIR . '/.config.ini')) {
    $baseConfigFile = file_get_contents(ROOT_DIR . '/.config.ini');

    $parser = new \Ini\Parser();

    $parser->use_array_object        = false;
    $parser->ini_parse_option        = INI_SCANNER_TYPED;
    $parser->array_literals_behavior = \Ini\Parser::PARSE_JSON;
    $parser->treat_ini_string        = true;

    $parsedConfig = $parser->parse($baseConfigFile);

    $env = $parsedConfig['ENV'] ?? 'local';
    if (!isset($parsedConfig[$env])) {
        throw new RuntimeException("Configuration group `${env}` missing in the configuration file");
    }

    return $parsedConfig[$env];
}

return [];