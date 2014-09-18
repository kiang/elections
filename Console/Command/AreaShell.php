<?php

class AreaShell extends AppShell {

    public $uses = array('Area');

    public function main() {
        $this->areas_population();
        $this->elections_population();
    }

    public function elections_population() {
        $areas = $this->Area->find('all', array(
            'fields' => array('Area.id', 'Area.population', 'Area.population_electors'),
        ));
        $population = Set::combine($areas, '{n}.Area.id', '{n}.Area.population');
        $population_electors = Set::combine($areas, '{n}.Area.id', '{n}.Area.population_electors');
        $this->Area->Election->updateAll(array('population' => 0, 'population_electors' => 0));
        $links = $this->Area->AreasElection->find('all', array(
            'fields' => array('Election_id', 'Area_id'),
        ));
        foreach ($links AS $link) {
            $this->Area->query("UPDATE elections E SET E.population = E.population + {$population[$link['AreasElection']['Area_id']]}, E.population_electors = E.population_electors + {$population_electors[$link['AreasElection']['Area_id']]} WHERE E.id = '{$link['AreasElection']['Election_id']}'");
        }
    }

    /*
     * ALTER TABLE  `areas` ADD  `population` INT( 11 ) UNSIGNED NOT NULL ,
      ADD  `population_electors` INT( 11 ) UNSIGNED NOT NULL ;
      ALTER TABLE  `elections` ADD  `population` INT( 11 ) UNSIGNED NOT NULL ,
      ADD  `population_electors` INT( 11 ) UNSIGNED NOT NULL ;
     */

    public function areas_population() {
        $areaCodes = $this->Area->find('list', array(
            'fields' => array('Area.code', 'Area.id'),
        ));
        $fh = fopen(__DIR__ . '/data/10304_age.csv', 'r');
        fgetcsv($fh, 2048);
        while ($line = fgetcsv($fh, 2048)) {
            if (!empty($areaCodes[$line[1]])) {
                $populationElectors = $line[4];
                for ($i = 7; $i <= 46; $i++) {
                    $populationElectors -= $line[$i];
                }
                $this->Area->save(array('Area' => array(
                        'id' => $areaCodes[$line[1]],
                        'population' => $line[4],
                        'population_electors' => $populationElectors,
                )));
            }
        }
        fclose($fh);
        $areas = $this->Area->find('all', array(
            'fields' => array('id', 'lft', 'rght'),
            'conditions' => array('Area.rght - Area.lft > 1'),
        ));
        foreach ($areas AS $area) {
            $result = $this->Area->query("SELECT SUM(population) AS p1, SUM(population_electors) AS p2 FROM areas WHERE rght - lft = 1 AND lft > {$area['Area']['lft']} AND rght < {$area['Area']['rght']}");
            if (!empty($result[0][0]['p1'])) {
                $this->Area->save(array('Area' => array(
                        'id' => $area['Area']['id'],
                        'population' => $result[0][0]['p1'],
                        'population_electors' => $result[0][0]['p2'],
                )));
            }
        }
    }

    public function dump_2014_areas() {
        $eLinks = array();
        $links = $this->Area->AreasElection->find('all', array(
            'fields' => array('Area_id', 'Election_id'),
        ));
        foreach ($links AS $link) {
            if (!isset($eLinks[$link['AreasElection']['Election_id']])) {
                $eLinks[$link['AreasElection']['Election_id']] = array();
            }
            $eLinks[$link['AreasElection']['Election_id']][] = $link['AreasElection']['Area_id'];
        }

        $areas = $this->Area->find('all', array(
            'fields' => array('Area.id', 'Area.name', 'Area.ivid', 'Area.code'),
        ));
        $areas = Set::combine($areas, '{n}.Area.id', '{n}.Area');


        $root = $this->Area->Election->find('first', array(
            'conditions' => array('Election.name' => '2014'),
        ));
        $electionTypes = $this->Area->Election->find('all', array(
            'conditions' => array('Election.parent_id' => $root['Election']['id']),
        ));
        foreach ($electionTypes AS $electionType) {
            $result = array();
            $leaves = $this->Area->Election->find('all', array(
                'conditions' => array(
                    'Election.rght - Election.lft = 1',
                    'Election.lft >' => $electionType['Election']['lft'],
                    'Election.rght <' => $electionType['Election']['rght'],
                ),
            ));
            foreach ($leaves AS $leaf) {
                $parents = Set::extract('{n}.Election.name', $this->Area->Election->getPath($leaf['Election']['id'], array('name')));
                $electionTitle = implode(' > ', $parents);
                $electionAreas = array();
                foreach ($eLinks[$leaf['Election']['id']] AS $areaId) {
                    $electionAreas[] = $areas[$areaId];
                }
                $result[] = array(
                    $electionTitle,
                    $electionAreas,
                );
            }
            file_put_contents(__DIR__ . "/data/2014_areas/{$electionType['Election']['name']}.json", json_encode($result));
        }
    }

}
