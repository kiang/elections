<?php

App::uses('AppController', 'Controller');
App::uses('Sanitize', 'Utility');

class CandidatesController extends ApiAppController {

    var $uses = array('Candidate');

    public function s($term = '') {
        if (!empty($term)) {
            $keyword = Sanitize::clean($term);
            $this->jsonData = $this->Candidate->find('all', array(
                'fields' => array('Candidate.id', 'Candidate.name', 'Candidate.no', 'Candidate.election_id'),
                'conditions' => array(
                    'Candidate.active_id IS NULL',
                    'Candidate.name LIKE' => "%{$term}%",
                ),
                'limit' => 20,
            ));
            foreach ($this->jsonData AS $k => $v) {
                $this->jsonData[$k]['Election'] = array(
                    'id' => $v['Candidate']['election_id'],
                    'name' => '',
                );
                $parents = $this->Candidate->Election->getPath($v['Candidate']['election_id'], array('name'));
                $this->jsonData[$k]['Election']['name'] = implode(' > ', Set::extract($parents, '{n}.Election.name'));
            }
        }
    }

    public function view($id = '') {
        if (!empty($id)) {
            $this->jsonData = $this->Candidate->find('first', array(
                'conditions' => array(
                    'Candidate.id' => $id,
                    'Candidate.active_id IS NULL',
                ),
                'fields' => array('Candidate.*'),
            ));
            if (!empty($this->jsonData['Candidate']['image'])) {
                $this->jsonData['Candidate']['image'] = Router::url('/img/' . $this->jsonData['Candidate']['image'], true);
            }
        }
    }

}
