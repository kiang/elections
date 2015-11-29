<?php

App::uses('AppController', 'Controller');
App::uses('Sanitize', 'Utility');

class CandidatesController extends AppController {

    public $name = 'Candidates';
    public $paginate = array();
    public $helpers = array('Olc');

    public function beforeFilter() {
        parent::beforeFilter();
        if (isset($this->Auth)) {
            $this->Auth->allow('index', 'add', 'view', 'edit', 's', 'tag', 'tag_list', 'tag_name', 'submits', 'links', 'name');
        }
    }

    public function name($name = '') {
        $name = trim(Sanitize::clean($name));
        if (!empty($name)) {
            $items = $this->Candidate->find('all', array(
                'fields' => array('Candidate.id', 'Candidate.name', 'Candidate.no', 'Candidate.stage',
                    'Candidate.party', 'Candidate.election_id'),
                'conditions' => array(
                    'Candidate.active_id IS NULL',
                    'Candidate.is_reviewed' => '1',
                    'Candidate.stage >' => '0',
                    'Candidate.name' => $name,
                ),
            ));
            foreach ($items AS $sk => $sv) {
                if (!isset($electionStack[$sv['Candidate']['election_id']])) {
                    $electionStack[$sv['Candidate']['election_id']] = $this->Candidate->Election->getPath($sv['Candidate']['election_id'], array('name'));
                }
                $items[$sk]['Election'] = Set::extract('{n}.Election.name', $electionStack[$sv['Candidate']['election_id']]);
            }
            usort($items, array('CandidatesController', 'electionSort'));

            $this->set('title_for_layout', $name . ' 參選記錄 @ ');
            $this->set('items', $items);
            $this->set('name', $name);
        } else {
            $this->Session->setFlash('');
            $this->redirect('/');
        }
    }

    public function tag_name($tagId = '') {
        $tag = $this->Candidate->Tag->find('first', array(
            'conditions' => array('Tag.id' => $tagId,)
        ));
        if (!empty($tag)) {
            $scope = array(
                'Candidate.active_id IS NULL',
                'Candidate.is_reviewed' => '1',
                'CandidatesTag.Tag_id' => $tagId,
            );

            $this->paginate['Candidate']['joins'] = array(
                array(
                    'table' => 'candidates_tags',
                    'alias' => 'CandidatesTag',
                    'type' => 'inner',
                    'conditions' => array(
                        'CandidatesTag.Candidate_id = Candidate.id',
                    ),
                ),
            );
            $this->paginate['Candidate']['order'] = array(
                'Candidate.party' => 'desc',
                'Candidate.modified' => 'desc',
            );
            $this->paginate['Candidate']['limit'] = 30;
            $this->paginate['Candidate']['fields'] = array('Candidate.id',
                'Candidate.name', 'Candidate.no', 'Candidate.stage',
                'Candidate.party', 'Candidate.election_id');
            $items = $this->paginate($this->Candidate, $scope);
            $electionStack = array();
            foreach ($items AS $k => $item) {
                if (!isset($electionStack[$item['Candidate']['election_id']])) {
                    $electionStack[$item['Candidate']['election_id']] = $this->Candidate->Election->getPath($item['Candidate']['election_id'], array('name'));
                }
                $items[$k]['Election'] = Set::extract('{n}.Election.name', $electionStack[$item['Candidate']['election_id']]);
                $list = $this->Candidate->find('all', array(
                    'fields' => array('Candidate.id', 'Candidate.stage', 'Candidate.party', 'Candidate.election_id'),
                    'conditions' => array(
                        'Candidate.active_id IS NULL',
                        'Candidate.is_reviewed' => '1',
                        'Candidate.stage >' => '0',
                        'Candidate.name' => $item['Candidate']['name'],
                        'Candidate.election_id !=' => $item['Candidate']['election_id'],
                    ),
                ));
                foreach ($list AS $sk => $sv) {
                    if (!isset($electionStack[$sv['Candidate']['election_id']])) {
                        $electionStack[$sv['Candidate']['election_id']] = $this->Candidate->Election->getPath($sv['Candidate']['election_id'], array('name'));
                    }
                    $list[$sk]['Election'] = Set::extract('{n}.Election.name', $electionStack[$sv['Candidate']['election_id']]);
                }
                usort($list, array('CandidatesController', 'electionSort'));
                $items[$k]['list'] = $list;
            }

            $this->set('title_for_layout', $tag['Tag']['name'] . ' 候選人');
            $this->set('items', $items);
            $this->set('url', array($tagId));
            $this->set('tag', $tag);
        } else {
            $this->redirect(array('controller' => 'areas'));
        }
    }

