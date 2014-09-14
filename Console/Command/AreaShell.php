<?php

class AreaShell extends AppShell {

    public $uses = array('Area');

    public function main() {
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
