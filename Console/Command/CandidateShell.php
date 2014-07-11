<?php

class CandidateShell extends AppShell {

    public $uses = array('Candidate');

    public function main() {
        $this->villmast();
        $this->suncy();
    }

    public function villmast() {
        $baseNode = $this->Candidate->Election->children(null, true);
        $cNode = $this->Candidate->Election->find('first', array(
            'conditions' => array(
                'parent_id' => $baseNode[0]['Election']['id'],
                'name' => '村里長',
            ),
        ));
        $nodes = $this->Candidate->Election->find('all', array(
            'conditions' => array(
                'lft >' => $cNode['Election']['lft'],
                'rght <' => $cNode['Election']['rght'],
            ),
            'order' => array('Election.lft ASC'),
        ));
        $stack = array();
        foreach ($nodes AS $node) {
            if ($node['Election']['parent_id'] === $cNode['Election']['id']) {
                $county = $node['Election'];
                if (!isset($stack[$county['name']])) {
                    $stack[$county['name']] = array();
                }
            } elseif ($node['Election']['parent_id'] === $county['id']) {
                $town = $node['Election'];
                if (!isset($stack[$county['name']][$town['name']])) {
                    $stack[$county['name']][$town['name']] = array();
                }
            } else {
                $stack[$county['name']][$town['name']][$node['Election']['name']] = $node['Election']['id'];
            }
        }
        $fh = fopen(__DIR__ . '/data/villmast_excel.csv', 'r');
        fgetcsv($fh, 2048);
        fgetcsv($fh, 2048);
        while ($line = fgetcsv($fh, 2048)) {
            $line[4] = str_replace(array('　', ' '), array('', ''), $line[4]);
            $line[1] = str_replace(array('台',), array('臺',), $line[1]);
            if (isset($stack[$line[1]][$line[2]][$line[3]])) {
                $candidates = $this->Candidate->find('list', array(
                    'fields' => array('name', 'name'),
                    'joins' => array(
                        array(
                            'table' => 'candidates_elections',
                            'alias' => 'CandidatesElection',
                            'type' => 'inner',
                            'conditions' => array(
                                'CandidatesElection.Candidate_id = Candidate.id',
                                'CandidatesElection.Election_id' => $stack[$line[1]][$line[2]][$line[3]],
                            ),
                        ),
                    ),
                ));
                if (!isset($candidates[$line[4]])) {
                    $this->Candidate->create();
                    if ($this->Candidate->save(array('Candidate' => array(
                                    'name' => $line[4],
                        )))) {
                        $this->Candidate->CandidatesElection->create();
                        $this->Candidate->CandidatesElection->save(array('CandidatesElection' => array(
                                'Election_id' => $stack[$line[1]][$line[2]][$line[3]],
                                'Candidate_id' => $this->Candidate->getInsertID(),
                        )));
                    }
                }
            }
        }
    }

    public function suncy() {
        $accTypes = $electionTree = array();
        $baseNode = $this->Candidate->Election->children(null, true);
        $nodes = $this->Candidate->Election->children($baseNode[0]['Election']['id'], true);
        foreach ($nodes AS $node) {
            $electionTree[$node['Election']['name']] = array();
            $subNodes = $this->Candidate->Election->children($node['Election']['id'], true);
            foreach ($subNodes AS $subNode) {
                $electionTree[$node['Election']['name']][$subNode['Election']['name']] = $subNode['Election'];
            }
        }
        $fh = fopen(__DIR__ . '/data/list_new.csv', 'r');
        while ($line = fgetcsv($fh, 2048)) {
            $a = explode('擬參選人', $line[1]);
            $a[0] = substr($a[0], strpos($a[0], '年') + 3);
            $county = mb_substr($a[0], 0, 3, 'utf-8');
            $eType = mb_substr($a[0], 3, null, 'utf-8');
            switch ($county) {
                case '桃園市':
                    $county = '桃園縣';
                case '臺北市':
                case '高雄市':
                case '新北市':
                case '臺中市':
                case '臺南市':
                    $electionName = '直轄市';
                    if ($eType === '市長') {
                        $electionName .= '長';
                    } else {
                        $electionName .= $eType;
                    }
                    break;
                default:
                    $electionName = '縣市';
                    if ($eType === '議員') {
                        $electionName .= '議員';
                    } else {
                        $electionName .= '長';
                    }
                    break;
            }
            $electionId = $this->Candidate->Election->field('id', array('name' => $electionName));
            if (!empty($electionId)) {
                $eCities = $this->Candidate->Election->children($electionId);
                foreach ($eCities AS $eCity) {
                    if ($county === $eCity['Election']['name']) {
                        $candidates = $this->Candidate->find('list', array(
                            'fields' => array('name', 'name'),
                            'joins' => array(
                                array(
                                    'table' => 'candidates_elections',
                                    'alias' => 'CandidatesElection',
                                    'type' => 'inner',
                                    'conditions' => array(
                                        'CandidatesElection.Candidate_id = Candidate.id',
                                        'CandidatesElection.Election_id' => $eCity['Election']['id'],
                                    ),
                                ),
                            ),
                        ));
                        if (!isset($candidates[$line[0]])) {
                            $this->Candidate->create();
                            if ($this->Candidate->save(array('Candidate' => array(
                                            'name' => $line[0],
                                )))) {
                                $this->Candidate->CandidatesElection->create();
                                $this->Candidate->CandidatesElection->save(array('CandidatesElection' => array(
                                        'Election_id' => $eCity['Election']['id'],
                                        'Candidate_id' => $this->Candidate->getInsertID(),
                                )));
                            }
                        }
                    }
                }
            }
        }
    }

}
