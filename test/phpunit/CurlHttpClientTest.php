<?php
namespace Gt\Curl\Test;

use Gt\Curl\Curl;
use Gt\Curl\CurlHttpClient;
use Gt\Curl\CurlMulti;
use Gt\Http\Request;
use Gt\Http\Response;
use Gt\Http\Stream;
use Gt\Http\Uri;
use PHPUnit\Framework\TestCase;

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
		$curlMulti = self::createMock(CurlMulti::class);
		$curlMulti->expects(self::once())
			->method("add")
			->with($curl);
		$curlMulti->expects(self::once())
			->method("exec")
			->willReturnCallback(function() use($curl):int {
				$curl->exec();
				return 1;
			});

		$request = self::createMock(Request::class);
		$request->method("getUri")
			->willReturn($uri);
		$responseBody = self::createMock(Stream::class);
		$responseBody->method("getContents")
			->willReturn($exampleBodyContent);
		$response = self::createMock(Response::class);
		$response->method("getStatusCode")
			->willReturn($exampleResponseCode);
		$response->method("getBody")
			->willReturn($responseBody);

		$sut = new CurlHttpClient();
		$sut->setCurlFactory(fn()=>$curl);
		$sut->setCurlMultiFactory(fn()=>$curlMulti);
		$sut->registerAsyncCallback(function() use($sut, $response) {
			$deferreds = $sut->getDeferredList();
			$deferreds[0]->resolve($response);
		});
		$response = $sut->sendRequest($request);
		self::assertEquals($exampleResponseCode, $response->getStatusCode());
		self::assertEquals($exampleBodyContent, $response->getBody()->getContents());
	}
}