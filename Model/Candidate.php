<?php

App::uses('AppModel', 'Model');

class Candidate extends AppModel {

    var $name = 'Candidate';
    var $validate = array(
        'name' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'This field is required',
            ),
        ),
    );
    var $hasAndBelongsToMany = array(
        'Election' => array(
            'joinTable' => 'candidates_elections',
            'foreignKey' => 'Candidate_id',
            'associationForeignKey' => 'Election_id',
            'className' => 'Election',
        ),
    );

}
