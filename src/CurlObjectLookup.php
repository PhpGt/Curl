<?php
namespace Gt\Curl;

use CurlHandle;

class CurlObjectLookup {
	/** @var array<CurlInterface> */
	protected static array $objectMap = [];
	/** @var array<CurlHandle> */
	protected static array $resourceMap = [];

	public static function add(CurlInterface $curl):void {
		self::$objectMap []= $curl;
		self::$resourceMap []= $curl->getHandle();
	}

	public static function getObjectFromHandle(CurlHandle $ch):?CurlInterface {
		foreach(self::$resourceMap as $i => $resource) {
			if($resource === $ch) {
				return self::$objectMap[$i];
			}
		}

		return null;
	}
}
