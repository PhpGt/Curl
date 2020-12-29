<?php
namespace Gt\Curl;

use Http\Client\HttpAsyncClient;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;

class CurlHttpAsyncClient implements HttpAsyncClient {
	public function sendAsyncRequest(RequestInterface $request):Promise {
		// TODO: Implement sendAsyncRequest() method.
	}
}