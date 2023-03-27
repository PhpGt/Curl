<?php
namespace Gt\Curl\Test;

use Gt\Curl\CurlMultiInfo;
use PHPUnit\Framework\TestCase;

class CurlMultiInfoTest extends TestCase {
	public function testGetMessage():void {
		$sut = new CurlMultiInfo([
			"msg" => CURLMSG_DONE,
			"result" => CURLE_OK,
			"handle" => curl_init(),
		]);
		self::assertSame(CURLMSG_DONE, $sut->getMessage());
	}

	public function testGetResult():void {
		$sut = new CurlMultiInfo([
			"msg" => CURLMSG_DONE,
			"result" => CURLE_OK,
			"handle" => curl_init(),
		]);
		self::assertSame(CURLE_OK, $sut->getResult());
	}
}
