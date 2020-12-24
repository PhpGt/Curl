<?php
namespace Gt\Curl\Test;

use Gt\Curl\Curl;
use Gt\Curl\CurlInterface;
use PHPUnit\Framework\TestCase;

class CurlTest extends TestCase {
	public function testImplementsCurlInterface() {
		$curl = new Curl();
		self::assertInstanceOf(CurlInterface::class, $curl);
	}

	public function testCloneObject() {
		$url = "https://test.php.gt/example";
		$curl = new Curl($url);
		self::assertEquals(
			$url,
			$curl->getInfo(CURLINFO_EFFECTIVE_URL)
		);

		$curl2 = clone $curl;
		self::assertEquals(
			$url,
			$curl2->getInfo(CURLINFO_EFFECTIVE_URL)
		);
	}
}