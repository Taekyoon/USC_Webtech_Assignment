<?php
class GoogleGeocodeTranslator {
  private $api_key;
  private $results;

  public function __construct($api_key) {
    $this->api_key = $api_key;
  }

  public function get_results() {
    return $this->results;
  }

  public function request_geocode($location) {
    try {
      $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".
                $location. "&key=".$this->api_key;
      $str = file_get_contents($url);
      $source = json_decode($str, true);
      $results = $source['results'][0];
      $geolocation = $results['geometry']['location'];
      $this->results = $geolocation['lat']. ','. $geolocation['lng'];
    } catch (Exception $e) {
      echo 'Geocode Translator has an error: ' . $e->getMessage();
      $this->results = null;
    }
  }
}
?>
