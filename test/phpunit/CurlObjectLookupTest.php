<?php
namespace Gt\Curl\Test;

use Gt\Curl\CurlInterface;
use Gt\Curl\CurlObjectLookup;
use PHPUnit\Framework\TestCase;

class CurlObjectLookupTest extends TestCase {
	public function testAddGet():void {
		$curlHandle1 = curl_init();
		$curlHandle2 = curl_init();
		$curlHandle3 = curl_init();

		$curlInterface1 = self::createMock(CurlInterface::class);
		$curlInterface1->method("getHandle")->willReturn($curlHandle1);
		$curlInterface2 = self::createMock(CurlInterface::class);
		$curlInterface2->method("getHandle")->willReturn($curlHandle2);
		$curlInterface3 = self::createMock(CurlInterface::class);
		$curlInterface3->method("getHandle")->willReturn($curlHandle3);
		CurlObjectLookup::add($curlInterface1);
		CurlObjectLookup::add($curlInterface2);
		CurlObjectLookup::add($curlInterface3);

		self::assertSame($curlInterface2, CurlObjectLookup::getObjectFromHandle($curlHandle2));
		self::assertSame($curlInterface1, CurlObjectLookup::getObjectFromHandle($curlHandle1));
		self::assertSame($curlInterface3, CurlObjectLookup::getObjectFromHandle($curlHandle3));
	}

	public function testGetObjectFromHandle_none():Void {
		self::assertNull(CurlObjectLookup::getObjectFromHandle(curl_init()));
	}
}
