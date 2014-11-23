<?php

App::uses('Sanitize', 'Utility');

/**
 * @property Bulletin Bulletin
 */
class BulletinsController extends AppController {

    public $name = 'Bulletins';
    public $paginate = array();

    public function beforeFilter() {
        parent::beforeFilter();
        if (isset($this->Auth)) {
            $this->Auth->allow('index', 'view');
        }
    }

    public function admin_index() {
        $bulletins = $this->paginate($this->Bulletin);
        $this->set('bulletins', $bulletins);
    }

    public function admin_add() {
        if (!empty($this->request->data)) {
            $this->Bulletin->create();
            if ($this->Bulletin->save($this->request->data)) {
                $this->Session->setFlash('資料已經儲存');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('資料儲存時發生錯誤，請重試');
            }
        }
    }

    public function admin_edit($id = null) {
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Please select a bulletin first!', true));
            $this->redirect($this->referer());
        }
        if (!empty($this->request->data)) {
            $this->Bulletin->id = $id;
            if ($this->Bulletin->save($this->request->data)) {
                $this->Session->setFlash('資料已經儲存');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('資料儲存時發生錯誤，請重試');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Bulletin->read(null, $id);
        }
    }

    public function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Please select a bulletin first!', true));
            $this->redirect($this->referer());
        }
        if ($this->Bulletin->delete($id)) {
            $this->Bulletin->Election->updateAll(array('Election.bulletin_key' => 'NULL'), array(
                "Election.bulletin_key = '{$id}'",));
            $this->Session->setFlash(__('The bulletin has been removed', true));
            $this->redirect(array('action' => 'index'));
        }
    }

    public function admin_links($bulletinId = '') {
        if (!empty($bulletinId)) {
            $bulletin = $this->Bulletin->find('first', array(
                'conditions' => array(
                    'Bulletin.id' => $bulletinId,
                ),
                'contain' => array('Election'),
            ));
        }
        if (!empty($bulletin)) {
            $this->set('bulletin', $bulletin);
        } else {
            $this->Session->setFlash(__('Please select a bulletin first!', true));
            $this->redirect($this->referer());
        }
    }

    public function admin_next_link() {
        $bulletinId = $this->Bulletin->field('id', array('count_elections' => 0));
        if (empty($bulletinId)) {
            $bulletinId = $this->Bulletin->field('id', null, array('modified' => 'ASC'));
        }
        if (!empty($bulletinId)) {
            $this->redirect(array('action' => 'links', $bulletinId));
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    public function admin_link_add($bulletinId = '', $electionId = '') {
        if (!empty($electionId) && !empty($bulletinId)) {
            $election = $this->Bulletin->Election->find('first', array(
                'conditions' => array('Election.id' => $electionId,),
                'fields' => array('id', 'lft', 'rght'),
            ));
            if ($election['Election']['rght'] - $election['Election']['lft'] !== 1) {
                $elections = $this->Bulletin->Election->find('list', array(
                    'conditions' => array(
                        'Election.rght - Election.lft = 1',
                        'Election.parent_id' => $election['Election']['id'],
                    ),
                    'fields' => array('id', 'id'),
                ));
            } else {
                $elections = array(
                    $electionId => $electionId,
                );
            }
            foreach ($elections AS $electionId) {
                $linkId = $this->Bulletin->BulletinsElection->field('id', array(
                    'Election_id' => $electionId,
                    'Bulletin_id' => $bulletinId,
                ));
                if (empty($linkId)) {
                    $this->Bulletin->BulletinsElection->create();
                    $this->Bulletin->BulletinsElection->save(array('BulletinsElection' => array(
                            'Election_id' => $electionId,
                            'Bulletin_id' => $bulletinId,
                    )));
                    $this->Bulletin->updateAll(array(
                        'Bulletin.count_elections' => 'Bulletin.count_elections + 1',
                        'Bulletin.modified' => 'now()',
                            ), array("Bulletin.id = '{$bulletinId}'"));
                    /*
                     * @todo: the following part not work as expected when updating multiple records.
                     * 
                     * But could be fixed batchly using following SQL:
                     * UPDATE elections e INNER JOIN bulletins_elections be ON be.Election_id = e.id SET e.bulletin_key = be.Bulletin_id
                     */
                    $this->Bulletin->Election->updateAll(array(
                        'Election.bulletin_key' => "'{$bulletinId}'"
                            ), array(
                        "Election.id = '{$electionId}'",
                        "Election.bulletin_key != '{$bulletinId}'",
                    ));
                }
            }
        }
        echo 'ok';
        exit();
    }

    public function admin_link_delete($linkId = '') {
        $link = $this->Bulletin->BulletinsElection->find('first', array(
            'conditions' => array('id' => $linkId),
        ));
        if (!empty($link)) {
            $this->Bulletin->BulletinsElection->delete($linkId);
            $this->Bulletin->updateAll(array(
                'Bulletin.count_elections' => 'Bulletin.count_elections - 1',
                'Bulletin.modified' => 'now()',
                    ), array("Bulletin.id = '{$link['BulletinsElection']['Bulletin_id']}'"));
            $this->Bulletin->Election->updateAll(array('Election.bulletin_key' => 'NULL'), array(
                "Election.id = '{$link['BulletinsElection']['Election_id']}'",
                "Election.bulletin_key = '{$link['BulletinsElection']['Bulletin_id']}'",));
        }
        echo 'ok';
        exit();
    }

    public function index($keyword = '') {
        $keyword = Sanitize::clean($keyword);
        $scope = array();
        if (!empty($keyword)) {
            $scope['Bulletin.name LIKE'] = "%{$keyword}%";
        }
        $this->paginate['Bulletin']['limit'] = 100;
        $bulletins = $this->paginate($this->Bulletin, $scope);
        $this->set('bulletins', $bulletins);
        $this->set('url', array($keyword));
        $this->set('keyword', $keyword);
    }

    public function view($bulletinId = '') {
        if (!empty($bulletinId)) {
            $bulletin = $this->Bulletin->find('first', array(
                'conditions' => array(
                    'Bulletin.id' => $bulletinId,
                ),
                'contain' => array('Election'),
            ));
        }
        if (!empty($bulletin)) {
            $this->set('bulletin', $bulletin);
            $this->set('title_for_layout', $bulletin['Bulletin']['name'] . ' 選舉公報 @ ');
        } else {
            $this->Session->setFlash(__('Please select a bulletin first!', true));
            $this->redirect($this->referer());
        }
    }

}
