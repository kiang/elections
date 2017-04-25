<?php

App::uses('AppModel', 'Model');

class Candidate extends AppModel {

    var $name = 'Candidate';
    var $validate = array(
        'name' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'This field is required',
            ),
        ),
    );
    public $belongsTo = array(
        'Election' => array(
            'foreignKey' => 'election_id',
            'className' => 'Election',
        ),
    );
    var $hasAndBelongsToMany = array(
        'Tag' => array(
            'joinTable' => 'candidates_tags',
            'foreignKey' => 'Candidate_id',
            'associationForeignKey' => 'Tag_id',
            'className' => 'Tag',
        ),
        'Keyword' => array(
            'joinTable' => 'candidates_keywords',
            'foreignKey' => 'Candidate_id',
            'associationForeignKey' => 'Keyword_id',
            'className' => 'Keyword',
        ),
    );

    public function beforeSave($options = array()) {
        if (isset($this->data['Candidate']['image']) && is_array($this->data['Candidate']['image'])) {
            if (!empty($this->data['Candidate']['image']['size'])) {
                $im = new Imagick($this->data['Candidate']['image']['tmp_name']);
                $im->resizeImage(512, 512, Imagick::FILTER_CATROM, 1, true);
                $path = WWW_ROOT . 'media';
                $fileName = str_replace('-', '/', CakeText::uuid()) . '.jpg';
                if (!file_exists($path . '/' . dirname($fileName))) {
                    mkdir($path . '/' . dirname($fileName), 0777, true);
                }
                $im->writeImage($path . '/' . $fileName);
                if (file_exists($path . '/' . $fileName)) {
                    $this->data['Candidate']['image'] = $fileName;
                } else {
                    unset($this->data['Candidate']['image']);
                }
            } else {
                unset($this->data['Candidate']['image']);
            }
        }
        if (isset($this->data['Candidate']['name'])) {
            $this->data['Candidate']['name'] = str_replace(array('&amp;bull;', '&bull;', '‧', '.', '•', '．．'), '．', $this->data['Candidate']['name']);
        }
        return parent::beforeSave($options);
    }

    public function getView($id) {
        $cacheKey = "CandidatesView{$id}";
        $result = Cache::read($cacheKey, 'long');
        if (!$result) {
            $result = array(
                'candidate' => array(),
                'parents' => array(),
            );
            $result['candidate'] = $this->find('first', array(
                'conditions' => array(
                    'Candidate.id' => $id,
                    'Candidate.active_id IS NULL',
                    'Candidate.is_reviewed' => '1',
                ),
                'contain' => array(
                    'Election' => array(
                        'fields' => array('id', 'population_electors', 'population',
                            'quota', 'quota_women', 'bulletin_key'),
                        'Area' => array(
                            'fields' => array('Area.id', 'Area.name'),
                        ),
                    ),
                    'Tag' => array(
                        'fields' => array('Tag.id', 'Tag.name'),
                    ),
                ),
            ));
            $result['parents'] = $this->Election->getPath($result['candidate']['Election']['id']);
            Cache::write($cacheKey, $result, 'long');
        }
        return $result;
    }

}