    public function electionSort($a, $b) {
        if ($a['Election'][0] == $b['Election'][0]) {
            return 0;
        }
        return ($a['Election'][0] > $b['Election'][0]) ? -1 : 1;
    }

    public function links($candidateId = '') {
        if (!empty($candidateId)) {
            $cPage = isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : '1';
            $cacheKey = "CandidatesLinks{$candidateId}{$cPage}";
            $result = Cache::read($cacheKey, 'long');
            if (!$result) {
                $result = array();
                $candidate = $this->Candidate->find('first', array(
                    'conditions' => array(
                        'Candidate.id' => $candidateId,
                        'Candidate.active_id IS NULL',
                        'Candidate.is_reviewed' => '1',
                    ),
                    'contain' => array(
                        'Keyword',
                    ),
                ));
                if (!empty($candidate)) {
                    $result['keywords'] = Set::combine($candidate['Keyword'], '{n}.id', '{n}.keyword');
                    $scope = array(
                        'LinksKeyword.Keyword_id' => array_keys($result['keywords']),
                    );

                    $this->paginate['Link']['joins'] = array(
                        array(
                            'table' => 'links_keywords',
                            'alias' => 'LinksKeyword',
                            'type' => 'inner',
                            'conditions' => array(
                                'LinksKeyword.Link_id = Link.id',
                            ),
                        ),
                    );
                    $this->paginate['Link']['order'] = array('Link.created' => 'desc');
                    $this->paginate['Link']['limit'] = 30;
                    $this->paginate['Link']['fields'] = array('Link.*', 'LinksKeyword.summary', 'LinksKeyword.Keyword_id');
                    $result['links'] = $this->paginate($this->Candidate->Keyword->Link, $scope);
                    $result['paging'] = $this->request->params['paging'];
                    Cache::write($cacheKey, $result, 'long');
                }
            } else {
                $this->request->params['paging'] = $result['paging'];
            }
        }
        if (!empty($result['links'])) {
            $this->set('url', array($candidateId));
            $this->set('linkKeywords', $result['keywords']);
            $this->set('newsLinks', $result['links']);
        }
    }

    public function submits() {
        $this->set('count', $this->Candidate->find('count', array(
                    'conditions' => array(
                        'Candidate.is_reviewed' => '0',
                    ),
        )));
    }

    public function s() {
        $result = array();
        if (isset($this->request->query['term'])) {
            $keyword = Sanitize::clean($this->request->query['term']);
        }
        if (!empty($keyword)) {
            $cacheKey = "CandidatesS{$keyword}";
            $result = Cache::read($cacheKey, 'long');
            if (!$result) {
                $result = $this->Candidate->find('all', array(
                    'fields' => array('Candidate.id', 'Candidate.name', 'Candidate.no',
                        'Candidate.party', 'Candidate.election_id'),
                    'conditions' => array(
                        'Candidate.active_id IS NULL',
                        'Candidate.is_reviewed' => '1',
                        'Candidate.name LIKE' => "%{$keyword}%",
                    ),
                    'limit' => 20,
                ));
                foreach ($result AS $k => $v) {
                    $result[$k]['jobTitle'] = '';
                    $parents = $this->Candidate->Election->getPath($v['Candidate']['election_id'], array('name'));
                    foreach ($parents AS $parent) {
                        $result[$k]['jobTitle'] .= $parent['Election']['name'];
                    }
                }
                Cache::write($cacheKey, $result, 'long');
            }
        }
        $this->set('result', $result);
    }

