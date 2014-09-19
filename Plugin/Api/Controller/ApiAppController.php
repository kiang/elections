<?php

App::uses('AppController', 'Controller');

class ApiAppController extends AppController {

    var $jsonData = array();

    public function beforeFilter() {
        header('Content-Type: application/json');
        if (isset($this->Auth)) {
            $this->Auth->allow();
        }
    }

    public function beforeRender() {
        echo json_encode($this->jsonData);
        exit();
    }

}
