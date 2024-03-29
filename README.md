cURL object wrapper.
====================

This library wraps PHP's native cURL extension functions with objects, for better code readability and testability.

Why? We wanted to lay an object oriented foundation for [PHP.Gt/Fetch](https://php.gt/fetch), our PHP implementation of the web's fetch API that uses cURL to create asynchronous HTTP calls with promises.

***

<a href="https://github.com/PhpGt/Curl/actions" target="_blank">
    <img src="https://badge.status.php.gt/curl-build.svg" alt="Build status" />
</a>
<a href="https://app.codacy.com/gh/PhpGt/Curl" target="_blank">
    <img src="https://badge.status.php.gt/curl-quality.svg" alt="Code quality" />
</a>
<a href="https://app.codecov.io/gh/PhpGt/Curl" target="_blank">
    <img src="https://badge.status.php.gt/curl-coverage.svg" alt="Code coverage" />
</a>
<a href="https://packagist.org/packages/PhpGt/Curl" target="_blank">
    <img src="https://badge.status.php.gt/curl-version.svg" alt="Current version" />
</a>
<a href="https://www.php.gt/curl" target="_blank">
    <img src="https://badge.status.php.gt/curl-docs.svg" alt="PHP.Gt/Curl documentation" />
</a>

Example usage: Get a JSON object from a remote source

When working with HTTP calls, it is extremely common to work with JSON. This library removes the need of a lot of boilerplate code by buffering the output of `exec()` calls for easy retrieval later with `output()` or `outputJson()`.

Example using PHP.Gt/Curl:
--------------------------

```php
$curl = new Curl("https://catfact.ninja/fact");
$curl->exec();
$json = $curl->outputJson();
echo "Here's a cat fact: {$json->getString("fact")}";
echo PHP_EOL;
echo "The fact's length is {$json->getInt("length")} characters.";
echo PHP_EOL;
```

Same example using PHP's native `curl_*` functions:
---------------------------------------------------

```php
// Using native functionality to achieve the same:
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://catfact.ninja/fact");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
if(false === $result) {
	die("CURL error: " . curl_error($ch));
}
$json = json_decode($result);
if(is_null($json)) {
	die("JSON decoding error: " . json_last_error_msg());
}

// Note: No type checks are made on the `fact` and `length` properties here.
echo "Here's a cat fact: {$json->fact}";
echo PHP_EOL;
echo "The fact's length is {$json->length} characters.";
echo PHP_EOL;
```
