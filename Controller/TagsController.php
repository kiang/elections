<?php

/**
 * @property Tag Tag
 */
class TagsController extends AppController {

    public $name = 'Tags';
    public $paginate = array();

    public function admin_index() {
        $this->paginate['Tag'] = array(
            'contain' => array(),
        );
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

}
