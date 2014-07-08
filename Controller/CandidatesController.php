<?php

App::uses('AppController', 'Controller');

class CandidatesController extends AppController {

    public $name = 'Candidates';
    public $paginate = array();
    public $helpers = array();


    function index($foreignModel = null, $foreignId = 0) {
        $foreignId = intval($foreignId);
        $foreignKeys = array();

        $foreignKeys = array(

            'Election' => 'Election_id',

            );


        $scope = array();
        if(array_key_exists($foreignModel, $foreignKeys) && $foreignId > 0) {
            $scope['Candidate.' . $foreignKeys[$foreignModel]] = $foreignId;

        } else {
            $foreignModel = '';
        }
        $this->set('scope', $scope);
        $this->paginate['Candidate']['limit'] = 20;
        $items = $this->paginate($this->Candidate, $scope);
        $this->set('items', $items);
        $this->set('foreignId', $foreignId);
        $this->set('foreignModel', $foreignModel);
    }


    function view($id = null) {
        if (!$id || !$this->data = $this->Candidate->read(null, $id)) {
            $this->Session->setFlash(__('Please do following links in the page', true));
            $this->redirect(array('action'=>'index'));
        }
    }





    function admin_index($foreignModel = null, $foreignId = 0, $op = null) {
        $foreignId = intval($foreignId);
        $foreignKeys = array();

        $foreignKeys = array(

            'Election' => 'Election_id',

        );


        $scope = array();
        if(array_key_exists($foreignModel, $foreignKeys) && $foreignId > 0) {
            $scope['Candidate.' . $foreignKeys[$foreignModel]] = $foreignId;

        } else {
            $foreignModel = '';
        }
        $this->set('scope', $scope);
        $this->paginate['Candidate']['limit'] = 20;
        $items = $this->paginate($this->Candidate, $scope);

        $this->set('items', $items);
        $this->set('foreignId', $foreignId);
        $this->set('foreignModel', $foreignModel);
    }


    function admin_view($id = null) {
        if (!$id || !$this->data = $this->Candidate->read(null, $id)) {
            $this->Session->setFlash(__('Please do following links in the page', true));
            $this->redirect(array('action'=>'index'));
        }
    }


    function admin_add($foreignModel = null, $foreignId = 0) {
        $foreignId = intval($foreignId);
        $foreignKeys = array(

            'Election' => 'Election_id',

        );
        if(array_key_exists($foreignModel, $foreignKeys) && $foreignId > 0) {
            if (!empty($this->data)) {
                $this->data['Candidate'][$foreignKeys[$foreignModel]] = $foreignId;
            }
        } else {
            $foreignModel = '';
        }
        if (!empty($this->data)) {
            $this->Candidate->create();
            if ($this->Candidate->save($this->data)) {
                $this->Session->setFlash(__('The data has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('Something was wrong during saving, please try again', true));
            }
        }
        $this->set('foreignId', $foreignId);
        $this->set('foreignModel', $foreignModel);
        
        $belongsToModels = array(
            'listElection' => array(
                'label' => 'Elections',
                'modelName' => 'Election',
                'foreignKey' => 'Election_id',
            ),
        );

        foreach($belongsToModels AS $key => $model) {
            if($foreignModel == $model['modelName']) {
                unset($belongsToModels[$key]);
                continue;
            }
            $this->set($key, $this->Candidate->$model['modelName']->find('list'));
        }
        $this->set('belongsToModels', $belongsToModels);
    }


    function admin_edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Please do following links in the page', true));
            $this->redirect($this->referer());
        }
        if (!empty($this->data)) {
            if ($this->Candidate->save($this->data)) {
                $this->Session->setFlash(__('The data has been saved', true));
                $this->redirect(array('action'=>'index'));
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

        foreach($belongsToModels AS $key => $model) {
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
        $this->redirect(array('action'=>'index'));
    }


}