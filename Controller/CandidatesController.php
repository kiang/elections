<?php

App::uses('AppController', 'Controller');

class CandidatesController extends AppController {

    public $name = 'Candidates';
    public $paginate = array();
    public $helpers = array();

    function admin_index($electionId = '') {
        if (!empty($electionId)) {
            $scope = array();
            $this->paginate['Candidate']['joins'] = array(
                array(
                    'table' => 'candidates_elections',
                    'alias' => 'CandidatesElection',
                    'type' => 'inner',
                    'conditions' => array(
                        'CandidatesElection.Candidate_id = Candidate.id',
                        'CandidatesElection.Election_id' => $electionId,
                    ),
                ),
            );
            $this->paginate['Candidate']['limit'] = 20;
            $items = $this->paginate($this->Candidate);

            $this->set('items', $items);
            $this->set('electionId', $electionId);
            $this->set('parents', $this->Candidate->Election->getPath($electionId));
        } else {
            $this->redirect(array('controller' => 'elections'));
        }
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
                unset($dataToSave['Candidate']['image']);
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

        $belongsToModels = array(
            'listElection' => array(
                'label' => 'Elections',
                'modelName' => 'Election',
                'foreignKey' => 'Election_id',
            ),
        );

        foreach ($belongsToModels AS $key => $model) {
            $this->set($key, $this->Candidate->$model['modelName']->find('list'));
        }
        $this->set('belongsToModels', $belongsToModels);
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
