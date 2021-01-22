<?php
namespace Gt\Curl;

use CurlHandle;
use Gt\Http\Response;
use Gt\Http\Stream;
use Gt\Promise\Deferred;
use Http\Client\HttpClient;
use Http\Client\HttpAsyncClient;
use Http\Promise\Promise as HttpPromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class CurlHttpClient implements HttpClient, HttpAsyncClient {
	const DEFAULT_ASYNC_LOOP_DELAY = 100_000;
	const DEFAULT_USER_AGENT = "PhpGt/Curl";

	private CurlOptions $defaultOptions;
	/** @var CurlOptions[] */
	private array $optionsQueue;
	/** @var callable */
	private $curlFactory;
	/** @var callable */
	private $curlMultiFactory;
	private CurlMulti $curlMulti;
	private ?int $curlMultiStatus;
	private int $active;
	/** @var Curl[] */
	private array $curlList;
	/** @var Response[] */
	private array $responseList;
	/** @var Deferred[] */
	private array $deferredList;
	/** @var callable[] */
	private array $asyncCallbackList;
	private int $asyncLoopDelay;

	public function __construct(
		CurlOptions $options = null
	) {
		if($options) {
			$this->defaultOptions = $options;
		}
		$this->optionsQueue = [];

		$this->curlMultiStatus = null;
		$this->active = 0;
		$this->curlList = [];
		$this->responseList = [];
		$this->deferredList = [];
		$this->asyncCallbackList = [];
		$this->asyncLoopDelay = self::DEFAULT_ASYNC_LOOP_DELAY;
	}

	public function setDefaultCurlOptions(CurlOptions $options):void {
		$this->defaultOptions = $options;
	}

	public function pushCurlOptions(CurlOptions $options):void {
		array_push($this->optionsQueue, $options);
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
		$this->completeAll();
		return $returnResponse;
	}

	public function sendAsyncRequest(RequestInterface $request):HttpPromiseInterface {
		$deferred = new Deferred();
		$deferred->addProcess(fn() => $this->processCurlMulti());
		$promise = $deferred->getPromise();
		array_push($this->deferredList, $deferred);
		$curl = $this->createCurl($request);
		$curlIndex = $this->getCurlIndex($curl->getHandle());
		$deferred->addProcess(fn() => $this->responseList[$curlIndex]->asyncProcessStream());

		if(!isset($this->curlMulti)) {
			$this->curlMulti = isset($this->curlMultiFactory)
				? call_user_func($this->curlMultiFactory)
				: new CurlMulti();
		}

		$options = array_pop($this->optionsQueue);
		if($options) {
			foreach($options as $key => $value) {
				$curl->setOpt($key, $value);
			}
		}

		$this->curlMulti->add($curl);
		return $promise;
	}

	public function completeAll():void {
		do {
			$active = $this->processCurlMulti();
			foreach($this->asyncCallbackList as $callback) {
				call_user_func($callback);
			}

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

	public function processCurlMulti():int {
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

	/** @return Deferred[] */
	public function getDeferredList():array {
		return $this->deferredList;
	}

	private function createCurl(RequestInterface $request):Curl {
		$curl = isset($this->curlFactory)
			? call_user_func($this->curlFactory)
			: new Curl();

		$curl->setOpt(CURLOPT_URL, $request->getUri());
		$response = new Response();
		$response = $response->withBody(new Stream());

		$curl->setOpt(
			CURLOPT_HEADERFUNCTION,
			fn($ch, $headerLine) => $this->headerFunction($ch, $headerLine)
		);
		$curl->setOpt(
			CURLOPT_WRITEFUNCTION,
			fn($ch, $data) => $this->writeFunction($ch, $data)
		);

		if(isset($this->defaultOptions)) {
			foreach($this->defaultOptions as $opt => $value) {
				$curl->setOpt($opt, $value);
			}
		}

		$curl->setOpt(CURLOPT_USERAGENT, self::DEFAULT_USER_AGENT);

		$httpHeaders = [];
		foreach($request->getHeaders() as $name => $value) {
			if(!is_array($value)) {
				$value = [$value];
			}
			$headerValue = implode(", ", $value);

			array_push($httpHeaders, "$name: $headerValue");
		}
		if(!empty($httpHeaders)) {
			$curl->setOpt(CURLOPT_HTTPHEADER, $httpHeaders);
		}

		array_push($this->curlList, $curl);
		array_push($this->responseList, $response);

		return $curl;
	}

	private function headerFunction(CurlHandle $ch, string $headerLine):int {
		$curlIndex = $this->getCurlIndex($ch);
		$curl = $this->curlList[$curlIndex];
		$response = $this->responseList[$curlIndex];

		if(!$response->getStatusCode()) {
			$response = $response->withStatus(
				$curl->getInfo(CURLINFO_RESPONSE_CODE)
			);
			$response = $response->withProtocolVersion(
				$curl->getInfo(CURLINFO_HTTP_VERSION)
			);

			$response = $response->withUri(
				$curl->getInfo(CURLINFO_EFFECTIVE_URL)
			);
		}

		$len = strlen($headerLine);
		$headerParts = explode(
			":",
			$headerLine,
			2
		);

		if(count($headerParts) >= 2) {
			$response = $response->withAddedHeader(
				$headerParts[0],
				$headerParts[1]
			);
		}

		$this->responseList[$curlIndex] = $response;
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