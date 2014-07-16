<?php

class CandidateShell extends AppShell {

    public $uses = array('Candidate');

    public function main() {
        //$this->villmast();
        //$this->suncy();
        $this->moi();
    }

    public function moi() {
        $srcFiles = array(
            '縣市議員' => 'http://cand.moi.gov.tw/of/ap/cand_json.jsp?electkind=0200000',
            '直轄市議員' => 'http://cand.moi.gov.tw/of/ap/cand_json.jsp?electkind=0100000'
        );
        $cachePath = TMP . 'moi';
        if (!file_exists($cachePath)) {
            mkdir($cachePath, 0777, true);
        }
        foreach ($srcFiles AS $eType => $srcFile) {
            $eTypeDb[$eType] = $this->Candidate->Election->find('first', array(
                'conditions' => array('name' => $eType),
            ));
        }
        foreach ($srcFiles AS $eType => $srcFile) {
            $cacheFile = $cachePath . '/' . md5($srcFile);
            if (!file_exists($cacheFile)) {
                file_put_contents($cacheFile, file_get_contents($srcFile));
            }
            $jsonContent = json_decode(file_get_contents($cacheFile), true);
            $counties = array();
            $zones = array();
            foreach ($jsonContent AS $c) {
                $c['cityname'] = str_replace('台', '臺', $c['cityname']);
                if ($c['cityname'] === '桃園縣') {
                    $ctype = '直轄市議員';
                } else {
                    $ctype = $eType;
                }
                if (!isset($counties[$c['cityname']])) {
                    $e = $this->Candidate->Election->find('first', array(
                        'conditions' => array(
                            'Election.parent_id' => $eTypeDb[$ctype]['Election']['id'],
                            'Election.name' => $c['cityname'],
                        ),
                    ));
                    if (!empty($e)) {
                        $counties[$c['cityname']] = $e;
                    } else {
                        echo "{$c['cityname']}\n";
                    }
                }
                $c['idname'] = str_replace(array('　'), array(''), $c['idname']);
                if (!isset($zones[$c['cityname']])) {
                    $zones[$c['cityname']] = array();
                }
                $eareaname = str_replace(array('一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '1001', '1002', '1003', '1004', '1005', '1006', '1007', '1008'), array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18'), $c['eareaname']);
                $eareaname = preg_replace('/[^0-9]/', '', $eareaname);
                $eareaname = str_pad($eareaname, 2, '0', STR_PAD_LEFT);
                if (!empty($eareaname) && !isset($zones[$c['cityname']][$eareaname])) {
                    $z = $this->Candidate->Election->find('first', array(
                        'conditions' => array(
                            'Election.parent_id' => $counties[$c['cityname']]['Election']['id'],
                            'Election.name LIKE' => "%{$eareaname}%",
                        ),
                    ));
                    if (!empty($z)) {
                        $zones[$c['cityname']][$eareaname] = $z;
                    }
                }

                if (!empty($zones[$c['cityname']][$eareaname])) {
                    $this->Candidate->create();
                    if ($this->Candidate->save(array('Candidate' => array(
                                    'name' => $c['idname'],
                                    'gender' => ($c['sex'] === '男') ? 'M' : 'F',
                                    'party' => $c['partymship'],
                                    'contacts_address' => $c['officeadress'],
                                    'contacts_phone' => $c['officetelphone'],
                                    'education' => $c['education'],
                                    'experience' => $c['profession'],
                        )))) {
                        $this->Candidate->CandidatesElection->create();
                        $this->Candidate->CandidatesElection->save(array('CandidatesElection' => array(
                                'Election_id' => $zones[$c['cityname']][$eareaname]['Election']['id'],
                                'Candidate_id' => $this->Candidate->getInsertID(),
                        )));
                    }
                }
            }
        }
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
