<?php

class AreaShell extends AppShell {

    public $uses = array('Area');

    public function main() {
        $this->dump_areas();
    }

    public function generateKeywords() {
        $nodes = $this->Area->find('list', array(
            'fields' => array('id', 'id'),
        ));
        $count = count($nodes);
        $this->out("found {$count} nodes");
        $count = 0;
        foreach ($nodes AS $nodeId) {
            $path = $this->Area->getPath($nodeId, array('id', 'name'));
            $this->Area->save(array('Area' => array(
                    'id' => $nodeId,
                    'keywords' => implode(',', Set::extract('{n}.Area.name', $path)),
            )));
            if(++$count % 1000 === 0) {
                $this->out("{$count} done");
            }
        }
    }

    public function duplicate_tree() {
        $baseRootNode = $this->Area->find('first', array(
            'conditions' => array(
                'Area.id' => '53c01bce-a960-420c-8efe-5460acb5b862',
            ),
        ));
        $nodes = $this->Area->find('all', array(
            'conditions' => array(
                'Area.lft >=' => $baseRootNode['Area']['lft'],
                'Area.rght <=' => $baseRootNode['Area']['rght'],
            ),
            'order' => array('Area.lft' => 'ASC'),
            'contain' => array('Election'),
        ));
        $count = count($nodes);
        $this->out("found {$count} nodes");
        $areaMap = $electionMap = array();
        foreach ($nodes AS $node) {
            $oldNodeId = $node['Area']['id'];
            unset($node['Area']['id']);
            unset($node['Area']['lft']);
            unset($node['Area']['rght']);

            if (empty($node['Area']['parent_id'])) {
                unset($node['Area']['parent_id']);
            } else {
                $node['Area']['parent_id'] = $areaMap[$node['Area']['parent_id']];
            }
            $this->Area->create();
            if ($this->Area->save(array(
                        'Area' => $node['Area']
                    ))) {
                $areaMap[$oldNodeId] = $this->Area->getInsertID();

                //to create the election
                foreach ($node['Election'] AS $election) {
                    $oldElectionId = $election['id'];
                    unset($election['id']);
                    unset($election['lft']);
                    unset($election['rght']);
                    if (empty($election['parent_id'])) {
                        unset($election['parent_id']);
                    } else {
                        if (!isset($electionMap[$election['parent_id']])) {
                            $parents = $this->Area->Election->getPath($election['parent_id']);
                            foreach ($parents AS $parent) {
                                $oldParentId = $parent['Election']['id'];
                                if (!isset($electionMap[$oldParentId])) {
                                    unset($parent['Election']['id']);
                                    unset($parent['Election']['lft']);
                                    unset($parent['Election']['rght']);
                                    if (empty($parent['Election']['parent_id'])) {
                                        unset($parent['Election']['parent_id']);
                                    } else {
                                        $parent['Election']['parent_id'] = $electionMap[$parent['Election']['parent_id']];
                                    }
                                    if (isset($parent['AreasElection'])) {
                                        unset($parent['AreasElection']);
                                    }
                                    $this->Area->Election->create();
                                    $this->Area->Election->save($parent);
                                    $electionMap[$oldParentId] = $this->Area->Election->getInsertID();
                                }
                            }
                        }
                        $election['parent_id'] = $electionMap[$election['parent_id']];
                    }
                    if (!isset($electionMap[$oldElectionId])) {
                        if (isset($election['AreasElection'])) {
                            unset($election['AreasElection']);
                        }
                        $this->Area->Election->create();
                        $this->Area->Election->save(array('Election' => $election));
                        $electionMap[$oldElectionId] = $this->Area->Election->getInsertID();
                    }

                    $this->Area->AreasElection->create();
                    $this->Area->AreasElection->save(array('AreasElection' => array(
                            'Area_id' => $areaMap[$oldNodeId],
                            'Election_id' => $electionMap[$oldElectionId],
                    )));
                }
            }
            if (--$count % 100 === 0) {
                $this->out("{$count} records left");
            }
        }
    }

    public function elections_population() {
        $queryCount = 0;
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
            if (++$queryCount % 500 == 0) {
                echo "finished elections queries: {$queryCount}\n";
            }
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
        $queryCount = 0;
        $fh = fopen(__DIR__ . '/data/10309_age.csv', 'r');
        fgetcsv($fh, 2048);
        while ($line = fgetcsv($fh, 2048)) {
            if (!empty($areaCodes[$line[1]])) {
                $populationElectors = $line[4];
                for ($i = 7; $i <= 46; $i++) {
                    $populationElectors -= $line[$i];
                }
                $this->Area->query("UPDATE areas SET population = {$line[4]}, population_electors = {$populationElectors} WHERE id = '{$areaCodes[$line[1]]}';");
                if (++$queryCount % 500 == 0) {
                    echo "finished areas queries: {$queryCount}\n";
                }
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
                if (++$queryCount % 500 == 0) {
                    echo "finished areas queries: {$queryCount}\n";
                }
            }
        }
    }

    public function dump_areas() {
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
            'fields' => array('Area.id', 'Area.name', 'Area.ivid', 'Area.code', 'Area.population', 'Area.population_electors'),
        ));
        $areas = Set::combine($areas, '{n}.Area.id', '{n}.Area');
        foreach ($areas AS $k => $v) {
            $parents = Set::extract('{n}.Area.name', $this->Area->getPath($v['id'], array('name')));
            $areas[$k]['name'] = implode(' > ', $parents);
            $areas[$k]['url'] = 'http://k.olc.tw/elections/areas/index/' . $v['id'];
        }

        $root = $this->Area->Election->find('first', array(
            'conditions' => array('Election.name' => '2014-11'),
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
                    'id' => $leaf['Election']['id'],
                    'url' => 'http://k.olc.tw/elections/candidates/index/' . $leaf['Election']['id'],
                    'election' => $electionTitle,
                    'quota' => $leaf['Election']['quota'],
                    'quota_woman' => $leaf['Election']['quota_women'],
                    'population' => $leaf['Election']['population'],
                    'population_electors' => $leaf['Election']['population_electors'],
                    'areas' => $electionAreas,
                );
            }
            file_put_contents(__DIR__ . "/data/2014_areas/{$electionType['Election']['name']}.json", json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }

}
