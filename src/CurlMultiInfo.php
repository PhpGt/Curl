<?php
namespace Gt\Curl;

/**
 * @see http://php.net/manual/en/function.curl-multi-info-read.php
 */
class CurlMultiInfo implements CurlMultiInfoInterface {
	/** @var int The CURLMSG_DONE constant */
	protected $message;
	/** @var int */
	protected $result;
	/** @var resource */
	protected $handle;

	public function __construct(array $curlMultiInfoRead) {
		$this->message = $curlMultiInfoRead["msg"];
		$this->result = $curlMultiInfoRead["result"];
		$this->handle = $curlMultiInfoRead["handle"];
	}

	/**
	 * @see curl_multi_info_read
	 * The CURLMSG_DONE constant. Other return values are currently not available.
	 */
	public function getMessage():string {
		return $this->message;
	}

	/**
	 * @see curl_multi_info_read
	 * One of the CURLE_* constants. If everything is OK, the CURLE_OK will be the result.
	 */
	public function getResult():int {
		return $this->result;
	}

	/**
	 * @see curl_multi_info_read
	 * Resource of type curl indicates the handle which it concerns.
	 */
	public function getHandle():CurlInterface {
		return CurlObjectLookup::getObjectFromHandle($this->handle);
	}
}