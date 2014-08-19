<?php
if (!empty($parents)) {
    foreach ($parents AS $parent) {
        $this->Html->addCrumb($parent['Area']['name'], array('action' => 'map', $parent['Area']['id']));
    }
}
?>
<div id="map-canvas" style="width: 800px; height: 600px;"></div>
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
        map.data.setStyle({
            fillColor: '#ff99ff',
            strokeWeight: 1
        });
        map.data.addListener('click', function(event) {
            location.href = '<?php echo $this->Html->url('/areas/map/'); ?>' + event.feature.getProperty('id');
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