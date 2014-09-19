<?php

App::uses('AppController', 'Controller');
App::uses('Sanitize', 'Utility');

class ElectionsController extends ApiAppController {

    var $uses = array('Election');

    public function s($term = '') {
        if (!empty($term)) {
            $keyword = Sanitize::clean($term);
        }
        if (!empty($keyword)) {
            $this->jsonData = $this->Election->find('all', array(
                'fields' => array('Election.id', 'Election.lft', 'Election.rght'),
                'conditions' => array(
                    'Election.parent_id IS NOT NULL',
                    'Election.name LIKE' => "%{$keyword}%",
                ),
                'limit' => 20,
            ));
            foreach ($this->jsonData AS $k => $v) {
                $parents = $this->Election->getPath($v['Election']['id'], array('name'));
                $this->jsonData[$k]['Election']['name'] = implode(' > ', Set::extract($parents, '{n}.Election.name'));
            }
        }
    }

    function index($parentId = '') {
        if (!empty($parentId)) {
            $parentId = $this->Election->field('id', array('id' => $parentId));
        }
        if (empty($parentId)) {
            $parentId = $this->Election->field('id', array('parent_id IS NULL'));
        }
        $this->jsonData = $this->Election->find('all', array(
            'fields' => array('Election.id', 'Election.name',
                'Election.lft', 'Election.rght', 'Election.population',
                'Election.population_electors',),
            'conditions' => array(
                'Election.parent_id' => empty($parentId) ? NULL : $parentId,
            ),
            'contain' => array(
                'Area' => array(
                    'fields' => array('Area.id', 'Area.name', 'Area.ivid', 'Area.code',
                        'Area.lft', 'Area.rght', 'Area.population', 'Area.population_electors'),
                )
            ),
        ));

        foreach ($this->jsonData AS $k => $v) {
            foreach ($v['Area'] AS $ek => $e) {
                $eParents = $this->Election->Area->getPath($e['id'], array('name'));
                $this->jsonData[$k]['Area'][$ek]['name'] = implode(' > ', Set::extract($eParents, '{n}.Area.name'));
                unset($this->jsonData[$k]['Area'][$ek]['AreasElection']);
            }
        }
    }

    public function candidates($electionId = '') {
        if (!empty($electionId)) {
            $electionId = $this->Election->field('id', array('id' => $electionId));
        }
        if (!empty($electionId)) {
            $this->jsonData = $this->Election->Candidate->find('all', array(
                'conditions' => array(
                    'CandidatesElection.Election_id' => $electionId,
                    'Candidate.active_id IS NULL',
                ),
                'fields' => array('Candidate.*', 'CandidatesElection.platform'),
                'joins' => array(
                    array(
                        'table' => 'candidates_elections',
                        'alias' => 'CandidatesElection',
                        'type' => 'inner',
                        'conditions' => array(
                            'CandidatesElection.Candidate_id = Candidate.id',
                        ),
                    ),
                ),
            ));
            foreach ($this->jsonData AS $k => $v) {
                $this->jsonData[$k]['Candidate']['platform'] = $v['CandidatesElection']['platform'];
                if (!empty($this->jsonData[$k]['Candidate']['image'])) {
                    $this->jsonData[$k]['Candidate']['image'] = Router::url('/img/' . $this->jsonData[$k]['Candidate']['image'], true);
                }
                unset($this->jsonData[$k]['CandidatesElection']);
            }
        }
    }

}
