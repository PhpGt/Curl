<?php
namespace Gt\Curl;

class CurlMulti implements CurlMultiInterface {
	/** @var resource */
	protected $mh;

	public function __construct() {
		$this->init();
	}

	public function __destruct() {
		$this->close();
	}

	/**
	 * Returns a text error message describing the given CURLM error code.
	 * @see http://php.net/manual/en/function.curl-multi-strerror.php
	 */
	public static function strerror(int $errorNum):string {
		return curl_multi_strerror($errorNum);
	}

	/**
	 * @see curl_multi_add_handle()
	 * @throws CurlException if a CURLM_XXX error is made
	 */
	public function add(CurlInterface $curl):void {
		curl_multi_add_handle($this->mh, $curl->getHandle());
	}

	/**
	 * Close the set of cURL handles
	 * @see http://php.net/manual/en/function.curl-multi-close.php
	 */
	public function close():void {
		curl_multi_close($this->mh);
	}

	/**
	 * Return the last multi curl error number
	 * @return int
	 */
	public function errno():int {
		return curl_multi_errno($this->mh);
	}

	/**
	 * Run the sub-connections of the current cURL handle
	 * @see http://php.net/manual/en/function.curl-multi-exec.php()
	 * @throws CurlException if a CURLM_XXX error is made
	 */
	public function exec(int &$stillRunning):int {
		return curl_multi_exec($this->mh, $stillRunning);
	}

	/**
	 * Obtain the underlying Curl Multi resource, as created with curl_multi_init.
	 * @return resource
	 */
	public function getHandle() {
		return $this->mh;
	}

	/**
	 * Return the content of a cURL handle if CURLOPT_RETURNTRANSFER is set
	 * @see http://php.net/manual/en/function.curl-multi-getcontent.php
	 * @return string the content of a cURL handle if CURLOPT_RETURNTRANSFER is set
	 */
	public function getContent(CurlInterface $curl):string {
		return curl_multi_getcontent($curl->getHandle());
	}

	/**
	 * Get information about the current transfers
	 * @see http://php.net/manual/en/function.curl-multi-info-read.php
	 * @throws CurlException on failure
	 */
	public function infoRead(int &$msgsInQueue = null):CurlMultiInfoInterface {
		return new CurlMultiInfo(curl_multi_info_read(
			$this->mh,
			$msgsInQueue
		));
	}

	/**
	 * Initialises a new cURL multi handle
	 * @see http://php.net/manual/en/function.curl-multi-init.php
	 */
	public function init():void {
		$this->mh = curl_multi_init();
	}

	/**
	 * Remove a multi handle from the set of cURL handles
	 * @see http://php.net/manual/en/function.curl-multi-remove-handle.php
	 * @throws CurlException on failure
	 */
	public function remove(CurlInterface $curl):void {
		curl_multi_remove_handle($this->mh, $curl->getHandle());
	}

	/**
	 * Wait for activity on any curl_multi connection
	 *
	 * @see http://php.net/manual/en/function.curl-multi-select.php
	 * @throws CurlException on select failure or timeout
	 */
	public function select(float $timeout = 1.0):int {
		return curl_multi_select($this->mh, $timeout);
	}

	/**
	 * Set an option for the cURL multi handle, one of the CURLMOPT_* constants.
	 *
	 * @see http://php.net/manual/en/function.curl-multi-setopt.php
	 * @throws CurlException on failure
	 */
	public function setOpt(int $option, $value):void {
		curl_multi_setopt($this->mh, $option, $value);
	}
}