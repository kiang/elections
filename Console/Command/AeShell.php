<?php

class AeShell extends AppShell {

    public $uses = array('Area');

    public function main() {
        $items = $this->Area->AreasElection->find('all', array(
            'fields' => array('AreasElection.Election_id', 'COUNT(AreasElection.id) AS cnt'),
            'group' => array('AreasElection.Election_id'),
            'order' => array('cnt DESC'),
        ));
        $rootAreaId = $this->Area->field('id', array(
            'name' => '2014',
        ));
        foreach ($items AS $item) {
            if ($item[0]['cnt'] > 1) {
                $areas = $this->Area->find('all', array(
                    'fields' => array('Area.id', 'Area.parent_id'),
                    'conditions' => array(
                        'AreasElection.Election_id' => $item['AreasElection']['Election_id'],
                    ),
                    'joins' => array(
                        array(
                            'table' => 'areas_elections',
                            'alias' => 'AreasElection',
                            'type' => 'inner',
                            'conditions' => array('AreasElection.Area_id = Area.id'),
                        )
                    ),
                ));
                $parentAreas = array();
                $areaList = Set::combine($areas, '{n}.Area.id', '{n}.Area.parent_id');
                foreach ($areas AS $area) {
                    if ($area['Area']['parent_id'] != $rootAreaId) {
                        if (!isset($parentAreas[$area['Area']['parent_id']])) {
                            $parentAreas[$area['Area']['parent_id']] = array();
                        }
                        $parentAreas[$area['Area']['parent_id']][] = $area['Area']['id'];
                    }
                }
                foreach ($parentAreas AS $parentAreaId => $children) {
                    if ($this->Area->find('count', array(
                                'conditions' => array(
                                    'Area.parent_id' => $parentAreaId,
                                ),
                            )) == count($children)) {
                        echo "Area_id = '{$parentAreaId}' AND Election_id = '{$item['AreasElection']['Election_id']}'\n";
                        if (!isset($areaList[$parentAreaId])) {
                            $this->Area->AreasElection->create();
                            $this->Area->AreasElection->save(array('AreasElection' => array(
                                    'Area_id' => $parentAreaId,
                                    'Election_id' => $item['AreasElection']['Election_id'],
                            )));
                        }
                        foreach ($children AS $childAreaId) {
                            $aeList = $this->Area->AreasElection->find('list', array(
                                'conditions' => array(
                                    'Area_id' => $childAreaId,
                                    'Election_id' => $item['AreasElection']['Election_id'],
                                ),
                                'fields' => array('AreasElection.id', 'AreasElection.id'),
                            ));
                            foreach($aeList AS $aeId) {
                                $this->Area->AreasElection->delete($aeId);
                            }
                        }
                    }
                }
            }
        }
    }

}
