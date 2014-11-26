<?php

App::uses('AppController', 'Controller');

class AreasController extends AppController {

    public $name = 'Areas';
    public $paginate = array();
    public $helpers = array();

    public function beforeFilter() {
        parent::beforeFilter();
        if (isset($this->Auth)) {
            $this->Auth->allow('index', 'map', 'json', 'breadcrumb');
        }
    }

    public function breadcrumb($parentId = '') {
        if (!empty($parentId)) {
            $parents = $this->Area->getPath($parentId, array('id', 'name'));
            $this->set('parents', $parents);
        }
    }

    public function json($areaId = '') {
        if (!empty($areaId)) {
            $area = $this->Area->find('first', array(
                'conditions' => array('id' => $areaId)
            ));
        }
        if (empty($area)) {
            $area = $this->Area->find('first', array(
                'conditions' => array('name' => '2014')
            ));
        }
        if ($area['Area']['rght'] - $area['Area']['lft'] === 1) {
            $areas = $this->Area->find('all', array(
                'conditions' => array('parent_id' => $area['Area']['parent_id']),
            ));
        } else {
            $areas = $this->Area->find('all', array(
                'conditions' => array('parent_id' => $area['Area']['id']),
            ));
        }
        $this->set('areas', $areas);
    }

    public function map($parentId = '') {
        if (empty($parentId)) {
            $parentId = $this->Area->field('id', array('name' => '2014'));
        }
        $parents = $this->Area->getPath($parentId, array('id', 'name'));
        $this->set('areaId', $parentId);
        $this->set('parents', $parents);

        $this->set('title_for_layout', implode(' > ', Set::extract('{n}.Area.name', $parents)) . '行政區 @ ');
    }

    function index($parentId = '', $areaMethod = 'index') {
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
        if (empty($items)) {
            $items = $this->Area->find('all', array(
                'conditions' => array(
                    'Area.parent_id' => $this->Area->field('parent_id', array('id' => $parentId)),
                )
            ));
        }

        $parents = $this->Area->getPath($parentId, array('id', 'name'));
        $elections = $this->Area->AreasElection->find('all', array(
            'conditions' => array(
                'AreasElection.Area_id' => Set::extract($parents, '{n}.Area.id'),
            ),
            'contain' => array(
                'Election' => array('fields' => array('population', 'population_electors',
                    'quota', 'quota_women', 'bulletin_key')),
            ),
        ));
        $electionStack = array();
        foreach ($elections AS $eKey => $election) {
            if (!isset($electionStack[$election['AreasElection']['Election_id']])) {
                $electionStack[$election['AreasElection']['Election_id']] = true;
            } else {
                unset($elections[$eKey]);
            }
        }
        foreach ($elections AS $k => $election) {
            $elections[$k]['AreasElection']['population'] = $election['Election']['population'];
            $elections[$k]['AreasElection']['population_electors'] = $election['Election']['population_electors'];
            $elections[$k]['AreasElection']['quota'] = $election['Election']['quota'];
            $elections[$k]['AreasElection']['quota_women'] = $election['Election']['quota_women'];
            $elections[$k]['AreasElection']['bulletin_key'] = $election['Election']['bulletin_key'];
            $elections[$k]['Election'] = $this->Area->Election->getPath($election['AreasElection']['Election_id'], array('id', 'name', 'parent_id'));
            $elections[$k]['Candidate'] = $this->Area->Election->Candidate->find('all', array(
                'fields' => array('Candidate.id', 'Candidate.name', 'Candidate.no', 'Candidate.stage', 'Candidate.image'),
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
                    'Candidate.active_id IS NULL',
                    'CandidatesElection.Election_id' => $election['AreasElection']['Election_id'],
                ),
                'order' => array('Candidate.stage' => 'DESC', 'Candidate.no' => 'ASC'),
            ));
        }

        $desc_for_layout = '';
        $descElections = Set::extract('{n}.Election.1.Election.name', $elections);
        if (!empty($descElections)) {
            $desc_for_layout .= implode(', ', $descElections) . '等各種候選人的資訊。';
        }
        $pageTitle = '行政區 @ ';
        if(!empty($parents)) {
            $pageTitle = implode(' > ', Set::extract('{n}.Area.name', $parents)) . $pageTitle;
        }
        $this->set('title_for_layout', $pageTitle);
        $this->set('desc_for_layout', $desc_for_layout);

        $this->set('items', $items);
        $this->set('url', array($parentId));
        $this->set('parentId', $parentId);
        $this->set('parents', $parents);
        $this->set('elections', $elections);
        $this->set('areaMethod', $areaMethod);
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
            $this->Session->setFlash('請依照網頁指示操作');
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
            if ($this->Area->save($this->data)) {
                $this->Session->setFlash('資料已經儲存');
                $this->redirect(array('action' => 'index', $this->Area->field('parent_id')));
            } else {
                $this->Session->setFlash('資料儲存時發生錯誤，請重試');
            }
        }
        $this->set('id', $id);
        $this->data = $this->Area->read(null, $id);
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('請依照網頁指示操作');
        } else if ($this->Area->delete($id)) {
            $this->Session->setFlash('資料已經刪除');
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
