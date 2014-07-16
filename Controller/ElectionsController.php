<?php

App::uses('AppController', 'Controller');

class ElectionsController extends AppController {

    public $name = 'Elections';
    public $paginate = array();
    public $helpers = array();

    public function beforeFilter() {
        parent::beforeFilter();
        if (isset($this->Auth)) {
            $this->Auth->allow('index');
        }
    }

    function index($parentId = '', $foreignModel = null, $foreignId = '', $op = null) {
        if (!empty($parentId)) {
            $parentId = $this->Election->field('id', array('id' => $parentId));
        }
        if (empty($parentId)) {
            $parentId = $this->Election->field('id', array('parent_id IS NULL'));
        }
        $foreignKeys = array();

        $habtmKeys = array(
            'Area' => 'Area_id',
            'Candidate' => 'Candidate_id',
        );
        $foreignKeys = array_merge($habtmKeys, $foreignKeys);

        $scope = array(
            'Election.parent_id' => empty($parentId) ? NULL : $parentId,
        );
        if (array_key_exists($foreignModel, $foreignKeys) && !empty($foreignId)) {
            $scope['Election.' . $foreignKeys[$foreignModel]] = $foreignId;

            $joins = array(
                'Area' => array(
                    0 => array(
                        'table' => 'areas_elections',
                        'alias' => 'AreasElection',
                        'type' => 'inner',
                        'conditions' => array('AreasElection.Election_id = Election.id'),
                    ),
                    1 => array(
                        'table' => 'areas',
                        'alias' => 'Area',
                        'type' => 'inner',
                        'conditions' => array('AreasElection.Area_id = Area.id'),
                    ),
                ),
                'Candidate' => array(
                    0 => array(
                        'table' => 'candidates_elections',
                        'alias' => 'CandidatesElection',
                        'type' => 'inner',
                        'conditions' => array('AreasElection.Election_id = Election.id'),
                    ),
                    1 => array(
                        'table' => 'candidates',
                        'alias' => 'Candidate',
                        'type' => 'inner',
                        'conditions' => array('AreasElection.Candidate_id = Candidate.id'),
                    ),
                ),
            );
            if (array_key_exists($foreignModel, $habtmKeys)) {
                unset($scope['Election.' . $foreignKeys[$foreignModel]]);
                if ($op != 'set') {
                    $scope[$joins[$foreignModel][0]['alias'] . '.' . $foreignKeys[$foreignModel]] = $foreignId;
                    $this->paginate['Election']['joins'] = $joins[$foreignModel];
                }
            }
        } else {
            $foreignModel = '';
        }
        $this->set('scope', $scope);
        $this->paginate['Election']['limit'] = 20;
        $items = $this->paginate($this->Election, $scope);

        if ($op == 'set' && !empty($joins[$foreignModel]) && !empty($foreignModel) && !empty($foreignId) && !empty($items)) {
            foreach ($items AS $key => $item) {
                $items[$key]['option'] = $this->Election->find('count', array(
                    'joins' => $joins[$foreignModel],
                    'conditions' => array(
                        'Election.id' => $item['Election']['id'],
                        $foreignModel . '.id' => $foreignId,
                    ),
                ));
                if ($items[$key]['option'] > 0) {
                    $items[$key]['option'] = 1;
                }
            }
            $this->set('op', $op);
        }
        $parents = $this->Election->getPath($parentId);
        $c = Set::extract('{n}.Election.name', $parents);

        $this->set('title_for_layout', implode(' > ', $c) . '選舉區 @ ');
        $this->set('items', $items);
        $this->set('url', array($parentId, $foreignModel, $foreignId, $op));
        $this->set('foreignId', $foreignId);
        $this->set('foreignModel', $foreignModel);
        $this->set('parentId', $parentId);
        $this->set('parents', $parents);
    }

