(function ($, Drupal, drupalSettings) {
  'use strict';
  console.log('woof1');
  /**
   * @namespace
   */
  drupalSettings.geolocation.html5 = drupalSettings.geolocation.html5 || {};
  drupalSettings.geolocation.html5.proximity_view_ids = drupalSettings.geolocation.html5.proximity_view_ids || [];

  Drupal.geolocation = Drupal.geolocation || {};
  Drupal.geolocation.proximityHTML5 = Drupal.geolocation.proximityHTML5 || {};

  Drupal.geolocation.proximityHTML5.refreshView = function(mapId, coordinates, context) {
    // Get the AJAX settings for this view.
    var viewSettings = Drupal.views.instances['views_dom_id:' + mapId];
    var geolocationAjaxSettings = viewSettings.element_settings;

    // Change the progress indicator.
    geolocationAjaxSettings.progress.type = 'throbber';

    // Add the coordinates to the data to be submitted with the
    // request.
    geolocationAjaxSettings.submit['proximity_lat'] = coordinates.latitude;
    geolocationAjaxSettings.submit['proximity_lng'] = coordinates.longitude;
    console.log(geolocationAjaxSettings);
    // Use AJAX to refresh the view.
    $('.js-view-dom-id-' + mapId, context).trigger('RefreshView');
  };

  Drupal.behaviors.geolocationProximityHTML5 = {
    attach: function(context) {
      Drupal.matt_context = context;
      console.log(context);
      if (!drupalSettings.geolocation.html5.proximity_view_ids.length ||
        drupalSettings.geolocation.html5.permissionDenied ||
        drupalSettings.geolocation.html5.has_coordinates) {
        return;
      }

      $.each(drupalSettings.geolocation.html5.proximity_view_ids, function(key, dom_id) {
        Drupal.geolocation.proximityHTML5[dom_id] = Drupal.geolocation.proximityHTML5[dom_id] || {};
        Drupal.geolocation.proximityHTML5[dom_id].mapLoaded = $.Deferred();
        Drupal.geolocation.proximityHTML5[dom_id].receivedCoordinates = $.Deferred();

        // Resolve map loaded promise immediately if there is no map
        // for this view DOM ID. Otherwise, wait until the map is loaded.
        if (!drupalSettings.geolocation.commonMap ||
            !drupalSettings.geolocation.commonMap[dom_id]) {
          Drupal.geolocation.proximityHTML5[dom_id].mapLoaded.resolve();
        }
        else {
          Drupal.geolocation.addMapLoadedCallback(function (map) {
            Drupal.geolocation.proximityHTML5[dom_id].mapLoaded.resolve(map);
          }, dom_id);
        }

        Drupal.geolocation.html5.addResultCallback(function (position) {
          //Drupal.matt_dom_id = dom_id;
          // If specified, auto refresh the view with the received
          // HTML5 coordinates.
          drupalSettings.geolocation.html5[dom_id].auto_refresh = false;
          if (drupalSettings.geolocation.html5[dom_id] &&
            drupalSettings.geolocation.html5[dom_id].auto_refresh) {
              console.log('ugh');
            Drupal.geolocation.proximityHTML5[dom_id].receivedCoordinates.resolve(position);
            Drupal.geolocation.proximityHTML5[dom_id].position = position;
          }
        });
        // Wait for promises to be resolved for the map to be loaded and
        // coordinates to be received before acting on the map.
        // setTimeout(function() {
        //   Drupal.geolocation.proximityHTML5.refreshView(dom_id,  Drupal.matt_position.coords, context);
        // },5000);
        $.when(
          Drupal.geolocation.proximityHTML5[dom_id].mapLoaded,
          Drupal.geolocation.proximityHTML5[dom_id].receivedCoordinates
        ).then(function(map, position) {
          console.log('test test test');
          Drupal.geolocation.proximityHTML5.refreshView(dom_id, position.coords, context);
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);