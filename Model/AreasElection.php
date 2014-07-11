<?php

App::uses('AppModel', 'Model');

class AreasElection extends AppModel {

    public $name = 'AreasElection';
    public $belongsTo = array(
        'Area' => array(
            'foreignKey' => 'Area_id',
            'className' => 'Area',
        ),
        'Election' => array(
            'foreignKey' => 'Election_id',
            'className' => 'Election',
        ),
    );

}