    function admin_index($parentId = '', $foreignModel = null, $foreignId = '', $op = null) {
        $foreignKeys = array();

        $habtmKeys = array(
            'Area' => 'Area_id',
            'Candidate' => 'Candidate_id',
        );
        $foreignKeys = array_merge($habtmKeys, $foreignKeys);

        if (!empty($parentId)) {
            $parentId = $this->Election->field('id', array('id' => $parentId));
        }

        $scope = array(
            'Election.parent_id' => empty($parentId) ? NULL : $parentId,
        );
        if (array_key_exists($foreignModel, $foreignKeys) && !empty($foreignId)) {
            $scope['Election.' . $foreignKeys[$foreignModel]] = $foreignId;

            $joins = array(
                'Area' => array(
                    0 => array(
                        'table' => 'areas_elections',
                        'alias' => 'AreasElection',
                        'type' => 'inner',
                        'conditions' => array('AreasElection.Election_id = Election.id'),
                    ),
                    1 => array(
                        'table' => 'areas',
                        'alias' => 'Area',
                        'type' => 'inner',
                        'conditions' => array('AreasElection.Area_id = Area.id'),
                    ),
                ),
                'Candidate' => array(
                    0 => array(
                        'table' => 'candidates_elections',
                        'alias' => 'CandidatesElection',
                        'type' => 'inner',
                        'conditions' => array('AreasElection.Election_id = Election.id'),
                    ),
                    1 => array(
                        'table' => 'candidates',
                        'alias' => 'Candidate',
                        'type' => 'inner',
                        'conditions' => array('AreasElection.Candidate_id = Candidate.id'),
                    ),
                ),
            );
            if (array_key_exists($foreignModel, $habtmKeys)) {
                unset($scope['Election.' . $foreignKeys[$foreignModel]]);
                if ($op != 'set') {
                    $scope[$joins[$foreignModel][0]['alias'] . '.' . $foreignKeys[$foreignModel]] = $foreignId;
                    $this->paginate['Election']['joins'] = $joins[$foreignModel];
                }
            }
        } else {
            $foreignModel = '';
        }
        $this->set('scope', $scope);
        $this->paginate['Election']['limit'] = 20;
        $items = $this->paginate($this->Election, $scope);

        if ($op == 'set' && !empty($joins[$foreignModel]) && !empty($foreignModel) && !empty($foreignId) && !empty($items)) {
            foreach ($items AS $key => $item) {
                $items[$key]['option'] = $this->Election->find('count', array(
                    'joins' => $joins[$foreignModel],
                    'conditions' => array(
                        'Election.id' => $item['Election']['id'],
                        $foreignModel . '.id' => $foreignId,
                    ),
                ));
                if ($items[$key]['option'] > 0) {
                    $items[$key]['option'] = 1;
                }
            }
            $this->set('op', $op);
        }

        $this->set('items', $items);
        $this->set('foreignId', $foreignId);
        $this->set('foreignModel', $foreignModel);
        $this->set('parentId', $parentId);
        $this->set('parents', $this->Election->getPath($parentId));
    }

    function admin_view($id = null) {
        if (!$id || !$this->data = $this->Election->read(null, $id)) {
            $this->Session->setFlash('請依照網頁指示操作');
            $this->redirect(array('action' => 'index'));
        }
    }

    function admin_add($parentId = '') {
        if (!empty($this->data)) {
            $dataToSave = $this->data;
            if (!empty($parentId)) {
                $dataToSave['Election']['parent_id'] = $this->Election->field('id', array('id' => $parentId));
            }

            $this->Election->create();
            if ($this->Election->save($dataToSave)) {
                $this->Session->setFlash('資料已經儲存');
                $this->redirect(array('action' => 'index', $parentId));
            } else {
                $this->Session->setFlash('資料儲存時發生錯誤，請重試');
            }
        }
    }

    function admin_edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('請依照網頁指示操作');
            $this->redirect($this->referer());
        }
        if (!empty($this->data)) {
            if ($this->Election->save($this->data)) {
                $this->Session->setFlash('資料已經儲存');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('資料儲存時發生錯誤，請重試');
            }
        }
        $this->set('id', $id);
        $this->data = $this->Election->read(null, $id);
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('請依照網頁指示操作');
        } else if ($this->Election->delete($id)) {
            $this->Session->setFlash('資料已經刪除');
        }
        $this->redirect(array('action' => 'index'));
    }

    function admin_habtmSet($foreignModel = null, $foreignId = 0, $id = 0, $switch = null) {
        $habtmKeys = array(
            'Area' => array(
                'associationForeignKey' => 'Area_id',
                'foreignKey' => 'Election_id',
                'alias' => 'AreasElection',
            ),
        );
        $foreignModel = array_key_exists($foreignModel, $habtmKeys) ? $foreignModel : null;
        $switch = in_array($switch, array('on', 'off')) ? $switch : null;
        if (empty($foreignModel) || $foreignId <= 0 || $id <= 0 || empty($switch)) {
            $this->set('habtmMessage', __('Wrong Parameters'));
        } else {
            $habtmModel = &$this->Election->$habtmKeys[$foreignModel]['alias'];
            $conditions = array(
                $habtmKeys[$foreignModel]['associationForeignKey'] => $foreignId,
                $habtmKeys[$foreignModel]['foreignKey'] => $id,
            );
            $status = ($habtmModel->find('count', array(
                        'conditions' => $conditions,
                    ))) ? 'on' : 'off';
            if ($status == $switch) {
                $this->set('habtmMessage', __('Duplicated operactions', true));
            } else if ($switch == 'on') {
                $habtmModel->create();
                if ($habtmModel->save(array($habtmKeys[$foreignModel]['alias'] => $conditions))) {
                    $this->set('habtmMessage', __('Updated', true));
                } else {
                    $this->set('habtmMessage', __('Update failed', true));
                }
            } else {
                if ($habtmModel->deleteAll($conditions)) {
                    $this->set('habtmMessage', __('Updated', true));
                } else {
                    $this->set('habtmMessage', __('Update failed', true));
                }
            }
        }
    }

}
