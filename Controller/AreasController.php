<?php

App::uses('AppController', 'Controller');

class AreasController extends AppController {

    public $name = 'Areas';
    public $paginate = array();
    public $helpers = array();

    public function beforeFilter() {
        parent::beforeFilter();
        if (isset($this->Auth)) {
            $this->Auth->allow('index');
        }
    }

    function index($parentId = '') {
        if (!empty($parentId)) {
            $parentId = $this->Area->field('id', array('id' => $parentId));
        } else {
            $parentId = $this->Area->field('id', array('parent_id IS NULL'));
        }

        $items = $this->Area->find('all', array(
            'conditions' => array(
                'Area.parent_id' => $parentId,
            )
        ));

        $parents = $this->Area->getPath($parentId, array('id', 'name'));
        $elections = $this->Area->AreasElection->find('all', array(
            'conditions' => array(
                'AreasElection.Area_id' => Set::extract($parents, '{n}.Area.id'),
            ),
        ));
        foreach ($elections AS $k => $election) {
            $elections[$k]['Election'] = $this->Area->Election->getPath($election['AreasElection']['Election_id'], array('id', 'name'));
            $elections[$k]['Candidate'] = $this->Area->Election->Candidate->find('all', array(
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
                'conditions' => array(
                    'CandidatesElection.Election_id' => $election['AreasElection']['Election_id'],
                ),
            ));
        }

        $this->set('items', $items);
        $this->set('url', array($parentId));
        $this->set('parentId', $parentId);
        $this->set('parents', $parents);
        $this->set('elections', $elections);
    }

    function admin_index($parentId = '', $foreignModel = null, $foreignId = '', $op = null) {
        $foreignKeys = array();


        $habtmKeys = array(
            'Election' => 'Election_id',
        );
        $foreignKeys = array_merge($habtmKeys, $foreignKeys);

        if (!empty($parentId)) {
            $parentId = $this->Area->field('id', array('id' => $parentId));
        }

        $scope = array(
            'Area.parent_id' => empty($parentId) ? NULL : $parentId,
        );
        if (array_key_exists($foreignModel, $foreignKeys) && !empty($foreignId)) {
            $scope['Area.' . $foreignKeys[$foreignModel]] = $foreignId;

            $joins = array(
                'Election' => array(
                    0 => array(
                        'table' => 'areas_elections',
                        'alias' => 'AreasElection',
                        'type' => 'inner',
                        'conditions' => array('AreasElection.Area_id = Area.id'),
                    ),
                    1 => array(
                        'table' => 'elections',
                        'alias' => 'Election',
                        'type' => 'inner',
                        'conditions' => array('AreasElection.Election_id = Election.id'),
                    ),
                ),
            );
            if (array_key_exists($foreignModel, $habtmKeys)) {
                unset($scope['Area.' . $foreignKeys[$foreignModel]]);
                if ($op != 'set') {
                    $scope[$joins[$foreignModel][0]['alias'] . '.' . $foreignKeys[$foreignModel]] = $foreignId;
                    $this->paginate['Area']['joins'] = $joins[$foreignModel];
                }
            }
        } else {
            $foreignModel = '';
        }
        $this->set('scope', $scope);
        $this->paginate['Area']['limit'] = 20;
        $items = $this->paginate($this->Area, $scope);

        if ($op == 'set' && !empty($joins[$foreignModel]) && !empty($foreignModel) && !empty($foreignId) && !empty($items)) {
            foreach ($items AS $key => $item) {
                $items[$key]['option'] = $this->Area->find('count', array(
                    'joins' => $joins[$foreignModel],
                    'conditions' => array(
                        'Area.id' => $item['Area']['id'],
                        $foreignModel . '.id' => $foreignId,
                    ),
                ));
                if ($items[$key]['option'] > 0) {
                    $items[$key]['option'] = 1;
                }
            }
            $this->set('op', $op);
        }

        $parents = $this->Area->getPath($parentId, array('id', 'name'));
        $elections = $this->Area->AreasElection->find('all', array(
            'conditions' => array(
                'AreasElection.Area_id' => Set::extract($parents, '{n}.Area.id'),
            ),
        ));
        foreach ($elections AS $k => $election) {
            $elections[$k]['Election'] = $this->Area->Election->getPath($election['AreasElection']['Election_id'], array('id', 'name'));
        }

        $this->set('items', $items);
        $this->set('foreignId', $foreignId);
        $this->set('foreignModel', $foreignModel);
        $this->set('url', array($parentId, $foreignModel, $foreignId));
        $this->set('parentId', $parentId);
        $this->set('parents', $parents);
        $this->set('elections', $elections);
    }

    function admin_view($id = null) {
        if (!$id || !$this->data = $this->Area->read(null, $id)) {
            $this->Session->setFlash(__('Please do following links in the page', true));
            $this->redirect(array('action' => 'index'));
        }
    }

    function admin_add($parentId = '') {
        if (!empty($this->data)) {
            $dataToSave = $this->data;
            if (!empty($parentId)) {
                $dataToSave['Area']['parent_id'] = $this->Area->field('id', array('id' => $parentId));
            }
            $this->Area->create();
            if ($this->Area->save($dataToSave)) {
                $this->Session->setFlash(__('The data has been saved', true));
                $this->redirect(array('action' => 'index', $parentId));
            } else {
                $this->Session->setFlash(__('Something was wrong during saving, please try again', true));
            }
        }
    }

    function admin_edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Please do following links in the page', true));
            $this->redirect($this->referer());
        }
        if (!empty($this->data)) {
            if ($this->Area->save($this->data)) {
                $this->Session->setFlash(__('The data has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Something was wrong during saving, please try again', true));
            }
        }
        $this->set('id', $id);
        $this->data = $this->Area->read(null, $id);
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Please do following links in the page', true));
        } else if ($this->Area->delete($id)) {
            $this->Session->setFlash(__('The data has been deleted', true));
        }
        $this->redirect(array('action' => 'index'));
    }

    function admin_habtmSet($foreignModel = null, $foreignId = 0, $id = 0, $switch = null) {
        $habtmKeys = array(
            'Election' => array(
                'associationForeignKey' => 'Election_id',
                'foreignKey' => 'Area_id',
                'alias' => 'AreasElection',
            ),
        );
        $foreignModel = array_key_exists($foreignModel, $habtmKeys) ? $foreignModel : null;
        $switch = in_array($switch, array('on', 'off')) ? $switch : null;
        if (empty($foreignModel) || $foreignId <= 0 || $id <= 0 || empty($switch)) {
            $this->set('habtmMessage', __('Wrong Parameters'));
        } else {
            $habtmModel = &$this->Area->$habtmKeys[$foreignModel]['alias'];
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
