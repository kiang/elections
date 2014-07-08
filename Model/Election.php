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
    );
    var $hasMany = array(
        'Candidate' => array(
            'foreignKey' => 'Election_id',
            'dependent' => false,
            'className' => 'Candidate',
        ),
    );

}
