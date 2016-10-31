<?php
namespace Gt\CurlInterface;

/**
 * Defines all methods associated with PHP's internal Curl Multi handler,
 * ensuring that objects that Curl Multi object implementations contain
 * matching methods for all internal functions.
 */
interface CurlMultiInterface {

/**
 * @see curl_multi_strerror
 * @param int $errornum
 * @return string error string for valid error code, NULL otherwise
 */
static public function strerror(int $errornum):string;

/**
 * @return resource The underlying Curl Multi resource
 */
public function getHandle():resource;

/**
 * @see curl_multi_add_handle()
 * @param Curl $curl Curl object to add
 * @return int 0 on success, or one of the CURLM_XXX errors code
 */
public function add(CurlInterface $curl):int;

/**
 * @see curl_multi_exec()
 * @param int $stillRunning Flag
 * @return int (One of CURLM_* constants)
 */
public function exec(int &$stillRunning):int;

/**
 * @see curl_multi_getcontent()
 * @param CurlInterface $curl
 * @return string the content of a cURL handle if CURLOPT_RETURNTRANSFER is set
 */
public function getContent(CurlInterface $curl):string;

/**
 * @see curl_multi_info_read()
 * @param int $msgs
 * @return array an associative array for the message, FALSE on failure
 * TODO: An exception should be thrown upon failure, not FALSE.
 */
public function infoRead(int &$msgs = null):array;

/**
 * @see curl_multi_remove_handle()
 * @param CurlInterface $curl Handle to remove
 * @return int 0 on success, or one of the CURLM_XXX error codes
 */
public function remove(Curl $curl):int;

/**
 * @see curl_multi_select()
 * @param float $timeout Timeout
 * @return int The number of descriptors contained in the descriptor sets, may
 * be 0, returns -1 on failure
 */
public function select(float $timeout = 1.0):int;

/**
 * @see curl_multi_setopt
 * @param int $opt One of the CURLMOPT_* constants
 * @param mixed $val The value to be set on option
 * @return boolean True on success, false on failure
 */
public function setOpt(int $opt, $val):bool;

}#