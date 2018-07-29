<?php
namespace Gt\Curl;

/**
 * @property-read $versionNumber int cURL 24 bit version number
 * @property-read $version string cURL version number, as a string
 * @property-read $sslVersionNumber int OpenSSL 24 bit version number
 * @property-read $sslVersion string OpenSSL version number, as a string
 * @property-read $libzVersion string zlib version number, as a string
 * @property-read $host string Information about the host where cURL was built
 * @property-read $age int
 * @property-read $features int A bitmask of the CURL_VERSION_XXX constants
 * @property-read $protocols array An array of protocols names supported by cURL
 */
interface CurlVersionInterface {}