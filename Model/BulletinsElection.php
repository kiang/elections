<?php

App::uses('AppModel', 'Model');

class BulletinsElection extends AppModel {

    public $name = 'BulletinsElection';
    public $belongsTo = array(
        'Bulletin' => array(
            'foreignKey' => 'Bulletin_id',
            'className' => 'Bulletin',
        ),
        'Election' => array(
            'foreignKey' => 'Election_id',
            'className' => 'Election',
        ),
    );

}