    public function tag($tagId = '') {
        $tag = $this->Candidate->Tag->find('first', array(
            'conditions' => array('Tag.id' => $tagId,)
        ));
        if (!empty($tag)) {
            $scope = array(
                'Candidate.active_id IS NULL',
                'Candidate.is_reviewed' => '1',
                'CandidatesTag.Tag_id' => $tagId,
            );

            $this->paginate['Candidate']['joins'] = array(
                array(
                    'table' => 'candidates_tags',
                    'alias' => 'CandidatesTag',
                    'type' => 'inner',
                    'conditions' => array(
                        'CandidatesTag.Candidate_id = Candidate.id',
                    ),
                ),
            );
            $this->paginate['Candidate']['order'] = array(
                'Candidate.party' => 'desc',
                'Candidate.modified' => 'desc',
            );
            $this->paginate['Candidate']['limit'] = 30;
            $this->paginate['Candidate']['fields'] = array('Candidate.id',
                'Candidate.name', 'Candidate.no', 'Candidate.stage',
                'Candidate.image', 'Candidate.election_id');
            $items = $this->paginate($this->Candidate, $scope);
            $electionStack = array();
            foreach ($items AS $k => $item) {
                if (!isset($electionStack[$item['Candidate']['election_id']])) {
                    $electionStack[$item['Candidate']['election_id']] = $this->Candidate->Election->getPath($item['Candidate']['election_id'], array('id', 'name'));
                }
                $items[$k]['Election'] = $electionStack[$item['Candidate']['election_id']];
            }

            $this->set('title_for_layout', $tag['Tag']['name'] . ' 候選人');
            $this->set('items', $items);
            $this->set('url', array($tagId));
            $this->set('tag', $tag);
        } else {
            $this->redirect(array('controller' => 'areas'));
        }
    }

    public function tag_list($tagId = '') {
        $tag = $this->Candidate->Tag->find('first', array(
            'conditions' => array('Tag.id' => $tagId,)
        ));
        if (!empty($tag)) {
            $scope = array(
                'Candidate.active_id IS NULL',
                'Candidate.is_reviewed' => '1',
                'CandidatesTag.Tag_id' => $tagId,
            );

            $this->paginate['Candidate']['joins'] = array(
                array(
                    'table' => 'candidates_tags',
                    'alias' => 'CandidatesTag',
                    'type' => 'inner',
                    'conditions' => array(
                        'CandidatesTag.Candidate_id = Candidate.id',
                    ),
                ),
            );
            $this->paginate['Candidate']['order'] = array(
                'Candidate.party' => 'desc',
                'Candidate.modified' => 'desc'
            );
            $this->paginate['Candidate']['limit'] = 30;
            $this->paginate['Candidate']['fields'] = array('Candidate.id',
                'Candidate.name', 'Candidate.no', 'Candidate.stage',
                'Candidate.party', 'Candidate.image', 'Candidate.election_id');
            $items = $this->paginate($this->Candidate, $scope);
            $electionStack = array();
            foreach ($items AS $k => $item) {
                if (!isset($electionStack[$item['Candidate']['election_id']])) {
                    $electionStack[$item['Candidate']['election_id']] = $this->Candidate->Election->getPath($item['Candidate']['election_id'], array('id', 'name'));
                }
                $items[$k]['Election'] = Set::extract('{n}.Election.name', $electionStack[$item['Candidate']['election_id']]);
            }

            $this->set('title_for_layout', $tag['Tag']['name'] . ' 候選人');
            $this->set('items', $items);
            $this->set('url', array($tagId));
            $this->set('tag', $tag);
        } else {
            $this->redirect(array('controller' => 'areas'));
        }
    }

