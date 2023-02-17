<?php
namespace Gt\Curl;

/**
 * @property-read int $versionNumber cURL 24 bit version number
 * @property-read string $version cURL version number, as a string
 * @property-read int $sslVersionNumber OpenSSL 24 bit version number
 * @property-read string $sslVersion OpenSSL version number, as a string
 * @property-read string $libzVersion zlib version number, as a string
 * @property-read string $host Information about the host where cURL was built
 * @property-read int $age
 * @property-read int $features A bitmask of the CURL_VERSION_XXX constants
 * @property-read array<string> $protocols An array of protocols names
 * supported by cURL
 */
interface CurlVersionInterface {}
