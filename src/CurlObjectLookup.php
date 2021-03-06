<?php
namespace Gt\Curl;

class CurlObjectLookup {
	protected static $objectMap = [];
	protected static $resourceMap = [];

	public static function add(CurlInterface $curl):void {
		self::$objectMap []= $curl;
		self::$resourceMap []= $curl->getHandle();
	}

	public static function getObjectFromHandle($ch):?CurlInterface {
		foreach(self::$resourceMap as $i => $resource) {
			if($resource === $ch) {
				return self::$objectMap[$i];
			}
		}

		return null;
	}
}