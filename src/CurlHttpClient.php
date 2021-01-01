<?php
namespace Gt\Curl;

use CurlHandle;
use Gt\Http\Header\ResponseHeaders;
use Gt\Http\Response;
use Gt\Promise\Deferred;
use Http\Client\HttpClient;
use Http\Client\HttpAsyncClient;
use Http\Promise\Promise as HttpPromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class CurlHttpClient implements HttpClient, HttpAsyncClient {
	const DEFAULT_ASYNC_LOOP_DELAY = 100_000;

	/** @var callable */
	private $curlFactory;
	/** @var callable */
	private $curlMultiFactory;
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
	private int $asyncLoopDelay;
	/** @var callable[] */
	private array $asyncCallbackList;

	public function __construct() {
		$this->curlMultiStatus = null;
		$this->active = 0;
		$this->curlList = [];
		$this->headerList = [];
		$this->responseList = [];
		$this->deferredList = [];
		$this->asyncLoopDelay = self::DEFAULT_ASYNC_LOOP_DELAY;
	}

	public function setCurlFactory(callable $factory):void {
		$this->curlFactory = $factory;
	}

	public function setCurlMultiFactory(callable $factory):void {
		$this->curlMultiFactory = $factory;
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
		$this->wait();
		return $returnResponse;
	}

	public function sendAsyncRequest(RequestInterface $request):HttpPromiseInterface {
		$deferred = new Deferred(fn() => $this->processAsync());
		$promise = $deferred->getPromise();
		array_push($this->deferredList, $deferred);
		$curl = $this->getNewCurl($request);

		if(!isset($this->curlMulti)) {
			$this->curlMulti = isset($this->curlMultiFactory)
				? call_user_func($this->curlMultiFactory)
				: new CurlMulti();
		}

		$this->curlMulti->add($curl);
		return $promise;
	}

	public function wait():void {
		do {
			$active = $this->processAsync();
			usleep($this->asyncLoopDelay);
		}
		while($active > 0);
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

	public function registerAsyncCallback(callable $callback):void {
		array_push($this->asyncCallbackList, $callback);
	}

	public function processAsync():int {
		$active = 0;
		$this->curlMultiStatus = $this->curlMulti->exec($active);
		$this->active = $active;

		do {
			$info = $this->curlMulti->infoRead($messagesInQueue);
			if(!$info) {
				continue;
			}
		}
		while($info && $messagesInQueue > 0);

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