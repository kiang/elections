<?php

App::uses('AppModel', 'Model');

class Link extends AppModel {

    var $name = 'Link';
    var $validate = array(
        'title' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'This field is required',
            ),
        ),
    );
    var $hasAndBelongsToMany = array(
        'Keyword' => array(
            'joinTable' => 'links_keywords',
            'foreignKey' => 'Link_id',
            'associationForeignKey' => 'Keyword_id',
            'className' => 'Keyword',
        ),
    );

}
