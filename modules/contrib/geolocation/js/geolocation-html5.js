/**
 * @file
 *   Javascript for integrating the W3C Geolocation API.
 */

(function ($, Drupal, drupalSettings, navigator) {

  'use strict';

  /**
   * @namespace
   */
  Drupal.geolocation = Drupal.geolocation || {};
  Drupal.geolocation.html5 = Drupal.geolocation.html5 || {};

  drupalSettings.geolocation.html5 = drupalSettings.geolocation.html5 || {};

  /**
   * Adds a callback that will be called when client location is retrieved.
   *
   * @callback {geolocationHTML5ResultCallback} callback - The callback
   */
  Drupal.geolocation.html5.addResultCallback = function (callback) {
    Drupal.geolocation.html5.resultCallbacks = Drupal.geolocation.html5.resultCallbacks || [];
    Drupal.geolocation.html5.resultCallbacks.push({callback: callback});
  };

  /**
   * Adds an error callback that will be called for the client location.
   *
   * @callback {geolocationHTML5ErrorCallback} callback - The callback
   */
  Drupal.geolocation.html5.addErrorCallback = function (callback) {
    Drupal.geolocation.html5.errorCallbacks = Drupal.geolocation.html5.errorCallbacks || [];
    Drupal.geolocation.html5.errorCallbacks.push({callback: callback});
  };

  /**
   * Get the client location using HTML5 Geolocation API.
   *
   * @callback success - The success callback
   * @callback error - The error callback
   * @param {object} options - The options for the HTML5 geolocation
   */
  Drupal.geolocation.html5.getClientLocation = function(success, error, options) {
    if (typeof success === 'undefined' || !success) {
      alert(Drupal.t('You must provide a success handler for the W3C Geolocation API.'));
      return;
    }

    var error = (error && typeof error === 'function') ? error : Drupal.geolocation.html5.getClientLocationError;
    var options = options || {
      enableHighAccuracy: true,
      timeout: 20000,
      maximumAge: 6000
    };

    // If the browser supports W3C Geolocation API.
    if (navigator.geolocation) {
      // Get the geolocation from the browser.
      navigator.geolocation.getCurrentPosition(success, error, options);

    }
    else {
      alert(Drupal.t('No location data found. Your browser does not support the W3C Geolocation API.'));
    }
  };

  /**
   * Default error handler for Geolocation API.
   *
   * @param {object} error - The error object.
   */
  Drupal.geolocation.html5.getClientLocationError = function(error) {
    // Alert with error message.
    switch (error.code) {
      case error.PERMISSION_DENIED:
        console.log(Drupal.t('No location data found. Reason: PERMISSION_DENIED.'));
        drupalSettings.geolocation.html5.permissionDenied = true;
        break;
      case error.POSITION_UNAVAILABLE:
        console.log(Drupal.t('No location data found. Reason: POSITION_UNAVAILABLE.'));
        break;
      case error.TIMEOUT:
        console.log(Drupal.t('No location data found. Reason: TIMEOUT.'));
        break;
      default:
        console.log(Drupal.t('No location data found. Reason: Unknown error.'));
        break;
    }
  };

  /**
   * Attach HTML5 geolocation functionality.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.geolocationHTML5 = {
    attach: function (context) {
      if (!drupalSettings.geolocation.html5.retrieved &&
          !drupalSettings.geolocation.html5.permissionDenied) {
            //debugger;
        // Get the client location using the Geolocation API.
        Drupal.geolocation.html5.getClientLocation(
          function(position) {
            // Save the state of client location retrieval.
            drupalSettings.geolocation.html5.retrieved = true;

            // Iterate over callbacks and call them with the geolocation result.
            Drupal.geolocation.html5.resultCallbacks = Drupal.geolocation.html5.resultCallbacks || [];
            $.each(Drupal.geolocation.html5.resultCallbacks, function (index, callbackContainer) {
              callbackContainer.callback(position);
            });
          },
          function(error) {
            // Run our error handling first.
            Drupal.geolocation.html5.getClientLocationError(error);

            // Iterate over callbacks and call them with the error.
            Drupal.geolocation.html5.errorCallbacks = Drupal.geolocation.html5.errorCallbacks || [];
            $.each(Drupal.geolocation.html5.errorCallbacks, function (index, callbackContainer) {
              callbackContainer.callback(error);
            });
          }
        );
      }
    }
  };

})(jQuery, Drupal, drupalSettings, navigator);
