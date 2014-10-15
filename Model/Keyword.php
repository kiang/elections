<?php

App::uses('AppModel', 'Model');

class Keyword extends AppModel {

    var $name = 'Keyword';
    var $validate = array(
        'keyword' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'This field is required',
            ),
        ),
    );
    var $hasAndBelongsToMany = array(
        'Link' => array(
            'joinTable' => 'links_keywords',
            'foreignKey' => 'Keyword_id',
            'associationForeignKey' => 'Link_id',
            'className' => 'Link',
        ),
        'Candidate' => array(
            'joinTable' => 'candidates_keywords',
            'foreignKey' => 'Keyword_id',
            'associationForeignKey' => 'Candidate_id',
            'className' => 'Candidate',
        ),
    );
    
    public function beforeSave($options = array()) {
        if(isset($this->data['Keyword']['keyword'])) {
            $this->data['Keyword']['keyword'] = str_replace(array('&amp;bull;', '&bull;', '‧', '.', '•', '．．'), '．', $this->data['Keyword']['keyword']);
        }
        return parent::beforeSave($options);
    }

}
