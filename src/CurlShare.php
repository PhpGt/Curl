<?php
namespace Gt\Curl;

use CurlShareHandle;

class CurlShare implements CurlShareInterface {
	protected CurlShareHandle $sh;

	public function __construct() {
		$this->init();
	}

	public function __destruct() {
		$this->close();
	}

	/**
	 * Return string describing the given error code
	 * @see http://php.net/manual/en/function.curl-share-strerror.php
	 */
	public static function strError(int $errorNum):string {
		return curl_share_strerror($errorNum);
	}

	/**
	 * Close the cURL share handle
	 * @see http://php.net/manual/en/function.curl-share-close.php
	 */
	public function close():void {
		curl_share_close($this->sh);
	}

	/**
	 * Initialize the cURL share handle
	 * @see http://php.net/manual/en/function.curl-share-init.php
	 */
	public function init():void {
		$this->sh = curl_share_init();
	}

	/**
	 * Set an option for the cURL share handle
	 * @see http://php.net/manual/en/function.curl-share-setopt.php
	 */
	public function setOpt(int $option, string $value):bool {
		return curl_share_setopt($this->sh, $option, $value);
	}
}
