<?php
namespace Gt\Curl;

/**
 * Defines all methods associated with PHP's internal Curl Multi handler,
 * ensuring that objects that Curl Multi object implementations contain
 * matching methods for all internal functions.
 */
interface CurlMultiInterface {
	public function __construct();

	public function __destruct();

	/**
	 * Returns a text error message describing the given CURLM error code.
	 * @see http://php.net/manual/en/function.curl-multi-strerror.php
	 */
	public static function strerror(int $errorNum):string;

	/**
	 * @see curl_multi_add_handle()
	 * @throws CurlException if a CURLM_XXX error is made
	 */
	public function add(CurlInterface $curl):void;

	/**
	 * Close the set of cURL handles
	 * @see http://php.net/manual/en/function.curl-multi-close.php
	 */
	public function close():void;

	/**
	 * Return the last multi curl error number
	 * @return int
	 */
	public function errno():int;

	/**
	 * Run the sub-connections of the current cURL handle
	 * @see http://php.net/manual/en/function.curl-multi-exec.php()
	 * @throws CurlException if a CURLM_XXX error is made
	 */
	public function exec(int &$stillRunning):int;

	/**
	 * Obtain the underlying Curl Multi resource, as created with curl_multi_init.
	 * @return resource
	 */
	public function getHandle();

	/**
	 * Return the content of a cURL handle if CURLOPT_RETURNTRANSFER is set
	 * @see http://php.net/manual/en/function.curl-multi-getcontent.php
	 * @return string the content of a cURL handle if CURLOPT_RETURNTRANSFER is set
	 */
	public function getContent(CurlInterface $curl):string;

	/**
	 * Get information about the current transfers
	 * @see http://php.net/manual/en/function.curl-multi-info-read.php
	 * @throws CurlException on failure
	 */
	public function infoRead(int &$msgsInQueue = null):CurlMultiInfoInterface;

	/**
	 * Initialises a new cURL multi handle
	 * @see http://php.net/manual/en/function.curl-multi-init.php
	 */
	public function init():void;

	/**
	 * Remove a multi handle from the set of cURL handles
	 * @see http://php.net/manual/en/function.curl-multi-remove-handle.php
	 * @throws CurlException on failure
	 */
	public function remove(CurlInterface $curl):void;

	/**
	 * Wait for activity on any curl_multi connection
	 *
	 * @see http://php.net/manual/en/function.curl-multi-select.php
	 * @throws CurlException on select failure or timeout
	 */
	public function select(float $timeout = 1.0):int;

	/**
	 * Set an option for the cURL multi handle, one of the CURLMOPT_* constants.
	 *
	 * @see http://php.net/manual/en/function.curl-multi-setopt.php
	 * @throws CurlException on failure
	 */
	public function setOpt(int $option, $value):void;
}
