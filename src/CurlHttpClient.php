<?php
namespace Gt\Curl;

use Gt\Http\Header\ResponseHeaders;
use Gt\Http\Response;
use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CurlHttpClient implements HttpClient {
	private CurlInterface $curl;

	public function setCurlClass(CurlInterface $curl):void {
		$this->curl = $curl;
	}

	public function sendRequest(RequestInterface $request):ResponseInterface {
		$curl = $this->curl ?? new Curl();
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
}