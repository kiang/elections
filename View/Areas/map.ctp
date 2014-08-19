<?php
$geoJson = new stdClass();
$geoJson->type = 'FeatureCollection';
$geoJson->features = array();
foreach ($areas AS $area) {
    $f = new stdClass();
    $f->type = 'Feature';
    $f->geometry = json_decode($area['Area']['polygons']);
    $geoJson->features[] = $f;
}
?>
<div id="map-canvas" style="width: 800px; height: 600px;"></div>
<script>
    function initialize() {
        var mapOptions = {
            center: new google.maps.LatLng(0, 0),
            zoom: 6
        };

        var map = new google.maps.Map(document.getElementById('map-canvas'),
                mapOptions);
        map.data.addGeoJson(<?php echo json_encode($geoJson); ?>);
    }

    function zoom(map) {
        var bounds = new google.maps.LatLngBounds();
        map.data.forEach(function(feature) {
            processPoints(feature.getGeometry(), bounds.extend, bounds);
        });
        map.fitBounds(bounds);
    }

    function processPoints(geometry, callback, thisArg) {
        if (geometry instanceof google.maps.LatLng) {
            callback.call(thisArg, geometry);
        } else if (geometry instanceof google.maps.Data.Point) {
            callback.call(thisArg, geometry.get());
        } else {
            $.foreach(geometry, function(g) {
                processPoints(g, callback, thisArg);
            });
        }
    }

    function loadScript() {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&' +
                'callback=initialize';
        document.body.appendChild(script);
    }

    window.onload = loadScript;
</script>