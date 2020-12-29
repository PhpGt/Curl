<?php
namespace Gt\Curl;

use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CurlHttpClient implements HttpClient {
	public function sendRequest(RequestInterface $request):ResponseInterface {
		// TODO: Implement sendRequest() method.
	}
}