    function index($electionId = '') {
        $cPage = isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : '1';
        $cacheKey = "CandidatesIndex{$electionId}{$cPage}";
        $result = Cache::read($cacheKey, 'long');
        if (!$result) {
            $result = array();
            $scope = array(
                'Candidate.active_id IS NULL',
                'Candidate.is_reviewed' => '1',
            );

            if (!empty($electionId)) {
                $scope['Candidate.election_id'] = $electionId;
                $this->paginate['Candidate']['order'] = array('Candidate.stage' => 'DESC', 'Candidate.no' => 'ASC');
            } else {
                $this->paginate['Candidate']['order'] = array('Candidate.modified' => 'desc');
            }
            $this->paginate['Candidate']['limit'] = 30;
            $this->paginate['Candidate']['fields'] = array('Candidate.id', 'Candidate.party',
                'Candidate.name', 'Candidate.no', 'Candidate.stage', 'Candidate.image',
                'Candidate.election_id');
            $result['items'] = $this->paginate($this->Candidate, $scope);
            $result['paging'] = $this->request->params['paging'];
            $electionStack = array();
            foreach ($result['items'] AS $k => $item) {
                if (!isset($electionStack[$item['Candidate']['election_id']])) {
                    $electionStack[$item['Candidate']['election_id']] = $this->Candidate->Election->getPath($item['Candidate']['election_id'], array('id', 'name'));
                }
                $result['items'][$k]['Election'] = $electionStack[$item['Candidate']['election_id']];
            }
            $result['parents'] = $this->Candidate->Election->getPath($electionId);
            Cache::write($cacheKey, $result, 'long');
        } else {
            $this->request->params['paging'] = $result['paging'];
        }


        $c = array();
        if (!empty($result['parents'])) {
            $c = Set::extract('{n}.Election.name', $result['parents']);
        }

        $this->set('title_for_layout', implode(' > ', $c) . '候選人 @ ');
        $this->set('items', $result['items']);
        $this->set('electionId', $electionId);
        $this->set('url', array($electionId));
        $this->set('parents', $result['parents']);
    }

