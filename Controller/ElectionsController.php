<?php

App::uses('AppController', 'Controller');
App::uses('Sanitize', 'Utility');

class ElectionsController extends AppController {

    public $name = 'Elections';
    public $paginate = array();
    public $helpers = array();

    public function beforeFilter() {
        parent::beforeFilter();
        if (isset($this->Auth)) {
            $this->Auth->allow('index', 's');
        }
    }

    public function s() {
        $result = array();
        if (isset($this->request->query['term'])) {
            $keyword = Sanitize::clean($this->request->query['term']);
        }
        if (!empty($keyword)) {
            $cacheKey = "ElectionsS{$keyword}";
            $result = Cache::read($cacheKey, 'long');
            if (!$result) {
                $keywords = explode(' ', $keyword);
                $countKeywords = 0;
                $conditions = array(
                    'Election.parent_id IS NOT NULL',
                );
                foreach ($keywords AS $k => $keyword) {
                    $keyword = trim($keyword);
                    if (!empty($keyword) && ++$countKeywords < 4) {
                        $conditions[] = "Election.keywords LIKE '%{$keyword}%'";
                    }
                }

                $result = $this->Election->find('all', array(
                    'fields' => array('Election.id', 'Election.name', 'Election.lft', 'Election.rght'),
                    'conditions' => $conditions,
                    'limit' => 50,
                ));

                foreach ($result AS $k => $v) {
                    $parents = $this->Election->getPath($v['Election']['id'], array('name'));
                    $result[$k]['Election']['name'] = implode(' > ', Set::extract($parents, '{n}.Election.name'));
                }

                Cache::write($cacheKey, $result, 'long');
            }
        }
        $this->set('result', $result);
    }

    function index($parentId = '') {
        if (!empty($parentId)) {
            $parentId = $this->Election->field('id', array('id' => $parentId));
            $nameOrder = 'ASC';
        } else {
            $nameOrder = 'DESC';
        }
        $cacheKey = "ElectionsIndex{$parentId}";
        $result = Cache::read($cacheKey, 'long');
        if (!$result) {
            $result = array(
                'items' => array(),
                'parents' => array(),
            );
            $result['items'] = $this->Election->find('all', array(
                'conditions' => array(
                    'Election.parent_id' => empty($parentId) ? NULL : $parentId,
                ),
                'order' => array('Election.name' => $nameOrder),
            ));

            $result['parents'] = $this->Election->getPath($parentId);
            Cache::write($cacheKey, $result, 'long');
        }
        if (!empty($result['parents'])) {
            $c = Set::extract('{n}.Election.name', $result['parents']);
        } else {
            $c = array();
        }

        $this->set('title_for_layout', implode(' > ', $c) . '選舉區 @ ');
        $this->set('items', $result['items']);
        $this->set('url', array($parentId));
        $this->set('parentId', $parentId);
        $this->set('parents', $result['parents']);
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
        $this->paginate['Election']['contain'] = array(
            'AreasElection' => array('fields' => array('id')),
        );
        if (empty($parentId)) {
            $this->paginate['Election']['order'] = array('Election.name' => 'DESC');
        } else {
            $this->paginate['Election']['order'] = array('Election.name' => 'ASC');
        }

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
        $this->set('url', array($parentId, $foreignModel, $foreignId, $op));
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
                $this->redirect(array('action' => 'index', $this->Election->field('parent_id')));
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

    public function admin_links($electionId = '') {
        if (!empty($electionId)) {
            $election = $this->Election->find('first', array(
                'conditions' => array(
                    'Election.id' => $electionId,
                ),
                'contain' => array('Area'),
            ));
        }
        if (!empty($election)) {
            $this->set('election', $election);
            $this->set('parents', $this->Election->getPath($electionId));
        } else {
            $this->Session->setFlash(__('Please select a election first!', true));
            $this->redirect($this->referer());
        }
    }

    public function admin_link_add($electionId = '', $areaId = '') {
        if (!empty($areaId) && !empty($electionId)) {
            $linkId = $this->Election->AreasElection->field('id', array(
                'Area_id' => $areaId,
                'Election_id' => $electionId,
            ));
            if (empty($linkId)) {
                $this->Election->AreasElection->create();
                $this->Election->AreasElection->save(array('AreasElection' => array(
                        'Area_id' => $areaId,
                        'Election_id' => $electionId,
                )));
            }
        }
        echo 'ok';
        exit();
    }

    public function admin_link_delete($linkId = '') {
        $link = $this->Election->AreasElection->find('first', array(
            'conditions' => array('id' => $linkId),
        ));
        if (!empty($link)) {
            $this->Election->AreasElection->delete($linkId);
        }
        echo 'ok';
        exit();
    }

}
