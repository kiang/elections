<?php

App::uses('AppModel', 'Model');

class Area extends AppModel {

    var $name = 'Area';

    var $validate = array(

        'parent_id' => array(

            'notEmpty' => array(

                'rule' => 'notEmpty',

                'message' => 'This field is required',

            ),

        ),

        'name' => array(

            'notEmpty' => array(

                'rule' => 'notEmpty',

                'message' => 'This field is required',

            ),

        ),

        'lft' => array(

            'notEmpty' => array(

                'rule' => 'notEmpty',

                'message' => 'This field is required',

            ),

        ),

        'rght' => array(

            'notEmpty' => array(

                'rule' => 'notEmpty',

                'message' => 'This field is required',

            ),

        ),

        'is_area' => array(

            'booleanFormat' => array(

                'rule' => 'boolean',

                'message' => 'Wrong format',

                'allowEmpty' => true,

            ),

        ),

    );
                

    var $actsAs = array(

    );



    var $hasAndBelongsToMany = array(

        'Election' => array(


            'joinTable' => 'areas_elections',



            'foreignKey' => 'Area_id',



            'associationForeignKey' => 'Election_id',



            'className' => 'Election',


        ),

    );



    function afterSave($created, $options = array()) {

	}

}