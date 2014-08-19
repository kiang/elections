<div id="map-canvas" style="width: 800px; height: 600px;"></div>
<script>
    function initialize() {
        var mapOptions = {
            center: new google.maps.LatLng(0, 0),
            zoom: 6
        };

        var map = new google.maps.Map(document.getElementById('map-canvas'),
                mapOptions);
        $.getJSON('<?php echo $this->Html->url('/areas/json/' . $areaId); ?>', function(data) {
            map.data.addGeoJson(data);
        });

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