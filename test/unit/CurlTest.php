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
}