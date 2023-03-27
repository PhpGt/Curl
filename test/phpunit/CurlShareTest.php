<?php
namespace Gt\Curl\Test;

use Exception;
use Gt\Curl\CurlShare;
use PHPUnit\Framework\TestCase;

class CurlShareTest extends TestCase {
	public function testStrError():void {
		self::assertSame("No error", CurlShare::strError(0));
	}

	public function testSetOpt():void {
		$sut = new CurlShare();
		$exception = null;
		try {
			$sut->setOpt(CURLSHOPT_SHARE, CURL_LOCK_DATA_COOKIE);
		}
		catch(Exception $exception) {}
		self::assertNull($exception);
	}
}
