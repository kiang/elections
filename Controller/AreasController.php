<?php

App::uses('AppController', 'Controller');

class AreasController extends AppController {

    public $name = 'Areas';
    public $paginate = array();
    public $helpers = array();

    public function beforeFilter() {
        parent::beforeFilter();
        if (isset($this->Auth)) {
            $this->Auth->allow('index', 'map', 'json', 'breadcrumb', 's');
        }
    }

    public function s() {
        $result = array();
        if (isset($this->request->query['term'])) {
            $keyword = Sanitize::clean($this->request->query['term']);
        }
        if (!empty($keyword)) {
            $keywords = explode(' ', $keyword);
            $countKeywords = 0;
            $conditions = array();
            foreach ($keywords AS $k => $keyword) {
                $keyword = trim($keyword);
                if (!empty($keyword) && ++$countKeywords < 4) {
                    $conditions[] = array(
                        "Area.name LIKE '%{$keyword}%'",
                    );
                }
            }

            $result = $this->Area->find('all', array(
                'fields' => array('Area.id', 'Area.name', 'Area.lft', 'Area.rght'),
                'conditions' => $conditions,
                'order' => array('Area.ivid' => 'ASC'),
                'limit' => 50,
            ));

            foreach ($result AS $k => $v) {
                $parents = $this->Area->getPath($v['Area']['id'], array('name'));
                $result[$k]['Area']['name'] = implode(' > ', Set::extract($parents, '{n}.Area.name'));
            }
        }
        $this->set('result', $result);
    }

    public function breadcrumb($parentId = '') {
        if (!empty($parentId)) {
            $cacheKey = "AreasBreadcrumb{$parentId}";
            $result = Cache::read($cacheKey, 'long');
            if (!$result) {
                $result = $this->Area->getPath($parentId, array('id', 'name'));
                Cache::write($cacheKey, $result, 'long');
            }
            $this->set('parents', $result);
        }
    }

    public function json($areaId = '') {
        $cacheKey = "AreasJson{$areaId}";
        $result = Cache::read($cacheKey, 'long');
        if (!$result) {
            if (!empty($areaId)) {
                $area = $this->Area->find('first', array(
                    'conditions' => array('id' => $areaId)
                ));
            }
            if (empty($area)) {
                $area = $this->Area->find('first', array(
                    'conditions' => array('Area.parent_id IS NULL'),
                    'order' => array('Area.lft' => 'DESC'),
                ));
            }
            if ($area['Area']['rght'] - $area['Area']['lft'] === 1) {
                $result = $this->Area->find('all', array(
                    'conditions' => array('parent_id' => $area['Area']['parent_id']),
                ));
            } else {
                $result = $this->Area->find('all', array(
                    'conditions' => array('parent_id' => $area['Area']['id']),
                ));
            }
            Cache::write($cacheKey, $result, 'long');
        }
        $this->set('areas', $result);
    }

    public function map($parentId = '') {
        if (empty($parentId)) {
            $parentId = $this->Area->field('id', array('Area.parent_id IS NULL'), array('Area.lft' => 'DESC'));
        }
        $cacheKey = "AreasMap{$parentId}";
        $result = Cache::read($cacheKey, 'long');
        if (!$result) {
            $result = $this->Area->getPath($parentId, array('id', 'name'));
            Cache::write($cacheKey, $result, 'long');
        }

        $this->set('areaId', $parentId);
        $this->set('parents', $result);

        $this->set('title_for_layout', implode(' > ', Set::extract('{n}.Area.name', $result)) . '行政區 @ ');
    }

    function index($parentId = '', $areaMethod = 'index') {
        if (!empty($parentId)) {
            $parentId = $this->Area->field('id', array('id' => $parentId));
        } else {
            $parentId = $this->Area->field('id', array('parent_id IS NULL'));
        }

        $cacheKey = "AreasIndex{$parentId}";
        $result = Cache::read($cacheKey, 'long');
        if (!$result) {
            $result = array(
                'items' => array(),
                'parents' => array(),
                'elections' => array(),
            );
            $result['items'] = $this->Area->find('all', array(
                'conditions' => array(
                    'Area.parent_id' => $parentId,
                )
            ));
            if (empty($result['items'])) {
                $result['items'] = $this->Area->find('all', array(
                    'conditions' => array(
                        'Area.parent_id' => $this->Area->field('parent_id', array('id' => $parentId)),
                    )
                ));
            }
            $result['parents'] = $this->Area->getPath($parentId, array('id', 'name'));
            $result['elections'] = $this->Area->AreasElection->find('all', array(
                'conditions' => array(
                    'AreasElection.Area_id' => Set::extract($result['parents'], '{n}.Area.id'),
                ),
                'contain' => array(
                    'Election' => array('fields' => array('population', 'population_electors',
                            'quota', 'quota_women', 'bulletin_key')),
                ),
            ));
            $electionStack = array();
            foreach ($result['elections'] AS $eKey => $election) {
                if (!isset($electionStack[$election['AreasElection']['Election_id']])) {
                    $electionStack[$election['AreasElection']['Election_id']] = true;
                } else {
                    unset($result['elections'][$eKey]);
                }
            }
            foreach ($result['elections'] AS $k => $election) {
                $result['elections'][$k]['AreasElection']['population'] = $election['Election']['population'];
                $result['elections'][$k]['AreasElection']['population_electors'] = $election['Election']['population_electors'];
                $result['elections'][$k]['AreasElection']['quota'] = $election['Election']['quota'];
                $result['elections'][$k]['AreasElection']['quota_women'] = $election['Election']['quota_women'];
                $result['elections'][$k]['AreasElection']['bulletin_key'] = $election['Election']['bulletin_key'];
                $result['elections'][$k]['Election'] = $this->Area->Election->getPath($election['AreasElection']['Election_id'], array('id', 'name', 'parent_id'));
                $result['elections'][$k]['Candidate'] = $this->Area->Election->Candidate->find('all', array(
                    'fields' => array('Candidate.id', 'Candidate.name', 'Candidate.no', 'Candidate.party', 'Candidate.stage', 'Candidate.image'),
                    'conditions' => array(
                        'Candidate.active_id IS NULL',
                        'Candidate.is_reviewed' => '1',
                        'Candidate.election_id' => $election['AreasElection']['Election_id'],
                    ),
                    'order' => array('Candidate.stage' => 'DESC', 'Candidate.no' => 'ASC'),
                ));
            }
            Cache::write($cacheKey, $result, 'long');
        }

        $desc_for_layout = '';
        $descElections = Set::extract('{n}.Election.1.Election.name', $result['elections']);
        if (!empty($descElections)) {
            $desc_for_layout .= implode(', ', $descElections) . '等各種候選人的資訊。';
        }
        $pageTitle = '行政區 @ ';
        if (!empty($result['parents'])) {
            $pageTitle = implode(' > ', Set::extract('{n}.Area.name', $result['parents'])) . $pageTitle;
        }
        $this->set('title_for_layout', $pageTitle);
        $this->set('desc_for_layout', $desc_for_layout);

        $this->set('items', $result['items']);
        $this->set('parents', $result['parents']);
        $this->set('elections', $result['elections']);

        $this->set('url', array($parentId));
        $this->set('parentId', $parentId);
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
        if (empty($parentId)) {
            $this->paginate['Area']['order'] = array('Area.name' => 'DESC');
        } else {
            $this->paginate['Area']['order'] = array('Area.ivid' => 'ASC');
        }
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
