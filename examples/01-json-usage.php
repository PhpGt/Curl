<?php
/*
This example shows how to load a JSON API and interact with the response in a
type-safe way. This utilises https://php.gt/json for the response object.
*/
use Gt\Curl\Curl;

require(__DIR__ . "/../vendor/autoload.php");

$curl = new Curl("https://catfact.ninja/fact");
$curl->exec();
$json = $curl->outputJson();
echo "Here's a cat fact: {$json->getString("fact")}";
echo PHP_EOL;
echo "The fact's length is {$json->getInt("length")} characters.";
echo PHP_EOL;

/* Example output:
Here's a cat fact: Phoenician cargo ships are thought to have brought the first domesticated cats to Europe in about 900 BC.
The fact's length is 105 characters.
*/
