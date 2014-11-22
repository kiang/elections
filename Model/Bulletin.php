<?php

App::uses('AppModel', 'Model');

/**
 * Bulletin Model
 *
 * @property Election $Election
 */
class Bulletin extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';


    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = array(
        'Election' => array(
            'className' => 'Election',
            'joinTable' => 'bulletins_elections',
            'foreignKey' => 'Bulletin_id',
            'associationForeignKey' => 'Election_id',
            'unique' => 'keepExisting',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
        ),
    );

}
