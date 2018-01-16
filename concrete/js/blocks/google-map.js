concreteGoogleMapInit = function () {
  $('.googleMapCanvas').each(function () {
    try {
      var latitude = $(this).data('latitude');
      var longitude = $(this).data('longitude');
      var zoom = $(this).data('zoom');
      var scrollwheel = $(this).data('scrollwheel') === 'true';
      var draggable = $(this).data('draggable') === 'true';

      var latlng = new google.maps.LatLng(latitude, longitude);

      var mapOptions = {
        zoom: zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        streetViewControl: false,
        scrollwheel: scrollwheel,
        draggable: draggable,
        mapTypeControl: false
      };

      var map = new google.maps.Map(this, mapOptions);

      new google.maps.Marker({
        position: latlng,
        map: map
      });
    } catch (e) {
      $(this).replaceWith("<p>Unable to display map: " + e.message + "</p>")
    }
  });
};
