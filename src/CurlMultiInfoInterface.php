<?php
namespace Gt\CurlInterface;

interface CurlMultiInfoInterface {
	/**
	 * @see curl_multi_info_read
	 * The CURLMSG_DONE constant. Other return values are currently not available.
	 */
	public function getMessage():string;

	/**
	 * @see curl_multi_info_read
	 * One of the CURLE_* constants. If everything is OK, the CURLE_OK will be the result.
	 */
	public function getResult():int;

	/**
	 * @see curl_multi_info_read
	 * Resource of type curl indicates the handle which it concerns.
	 */
	public function getHandle():resource;
}