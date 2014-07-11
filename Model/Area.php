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

}
