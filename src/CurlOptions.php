<?php
namespace Gt\Curl;

use Iterator;

class CurlOptions implements Iterator {
	/** @var mixed[] */
	private array $optArray;
	private int $iteratorKey;

	public function __construct() {
		$this->optArray = [];
		$this->iteratorKey = 0;
	}

	public function set(int $curlOpt, $value):void {
		$this->optArray[$curlOpt] = $value;
	}

	public function get(int $curlOpt):mixed {
		return $this->optArray[$curlOpt] ?? null;
	}

	public function rewind():void {
		$this->iteratorKey = 0;
	}

	public function valid():bool {
		return isset($this->optArray[$this->key()]);
	}

	public function key():?int {
		$keys = array_keys($this->optArray);
		return $keys[$this->iteratorKey] ?? null;
	}

	public function current():mixed {
		$key = $this->key();
		return $this->optArray[$key] ?? null;
	}

	public function next():void {
		$this->iteratorKey++;
	}
}