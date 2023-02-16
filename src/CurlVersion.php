<?php
namespace Gt\Curl;

class CurlVersion implements CurlVersionInterface {
	/** @param array<string, string> $curlVersionData */
	public function __construct(
		protected array $curlVersionData
	) {}

	/** @return null|string|array<string, mixed> */
	public function __get(string $key):null|string|array {
		$key = $this->toSnakeCase($key);
		return $this->curlVersionData[$key] ?? null;
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
