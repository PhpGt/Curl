<?php
namespace Gt\Curl;

use Gt\Http\Header\ResponseHeaders;
use Gt\Http\Response;
use Http\Client\HttpClient;
use Http\Client\HttpAsyncClient;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CurlHttpClient implements HttpClient, HttpAsyncClient {
	/** @var callable */
	private $curlFactory;

	public function setCurlFactory(callable $factory):void {
		$this->curlFactory = $factory;
	}

	public function sendRequest(RequestInterface $request):ResponseInterface {
		$curl = isset($this->curlFactory)
			? call_user_func($this->curlFactory)
			: new Curl;

		$curl->setOpt(CURLOPT_URL, $request->getUri());
		$headers = new ResponseHeaders();

		$curl->setOpt(CURLOPT_HEADERFUNCTION,
		function($curl, $header) use(&$headers):int {
			$len = strlen($header);
			$header = explode(":", $header, 2);
			if(count($header) < 2) {
				return $len;
			}

			$headers->add($header[0], $header[1]);
			return $len;
		});

		$body = $curl->exec();
		$responseCode = $curl->getInfo(CURLINFO_RESPONSE_CODE);
		return new Response($responseCode, $headers, $body);
	}

	public function sendAsyncRequest(RequestInterface $request):Promise {
		// TODO: Implement sendAsyncRequest() method.
	}
}