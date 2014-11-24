<?php

/*
 * 宜蘭縣第20屆鄉(鎮、市)民代表
 * http://www.ilec.gov.tw/files/15-1006-23417,c4988-1.php
 * 
 * 新竹縣第20屆（竹北市第8屆、竹東鎮、寶山鄉第18屆）鄉（鎮、市）民代表
 * http://www.hccec.gov.tw/files/15-1008-23185,c5491-1.php
 * 
 * 苗栗縣第20屆(苗栗市第10屆)鄉鎮市民代表
 * http://www.mlec.gov.tw/files/15-1009-23436,c5044-1.php
 * 
 * 彰化縣各鄉(鎮、市)民代表會第20屆（彰化市第18屆、伸港鄉第19屆）代表
 * http://www.chec.gov.tw/files/11-1011-4580.php
 * 
 * 南投各鄉鎮市民代表
 * http://www.ntec.gov.tw/files/15-1010-23431,c2520-1.php
 * 
 * 雲林縣鄉鎮市民代表
 * http://www.ylec.gov.tw/files/15-1012-23466,c5403-1.php
 * 
 * 嘉義縣各鄉（鎮、市）民代表會代表
 * http://www.cycec.gov.tw/files/15-1013-23406,c4681-1.php
 * 
 * 屏東縣各鄉（鎮、市）民代表
 * http://www.ptec.gov.tw/files/15-1014-23509,c5218-1.php
 * 
 * 臺東縣第19屆鄉鎮市民代表選舉
 * http://www.ttec.gov.tw/ezfiles/15/1015/attach/39/20100401121714.doc
 * 
 * 花蓮縣第20屆鄉鎮市民代表
 * http://www.hlec.gov.tw/files/15-1016-22127,c2727-1.php
 * 
 * 澎湖縣第20屆（馬公市第10屆）鄉市民代表
 * http://www.phec.gov.tw/files/15-1020-23445,c5278-1.php
 * 
 * 連江縣鄉民代表
 * http://www.lcec.gov.tw/ezfiles/22/1022/img/182/164064890.doc
 * 
 * 金門縣第 11 屆(烏坵鄉第 9 屆)鄉（鎮）民代表
 * http://www.kmec.gov.tw/files/15-1021-23465,c5288-1.php
 * 
 * 直轄市議員、縣(市) 議員
 * http://web.cec.gov.tw/files/13-1000-23452.php
 * 
 * 新北市第1屆烏來區長、烏來區民代表
 * http://www.tpcec.gov.tw/files/15-1002-23439,c5180-1.php
 * 
 * 桃園市復興區第1屆區民代表
 * http://www.tyec.gov.tw/files/15-1007-23414,c4900-1.php
 * 
 * 臺中市和平區第1屆區民代表
 * http://www.tcec.gov.tw/ezfiles/3/1003/attach/72/pta_18093_1876285_91745.pdf
 * 
 * 高雄市山地原住民區第1屆區民代表
 * http://www.khec.gov.tw/files/15-1005-23446,c5310-1.php
 */

class ElectionShell extends AppShell {

    public $uses = array('Election');

    public function main() {
        $this->generateKeywords();
    }

    public function generateKeywords() {
        $nodes = $this->Election->find('list', array(
            'conditions' => array('rght - lft = 1'),
            'fields' => array('id', 'id'),
        ));
        foreach ($nodes AS $nodeId) {
            $path = $this->Election->getPath($nodeId, array('id', 'name'));
            $this->Election->save(array('Election' => array(
                    'id' => $nodeId,
                    'keywords' => implode(',', Set::extract('{n}.Election.name', $path)),
            )));
        }
    }

    /*
     * 計算 同額競選 名單
     */

    public function quota_match() {
        $elections = $this->Election->find('all', array(
            'fields' => array(
                'Election.id', 'Election.quota',
                '(SELECT COUNT(*) FROM candidates_elections CE INNER JOIN candidates C ON C.id = CE.Candidate_id WHERE CE.Election_id = Election.id AND C.stage = 1 AND C.active_id IS NULL) AS n'
            ),
            'conditions' => array(
                'Election.rght - Election.lft = 1',
            ),
            'order' => array('Election.lft' => 'ASC'),
        ));
        $fh = fopen(__DIR__ . '/data/2014_quota_match.csv', 'w');
        fputcsv($fh, array('選區', '姓名', '政黨', '候選人網址', '選區網址'));
        foreach ($elections AS $election) {
            if ($election['Election']['quota'] === $election[0]['n']) {
                $path = implode(' > ', Set::extract('{n}.Election.name', $this->Election->getPath($election['Election']['id'], array('name'))));
                $candidates = $this->Election->Candidate->find('all', array(
                    'joins' => array(
                        array(
                            'table' => 'candidates_elections',
                            'alias' => 'CandidatesElection',
                            'type' => 'inner',
                            'conditions' => array('CandidatesElection.Candidate_id = Candidate.id'),
                        ),
                    ),
                    'conditions' => array(
                        'Candidate.active_id IS NULL',
                        'Candidate.stage' => '1',
                        'CandidatesElection.Election_id' => $election['Election']['id'],
                    ),
                    'fields' => array('Candidate.id', 'Candidate.name', 'Candidate.party'),
                ));
                foreach ($candidates AS $candidate) {
                    fputcsv($fh, array(
                        $path,
                        $candidate['Candidate']['name'],
                        $candidate['Candidate']['party'],
                        'http://k.olc.tw/elections/candidates/view/' . $candidate['Candidate']['id'],
                        'http://k.olc.tw/elections/candidates/index/' . $election['Election']['id'],
                    ));
                }
            }
        }
        fclose($fh);
    }

