<?php

App::uses('AppController', 'Controller');

class AreasController extends ApiAppController {

    var $uses = array('Area');

    function index($parentId = '') {
        if (!empty($parentId)) {
            $parentId = $this->Area->field('id', array('id' => $parentId));
        } else {
            $parentId = $this->Area->field('id', array('parent_id IS NULL'));
        }

        $this->jsonData = $this->Area->find('all', array(
            'fields' => array('Area.id', 'Area.name', 'Area.ivid', 'Area.code',
                'Area.lft', 'Area.rght', 'Area.population',
                'Area.population_electors'),
            'conditions' => array(
                'Area.parent_id' => $parentId,
            ),
            'contain' => array(
                'Election' => array(
                    'fields' => array('Election.id', 'Election.name',
                        'Election.lft', 'Election.rght', 'Election.population',
                        'Election.population_electors',),
                ),
            ),
        ));
        foreach ($this->jsonData AS $k => $v) {
            foreach($v['Election'] AS $ek => $e) {
                $eParents = $this->Area->Election->getPath($e['id'], array('name'));
                $this->jsonData[$k]['Election'][$ek]['name'] = implode(' > ', Set::extract($eParents, '{n}.Election.name'));
                unset($this->jsonData[$k]['Election'][$ek]['AreasElection']);
            }
        }
    }

}
