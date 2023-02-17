<?php
namespace Gt\Curl;

interface CurlShareInterface {
	public function __construct();

	public function __destruct();

	/**
	 * Return string describing the given error code
	 * @see http://php.net/manual/en/function.curl-share-strerror.php
	 */
	public static function strError(int $errorNum):string;

	/**
	 * Close the cURL share handle
	 * @see http://php.net/manual/en/function.curl-share-close.php
	 */
	public function close():void;

	/**
	 * Initialize the cURL share handle
	 * @see http://php.net/manual/en/function.curl-share-init.php
	 */
	public function init():void;

	/**
	 * Set an option for the cURL share handle
	 * @see http://php.net/manual/en/function.curl-share-setopt.php
	 */
	public function setOpt(int $option, string $value):bool;
}
