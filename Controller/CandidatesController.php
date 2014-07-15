<?php

App::uses('AppController', 'Controller');
App::uses('Sanitize', 'Utility');

class CandidatesController extends AppController {

    public $name = 'Candidates';
    public $paginate = array();
    public $helpers = array();

    public function beforeFilter() {
        parent::beforeFilter();
        if (isset($this->Auth)) {
            $this->Auth->allow('index', 'add', 'view');
        }
    }

    function index($electionId = '') {
        $scope = array();
        if (isset($this->data['Candidate']['keyword'])) {
            $keyword = Sanitize::clean($this->data['Candidate']['keyword']);
            $this->Session->write('Candidates.index.keyword', $keyword);
        } else {
            $keyword = $this->Session->read('Candidates.index.keyword');
        }
        if (!empty($keyword)) {
            $scope['Candidate.name LIKE'] = "%{$keyword}%";
        }

        if (!empty($electionId)) {
            $scope['CandidatesElection.Election_id'] = $electionId;
        }

        $this->paginate['Candidate']['joins'] = array(
            array(
                'table' => 'candidates_elections',
                'alias' => 'CandidatesElection',
                'type' => 'inner',
                'conditions' => array(
                    'CandidatesElection.Candidate_id = Candidate.id',
                ),
            ),
        );
        $this->paginate['Candidate']['order'] = array('Candidate.modified' => 'desc');
        $this->paginate['Candidate']['limit'] = 20;
        $this->paginate['Candidate']['fields'] = array('Candidate.*', 'CandidatesElection.Election_id');
        $items = $this->paginate($this->Candidate, $scope);
        foreach ($items AS $k => $v) {
            $items[$k]['Election'] = $this->Candidate->Election->getPath($v['CandidatesElection']['Election_id']);
        }
        $parents = $this->Candidate->Election->getPath($electionId);
        $c = array();
        if (!empty($parents)) {
            $c = Set::extract('{n}.Election.name', $parents);
        }

        $this->set('title_for_layout', implode(' > ', $c) . '候選人 @ ');
        $this->set('items', $items);
        $this->set('electionId', $electionId);
        $this->set('url', array($electionId));
        $this->set('keyword', $keyword);
        $this->set('parents', $parents);
    }

    function add($electionId = '') {
        if (!empty($electionId)) {
            if (!empty($this->data)) {
                $dataToSave = Sanitize::clean($this->data);
                $this->Candidate->create();
                if ($this->Candidate->save($dataToSave)) {
                    $dataToSave['CandidatesElection']['Election_id'] = $electionId;
                    $dataToSave['CandidatesElection']['Candidate_id'] = $this->Candidate->getInsertID();
                    $this->Candidate->CandidatesElection->create();
                    $this->Candidate->CandidatesElection->save($dataToSave);
                    $areaId = $this->Candidate->Election->AreasElection->field('Area_id', array('Election_id' => $electionId));
                    $this->Session->setFlash(__('The data has been saved', true));
                    $this->redirect(array('controller' => 'areas', 'action' => 'index', $areaId));
                } else {
                    $this->Session->setFlash(__('Something was wrong during saving, please try again', true));
                }
            }
            $parents = $this->Candidate->Election->getPath($electionId);
            $c = array();
            foreach ($parents AS $parent) {
                $c[] = $parent['Election']['name'];
            }
            $c[] = '新增候選人';
            $this->set('title_for_layout', implode(' > ', $c) . ' @ ');
            $this->set('electionId', $electionId);
            $this->set('referer', $this->request->referer());
            $this->set('parents', $parents);
        } else {
            $this->redirect(array('controller' => 'areas'));
        }
    }

    function view($id = null) {
        $this->data = $this->Candidate->find('first', array(
            'conditions' => array('Candidate.id' => $id),
            'contain' => array('Election'),
        ));
        if (!empty($this->data)) {
            $parents = $this->Candidate->Election->getPath($this->data['Election'][0]['id']);
            $desc_for_layout = '';
            $descElections = Set::extract('{n}.Election.name', $parents);
            if (!empty($descElections)) {
                $desc_for_layout .= $this->data['Candidate']['name'] . '在' . implode(' > ', $descElections) . '的參選資訊。';
            }
            $descElections[] = $this->data['Candidate']['name'];
            $this->set('referer', $this->request->referer());
            $this->set('desc_for_layout', $desc_for_layout);
            $this->set('title_for_layout', implode(' > ', $descElections) . '候選人 @ ');
            $this->set('parents', $parents);
        } else {
            $this->Session->setFlash(__('Please do following links in the page', true));
            $this->redirect(array('action' => 'index'));
        }
    }

    function admin_index($electionId = '') {
        $scope = array();
        if (!empty($electionId)) {
            $scope['CandidatesElection.Election_id'] = $electionId;
        }
        $this->paginate['Candidate']['joins'] = array(
            array(
                'table' => 'candidates_elections',
                'alias' => 'CandidatesElection',
                'type' => 'inner',
                'conditions' => array(
                    'CandidatesElection.Candidate_id = Candidate.id',
                ),
            ),
        );
        $this->paginate['Candidate']['limit'] = 20;
        $items = $this->paginate($this->Candidate, $scope);

        $this->set('items', $items);
        $this->set('electionId', $electionId);
        $this->set('url', array($electionId));
        $this->set('parents', $this->Candidate->Election->getPath($electionId));
    }

    function admin_view($id = null) {
        if (!$id || !$this->data = $this->Candidate->read(null, $id)) {
            $this->Session->setFlash(__('Please do following links in the page', true));
            $this->redirect(array('action' => 'index'));
        }
    }

    function admin_add($electionId = '') {
        if (!empty($electionId)) {
            if (!empty($this->data)) {
                $dataToSave = $this->data;
                $this->Candidate->create();
                if ($this->Candidate->save($dataToSave)) {
                    $dataToSave['CandidatesElection']['Election_id'] = $electionId;
                    $dataToSave['CandidatesElection']['Candidate_id'] = $this->Candidate->getInsertID();
                    $this->Candidate->CandidatesElection->create();
                    $this->Candidate->CandidatesElection->save($dataToSave);
                    $this->Session->setFlash(__('The data has been saved', true));
                    $this->redirect(array('action' => 'index', $electionId));
                } else {
                    $this->Session->setFlash(__('Something was wrong during saving, please try again', true));
                }
            }
            $this->set('electionId', $electionId);
        } else {
            $this->redirect(array('controller' => 'elections'));
        }
    }

    function admin_edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Please do following links in the page', true));
            $this->redirect($this->referer());
        }
        if (!empty($this->data)) {
            if ($this->Candidate->save($this->data)) {
                $this->Session->setFlash(__('The data has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Something was wrong during saving, please try again', true));
            }
        }
        $this->set('id', $id);
        $this->data = $this->Candidate->read(null, $id);
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Please do following links in the page', true));
        } else if ($this->Candidate->delete($id)) {
            $this->Session->setFlash(__('The data has been deleted', true));
        }
        $this->redirect(array('action' => 'index'));
    }

}
