<?php

/**
 * @property Tag Tag
 */
class TagsController extends AppController {

    public $name = 'Tags';
    public $paginate = array();

    public function beforeFilter() {
        parent::beforeFilter();
        if (isset($this->Auth)) {
            $this->Auth->allow('index');
        }
    }

    public function admin_index() {
        $tags = $this->paginate($this->Tag);
        $this->set('tags', $tags);
    }

    public function admin_add() {
        if (!empty($this->request->data)) {
            $this->Tag->create();
            if ($this->Tag->save($this->request->data)) {
                $this->Session->setFlash('資料已經儲存');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('資料儲存時發生錯誤，請重試');
            }
        }
    }

    public function admin_edit($id = null) {
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Please select a tag first!', true));
            $this->redirect($this->referer());
        }
        if (!empty($this->request->data)) {
            if ($this->Tag->save($this->request->data)) {
                $this->Session->setFlash('資料已經儲存');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('資料儲存時發生錯誤，請重試');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Tag->read(null, $id);
        }
    }

    public function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Please select a tag first!', true));
            $this->redirect($this->referer());
        }
        if ($this->Tag->delete($id)) {
            $this->Session->setFlash(__('The tag has been removed', true));
            $this->redirect(array('action' => 'index'));
        }
    }

    public function admin_links($tagId = '') {
        if (!empty($tagId)) {
            $tag = $this->Tag->find('first', array(
                'conditions' => array(
                    'Tag.id' => $tagId,
                ),
                'contain' => array('Candidate'),
            ));
        }
        if (!empty($tag)) {
            $this->set('tag', $tag);
        } else {
            $this->Session->setFlash(__('Please select a tag first!', true));
            $this->redirect($this->referer());
        }
    }

    public function admin_link_add($tagId = '', $candidateId = '') {
        $linkId = $this->Tag->CandidatesTag->field('id', array(
            'Candidate_id' => $candidateId,
            'Tag_id' => $tagId,
        ));
        if (empty($linkId)) {
            $this->Tag->CandidatesTag->create();
            $this->Tag->CandidatesTag->save(array('CandidatesTag' => array(
                    'Candidate_id' => $candidateId,
                    'Tag_id' => $tagId,
            )));
            $this->Tag->updateAll(array('Tag.count' => 'Tag.count + 1'), array('Tag.id' => "'{$tagId}'"));
        }
        echo 'ok';
        exit();
    }

    public function admin_link_delete($linkId = '') {
        $tagId = $this->Tag->CandidatesTag->field('Tag_id', array('id' => $linkId));
        $this->Tag->CandidatesTag->delete($linkId);
        if (!empty($tagId)) {
            $this->Tag->updateAll(array('Tag.count' => 'Tag.count - 1'), array('Tag.id' => "'{$tagId}'"));
        }
        echo 'ok';
        exit();
    }

    public function index() {
        $this->paginate['Tag']['limit'] = 100;
        $tags = $this->paginate($this->Tag);
        $this->set('tags', $tags);
    }

}
