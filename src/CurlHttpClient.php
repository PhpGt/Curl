<?php
namespace Gt\Curl;

use CurlHandle;
use Gt\Http\Header\ResponseHeaders;
use Gt\Http\Response;
use Gt\Promise\Deferred;
use Gt\Promise\Promise;
use Http\Client\HttpClient;
use Http\Client\HttpAsyncClient;
use Http\Promise\Promise as HttpPromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class CurlHttpClient implements HttpClient, HttpAsyncClient {
	/** @var callable */
	private $curlFactory;

	private CurlMulti $curlMulti;
	private ?int $curlMultiStatus;
	private int $active;
	/** @var Curl[] */
	private array $curlList;
	/** @var ResponseHeaders[] */
	private array $headerList;
	/** @var Response[] */
	private array $responseList;
	/** @var Deferred[] */
	private array $deferredList;

	public function __construct() {
		$this->curlMulti = new CurlMulti();
		$this->curlMultiStatus = null;
		$this->active = 0;
		$this->curlList = [];
		$this->headerList = [];
		$this->responseList = [];
		$this->deferredList = [];
	}

	public function setCurlFactory(callable $factory):void {
		$this->curlFactory = $factory;
	}

	public function sendRequest(RequestInterface $request):ResponseInterface {
		$returnResponse = null;

		$promise = $this->sendAsyncRequest($request);
		$promise->then(
			function(Response $response)use(&$returnResponse) {
				$returnResponse = $response;
			},
			function(Throwable $reason) {
				throw $reason;
			}
		);
		$promise->wait();
		return $returnResponse;
	}

	public function sendAsyncRequest(RequestInterface $request):HttpPromiseInterface {
		$deferred = new Deferred(fn() => $this->processAsync());
		$promise = $deferred->getPromise();
		array_push($this->deferredList, $deferred);
		$curl = $this->getNewCurl($request);
		$this->curlMulti->add($curl);
		return $promise;
	}

	public function asyncWaiting():int {
		if($this->active > 0) {
			return $this->active;
		}

		if(is_null($this->curlMultiStatus)) {
			return count($this->curlList);
		}

		return 0;
	}

	public function processAsync():int {
		$active = 0;
		$this->curlMultiStatus = $this->curlMulti->exec($active);
		$this->active = $active;

		do {
			$info = curl_multi_info_read($this->curlMulti->getHandle(), $q);
			if(!$info) {
				continue;
			}

//			if($info["msg"] === CURLMSG_DONE) {
//				$doneIndex = $this->getCurlIndex($info["handle"]);
//				$this->deferredList[$doneIndex]->resolve(
//					$this->responseList[$doneIndex]
//				);
//			}
		}
		while($info && $q > 0);

		return $active;
	}

	private function getNewCurl(RequestInterface $request):Curl {
		$curl = isset($this->curlFactory)
			? call_user_func($this->curlFactory)
			: new Curl();

		$curl->setOpt(CURLOPT_URL, $request->getUri());
		$headers = new ResponseHeaders();
		$response = new Response();

		$curl->setOpt(
			CURLOPT_HEADERFUNCTION,
			fn($ch, $headerLine) => $this->headerFunction($ch, $headerLine)
		);
		$curl->setOpt(
			CURLOPT_WRITEFUNCTION,
			fn($ch, $data) => $this->writeFunction($ch, $data)
		);

		array_push($this->curlList, $curl);
		array_push($this->headerList, $headers);
		array_push($this->responseList, $response);

		return $curl;
	}

	private function headerFunction(CurlHandle $ch, string $headerLine):int {
		$curlIndex = $this->getCurlIndex($ch);
		$headers = $this->headerList[$curlIndex];

		$len = strlen($headerLine);
		$headerParts = explode(
			":",
			$headerLine,
			2
		);

		if(count($headerParts) < 2) {
			return $len;
		}

		$headers->add($headerParts[0], $headerParts[1]);
		return $len;
	}

	private function writeFunction(CurlHandle $ch, string $data):int {
		$curlIndex = $this->getCurlIndex($ch);

		$this->deferredList[$curlIndex]->resolve(
			$this->responseList[$curlIndex]
		);

		$response = $this->responseList[$curlIndex];
		$body = $response->getBody();
		$body->write($data);

		return strlen($data);
	}

	private function getCurlIndex(CurlHandle $ch):?int {
		foreach($this->curlList as $i => $curl) {
			if($curl->getHandle() === $ch) {
				return $i;
			}
		}

		return null;
	}
}