<?php namespace Mauro\Taxer;

use Config;

class Taxer {

	static protected $uri = 'https://taxrates.api.avalara.com:443/postal?country=%s&postal=%s&apikey=%s';

	static function  zip($zip, $country = 'usa') {

		$url = sprintf(self::$uri, $country, $zip, urlencode(Config::get('taxer::key')) );

		$rate_object = json_decode( file_get_contents( $url ) );

		return (float) $rate_object->totalRate;
	}

}