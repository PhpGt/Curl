<?php
namespace Gt\Curl\Test;

use Gt\Curl\CurlVersion;
use PHPUnit\Framework\TestCase;

class CurlVersionTest extends TestCase {
	public function testVersion() {
		$versionData = curl_version();
		$versionObject = new CurlVersion($versionData);

		self::assertEquals(
			$versionData["version_number"],
			$versionObject->versionNumber
		);
		self::assertEquals(
			$versionData["version"],
			$versionObject->version
		);
		self::assertEquals(
			$versionData["ssl_version_number"],
			$versionObject->sslVersionNumber
		);
		self::assertEquals(
			$versionData["ssl_version"],
			$versionObject->sslVersion
		);
		self::assertEquals(
			$versionData["host"],
			$versionObject->host
		);
		self::assertEquals(
			$versionData["age"],
			$versionObject->age
		);
		self::assertEquals(
			$versionData["features"],
			$versionObject->features
		);
		self::assertEquals(
			$versionData["protocols"],
			$versionObject->protocols
		);
	}
}
