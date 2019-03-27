<?php

namespace Drupal\geolocation;

/**
 * A service to store and retrieve geolocation data across HTTP requests.
 */
class GeolocationTempStore {

  /**
   * An array of the client's location coordinates.
   *
   * @var array $client_location
   */
  protected static $client_location;

  /**
   * Store the client location for use across HTTP requests in the SESSION.
   *
   * @TODO: Integrate with TempStore. See https://www.drupal.org/node/2743931.
   *
   * @param string $latitude
   *   The client's latitude coordinate.
   * @param string $longitude
   *   The client's longitude coordinate.
   */
  public function setClientLocation($latitude, $longitude) {
    if (empty($latitude) || empty($longitude)) {
      return;
    }
    setCookie('client_location', "{$latitude}:{$longitude}", 0, '/', \Drupal::request()->getHost(), FALSE, TRUE);
  }

  /**
   * Get the client location.
   *
   * @return bool|array
   *   Return FALSE if no client location cookie exists, else return the array
   * of the geolocation data.
   */
  public function getClientLocation() {
    if (isset(self::$client_location)) {
      return self::$client_location;
    }
    $client_location = [];
    $request = \Drupal::request();
    $stored_client_location = $request->cookies->get('client_location');
    $latitude_param = $request->get('proximity_lat', '');
    $longitude_param = $request->get('proximity_lng', '');
    if (!empty($stored_client_location)) {
      list($latitude, $longitude) = explode(':', $stored_client_location);
      // If we have coordinate parameters and they are not the same as the
      // stored geolocation coordinates, then use the coordinate parameter
      // values and store them.
      if ((!empty($latitude_param) && !empty($longitude_param)) &&
          ($latitude !== $latitude_param || $longitude !== $longitude_param)) {
        $latitude = $latitude_param;
        $longitude = $longitude_param;
        // Store the geolocation overrides.
        $this->setClientLocation($latitude, $longitude);
      }
    }
    else {
      $latitude = $latitude_param;
      $longitude = $longitude_param;
      // Store the new geolocation coordinates.
      $this->setClientLocation($latitude, $longitude);
    }
    if (!empty($latitude) && !empty($longitude)) {
      $client_location = [
        'lat' => $latitude,
        'lng' => $longitude,
      ];
    }
    if (!isset(self::$client_location)) {
      self::$client_location = $client_location;
    }
    return $client_location;
  }

}