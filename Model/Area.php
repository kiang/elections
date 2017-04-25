<?php

App::uses('AppModel', 'Model');

class Area extends AppModel {

    var $name = 'Area';
    var $validate = array(
        'name' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'This field is required',
            ),
        ),
    );
    var $actsAs = array('Tree');
    var $hasAndBelongsToMany = array(
        'Election' => array(
            'joinTable' => 'areas_elections',
            'foreignKey' => 'Area_id',
            'associationForeignKey' => 'Election_id',
            'className' => 'Election',
        ),
    );
    public $hasMany = array(
        'AreasElection' => array(
            'foreignKey' => 'Area_id',
            'dependent' => false,
            'className' => 'AreasElection',
        ),
    );

    public function beforeSave($options = array()) {
        if (!empty($this->id) && isset($this->data['Area']['name'])) {
            $oldName = $this->field('name');
            if ($oldName !== $this->data['Area']['name']) {
                $electionIds = $this->AreasElection->find('list', array(
                    'fields' => array('Election_id', 'Election_id'),
                    'conditions' => array(
                        'AreasElection.Area_id' => $this->id,
                    ),
                ));
                $this->Election->updateAll(array('Election.name' => "'{$this->data['Area']['name']}'"), array(
                    'Election.id' => $electionIds,
                    'Election.name' => $oldName,
                ));
            }
        }
    }

    public function getView($id = '') {
        if (!empty($id)) {
            $cacheKey = "AreasView{$id}";
        } else {
            $cacheKey = "AreasViewRoot";
        }
        $result = Cache::read($cacheKey, 'long');
        if (!$result) {
            $result = array(
                'area' => array(),
                'parents' => array(),
                'children' => array(),
            );
            if (!empty($id)) {
                $result['area'] = $this->find('first', array(
                    'conditions' => array(
                        'Area.id' => $id,
                    ),
                    'contain' => array(
                        'Election' => array(
                            'fields' => array('id', 'population_electors', 'population',
                                'quota', 'quota_women', 'bulletin_key'),
                        ),
                    ),
                ));
                $result['parents'] = $this->getPath($id);
                $result['children'] = $this->find('all', array(
                    'conditions' => array(
                        'Area.parent_id' => $id,
                    )
                ));
            } else {
                $result['children'] = $this->find('all', array(
                    'conditions' => array(
                        'Area.parent_id IS NULL',
                    )
                ));
            }
            Cache::write($cacheKey, $result, 'long');
        }
        return $result;
    }

}