    public function quota_match_links() {
        $elections = $this->Election->find('all', array(
            'fields' => array(
                'Election.id', 'Election.quota',
                '(SELECT COUNT(*) FROM candidates_elections CE INNER JOIN candidates C ON C.id = CE.Candidate_id WHERE CE.Election_id = Election.id AND C.stage = 1 AND C.active_id IS NULL) AS n'
            ),
            'conditions' => array(
                'Election.rght - Election.lft = 1',
            ),
            'order' => array('Election.lft' => 'ASC'),
        ));
        $fh = fopen(TMP . '2014_quota_match.txt', 'w');
        foreach ($elections AS $election) {
            if ($election['Election']['quota'] === $election[0]['n']) {
                $path = implode(' > ', Set::extract('{n}.Election.name', $this->Election->getPath($election['Election']['id'], array('name'))));
                if (false === strpos($path, '村里')) {
                    $candidates = $this->Election->Candidate->find('all', array(
                        'joins' => array(
                            array(
                                'table' => 'candidates_elections',
                                'alias' => 'CandidatesElection',
                                'type' => 'inner',
                                'conditions' => array('CandidatesElection.Candidate_id = Candidate.id'),
                            ),
                        ),
                        'conditions' => array(
                            'Candidate.active_id IS NULL',
                            'Candidate.stage' => '1',
                            'CandidatesElection.Election_id' => $election['Election']['id'],
                        ),
                        'fields' => array('Candidate.id', 'Candidate.name', 'Candidate.party'),
                    ));
                    $line = "<li><a href=\"http://k.olc.tw/elections/candidates/index/{$election['Election']['id']}\" target=\"_blank\">{$path}</a>： ";
                    $cLinks = array();
                    foreach ($candidates AS $candidate) {
                        $cLinks[] = "<a href=\"'http://k.olc.tw/elections/candidates/view/{$candidate['Candidate']['id']}\" target=\"_blank\">{$candidate['Candidate']['name']}({$candidate['Candidate']['party']})</a>";
                    }
                    $line .= implode(', ', $cLinks) . "</li>\n";
                    fputs($fh, $line);
                }
            }
        }
        fclose($fh);
    }

    public function quota_export() {
        $root = $this->Election->find('first', array(
            'conditions' => array(
                'name' => '2014',
                'parent_id IS NULL',
            ),
        ));
        $elections = $this->Election->find('all', array(
            'conditions' => array(
                'parent_id' => $root['Election']['id'],
                'name' => array('縣市議員', '直轄市議員', '鄉鎮市民代表', '直轄市山地原住民區民代表'),
            ),
        ));
        $exportPath = __DIR__ . '/data/2014_quota';
        if (!file_exists($exportPath)) {
            mkdir($exportPath, 0777, true);
        }
        foreach ($elections AS $election) {
            $exportFile = "{$exportPath}/{$election['Election']['name']}.csv";
            $nodes = $this->Election->find('all', array(
                'conditions' => array(
                    'Election.rght - Election.lft = 1',
                    'Election.lft >' => $election['Election']['lft'],
                    'Election.rght <' => $election['Election']['rght'],
                ),
                'order' => array(
                    'Election.lft' => 'ASC'
                ),
            ));
            $fh = fopen($exportFile, 'w');
            fputcsv($fh, array('選區', '名額', '婦女', 'id'));
            foreach ($nodes AS $node) {
                $parents = $this->Election->getPath($node['Election']['id'], array('name'));
                unset($parents[0]);
                unset($parents[1]);
                $parents = implode(' > ', Set::extract('{n}.Election.name', $parents));
                fputcsv($fh, array($parents, $node['Election']['quota'], $node['Election']['quota_women'], $node['Election']['id']));
            }
            fclose($fh);
        }
    }

    /*
     * ALTER TABLE  `elections` ADD  `quota` INT( 10 ) NOT NULL DEFAULT  '0',
      ADD  `quota_women` INT( 10 ) NOT NULL DEFAULT  '0';
     */

    public function quota_import() {
        $targets = array('縣市議員', '直轄市議員', '鄉鎮市民代表', '直轄市山地原住民區民代表');
        $importPath = __DIR__ . '/data/2014_quota';
        $sql = array(
            'UPDATE elections SET quota = 1;'
        );
        foreach ($targets AS $target) {
            $importFile = "{$importPath}/{$target}.csv";
            if (file_exists($importFile)) {
                $fh = fopen($importFile, 'r');
                fgetcsv($fh, 512);
                while ($line = fgetcsv($fh, 2048)) {
                    if (!empty($line[3])) {
                        $sql[] = "UPDATE elections SET quota = {$line[1]}, quota_women = {$line[2]} WHERE id = '{$line[3]}';";
                    }
                }
                fclose($fh);
            }
        }
        file_put_contents(TMP . 'quota.sql', implode("\n", $sql));
    }

}
