<?php

App::uses('AppModel', 'Model');

class Election extends AppModel {

    var $name = 'Election';
    var $validate = array(
        'name' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'This field is required',
            ),
        ),
    );
    var $actsAs = array('Tree');
    var $hasAndBelongsToMany = array(
        'Area' => array(
            'joinTable' => 'areas_elections',
            'foreignKey' => 'Election_id',
            'associationForeignKey' => 'Area_id',
            'className' => 'Area',
        ),
        'Bulletin' => array(
            'className' => 'Bulletin',
            'joinTable' => 'bulletins_elections',
            'foreignKey' => 'Election_id',
            'associationForeignKey' => 'Bulletin_id',
            'unique' => 'keepExisting',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
        )
    );
    public $hasMany = array(
        'Candidate' => array(
            'foreignKey' => 'election_id',
            'dependent' => false,
            'className' => 'Candidate',
        ),
        'AreasElection' => array(
            'foreignKey' => 'Election_id',
            'dependent' => false,
            'className' => 'AreasElection',
        ),
    );

    public function getView($id = '') {
        if (!empty($id)) {
            $cacheKey = "ElectionsView{$id}";
        } else {
            $cacheKey = "ElectionsViewRoot";
        }
        $result = Cache::read($cacheKey, 'long');
        if (!$result) {
            $result = array(
                'election' => array(),
                'parents' => array(),
                'children' => array(),
            );
            if (!empty($id)) {
                $result['election'] = $this->find('first', array(
                    'conditions' => array(
                        'Election.id' => $id,
                    ),
                    'contain' => array(
                        'Area' => array(
                            'fields' => array('Area.id', 'Area.name', 'Area.is_area',
                                'Area.keywords',),
                        ),
                        'Candidate' => array(
                            'conditions' => array(
                                'Candidate.active_id IS NULL',
                                'Candidate.is_reviewed' => '1',
                            ),
                            'fields' => array('Candidate.id', 'Candidate.image',
                                'Candidate.stage', 'Candidate.name', 'Candidate.party',
                                'Candidate.gender', 'Candidate.no', 'Candidate.name_english'),
                        ),
                    ),
                ));
                $result['parents'] = $this->getPath($id);
                $result['children'] = $this->find('all', array(
                    'conditions' => array(
                        'Election.parent_id' => $id,
                    ),
                    'order' => array('Election.name' => 'ASC'),
                ));
            } else {
                $result['children'] = $this->find('all', array(
                    'conditions' => array(
                        'Election.parent_id IS NULL',
                    ),
                    'order' => array('Election.name' => 'DESC'),
                ));
            }

            Cache::write($cacheKey, $result, 'long');
        }
        return $result;
    }

    public function getAreas($electionId) {
        $result = array();
        $areas = $this->AreasElection->find('list', array(
            'contain' => array('Area'),
            'fields' => array('Area.id', 'Area.keywords'),
            'conditions' => array(
                'AreasElection.Election_id' => $electionId
            ),
        ));
        if (count($areas) === 1) {
            $result[0] = array_pop($areas);
            $pos = strrpos($result[0], ',');
            if (false !== $pos) {
                $result[0] = substr($result[0], $pos + 1);
            }
        } else {
            $maxElementCount = 0;
            foreach ($areas AS $areaKey => $areaVal) {
                $areas[$areaKey] = explode(',', $areaVal);
                $count = count($areas[$areaKey]);
                if ($count > $maxElementCount) {
                    $maxElementCount = $count;
                }
            }
            if ($maxElementCount === 4) {
                $newAreas = array();
                foreach ($areas AS $areaKey => $areaVal) {
                    if (!isset($newAreas[$areaVal[2]])) {
                        $newAreas[$areaVal[2]] = array();
                    }
                    if (isset($areaVal[3])) {
                        $newAreas[$areaVal[2]][] = $areaVal[3];
                    }
                }
                foreach ($newAreas AS $areaKey => $areaVal) {
                    if (!empty($areaVal)) {
                        $result[] = $areaKey . '(' . implode(',', $areaVal) . ')';
                    } else {
                        $result[] = $areaKey;
                    }
                }
            } else {
                foreach ($areas AS $areaKey => $areaVal) {
                    $result[] = array_pop($areaVal);
                }
            }
        }
        return $result;
    }

}
