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
        'Tag' => array(
            'joinTable' => 'candidates_tags',
            'foreignKey' => 'Candidate_id',
            'associationForeignKey' => 'Tag_id',
            'className' => 'Tag',
        ),
    );

    public function beforeSave($options = array()) {
        if (isset($this->data['Candidate']['image']) && is_array($this->data['Candidate']['image'])) {
            if (!empty($this->data['Candidate']['image']['size'])) {
                $im = new Imagick($this->data['Candidate']['image']['tmp_name']);
                $im->resizeImage(512, 512, Imagick::FILTER_CATROM, 1, true);
                $path = WWW_ROOT . 'media';
                $fileName = str_replace('-', '/', String::uuid()) . '.jpg';
                if (!file_exists($path . '/' . dirname($fileName))) {
                    mkdir($path . '/' . dirname($fileName), 0777, true);
                }
                $im->writeImage($path . '/' . $fileName);
                if (file_exists($path . '/' . $fileName)) {
                    $this->data['Candidate']['image'] = $fileName;
                } else {
                    $this->data['Candidate']['image'] = '';
                }
            } else {
                $this->data['Candidate']['image'] = '';
            }
        }
        return parent::beforeSave($options);
    }

}
