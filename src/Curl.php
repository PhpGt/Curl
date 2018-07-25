<?php

namespace Gt\Curl;

use Gt\CurlInterface\CurlException;
use Gt\CurlInterface\CurlInterface;

class Curl implements CurlInterface {
	protected $ch;

	public function __construct(string $url = null) {
		$this->init($url);
	}

	/**
	 * Close a cURL session
	 * @see http://php.net/manual/en/function.curl-close.php
	 */
	public function __destruct() {
		curl_close($this->ch);
	}

	/**
	 * Copy a cURL handle along with all of its preferences
	 * @see http://php.net/manual/en/function.curl-copy-handle.php
	 */
	public function __clone() {
		curl_copy_handle($this->ch);
	}

	/**
	 * @see http://php.net/manual/en/function.curl-version.php
	 */
	public static function version(int $age = CURLVERSION_NOW):array {
		return curl_version($age);
	}

	/**
	 * Return string describing the given error code
	 * @see http://php.net/manual/en/function.curl-strerror.php
	 */
	public static function strError(int $errorNum):string {
		return curl_strerror($errorNum);
	}

	/**
	 * Return the last error number
	 * @see http://php.net/manual/en/function.curl-errno.php
	 */
	public function errno():int {
		return curl_errno($this->ch);
	}

	/**
	 * Return a string containing the last error for the current session
	 * @see http://php.net/manual/en/function.curl-error.php
	 */
	public function error():string {
		return curl_error($this->ch);
	}

	/**
	 * URL encodes the given string
	 * @see http://php.net/manual/en/function.curl-escape.php
	 */
	public function escape(string $str):string {
		return curl_escape($this->ch, $str);
	}

	/**
	 * Perform a cURL session
	 * @see http://php.net/manual/en/function.curl-exec.php
	 */
	public function exec():string {
		$response = curl_exec($this->ch);

		if(false === $response) {
			throw new CurlException("Exec failure");
		}

		return $response;
	}

	/**
	 * Get information regarding the transfer
	 * @see http://php.net/manual/en/function.curl-getinfo.php
	 */
	public function getInfo(int $opt):string {
		if($opt <= 0) {
			throw new CurlException(
				"Option must be greater than zero, "
				. $opt
				. " given."
			);
		}
		return curl_getinfo($this->ch, $opt);
	}

	/**
	 * Initialize a cURL session
	 * @see http://php.net/manual/en/function.curl-init.php
	 */
	public function init(string $url = null):void {
		$this->ch = curl_init($url);
	}

	/**
	 * Pause and unpause a connection
	 * @see http://php.net/manual/en/function.curl-pause.php
	 */
	public function pause(int $bitmask):int {
		return curl_pause($this->ch, $bitmask);
	}

	/**
	 * Reset all options of the libcurl session handle
	 * @see http://php.net/manual/en/function.curl-reset.php
	 */
	public function reset():void {
		curl_reset($this->ch);
	}

	/**
	 * Set an option for the cURL transfer
	 * @see http://php.net/manual/en/function.curl-setopt.php
	 */
	public function setOpt(int $option, $value):bool {
		return curl_setopt($this->ch, $option, $value);
	}

	/**
	 * Set multiple options for the cURL transfer
	 * @see http://php.net/manual/en/function.curl-setopt-array.php
	 */
	public function setOptArray(array $options):bool {
		return curl_setopt_array($this->ch, $options);
	}

	/**
	 * Decodes the given URL encoded string
	 * @see http://php.net/manual/en/function.curl-unescape.php
	 */
	public function unescape(string $str):string {
		return curl_unescape($this->ch, $str);
	}

	/**
	 * Obtain the underlying curl resource, as created with curl_init.
	 */
	public function getHandle(){
		return $this->ch;
	}

	/**
	 * Gets all CURLINFO_ data, identical to calling curl_getinfo with no arguments.
	 */
	public function getAllInfo():array {
		return $this->getInfo();
	}
}