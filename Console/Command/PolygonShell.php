<?php

App::import('Vendor', 'GeoTopoJSON', array('file' => 'GeoTopoJSON.php'));
App::import('Vendor', 'geoPHP', array('file' => 'geoPHP/geoPHP.inc'));

class PolygonShell extends AppShell {

    public $uses = array('Area');

    public function main() {
        $files = array(
            'twcounty2010.3.json',
            'twtown2010.3.json',
            'twvillage2010.3.json',
        );
        $rootNode = $this->Area->find('first', array(
            'conditions' => array(
                'name' => '2014'
            ),
        ));
        foreach ($files AS $file) {
            $jsonFile = TMP . "json/{$file}";
            if (!file_exists($jsonFile)) {
                file_put_contents($jsonFile, file_get_contents('https://github.com/ronnywang/twgeojson/raw/master/' . $file));
            }
            $json = json_decode(file_get_contents($jsonFile));
            foreach ($json->features AS $feature) {
                if (isset($feature->properties->v_id)) {
                    $areaId = $this->Area->field('id', array(
                        'code' => $feature->properties->v_id,
                        'lft >' => $rootNode['Area']['lft'],
                        'rght <' => $rootNode['Area']['rght'],
                    ));
                    if (!empty($areaId)) {
                        $this->Area->save(array('Area' => array(
                                'id' => $areaId,
                                'polygons' => json_encode($feature->geometry),
                        )));
                    }
                } elseif (isset($feature->properties->town_id)) {
                    $areaId = $this->Area->field('id', array(
                        'code' => $feature->properties->town_id,
                        'lft >' => $rootNode['Area']['lft'],
                        'rght <' => $rootNode['Area']['rght'],
                    ));
                    if (!empty($areaId)) {
                        $this->Area->save(array('Area' => array(
                                'id' => $areaId,
                                'polygons' => json_encode($feature->geometry),
                        )));
                    }
                } else {
                    $areaId = $this->Area->field('id', array(
                        'code' => $feature->properties->county_id,
                        'lft >' => $rootNode['Area']['lft'],
                        'rght <' => $rootNode['Area']['rght'],
                    ));
                    if (!empty($areaId)) {
                        $this->Area->save(array('Area' => array(
                                'id' => $areaId,
                                'polygons' => json_encode($feature->geometry),
                        )));
                    }
                }
            }
        }
    }

}
