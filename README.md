cURL object wrapper.
====================

This library simply wraps PHP's native cURL extension functions with objects, for better code readability and testability.

Why? We wanted to lay an object oriented foundation for [PHP.Gt/Fetch](https://php.gt/fetch), our PHP implementation of the web's fetch API that uses cURL to create asynchronous HTTP calls with promises.

Example:

```php
// Native curl functions:
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://google.com");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);

// Object-wrapped alternative:
$curl = new Curl();
$curl->setOpt(CURLOPT_URL, "https://google.com");
$curl->setOpt(CURLOPT_RETURNTRANSFER, true);
$result = curl_exec();
``` 