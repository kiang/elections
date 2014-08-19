<?php
if (!empty($parents)) {
    foreach ($parents AS $parent) {
        $this->Html->addCrumb($parent['Area']['name'], array('action' => 'map', $parent['Area']['id']));
    }
}
?>
<div id="map-canvas" style="width: 100%; height: 400px;"></div>
選擇的項目： <span class="mapHoverName"></span>
<div class="clearfix"></div>
<div id="mapAreaIndex"></div>
<script>
    function initialize() {
        var mapOptions = {
            center: new google.maps.LatLng(23.958388030344, 120.70910282983),
            zoom: 7
        };

        var map = new google.maps.Map(document.getElementById('map-canvas'),
                mapOptions);
        $.getJSON('<?php echo $this->Html->url('/areas/json/' . $areaId); ?>', function(data) {
            map.data.addGeoJson(data);
        });
        $('div#mapAreaIndex').load('<?php echo $this->Html->url('/areas/index/' . $areaId); ?>/map');
        map.data.setStyle({
            fillColor: '#ff99ff',
            strokeWeight: 1
        });
        map.data.addListener('click', function(event) {
            var selectedId = event.feature.getProperty('id');
            map.data.forEach(function(f) {
                map.data.remove(f);
            });
            $.getJSON('<?php echo $this->Html->url('/areas/json/'); ?>' + selectedId, function(data) {
                map.data.addGeoJson(data);
                $.get('<?php echo $this->Html->url('/areas/breadcrumb/'); ?>' + selectedId, function(block) {
                    $('#header .breadcrumb').html(block);
                });
            });
            $('div#mapAreaIndex').load('<?php echo $this->Html->url('/areas/index/'); ?>' + selectedId + '/map');
        });
        map.data.addListener('mouseover', function(event) {
            map.data.overrideStyle(event.feature, {
                fillColor: '#009900'
            });
            $('span.mapHoverName').html(event.feature.getProperty('name'));
        });
        map.data.addListener('mouseout', function(event) {
            map.data.overrideStyle(event.feature, {
                fillColor: '#ff99ff'
            });
            $('span.mapHoverName').html('');
        });

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