<?php

class JsonShell extends AppShell {

    public $uses = array('Election');

    public function main() {
        $this->candidatesNew();
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
