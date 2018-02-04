<?php
namespace Gt\CurlInterface;

/**
 * Defines all methods associated with PHP's internal Curl Multi handler,
 * ensuring that objects that Curl Multi object implementations contain
 * matching methods for all internal functions.
 */
interface CurlMultiInterface {
	/**
	 * Obtain the underlying Curl Multi resource, as created with curl_multi_init.
	 */
	public function getHandle():resource;

	/**
	 * Returns a text error message describing the given CURLM error code.
	 *
	 * @see curl_multi_strerror
	 */
	static public function strerror(int $errorNum):string;

	/**
	 * @see curl_multi_add_handle()
	 * @throws CurlException if a CURLM_XXX error is made
	 */
	public function add(CurlInterface $curl):void;

	/**
	 * @see curl_multi_exec()
	 * @throws CurlException if a CURLM_XXX error is made
	 */
	public function exec(int &$stillRunning):void;

	/**
	 * @see curl_multi_getcontent()
	 * @return string the content of a cURL handle if CURLOPT_RETURNTRANSFER is set
	 */
	public function getContent(CurlInterface $curl):string;

	/**
	 * @see curl_multi_info_read()
	 * @throws CurlException on failure
	 */
	public function infoRead(int &$msgs = null):CurlMultiInfoInterface;

	/**
	 * @see curl_multi_remove_handle()
	 * @throws CurlException on failure
	 */
	public function remove(CurlInterface $curl):void;

	/**
	 * Wait for activity on any curl_multi connection.
	 *
	 * @see curl_multi_select()
	 * @throws CurlException on select failure or timeout
	 */
	public function select(float $timeout = 1.0):int;

	/**
	 * Set an option for the cURL multi handle, one of the CURLMOPT_* constants.
	 *
	 * @see curl_multi_setopt
	 * @throws CurlException on failure
	 */
	public function setOpt(int $opt, $val):void;
}
