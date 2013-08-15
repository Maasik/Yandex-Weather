<?php

/**
 * 
 * @name:Simple DLE Weather
 * @author: jtiq
 * @date: 15-08-2013
 * 
**/

Class jWeather {
  private $cache = 7200;

  public function load($code) {
    $data = @file_get_contents("http://export.yandex.ru/weather-ng/forecasts/$code.xml");

    if (!is_null($data) && strlen($data)) {
      $xml = @simplexml_load_string($data);
      $i = 'image-v3';

      return array(
        'name' => $xml->attributes()->city,
        'temp' => $xml->fact->temperature,
        'image' => "http://yandex.st/weather/1.1.76.1/i/icons/30x30/".$xml->fact->$i.".png",
        'time' => (time() + mt_rand(-30, 30))
      );
    }

    return null;
  }

  public function get($codes) {
    $return = array();

    if (is_array($codes)) {
      foreach ($codes as $code) {
        $jwdata = get_vars('jweather_'.$code);
        $return[$code] = $jwdata;

        if (($jwdata['time'] + $this->cache) < time()) {
          $jwdata = $this->load($code);

          if (is_array($jwdata)) {
            $return[$code] = $jwdata;
          }
        }
      }
    } else {
      $jwdata = get_vars('jweather_'.$code);
      $return[$code] = $jwdata;

      if (($jwdata['time'] + $this->cache) < time()) {
        $jwdata = $this->load($code);

        if (is_array($jwdata)) {
          $return[$code] = $jwdata;
        }
      }
    }

    return $return;
  }
}

?>
