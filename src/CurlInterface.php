<?php
namespace Gt\CurlInterface;

/**
 * Defines all methods associated with PHP's internal Curl handler,
 * ensuring that objects that Curl object implementations contain
 * matching methods for all internal functions.
 */
interface CurlInterface {
	/**
	 * @see curl_copy_handle()
	 */
	public function __clone();

	/**
	 * Obtain the underlying curl resource, as created with curl_init.
	 */
	public function getHandle():resource;

	/**
	 * @see curl_version()
	 */
	static public function version(int $age = CURLVERSION_NOW):array;

	/**
	 * @see curl_strerror()
	 */
	static public function strerror(int $errornum):array;

	/**
	 * @see curl_init()
	 */
	public function init(string $url = null):void;

	/**
	 * @see curl_errno()
	 */
	public function errno():int;

	/**
	 * @see curl_error()
	 */
	public function error():string;

	/**
	 * @see curl_exec()
	 * @throws CurlException if curl_exec() returned false
	 *
	 * @todo Can't have string and bool as return type! Force exception?
	 */
	public function exec(int $attempts = 1, bool $useException = false):string;

	/**
	 * Gets the info corresponding to CURLINFO_* constant. For all info, use getAllInfo()
	 * @see curl_getinfo()
	 */
	public function getInfo(int $opt = 0):string;

	/**
	 * Gets all CURLINFO_ data, identical to calling curl_getinfo with no arguments.
	 * @see curl_getinfo()
	 */
	public function getAllInfo():array;

	/**
	 * @see curl_setopt()
	 */
	public function setOpt(int $option, $value):bool;

	/**
	 * @see curl_setopt_array()
	 */
	public function setOptArray(array $options):bool;

	/**
	 * @see curl_escape()
	 * @throws CurlException on failure
	 */
	public function escape(string $str):string;

	/**
	 * @see curl_unescape()
	 * @throws CurlException on failure
	 */
	public function unescape(string $str):string;

	/**
	 * @see curl_reset()
	 */
	public function reset():void;

	/**
	 * Sets combination of CURLPAUSE_* constants, returning CURLE_OK on success.
	 * @see curl_pause()
	 */
	public function pause(int $bitmask):int;
}
