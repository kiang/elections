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
    var $belongsTo = array(
        'Election' => array(
            'foreignKey' => 'Election_id',
            'className' => 'Election',
        ),
    );

}
