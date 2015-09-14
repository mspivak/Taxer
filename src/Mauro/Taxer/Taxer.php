<?php namespace Mauro\Taxer;

use Config;

class Taxer {

	static protected $uri = 'http://api.zip-tax.com/request/v20?key=%s&postalcode=%s&format=JSON';

	static function zip($zip, $city = null) {

		$url = sprintf(self::$uri, Config::get('taxer::key'), $zip);

		$rates = json_decode( file_get_contents( $url ) );

		$rate_object = self::getTaxRateFromSet( $rates->results, $city );

		return (float) round($rate_object->taxSales * 100, 2);
	}

	static function getTaxRateFromSet( $set, $city ) {

		if (!is_null($city)) {
			if ($tax_rate = self::getTaxRateFromSetUsingCity( $set, $city )) {
				return $tax_rate;
			}
		}

		return self::getTaxRateFromSetUsingMax( $set );

	}

	static function getTaxRateFromSetUsingCity( $set, $city ) {

		foreach ($set as $rate) {

			if (strtolower($rate->geoCity) == strtolower($city)) {
				return $rate;
			}

		}

		return null;

	}

	static function getTaxRateFromSetUsingMax( $set ) {
		
		$maxRate = null;
		foreach ($set as $rate) {
		
			if (is_null($maxRate) 
			||	$rate->taxSales > $maxRate->taxSales) {
				$maxRate = $rate;
			}
		
		}
		
		return $maxRate;

	}


}