<?php
namespace Gt\Curl;

class CurlVersion implements CurlVersionInterface {
	public readonly int $versionNumber;
	public readonly string $version;
	public readonly int $sslVersionNumber;
	public readonly string $sslVersion;
	public readonly string $host;
	public readonly int $age;
	public readonly int $features;
	/** @var array<string> */
	public readonly array $protocols;

	/** @param array<string, string|int|array<string>> $curlVersionData */
	public function __construct(array $curlVersionData) {
		$this->versionNumber = $curlVersionData["version_number"] ?? 0;
		$this->version = $curlVersionData["version"] ?? "";
		$this->sslVersionNumber = $curlVersionData["ssl_version_number"] ?? 0;
		$this->sslVersion = $curlVersionData["ssl_version"] ?? "";
		$this->host = $curlVersionData["host"] ?? "";
		$this->age = $curlVersionData["age"] ?? 0;
		$this->features = $curlVersionData["features"] ?? 0;
		$this->protocols = $curlVersionData["protocols"] ?? [];
	}
}
