<?php
namespace Gt\Curl;

class CurlVersion implements CurlVersionInterface {
	protected $data;

	public function __construct(array $curlVersionData) {
		$this->data = $curlVersionData;
	}

	public function __get(string $key) {
		$key = $this->toSnakeCase($key);
		return $this->data[$key] ?? null;
	}

	protected function toSnakeCase(string $input):string {
		preg_match_all(
			"!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!",
			$input,
			$matches
		);

		$pieces = $matches[0];

		foreach($pieces as &$piece) {
			$piece = ($piece === strtoupper($piece))
				? strtolower($piece)
				: lcfirst($piece);
		}

		return implode('_', $pieces);
	}
}