<?php

$geoJson = new stdClass();
$geoJson->type = 'FeatureCollection';
$geoJson->features = array();
foreach ($areas AS $area) {
    $f = new stdClass();
    $f->type = 'Feature';
    $f->geometry = json_decode($area['Area']['polygons']);
    unset($area['Area']['polygons']);
    $f->properties = $area['Area'];
    $geoJson->features[] = $f;
}
echo json_encode($geoJson);
