<?php
namespace Gt\Curl;

use CurlHandle;
use Gt\Json\JsonDecodeException;
use Gt\Json\JsonObject;
use Gt\Json\JsonObjectBuilder;

class Curl implements CurlInterface {
	protected CurlHandle $ch;
	protected ?string $buffer;

	public function __construct(string $url = null) {
		$this->buffer = null;
		$this->init($url);
	}

	/**
	 * Close a cURL session
	 * @see http://php.net/manual/en/function.curl-close.php
	 */
	public function __destruct() {
		curl_close($this->ch);
	}

	/**
	 * Copy a cURL handle along with all of its preferences
	 * @see http://php.net/manual/en/function.curl-copy-handle.php
	 */
	public function __clone() {
		$this->ch = curl_copy_handle($this->ch);
	}

	/**
	 * @see http://php.net/manual/en/function.curl-version.php
	 */
	public static function version(
		int $age = CURLVERSION_NOW
	):CurlVersionInterface {
		return new CurlVersion(curl_version());
	}

	/**
	 * Return string describing the given error code
	 * @see http://php.net/manual/en/function.curl-strerror.php
	 */
	public static function strError(int $errorNum):string {
		return curl_strerror($errorNum);
	}

	/**
	 * Return string containing last exec call's output
	 * @throws NoOutputException
	 */
	public function output():string {
// Buffer will be null before exec is called...
		if(is_null($this->buffer)) {
			$this->exec();
		}

		curl_close($this->ch);

// ...But will always be a string after exec is called, even if empty.
		if(strlen($this->buffer) === 0) {
			throw new NoOutputException();
		}

		return $this->buffer;
	}

	/**
	 * Return json-decoded output from last exec call
	 * @throws JsonDecodeException
	 */
	public function outputJson(
		int $depth = 512,
		int $options = 0
	):JsonObject {
		$builder = new JsonObjectBuilder($depth, $options);
		return $builder->fromJsonString($this->output());
	}

	/**
	 * Return the last error number
	 * @see http://php.net/manual/en/function.curl-errno.php
	 */
	public function errno():int {
		return curl_errno($this->ch);
	}

	/**
	 * Return a string containing the last error for the current session
	 * @see http://php.net/manual/en/function.curl-error.php
	 */
	public function error():string {
		return curl_error($this->ch);
	}

	/**
	 * URL encodes the given string
	 * @see http://php.net/manual/en/function.curl-escape.php
	 */
	public function escape(string $str):string {
		return curl_escape($this->ch, $str);
	}

	/**
	 * Perform a cURL session
	 * @see http://php.net/manual/en/function.curl-exec.php
	 */
	public function exec():string {
		ob_start();
		$response = curl_exec($this->ch);
		$this->buffer = ob_get_contents();
		ob_end_clean();

		if(false === $response) {
			throw new CurlException($this->error());
		}
		if(true === $response) {
			$response = $this->buffer;
		}

		return $response;
	}

	/**
	 * Get information regarding the transfer
	 * @see http://php.net/manual/en/function.curl-getinfo.php
	 */
	public function getInfo(int $opt):mixed {
		if($opt <= 0) {
			throw new CurlException(
				"Option must be greater than zero, "
				. $opt
				. " given."
			);
		}
		return curl_getinfo($this->ch, $opt);
	}

	/**
	 * Initialize a cURL session
	 * @see http://php.net/manual/en/function.curl-init.php
	 */
	public function init(string $url = null):void {
		$this->ch = curl_init($url);
		CurlObjectLookup::add($this);
	}

	/**
	 * Pause and unpause a connection
	 * @see http://php.net/manual/en/function.curl-pause.php
	 */
	public function pause(int $bitmask):int {
		return curl_pause($this->ch, $bitmask);
	}

	/**
	 * Reset all options of the libcurl session handle
	 * @see http://php.net/manual/en/function.curl-reset.php
	 */
	public function reset():void {
		curl_reset($this->ch);
	}

	/**
	 * Set an option for the cURL transfer
	 * @see http://php.net/manual/en/function.curl-setopt.php
	 */
	public function setOpt(int $option, mixed $value):bool {
		return curl_setopt($this->ch, $option, $value);
	}

	/**
	 * Set multiple options for the cURL transfer
	 * @see http://php.net/manual/en/function.curl-setopt-array.php
	 */
	public function setOptArray(array $options):bool {
		return curl_setopt_array($this->ch, $options);
	}

	/**
	 * Decodes the given URL encoded string
	 * @see http://php.net/manual/en/function.curl-unescape.php
	 */
	public function unescape(string $str):string {
		return curl_unescape($this->ch, $str);
	}

	/**
	 * Obtain the underlying curl resource, as created with curl_init.
	 */
	public function getHandle():CurlHandle {
		return $this->ch;
	}

	/**
	 * Gets all CURLINFO_ data, identical to calling curl_getinfo with no arguments.
	 * @return array<mixed>
	 */
	public function getAllInfo():array {
		/** @var array<string, mixed>|false $result */
		$result = curl_getinfo($this->ch, 0);
		return $result ?: [];
	}
}
