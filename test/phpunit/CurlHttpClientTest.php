<?php
namespace Gt\Curl\Test;

use Gt\Curl\Curl;
use Gt\Curl\CurlHttpClient;
use Gt\Http\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class CurlHttpClientTest extends TestCase {
	public function testSendRequest() {
		$exampleUrl = "https://example.php.gt";
		$exampleBodyContent = '{"name": "example", "repo": "phpgt"}';
		$exampleResponseCode = 200;

		$uri = self::createMock(Uri::class);
		$uri->method("__toString")
			->willReturn($exampleUrl);
		$curl = self::createMock(Curl::class);
		$curl->method("setOpt")
			->withConsecutive(
				[CURLOPT_URL, $exampleUrl],
				[CURLOPT_HEADERFUNCTION, fn()=>null]
			);
		$curl->expects(self::once())
			->method("exec")
			->willReturn($exampleBodyContent);
		$curl->expects(self::once())
			->method("getInfo")
			->with(CURLINFO_RESPONSE_CODE)
			->willReturn($exampleResponseCode);

		$request = self::createMock(RequestInterface::class);
		$request->method("getUri")
			->willReturn($uri);

		$sut = new CurlHttpClient();
		$sut->setCurlClass($curl);
		$response = $sut->sendRequest($request);
		self::assertEquals($exampleResponseCode, $response->getStatusCode());
		self::assertEquals($exampleBodyContent, $response->getBody()->getContents());
	}
}