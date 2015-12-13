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

}
