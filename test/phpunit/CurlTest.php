<?php
namespace Gt\Curl\Test;

use Gt\Curl\Curl;
use Gt\Curl\CurlException;
use Gt\Curl\CurlInterface;
use Gt\Curl\NoOutputException;
use PHPUnit\Framework\TestCase;

class CurlTest extends TestCase {
	public function testImplementsCurlInterface():void {
		$curl = new Curl();
		self::assertInstanceOf(CurlInterface::class, $curl);
	}

	public function testCloneObject():void {
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

	public function testVersion():void {
		$sut = Curl::version();
		$curlVersionArray = curl_version();

		self::assertEquals(
			$curlVersionArray["version_number"],
			$sut->versionNumber
		);
		self::assertEquals(
			$curlVersionArray["version"],
			$sut->version
		);
		self::assertEquals(
			$curlVersionArray["ssl_version_number"],
			$sut->sslVersionNumber
		);
		self::assertEquals(
			$curlVersionArray["ssl_version"],
			$sut->sslVersion
		);
		self::assertEquals(
			$curlVersionArray["host"],
			$sut->host
		);
		self::assertEquals(
			$curlVersionArray["age"],
			$sut->age
		);
		self::assertEquals(
			$curlVersionArray["features"],
			$sut->features
		);
		self::assertEquals(
			$curlVersionArray["protocols"],
			$sut->protocols
		);
	}

	public function testStrError():void {
		self::assertSame(
			"No error",
			Curl::strError(CURLE_OK)
		);
		self::assertSame(
			"Unsupported protocol",
			Curl::strError(CURLE_UNSUPPORTED_PROTOCOL)
		);
		self::assertSame(
			"Failed initialization",
			Curl::strError(CURLE_FAILED_INIT)
		);
		self::assertSame(
			"URL using bad/illegal format or missing URL",
			Curl::strError(CURLE_URL_MALFORMAT)
		);
		self::assertSame(
			"Couldn't resolve proxy name",
			Curl::strError(CURLE_COULDNT_RESOLVE_PROXY)
		);
		self::assertSame(
			"Couldn't resolve host name",
			Curl::strError(CURLE_COULDNT_RESOLVE_HOST)
		);
	}

	public function testErrno():void {
		$sut = new Curl("http://nowhere");
		self::assertSame(CURLE_OK, $sut->errno());

		try {
			$sut->exec();
		}
		catch(CurlException) {
			self::assertSame(CURLE_COULDNT_RESOLVE_HOST, $sut->errno());
		}
	}

	public function testError():void {
		$sut = new Curl("http://nowhere");
		self::assertSame("", $sut->error());

		try {
			$sut->exec();
		}
		catch(CurlException) {
			self::assertSame("Could not resolve host: nowhere", $sut->error());
		}
	}

	public function testEscape():void {
		$sut = new Curl();
		$location = $sut->escape("Hofbräuhaus / München");
		$url = "http://example.com/add_location.php?location={$location}";
		self::assertSame($url, "http://example.com/add_location.php?location=Hofbr%C3%A4uhaus%20%2F%20M%C3%BCnchen");
	}

	public function testPause():void {
		$sut = new Curl("http://example.com");
		// Very difficult to test in unit tests, but if there's an
		// error it will affect the errno.
		$sut->pause(CURLPAUSE_ALL);
		self::assertSame(CURLE_OK, $sut->errno());
	}

	public function testReset():void {
		$sut = new Curl("http://nowhere");
		try {
			$sut->exec();
		}
		catch(CurlException) {
			$code = $sut->getInfo(  CURLINFO_TOTAL_TIME_T);
			self::assertGreaterThan(0, $code);
		}
		finally {
			$sut->reset();
			self::assertSame(0, $sut->getInfo(  CURLINFO_TOTAL_TIME_T));
		}

	}

	public function testSetOpt():void {
		$sut = new Curl();
		$sut->setOpt(CURLOPT_URL, "http://example.com");
		self::assertSame("http://example.com", $sut->getInfo(CURLINFO_EFFECTIVE_URL));
	}

	public function testSetOptArray():void {
		$sut = new Curl();
		$sut->setOptArray([
			CURLOPT_URL => "http://nothing",
			CURLOPT_PRIVATE => "This is a secret",
		]);
		try {
			$sut->exec();
		}
		catch(CurlException) {}
		self::assertSame("http://nothing/", $sut->getInfo(CURLINFO_EFFECTIVE_URL));
		self::assertSame("This is a secret", $sut->getInfo(    CURLINFO_PRIVATE));
	}

	public function testUnescape():void {
		$sut = new Curl();
		$location = "Hofbr%C3%A4uhaus%20%2F%20M%C3%BCnchen";
		self::assertSame("Hofbräuhaus / München", $sut->unescape($location));
	}

	public function testGetAllInfo():void {
		$sut = new Curl("http://nowhere");
		$info = $sut->getAllInfo();
		self::assertSame("http://nowhere", $info["url"]);
		self::assertSame(0, $info["http_code"]);
	}

	public function testGetInfo_invalid():void {
		$sut = new Curl();
		self::expectException(CurlException::class);
		self::expectExceptionMessage("Option must be greater than zero, -1 given.");
		$sut->getInfo(-1);
	}

	public function testOutput_noBuffer():void {
		$sut = new Curl();
		self::expectException(CurlException::class);
		self::expectExceptionMessage("No URL set");
		$sut->output();
	}

	public function testOutput():void {
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
		$sut = new Curl("http://localhost:$port");
		$sut->exec();
		self::assertSame($expectedMessage, $sut->output());
	}

	public function testOutput_empty():void {
		$expectedMessage = "";
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
		$sut = new Curl("http://localhost:$port");
		$sut->exec();
		self::expectException(NoOutputException::class);
		$sut->output();
	}

	public function testOutputJson():void {
		$expectedObj = (object)[
			"message" => "Hello, PHP.Gt!",
		];
		$expectedMessage = json_encode($expectedObj);
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
		$sut = new Curl("http://localhost:$port");
		$sut->exec();
		self::assertSame("Hello, PHP.Gt!", $sut->outputJson()->getString("message"));
	}
}
