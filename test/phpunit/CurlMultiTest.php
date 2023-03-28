<?php
namespace Gt\Curl\Test;

use CurlMultiHandle;
use Exception;
use Gt\Curl\Curl;
use Gt\Curl\CurlInterface;
use Gt\Curl\CurlMulti;
use PHPUnit\Framework\TestCase;

class CurlMultiTest extends TestCase {
	public function testStrError():void {
		self::assertSame("No error", CurlMulti::strError(CURLM_OK));
		self::assertSame("Invalid multi handle", CurlMulti::strError(CURLM_BAD_HANDLE));
		self::assertSame("Invalid easy handle", CurlMulti::strError(CURLM_BAD_EASY_HANDLE));
		self::assertSame("Out of memory", CurlMulti::strError(CURLM_OUT_OF_MEMORY));
		self::assertSame("Internal error", CurlMulti::strError(CURLM_INTERNAL_ERROR));
	}

	public function testAdd():void {
		$curlInterface = self::createMock(CurlInterface::class);
		$curlInterface->expects(self::once())
			->method("getHandle")
			->willReturn(curl_init());
		$sut = new CurlMulti();
		$sut->add($curlInterface);
	}

	public function testClose():void {
		$sut = new CurlMulti();
		$exception = null;
		try {
			$sut->close();
		}
		catch(Exception $exception) {}
		self::assertNull($exception);
	}

	public function testErrno():void {
		$sut = new CurlMulti();
		self::assertSame(CURLM_OK, $sut->errno());
	}

	public function testGetContent_empty():void {
		$sut = new CurlMulti();
		$curl = new Curl();
		$sut->add($curl);
		self::assertSame("", $sut->getContent($curl));
	}

	public function testGetContent():void {
		$expectedMessage = "Hello, PHP.Gt!";
// Start a basic HTTP server that responds with a known response.
		$tmpFile = tempnam(sys_get_temp_dir(), "phpgt-curl-test-");
		file_put_contents($tmpFile, $expectedMessage);
		$desc = [
			0 => ["pipe", "r"],
			1 => ["pipe", "w"],
			2 => ["pipe", "w"],
		];
		$port = rand(10000, 65535);
		while(true) {
			proc_open(["php", "-S", "0.0.0.0:$port", $tmpFile], $desc, $pipes);
			$socket = @fsockopen(
				"localhost",
				$port,
				$errorCode,
				$errorMessage,
				0.1
			);
			if($socket) {
				break;
			}
		}
		$curl = new Curl("http://localhost:$port");
		$curl->setOpt(CURLOPT_RETURNTRANSFER, true);
		$sut = new CurlMulti();
		$sut->add($curl);
		$stillRunning = 0;
		do {
			$sut->exec($stillRunning);
		}
		while($stillRunning > 0);
		self::assertSame($expectedMessage, $sut->getContent($curl));
	}

	public function testGetHandle():void {
		$sut = new CurlMulti();
		self::assertInstanceOf(CurlMultiHandle::class, $sut->getHandle());
	}

	public function testInfoRead_nullWhenNotStarted():void {
		$sut = new CurlMulti();
		self::assertNull($sut->infoRead());
	}

	public function testInfoRead():void {
		$expectedMessage = "Hello, PHP.Gt!";
// Start a basic HTTP server that responds with a known response.
		$tmpFile = tempnam(sys_get_temp_dir(), "phpgt-curl-test-");
		file_put_contents($tmpFile, $expectedMessage);
		$desc = [
			0 => ["pipe", "r"],
			1 => ["pipe", "w"],
			2 => ["pipe", "w"],
		];
		$port = rand(10000, 65535);
		while(true) {
			proc_open(["php", "-S", "0.0.0.0:$port", $tmpFile], $desc, $pipes);
			$socket = @fsockopen(
				"localhost",
				$port,
				$errorCode,
				$errorMessage,
				0.1
			);
			if($socket) {
				break;
			}
		}
		$curl1 = new Curl("http://localhost:$port");
		$curl2 = new Curl("http://localhost:$port");
		$sut = new CurlMulti();
		$sut->add($curl1);
		$sut->add($curl2);
		$stillRunning = 0;
		do {
			$sut->exec($stillRunning);
			$info = $sut->infoRead();
		}
		while($stillRunning > 0);
		self::assertSame(CURLMSG_DONE, $info->getMessage());
	}

	public function testRemove():void {
		$expectedMessage = "Hello, PHP.Gt!";
// Start a basic HTTP server that responds with a known response.
		$tmpFile = tempnam(sys_get_temp_dir(), "phpgt-curl-test-");
		file_put_contents($tmpFile, $expectedMessage);
		$desc = [
			0 => ["pipe", "r"],
			1 => ["pipe", "w"],
			2 => ["pipe", "w"],
		];
		$port = rand(10000, 65535);
		while(true) {
			proc_open(["php", "-S", "0.0.0.0:$port", $tmpFile], $desc, $pipes);
			$socket = @fsockopen(
				"localhost",
				$port,
				$errorCode,
				$errorMessage,
				0.1
			);
			if($socket) {
				break;
			}
		}
		$curl1 = new Curl("http://localhost:$port");
		$curl2 = new Curl("http://localhost:$port");
		$sut = new CurlMulti();
		$sut->add($curl1);
		$sut->add($curl2);
		$sut->remove($curl1);
		$stillRunning = 0;
		do {
			$sut->exec($stillRunning);
			$info = $sut->infoRead();
		}
		while($stillRunning > 0);
		self::assertSame($curl2, $info->getHandle());
	}

	public function testSelect():void {
		$expectedMessage = "Hello, PHP.Gt!";
// Start a basic HTTP server that responds with a known response.
		$tmpFile = tempnam(sys_get_temp_dir(), "phpgt-curl-test-");
		file_put_contents($tmpFile, $expectedMessage);
		$desc = [
			0 => ["pipe", "r"],
			1 => ["pipe", "w"],
			2 => ["pipe", "w"],
		];
		$port = rand(10000, 65535);
		while(true) {
			proc_open(["php", "-S", "0.0.0.0:$port", $tmpFile], $desc, $pipes);
			$socket = @fsockopen(
				"localhost",
				$port,
				$errorCode,
				$errorMessage,
				0.1
			);
			if($socket) {
				break;
			}
		}
		$curl1 = new Curl("http://localhost:$port");
		$curl2 = new Curl("http://localhost:$port");
		$sut = new CurlMulti();
		$sut->add($curl1);
		$sut->add($curl2);
		$sut->remove($curl1);
		$stillRunning = 0;
		do {
			$sut->exec($stillRunning);
		}
// Note: we're only waiting for one of the curl requests to finish, so there
// will be one left running when `select` is called.
		while($stillRunning > 1);
		self::assertSame(1, $sut->select(0.1));
	}

	public function testSetOpt():void {
		$sut = new CurlMulti();
		$exception = null;
		try {
			$sut->setOpt(CURLMOPT_MAXCONNECTS, 1);
		}
		catch(Exception $exception) {}
		self::assertNull($exception);
	}
}
