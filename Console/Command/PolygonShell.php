<?php

App::import('Vendor', 'GeoTopoJSON', array('file' => 'GeoTopoJSON.php'));
App::import('Vendor', 'geoPHP', array('file' => 'geoPHP/geoPHP.inc'));

class PolygonShell extends AppShell {

    public $uses = array('Area');

    public function main() {
        $rootNode = $this->Area->find('first', array(
            'conditions' => array(
                'name' => '2014'
            ),
        ));
        $nodes = $this->Area->find('threaded', array(
            'conditions' => array(
                'lft >' => $rootNode['Area']['lft'],
                'rght <' => $rootNode['Area']['rght'],
            ),
        ));
        foreach ($nodes AS $countyNode) {
            $countyPolygon = false;
            foreach ($countyNode['children'] AS $townNode) {
                $townPolygon = false;

                $cunliCodes = array();
                foreach ($townNode['children'] AS $cunliNode) {
                    $cunliCodes[$cunliNode['Area']['code']] = $cunliNode['Area']['id'];
                }

                $topoJsonFile = TMP . "json/{$countyNode['Area']['code']}_{$townNode['Area']['code']}.json";
                if (!file_exists($topoJsonFile)) {
                    $jsonContent = file_get_contents("http://kiang.github.io/cunli/json/{$countyNode['Area']['code']}_{$townNode['Area']['code']}.json");
                    if (empty($jsonContent)) {
                        continue;
                    }
                    file_put_contents($topoJsonFile, $jsonContent);
                }
                $geoJson = GeoTopoJSON::toGeoJSONs(file_get_contents($topoJsonFile));
                foreach ($geoJson['layer1']->features AS $feature) {
                    $cunliPolygon = geoPHP::load(json_encode($feature), 'json');
                    if (false === $townPolygon) {
                        $townPolygon = $cunliPolygon;
                    } else {
                        try {
                            $townPolygon = $townPolygon->union($cunliPolygon);
                        } catch (Exception $e) {
                            
                        }
                    }
                    if (isset($cunliCodes[$feature->properties->V_ID])) {
                        $this->Area->save(array('Area' => array(
                                'id' => $cunliCodes[$feature->properties->V_ID],
                                'polygons' => $cunliPolygon->out('json'),
                        )));
                    }
                }
                $this->Area->save(array('Area' => array(
                        'id' => $townNode['Area']['id'],
                        'polygons' => $townPolygon->out('json'),
                )));
                if (false === $countyPolygon) {
                    $countyPolygon = $townPolygon;
                } else {
                    try {
                        $countyPolygon = $countyPolygon->union($townPolygon);
                    } catch (Exception $e) {
                        
                    }
                }
            }
            $this->Area->save(array('Area' => array(
                    'id' => $countyNode['Area']['id'],
                    'polygons' => $countyPolygon->out('json'),
            )));
        }
    }

}
