<?php
namespace Gt\CurlInterface;

/**
 * Defines all methods associated with PHP's internal Curl Multi handler,
 * ensuring that objects that Curl Multi object implementations contain
 * matching methods for all internal functions.
 */
interface CurlInterface {
	/**
	 * @see curl_version()
	 * @param int $age
	 * @return array Information about the cURL version
	 */
	static public function version(int $age = CURLVERSION_NOW):array;

	/**
	 * @see curl_strerror()
	 * @param int $errornum One of the cURL error codes constants
	 * @return array error description or NULL for invalid error code
	 *
	 * @todo throw exception on invalid error code?
	 */
	static public function strerror(int $errornum):array;

	/**
	 * @see curl_copy_handle()
	 * @return void
	 */
	public function __clone();

	/**
	 * @return resource The underlying Curl resource
	 */
	public function getHandle():resource;

	/**
	 * @see curl_init()
	 * @param string $url URL
	 * @return void
	 *
	 * @todo PHP 7.1 can define string? nullable type.
	 */
	public function init($url = null);

	/**
	 * @see curl_errno()
	 * @return int The error number or 0 (zero) if no error occurred
	 */
	public function errno():int;

	/**
	 * @see curl_error()
	 * @return string a clear text error message for the last cURL operation
	 */
	public function error():string;

	/**
	 * @see curl_exec()
	 * @param int $attempts Connection attempts (default is 1)
	 * @param boolean $useException Throw \RuntimeException on failure
	 * @return boolean|string
	 * @throws InvalidArgumentException if the number of attempts is invalid
	 * @throws CurlException if curl_exec() returned false
	 *
	 * @todo Can't have string and bool as return type! Force exception?
	 */
	public function exec($attempts = 1, $useException = false):string;

	/**
	 * @see curl_getinfo()
	 * @param int $opt CURLINFO_*
	 * @return array|string
	 *
	 * @todo returns an array OR a string? I know this is by design of curl,
	 * but it is such bad design that it feel irresponsible not to fix it. Force
	 * an array? Maybe split into getInfo():string and getInfoArray():array ?
	 * We have setOpt and setOptArray, so for consistency getInfoArray() ?
	 */
	public function getInfo(int $opt = 0):array;

	/**
	 * @see curl_setopt()
	 * @param int   $option The CURLOPT_XXX option to set
	 * @param mixed $value  The value to be set on option
	 * @return boolean True on success, false on failure
	 */
	public function setOpt(int $option, $value):bool;

	/**
	 * @see curl_setopt_array()
	 * @param array $options An array specifying which options to set and their
	 *  values; the keys should be valid curl_setopt() constants or their
	 *  integer equivalents
	 * @return boolean True if all options were successfully set; if an option
	 *  could not be successfully set, FALSE is immediately returned, ignoring
	 *  any future options in the options array
	 */
	public function setOptArray(array $options):bool;

	/**
	 * @see curl_escape()
	 * @param string $str The string to be encoded
	 * @return string Escaped string or FALSE on failure
	 *
	 * @todo docs says false on failure. exception?
	 */
	public function escape(string $str):string;

	/**
	 * @see curl_unescape()
	 * @param string $str The URL encoded string to be decoded
	 * @return string Decoded string or FALSE on failure
	 *
	 * @todo docs say false on failure. exception?
	 */
	public function unescape($str):string;

	/**
	 * @see curl_reset()
	 * @return void
	 */
	public function reset();

	/**
	 * @see curl_pause()
	 * @param int $bitmask Combination of CURLPAUSE_* constants
	 * @return int CURLE_OK on success, or error code on failure
	 */
	public function pause(int $bitmask):int;
}
