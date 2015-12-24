<?php

class JsonShell extends AppShell {

    public $uses = array('Election');

    public function main() {
        $this->elections();
        $this->areas();
        $this->candidatesNew();
    }
    
    /*
     * to dump all areas and related elections
     * 
     * area
     * parents - for breadcrumbs
     * direct children - for map
     * elections
     * 
     * 1. get one area
     * 2. display map() and breadcrumbs
     * 3. get related elections and then display candidates' icons using data from elections
     */
    public function areas() {
        $basePath = Configure::read('json_target') . '/areas/';
        $areas = $this->Election->Area->find('list', array(
            'fields' => array('id', 'id'),
        ));
        foreach($areas AS $area) {
            $targetFile = $basePath . str_replace('-', '/', $area) . '.json';
            $targetPath = substr($targetFile, 0, strrpos($targetFile, '/'));
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }
            file_put_contents($targetFile, json_encode($this->Election->Area->getView($area), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }
    
    /*
     * to dump all elections and related candidates(basic info)
     */
    public function elections() {
        $basePath = Configure::read('json_target') . '/elections/';
        $elections = $this->Election->find('list', array(
            'fields' => array('id', 'id'),
        ));
        foreach($elections AS $election) {
            $targetFile = $basePath . str_replace('-', '/', $election) . '.json';
            $targetPath = substr($targetFile, 0, strrpos($targetFile, '/'));
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }
            file_put_contents($targetFile, json_encode($this->Election->getView($election), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }

    public function candidatesFull() {
        $basePath = Configure::read('json_target') . '/candidates/';
        $candidates = $this->Election->Candidate->find('list', array(
            'conditions' => array(
                'Candidate.active_id IS NULL',
                'Candidate.is_reviewed' => '1',
            ),
            'fields' => array('id', 'id'),
        ));

        foreach ($candidates AS $candidate) {
            $targetFile = $basePath . str_replace('-', '/', $candidate) . '.json';
            $targetPath = substr($targetFile, 0, strrpos($targetFile, '/'));
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }
            file_put_contents($targetFile, json_encode($this->Election->Candidate->getView($candidate), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }
    
    public function candidatesNew() {
        $basePath = Configure::read('json_target') . '/candidates/';
        $candidates = $this->Election->Candidate->find('list', array(
            'conditions' => array(
                'Candidate.active_id IS NULL',
                'Candidate.is_reviewed' => '1',
                'Candidate.modified >= ( CURDATE() - INTERVAL 30 DAY )',
            ),
            'fields' => array('id', 'id'),
        ));

        foreach ($candidates AS $candidate) {
            $targetFile = $basePath . str_replace('-', '/', $candidate) . '.json';
            $targetPath = substr($targetFile, 0, strrpos($targetFile, '/'));
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }
            file_put_contents($targetFile, json_encode($this->Election->Candidate->getView($candidate), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }

}