    function add($electionId = '') {
        if (!empty($electionId)) {
            if (!empty($this->data)) {
                $dataToSave = Sanitize::clean($this->data, array('encode' => false));
                $dataToSave['Candidate']['election_id'] = $electionId;
                $this->Candidate->create();
                if ($this->Candidate->save($dataToSave)) {
                    $areaId = $this->Candidate->Election->AreasElection->field('Area_id', array('Election_id' => $electionId));
                    $this->Session->setFlash('感謝您提供的資料，資料確認後會盡快更新！');
                    $this->redirect(array('controller' => 'areas', 'action' => 'index', $areaId));
                } else {
                    $this->Session->setFlash('資料儲存時發生錯誤，請重試');
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

    function edit($candidateId = '') {
        if (!empty($candidateId)) {
            $candidate = $this->Candidate->find('first', array(
                'conditions' => array(
                    'Candidate.id' => $candidateId,
                    'Candidate.active_id IS NULL',
                    'Candidate.is_reviewed' => '1',
                ),
                'contain' => array('Election'),
            ));
        }
        if (!empty($candidate)) {
            if (!empty($this->data)) {
                $dataToSave = Sanitize::clean($this->data, array('encode' => false));
                $dataToSave['Candidate']['active_id'] = $candidateId;
                $dataToSave['Candidate']['election_id'] = $candidate['Candidate']['election_id'];
                $this->Candidate->create();
                if ($this->Candidate->save($dataToSave)) {
                    $areaId = $this->Candidate->Election->AreasElection->field('Area_id', array('Election_id' => $candidate['Election']['id']));
                    $this->Session->setFlash('感謝您提供的資料，資料確認後會盡快更新！');
                    $this->redirect(array('controller' => 'areas', 'action' => 'index', $areaId));
                } else {
                    $this->Session->setFlash('資料儲存時發生錯誤，請重試');
                }
            } else {
                $latestUnRevied = $this->Candidate->find('first', array(
                    'conditions' => array(
                        'Candidate.active_id' => $candidateId,
                        'Candidate.is_reviewed' => '0',
                    ),
                    'order' => array('Candidate.created' => 'DESC'),
                    'contain' => array('Election'),
                ));
                if (!empty($latestUnRevied)) {
                    $candidate = $latestUnRevied;
                }
                $candidate['Candidate']['platform'] = str_replace('\\n', "\n", $candidate['Candidate']['platform']);
                $candidate['Candidate']['links'] = str_replace('\\n', "\n", $candidate['Candidate']['links']);
                $candidate['Candidate']['education'] = str_replace('\\n', "\n", $candidate['Candidate']['education']);
                $candidate['Candidate']['experience'] = str_replace('\\n', "\n", $candidate['Candidate']['experience']);
                $this->data = $candidate;
            }
            $parents = $this->Candidate->Election->getPath($candidate['Candidate']['election_id']);
            $c = array();
            foreach ($parents AS $parent) {
                $c[] = $parent['Election']['name'];
            }
            $c[] = '更新候選人';
            $this->set('title_for_layout', implode(' > ', $c) . ' @ ');
            $this->set('candidateId', $candidateId);
            $this->set('referer', $this->request->referer());
            $this->set('parents', $parents);
        } else {
            $this->redirect(array('controller' => 'areas'));
        }
    }

    function view($id = null) {
        if (!empty($id)) {
            $result = $this->Candidate->getView($id);
        }

        if (!empty($result['candidate'])) {
            $desc_for_layout = '';
            $descElections = Set::extract('{n}.Election.name', $result['parents']);
            if (!empty($descElections)) {
                $desc_for_layout .= $result['candidate']['Candidate']['name'] . '在' . implode(' > ', $descElections) . '的參選資訊。';
            }
            if (!empty($result['candidate']['Candidate']['no'])) {
                $descElections[] = "{$result['candidate']['Candidate']['no']}號 {$result['candidate']['Candidate']['name']}";
            } else {
                $descElections[] = $result['candidate']['Candidate']['name'];
            }
            $this->set('candidate', $result['candidate']);
            $this->set('referer', $this->request->referer());
            $this->set('desc_for_layout', $desc_for_layout);
            $this->set('title_for_layout', implode(' > ', $descElections) . '候選人 @ ');
            $this->set('parents', $result['parents']);
        } else {
            $this->Session->setFlash('請依照網頁指示操作');
            $this->redirect(array('action' => 'index'));
        }
    }

    function admin_index($electionId = '0', $keyword = '') {
        $scope = array(
            'Candidate.active_id IS NULL',
        );
        $keyword = trim($keyword);
        if (!empty($keyword)) {
            $scope['Candidate.name LIKE'] = "%{$keyword}%";
        }
        if (!empty($electionId) && $electionId !== '0') {
            $scope['Candidate.election_id'] = $electionId;
        }
        $this->paginate['Candidate']['limit'] = 20;
        $this->paginate['Candidate']['contain'] = array();
        $this->paginate['Candidate']['order'] = array(
            'Candidate.created' => 'DESC',
        );
        $items = $this->paginate($this->Candidate, $scope);
        foreach ($items AS $k => $v) {
            $items[$k]['Election'] = $this->Candidate->Election->getPath($v['Candidate']['election_id'], array('name'));
            $items[$k]['Election'] = Set::extract('{n}.Election.name', $items[$k]['Election']);
        }

        $this->set('items', $items);
        $this->set('electionId', $electionId);
        $this->set('url', array($electionId, $keyword));
        $this->set('keyword', $keyword);
        $this->set('parents', $this->Candidate->Election->getPath($electionId));
    }

    function admin_view($id = null) {
        if (!empty($id)) {
            $this->data = $this->Candidate->find('first', array(
                'conditions' => array(
                    'Candidate.id' => $id,
                ),
                'contain' => array(
                    'Election' => array(
                        'fields' => array('Election.id', 'Election.population_electors', 'Election.population'),
                    ),
                    'Keyword',
                ),
            ));
        }

        if (empty($this->data)) {
            $this->Session->setFlash('請依照網頁指示操作');
            $this->redirect(array('action' => 'index'));
        } else {
            if (!empty($this->data['Candidate']['active_id'])) {
                $targetId = $this->data['Candidate']['active_id'];
            } else {
                $targetId = $this->data['Candidate']['id'];
            }
            $versions = $this->Candidate->find('all', array(
                'conditions' => array('OR' => array(
                        'Candidate.id' => $targetId,
                        'Candidate.active_id' => $targetId,
                    )),
                'order' => array('Candidate.created DESC'),
            ));
            $this->set('versions', $versions);
            $this->set('parents', $this->Candidate->Election->getPath($this->data['Election']['id']));
        }
    }

    function admin_add($electionId = '') {
        if (!empty($electionId)) {
            if (!empty($this->data)) {
                $dataToSave = Sanitize::clean($this->data, array('encode' => false));
                $dataToSave['Candidate']['election_id'] = $electionId;
                $this->Candidate->create();
                if ($this->Candidate->save($dataToSave)) {
                    $this->Session->setFlash('資料已經儲存');
                    $this->redirect(array('action' => 'index', $electionId));
                } else {
                    $this->Session->setFlash('資料儲存時發生錯誤，請重試');
                }
            }
            $parents = $this->Candidate->Election->getPath($electionId);
            $this->set('electionId', $electionId);
            $this->set('parents', $parents);
        } else {
            $this->redirect(array('controller' => 'elections'));
        }
    }

    function admin_edit($id = null, $after = '') {
        if (!empty($id)) {
            $candidate = $this->Candidate->find('first', array(
                'conditions' => array(
                    'Candidate.id' => $id,
                ),
                'contain' => array('Election'),
            ));
        }
        if (!empty($candidate)) {
            if (!empty($this->data)) {
                $dataToSave = Sanitize::clean($this->data, array('encode' => false));
                $dataToSave['Candidate']['id'] = $id;
                if (empty($dataToSave['Candidate']['active_id'])) {
                    unset($dataToSave['Candidate']['active_id']);
                }
                if (!empty($dataToSave['Candidate']['image_upload']['size'])) {
                    $dataToSave['Candidate']['image'] = $dataToSave['Candidate']['image_upload'];
                }
                if ($this->Candidate->save($dataToSave)) {
                    $this->Session->setFlash('資料已經儲存');
                    if ($after !== 'submits') {
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->redirect(array('action' => 'submits'));
                    }
                } else {
                    $this->Session->setFlash('資料儲存時發生錯誤，請重試');
                }
            } else {
                $candidate['Candidate']['platform'] = str_replace('\\n', "\n", $candidate['Candidate']['platform']);
                $candidate['Candidate']['links'] = str_replace('\\n', "\n", $candidate['Candidate']['links']);
                $candidate['Candidate']['education'] = str_replace('\\n', "\n", $candidate['Candidate']['education']);
                $candidate['Candidate']['experience'] = str_replace('\\n', "\n", $candidate['Candidate']['experience']);
                $this->set('id', $id);
                $this->data = $candidate;
            }
        } else {
            $this->Session->setFlash('請依照網頁指示操作');
            $this->redirect($this->referer());
        }
    }

    function admin_delete($id = null, $after = '') {
        if (!$id) {
            $this->Session->setFlash('請依照網頁指示操作');
        } else if ($this->Candidate->delete($id)) {
            $this->Session->setFlash('資料已經刪除');
        }
        if ($after !== 'submits') {
            $this->redirect(array('action' => 'index'));
        } else {
            $this->redirect(array('action' => 'submits'));
        }
    }

    public function admin_submits() {
        if (!empty($this->data['Candidate']['id'])) {
            $deleteCounter = 0;
            foreach ($this->data['Candidate']['id'] AS $candidateId) {
                if ($this->Candidate->delete($candidateId)) {
                    ++$deleteCounter;
                }
            }
            if ($deleteCounter > 0) {
                $this->Session->setFlash("刪除了 {$deleteCounter} 筆資料");
            }
        }
        $scope = array(
            'Candidate.is_reviewed' => '0',
        );
        $this->paginate['Candidate']['limit'] = 20;
        $this->paginate['Candidate']['order'] = array(
            'Candidate.created' => 'ASC',
        );
        $items = $this->paginate($this->Candidate, $scope);
        $this->set('items', $items);
    }

    public function admin_review($candidateId = '', $approved = '') {
        $fields = array('Candidate.id', 'Candidate.active_id', 'Candidate.name',
            'Candidate.no', 'Candidate.education_level', 'Candidate.is_present',
            'Candidate.name_english', 'Candidate.birth_place',
            'Candidate.image', 'Candidate.party', 'Candidate.contacts_phone',
            'Candidate.contacts_fax', 'Candidate.contacts_email',
            'Candidate.contacts_address', 'Candidate.links', 'Candidate.gender',
            'Candidate.birth', 'Candidate.education', 'Candidate.experience');
        $submitted = $this->Candidate->find('first', array(
            'fields' => $fields,
            'conditions' => array('Candidate.id' => $candidateId),
            'contain' => array('Election' => array('fields' => array('Election.name'))),
        ));
        $originalId = '';
        $original = array();
        if (!empty($submitted['Candidate']['active_id'])) {
            $original = $this->Candidate->find('first', array(
                'fields' => $fields,
                'conditions' => array('Candidate.id' => $submitted['Candidate']['active_id']),
                'contain' => array('Election' => array('fields' => array('Election.name'))),
            ));
            $originalId = $original['Candidate']['id'];
        }

        if ($approved === 'yes') {
            if (!empty($originalId)) {
                $dataToSave = array(
                    'id' => $original['Candidate']['id'],
                );
                //update image
                if (!empty($submitted['Candidate']['image'])) {
                    $dataToSave['image'] = $submitted['Candidate']['image'];
                }

                //update candidate
                $cFields = array('name', 'party', 'contacts_phone', 'contacts_fax',
                    'no', 'education_level', 'is_present', 'name_english', 'birth_place',
                    'contacts_email', 'contacts_address', 'links', 'gender', 'birth',
                    'education', 'experience', 'platform');

                foreach ($cFields AS $cField) {
                    if (isset($submitted['Candidate'][$cField])) {
                        $dataToSave[$cField] = $submitted['Candidate'][$cField];
                    }
                }
                $this->Candidate->id = $originalId;
                $this->Candidate->save($dataToSave);
            }

            $this->Candidate->id = $candidateId;
            $this->Candidate->saveField('is_reviewed', '1');
            $unReviewId = $this->Candidate->field('id', array(
                'Candidate.is_reviewed' => '0',
                    ), array('Candidate.created' => 'DESC'));
            if (!empty($unReviewId)) {
                $this->redirect('/admin/candidates/review/' . $unReviewId);
            } else {
                $this->redirect('/admin/candidates/submits');
            }
        } else {
            if (!empty($originalId)) {
                unset($original['Candidate']['id']);
                unset($original['Candidate']['active_id']);
                unset($original['Candidate']['election_id']);
            }
            unset($submitted['Candidate']['id']);
            unset($submitted['Candidate']['active_id']);
            unset($submitted['Candidate']['election_id']);
        }
        $this->set('submitted', $submitted);
        $this->set('original', $original);
        $this->set('submittedId', $candidateId);
        $this->set('originalId', $originalId);
    }

    public function admin_keyword_add($candidateId = '') {
        if (!empty($candidateId)) {
            $candidate = $this->Candidate->find('first', array(
                'conditions' => array(
                    'Candidate.active_id IS NULL',
                    'Candidate.id' => $candidateId,
                ),
                'contain' => array('Keyword'),
            ));
        }
        if (!empty($candidate) && !empty($this->data['keyword'])) {
            $keywordId = $this->Candidate->Keyword->field('id', array('keyword' => $this->data['keyword']));
            if (!empty($keywordId)) {
                $keywordLinked = false;
                foreach ($candidate['Keyword'] AS $keyword) {
                    if ($keyword['keyword'] === $this->data['keyword']) {
                        $keywordLinked = true;
                    }
                }
                if (false === $keywordLinked) {
                    $this->Candidate->CandidatesKeyword->create();
                    $this->Candidate->CandidatesKeyword->save(array('CandidatesKeyword' => array(
                            'Candidate_id' => $candidateId,
                            'Keyword_id' => $keywordId,
                    )));
                }
            } else {
                $this->Candidate->Keyword->create();
                if ($this->Candidate->Keyword->save(array('Keyword' => array(
                                'keyword' => $this->data['keyword'],
                    )))) {
                    $keywordId = $this->Candidate->Keyword->getInsertID();
                    $this->Candidate->CandidatesKeyword->create();
                    $this->Candidate->CandidatesKeyword->save(array('CandidatesKeyword' => array(
                            'Candidate_id' => $candidateId,
                            'Keyword_id' => $keywordId,
                    )));
                }
            }
        }
        die('ok');
    }

    public function admin_keyword_delete($keywordCandidateId = '') {
        $this->Candidate->CandidatesKeyword->id = $keywordCandidateId;
        $candidateId = $this->Candidate->CandidatesKeyword->field('Candidate_id');
        $this->Candidate->CandidatesKeyword->delete($keywordCandidateId);
        if (!empty($candidateId)) {
            $this->redirect(array('action' => 'view', $candidateId));
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    function admin_duplicate($id = null) {
        if (!empty($id)) {
            $candidate = $this->Candidate->find('first', array(
                'conditions' => array(
                    'Candidate.id' => $id,
                ),
            ));
        }

        if (!empty($candidate)) {
            if (!empty($this->request->data['Candidate']['election_id'])) {
                unset($candidate['Candidate']['id']);
                $candidate['Candidate']['election_id'] = $this->request->data['Candidate']['election_id'];
                $candidate['Candidate']['is_reviewed'] = '1';
                $candidate['Candidate']['stage'] = '0';
                $candidate['Candidate']['vote_count'] = '0';
                $candidate['Candidate']['is_present'] = '0';
                unset($candidate['Candidate']['active_id']);
                unset($candidate['Candidate']['no']);
                unset($candidate['Candidate']['created']);
                unset($candidate['Candidate']['modified']);
                $path = WWW_ROOT . 'media';
                if (!empty($candidate['Candidate']['image']) && file_exists($path . '/' . $candidate['Candidate']['image'])) {
                    $fileName = str_replace('-', '/', String::uuid()) . '.jpg';
                    if (!file_exists($path . '/' . dirname($fileName))) {
                        mkdir($path . '/' . dirname($fileName), 0777, true);
                    }
                    copy($path . '/' . $candidate['Candidate']['image'], $path . '/' . $fileName);
                    $candidate['Candidate']['image'] = $fileName;
                } else {
                    $candidate['Candidate']['image'] = '';
                }
                $this->Candidate->create();
                if ($this->Candidate->save($candidate)) {
                    $this->Session->setFlash('資料完成複製');
                    $this->redirect(array('action' => 'view', $this->Candidate->getInsertID()));
                } else {
                    $this->Session->setFlash('資料複製失敗');
                    $this->redirect(array('action' => 'duplicate', $id));
                }
            }
            $this->set('candidate', $candidate);
        } else {
            $this->Session->setFlash('請依照網頁指示操作');
            $this->redirect(array('action' => 'index'));
        }
    }

}
