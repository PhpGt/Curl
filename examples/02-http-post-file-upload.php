<?php
/*
 * This example downloads a picture of a cat, then uploads it to an example API
 * using an HTTP POST upload.
*/
use Gt\Curl\Curl;

require(__DIR__ . "/../vendor/autoload.php");

$tmpFile = "/tmp/cat.jpg";

// Download a photo of a cat from cataas.com, save it to the $tmpFile
$curl = new Curl("https://cataas.com/cat");
file_put_contents($tmpFile, $curl->output());

// Now POST a form containing the cat photo to the Postman echo test server
$upload = new CURLFile($tmpFile);
$curl = new Curl("https://postman-echo.com/post");
$curl->setOpt(CURLOPT_POSTFIELDS, [
	"cat-photo" => $upload
]);
$curl->exec();
echo $curl->output();

// Remove the temporary file before finishing
unlink($tmpFile);

/*
 * Example output:
 * {
 *   "args": {},
 *   "data": {},
 *   "files": {
 *     "cat.jpg": "data:application/octet-stream;base64,/9j/4AAQSkZJRgABAQEIAAD"
 *   },
 *   "form": {},
 *   "headers": {
 *     "host": "postman-echo.com",
 *     "content-length": "42285",
 *     "accept": "*\/*",
 *     "content-type": "multipart/form-data; boundary=--------------d7b86ee9056"
 *   },
 *   "json": null,
 *   "url": "https://postman-echo.com/post"
 * }
*/
