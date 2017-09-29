<?php
if (!empty($parents)) {
    foreach ($parents AS $parent) {
        $this->Html->addCrumb($parent['Area']['name'], array('action' => 'map', $parent['Area']['id']));
    }
}
?>
<div class="pull-right btn-group">
    <?php
    foreach ($rootNodes AS $rootNodeId => $rootNodeName) {
        echo $this->Html->link($rootNodeName, '/areas/map/' . $rootNodeId, array('class' => 'btn btn-default'));
    }
    ?>
</div>
<div id="map-canvas" style="width: 100%; height: 400px;"></div>
<div class="clearfix"></div>
<div id="mapAreaIndex"></div>
<script>
    function initialize() {
        var mapOptions = {
            center: new google.maps.LatLng(23.958388030344, 120.70910282983),
            zoom: 7
        },
                map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

        $.getJSON('<?php echo $this->Html->url('/areas/json/' . $areaId); ?>', function (data) {
            map.data.addGeoJson(data);
            zoom(map);
        });
        $('#mapAreaIndex').load('<?php echo $this->Html->url('/areas/index/' . $areaId); ?>/map');
        map.data.setStyle({
            fillColor: '#ff99ff',
            strokeWeight: 1
        });
        map.data.addListener('click', function (event) {
            var selectedId = event.feature.getProperty('id');
            map.data.forEach(function (f) {
                map.data.remove(f);
            });
            $.getJSON('<?php echo $this->Html->url('/areas/json/'); ?>' + selectedId, function (data) {
                map.data.addGeoJson(data);
                $.get('<?php echo $this->Html->url('/areas/breadcrumb/'); ?>' + selectedId, function (block) {
                    $('#header .breadcrumb').html(block);
                });
                zoom(map);
            });
            $('#mapAreaIndex').load('<?php echo $this->Html->url('/areas/index/'); ?>' + selectedId + '/map');
        });
        map.data.addListener('mouseover', function (event) {
            $('a.code' + event.feature.getProperty('code')).addClass('navActive animated bounce');
            map.data.overrideStyle(event.feature, {
                fillColor: '#009900'
            });
        });
        map.data.addListener('mouseout', function (event) {
            $('a.code' + event.feature.getProperty('code')).removeClass('navActive animated bounce');
            map.data.overrideStyle(event.feature, {
                fillColor: '#ff99ff'
            });
        });

    }

    function zoom(map) {
        var bounds = new google.maps.LatLngBounds();
        map.data.forEach(function (feature) {
            processPoints(feature.getGeometry(), bounds.extend, bounds);
        });
        map.fitBounds(bounds);
    }

    function processPoints(geometry, callback, thisArg) {
        if (geometry instanceof google.maps.LatLng) {
            callback.call(thisArg, geometry);
        } else if (geometry instanceof google.maps.Data.Point) {
            callback.call(thisArg, geometry.get());
        } else if (geometry !== null) {
            geometry.getArray().forEach(function (g) {
                processPoints(g, callback, thisArg);
            });
        }
    }

    function loadScript() {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAjE7h1f29c7yQmOBbKUao5XbjH_ZK-e2c&v=3.exp&' +
                'callback=initialize';
        document.body.appendChild(script);
    }

    window.onload = loadScript;
</script>