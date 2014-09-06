<?php

App::uses('AppModel', 'Model');

class Area extends AppModel {

    var $name = 'Area';
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
        if (!empty($this->id)) {
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

}
