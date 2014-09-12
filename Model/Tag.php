<?php

App::uses('AppModel', 'Model');

class Tag extends AppModel {

    var $name = 'Tag';
    var $validate = array(
        'name' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'This field is required',
            ),
        ),
    );
    var $hasAndBelongsToMany = array(
        'Candidate' => array(
            'joinTable' => 'candidates_tags',
            'foreignKey' => 'Tag_id',
            'associationForeignKey' => 'Candidate_id',
            'className' => 'Candidate',
        ),
    );

    public function beforeDelete($cascade = true) {
        $this->CandidatesTag->deleteAll(array('Tag_id' => $this->id));
        return true;
    }

}
