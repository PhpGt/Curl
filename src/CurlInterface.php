<?php
namespace Gt\Curl;

use CurlHandle;

/**
 * Defines all methods associated with PHP's internal Curl handler,
 * ensuring that objects that Curl object implementations contain
 * matching methods for all internal functions.
 */
interface CurlInterface {
	public function __construct(string $url = null);

	/**
	 * Close a cURL session
	 * @see http://php.net/manual/en/function.curl-close.php
	 */
	public function __destruct();

	/**
	 * Copy a cURL handle along with all of its preferences
	 * @see http://php.net/manual/en/function.curl-copy-handle.php
	 */
	public function __clone();

	/**
	 * Gets cURL version information
	 * @see http://php.net/manual/en/function.curl-version.php
	 */
	public static function version():CurlVersionInterface;

	/**
	 * Return string describing the given error code
	 * @see http://php.net/manual/en/function.curl-strerror.php
	 */
	public static function strError(int $errorNum):string;

	/**
	 * Return the last error number
	 * @see http://php.net/manual/en/function.curl-errno.php
	 */
	public function errno():int;

	/**
	 * Return a string containing the last error for the current session
	 * @see http://php.net/manual/en/function.curl-error.php
	 */
	public function error():string;

	/**
	 * URL encodes the given string
	 * @see http://php.net/manual/en/function.curl-escape.php
	 */
	public function escape(string $str):string;

	/**
	 * Perform a cURL session
	 * @throws CurlException when curl_exec returns false
	 * @see http://php.net/manual/en/function.curl-exec.php
	 */
	public function exec():string;

	/**
	 * Get information regarding the transfer
	 * @see http://php.net/manual/en/function.curl-getinfo.php
	 */
	public function getInfo(int $opt):mixed;

	/**
	 * Initialize a cURL session
	 * @see http://php.net/manual/en/function.curl-init.php
	 */
	public function init(string $url = null):void;

	/**
	 * Pause and unpause the connection
	 * @see http://php.net/manual/en/function.curl-pause.php
	 */
	public function pause(int $bitmask):int;

	/**
	 * Reset all options of the libcurl session handle
	 * @see http://php.net/manual/en/function.curl-reset.php
	 */
	public function reset():void;

	/**
	 * Set an option for the cURL transfer
	 * @see http://php.net/manual/en/function.curl-setopt.php
	 */
	public function setOpt(int $option, mixed $value):bool;

	/**
	 * Set multiple options for the cURL transfer
	 * @param array<int, mixed> $options
	 * @see http://php.net/manual/en/function.curl-setopt-array.php
	 */
	public function setOptArray(array $options):bool;

	/**
	 * Decodes the given URL encoded string
	 * @see http://php.net/manual/en/function.curl-unescape.php
	 */
	public function unescape(string $str):string;

	/**
	 * Obtain the underlying curl resource, as created with curl_init.
	 */
	public function getHandle():CurlHandle;

	/**
	 * Gets all CURLINFO_ data, identical to calling
	 * with no arguments.
	 * @return array<string, mixed>
	 */
	public function getAllInfo():array;
}
