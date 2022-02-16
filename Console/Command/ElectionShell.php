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

class ElectionShell extends AppShell
{

    public $uses = array('Election');

    public function main()
    {
        $this->cunliFix();
    }

    public function cunliFix()
    {
        $json = json_decode(file_get_contents('https://github.com/kiang/taiwan_basecode/raw/gh-pages/cunli/topo/20211214.json'), true);
        $ref = array();
        foreach ($json['objects']['20211214']['geometries'] as $obj) {
            if (empty($obj['properties']['VILLNAME'])) {
                continue;
            }
            switch ($obj['properties']['VILLNAME']) {
                case '石[曹]里':
                    $obj['properties']['VILLNAME'] = '石嘈里';
                    break;
                case '[那]拔里':
                    $obj['properties']['VILLNAME'] = '𦰡拔里';
                    break;
            }
            $area = $obj['properties']['COUNTYNAME'] . $obj['properties']['TOWNNAME'];
            if (!isset($ref[$area])) {
                $ref[$area] = array();
            }
            $ref[$area][$obj['properties']['VILLNAME']] = $obj['properties'];
        }
        $rootNode = $this->Election->find('first', array(
            'conditions' => array('id' => '62053692-886c-4aa7-a03b-1619acb5b862'),
        ));
        $cunliNodes = $this->Election->find('all', array(
            'conditions' => array(
                'lft >' => $rootNode['Election']['lft'],
                'rght <' => $rootNode['Election']['rght'],
                'rght - lft = 1'
            ),
            'contain' => array(
                'Area' => array('fields' => array('name', 'parent_id'))
            )
        ));
        $areaPool = array();
        foreach ($cunliNodes as $cunliNode) {
            $pathNodes = $this->Election->getPath($cunliNode['Election']['id']);
            $area = $pathNodes[2]['Election']['name'] . $pathNodes[3]['Election']['name'];

            if (!isset($areaPool[$area])) {
                $areaLinks = $this->Election->AreasElection->find('list', array(
                    'fields' => array(
                        'AreasElection.Election_id', 'AreasElection.Election_id',
                    ),
                    'conditions' => array(
                        'AreasElection.Area_id' => $cunliNode['Area'][0]['AreasElection']['Area_id'],
                        'AreasElection.id !=' => $cunliNode['Area'][0]['AreasElection']['id'],
                    ),
                ));
                $areaPool[$area] = array(
                    'election_parent' => $pathNodes[3]['Election']['id'],
                    'area_parent' => $cunliNode['Area'][0]['parent_id'],
                    'links' => $areaLinks,
                );
            }

            if (isset($ref[$area][$pathNodes[4]['Election']['name']])) {
                unset($ref[$area][$pathNodes[4]['Election']['name']]);
            } else {
                if (isset($areaPool[$area])) {
                    $areaLinks = $this->Election->AreasElection->find('list', array(
                        'fields' => array(
                            'AreasElection.Election_id', 'AreasElection.Election_id',
                        ),
                        'conditions' => array(
                            'AreasElection.Area_id' => $cunliNode['Area'][0]['AreasElection']['Area_id'],
                            'AreasElection.id !=' => $cunliNode['Area'][0]['AreasElection']['id'],
                        ),
                    ));
                    $areaPool[$area] = array(
                        'election_parent' => $pathNodes[3]['Election']['id'],
                        'area_parent' => $cunliNode['Area'][0]['parent_id'],
                        'links' => $areaLinks,
                    );
                }
                // to delete election/area node
                $this->Election->delete($cunliNode['Area'][0]['AreasElection']['Election_id']);
                $this->Election->Area->delete($cunliNode['Area'][0]['AreasElection']['Area_id']);
                $this->Election->AreasElection->delete($cunliNode['Area'][0]['AreasElection']['id']);
            }
        }

        foreach ($ref as $area => $cunlis) {
            foreach ($cunlis as $cunliNode) {
                $link = [];
                // to create election/area node
                $this->Election->create();
                $this->Election->save(array('Election' => array(
                    'parent_id' => $areaPool[$area]['election_parent'],
                    'name' => $cunliNode['VILLNAME'],
                    'population' => 0,
                    'population_electors' => 0,
                    'quota_women' => 0,
                    'quota' => 1,
                    'keywords' => $cunliNode['VILLNAME'],
                )));
                $link['Election_id'] = $this->Election->getInsertID();
                $this->Election->Area->create();
                $this->Election->Area->save(array('Area' => array(
                    'parent_id' => $areaPool[$area]['area_parent'],
                    'code' => $cunliNode['VILLCODE'],
                    'name' => $cunliNode['VILLNAME'],
                    'is_area' => 1,
                    'population' => 0,
                    'population_electors' => 0,
                    'keywords' => $cunliNode['VILLNAME'],
                )));
                $link['Area_id'] = $this->Election->Area->getInsertID();
                $this->Election->AreasElection->create();
                $this->Election->AreasElection->save(array('AreasElection' => $link));
                if(!empty($areaPool[$area]['links'])) {
                    foreach($areaPool[$area]['links'] AS $linkId) {
                        $link['Election_id'] = $linkId;
                        $this->Election->AreasElection->create();
                        $this->Election->AreasElection->save(array('AreasElection' => $link));
                    }
                }
            }
        }
    }

    public function duplicateCandidates()
    {
        $rootSource = $this->Election->find('first', array(
            'conditions' => array(
                'Election.id' => '54d9c44b-80a4-4cbe-b677-6b30acb5b862',
            ),
        ));
        $sourceTree = $this->Election->find('threaded', array(
            'fields' => array('id', 'parent_id', 'name'),
            'conditions' => array(
                'Election.lft >' => $rootSource['Election']['lft'],
                'Election.rght <' => $rootSource['Election']['rght'],
            ),
        ));
        $unsetFields = array('id', 'active_id', 'created', 'modified', 'no');
        $sourceNodes = array();
        foreach ($sourceTree as $node) {
            if (!empty($node['children'])) {
                foreach ($node['children'] as $child) {
                    $nodeKey = "{$node['Election']['name']}{$child['Election']['name']}";
                    $nodeKey = str_replace('桃園縣', '桃園市', $nodeKey);
                    $sourceNodes[$nodeKey] = $this->Election->Candidate->find('all', array(
                        'conditions' => array(
                            'Candidate.election_id' => $child['Election']['id'],
                            'Candidate.stage' => '2',
                            'Candidate.active_id IS NULL',
                        ),
                    ));
                }
            } else {
                $nodeKey = "{$node['Election']['name']}";
                $sourceNodes[$nodeKey] = $this->Election->Candidate->find('all', array(
                    'conditions' => array(
                        'Candidate.election_id' => $node['Election']['id'],
                        'Candidate.stage' => '2',
                        'Candidate.active_id IS NULL',
                    ),
                ));
            }
        }

        $rootTarget = $this->Election->find('first', array(
            'conditions' => array(
                'Election.id' => '55085e1a-c494-40e0-ba31-2f916ab936af',
            ),
        ));
        $targetTree = $this->Election->find('threaded', array(
            'fields' => array('id', 'parent_id', 'name'),
            'conditions' => array(
                'Election.lft >' => $rootTarget['Election']['lft'],
                'Election.rght <' => $rootTarget['Election']['rght'],
            ),
        ));
        $path = WWW_ROOT . 'media';
        foreach ($targetTree as $node) {
            if (!empty($node['children'])) {
                foreach ($node['children'] as $child) {
                    $nodeKey = "{$node['Election']['name']}{$child['Election']['name']}";
                    foreach ($sourceNodes[$nodeKey] as $candidate) {
                        foreach ($unsetFields as $unsetField) {
                            unset($candidate['Candidate'][$unsetField]);
                        }
                        $candidate['Candidate']['election_id'] = $child['Election']['id'];
                        $candidate['Candidate']['stage'] = '0';
                        $candidate['Candidate']['vote_count'] = '0';
                        $candidate['Candidate']['is_present'] = '1';

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
                        $this->Election->Candidate->create();
                        $this->Election->Candidate->save($candidate);
                    }
                }
            } else {
                $nodeKey = "{$node['Election']['name']}";
                foreach ($sourceNodes[$nodeKey] as $candidate) {
                    foreach ($unsetFields as $unsetField) {
                        unset($candidate['Candidate'][$unsetField]);
                    }
                    $candidate['Candidate']['election_id'] = $node['Election']['id'];
                    $candidate['Candidate']['stage'] = '0';
                    $candidate['Candidate']['vote_count'] = '0';
                    $candidate['Candidate']['is_present'] = '1';

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
                    $this->Election->Candidate->create();
                    $this->Election->Candidate->save($candidate);
                }
            }
        }
    }

    public function extractTree()
    {
        $nodes = $this->Election->find('all', array(
            'fields' => array('id', 'parent_id', 'name'),
            'conditions' => array(
                'Election.name LIKE' => '%第%',
                'Election.name NOT LIKE' => '第%',
            ),
        ));
        $newTree = array();
        foreach ($nodes as $node) {
            $nameParts = explode('第', $node['Election']['name']);
            if (!isset($newTree[$node['Election']['parent_id']])) {
                $newTree[$node['Election']['parent_id']] = array();
            }
            if (!isset($newTree[$node['Election']['parent_id']][$nameParts[0]])) {
                $this->Election->create();
                if ($this->Election->save(array('Election' => array(
                    'name' => $nameParts[0],
                    'parent_id' => $node['Election']['parent_id'],
                )))) {
                    $newTree[$node['Election']['parent_id']][$nameParts[0]] = $this->Election->getInsertID();
                }
            }
            if (isset($newTree[$node['Election']['parent_id']][$nameParts[0]])) {
                $this->Election->id = $node['Election']['id'];
                $this->Election->save(array('Election' => array(
                    'name' => '第' . $nameParts[1],
                    'parent_id' => $newTree[$node['Election']['parent_id']][$nameParts[0]],
                )));
            }
        }
    }

    public function dumpAreas()
    {
        $rootNode = $this->Election->find('first', array(
            'conditions' => array(
                'Election.id' => '55085e1a-c494-40e0-ba31-2f916ab936af',
            )
        ));
        $nodes = $this->Election->find('all', array(
            'conditions' => array(
                'Election.lft >' => $rootNode['Election']['lft'],
                'Election.rght <' => $rootNode['Election']['rght'],
                'Election.rght - Election.lft = 1'
            ),
            'contain' => array(
                'Area' => array(
                    'fields' => array('id'),
                    'order' => array('Area.lft' => 'ASC')
                ),
            ),
            'fields' => array('id'),
        ));
        $fh = fopen(__DIR__ . '/data/2016_election/elections_areas.csv', 'w');
        fputcsv($fh, array('選區', '行政區'));
        foreach ($nodes as $node) {
            $electionPath = $this->Election->getPath($node['Election']['id'], array('name'));
            $election = implode(' > ', Set::extract('{n}.Election.name', $electionPath));
            foreach ($node['Area'] as $area) {
                $areaPath = $this->Election->Area->getPath($area['id'], array('name'));
                $area = implode(' > ', Set::extract('{n}.Area.name', $areaPath));
                fputcsv($fh, array($election, $area));
            }
        }
        fclose($fh);
    }

    public function generateKeywords()
    {
        $nodes = $this->Election->find('list', array(
            'conditions' => array('rght - lft = 1'),
            'fields' => array('id', 'id'),
        ));
        foreach ($nodes as $nodeId) {
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

    public function quota_match()
    {
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
        foreach ($elections as $election) {
            if ($election['Election']['quota'] === $election[0]['n']) {
                $path = implode(' > ', Set::extract('{n}.Election.name', $this->Election->getPath($election['Election']['id'], array('name'))));
                $candidates = $this->Election->Candidate->find('all', array(
                    'conditions' => array(
                        'Candidate.active_id IS NULL',
                        'Candidate.stage' => '1',
                        'Candidate.election_id' => $election['Election']['id'],
                    ),
                    'fields' => array('Candidate.id', 'Candidate.name', 'Candidate.party'),
                ));
                foreach ($candidates as $candidate) {
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

    public function quota_match_links()
    {
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
        foreach ($elections as $election) {
            if ($election['Election']['quota'] === $election[0]['n']) {
                $path = implode(' > ', Set::extract('{n}.Election.name', $this->Election->getPath($election['Election']['id'], array('name'))));
                if (false === strpos($path, '村里')) {
                    $candidates = $this->Election->Candidate->find('all', array(
                        'conditions' => array(
                            'Candidate.active_id IS NULL',
                            'Candidate.stage' => '1',
                            'Candidate.election_id' => $election['Election']['id'],
                        ),
                        'fields' => array('Candidate.id', 'Candidate.name', 'Candidate.party'),
                    ));
                    $line = "<li><a href=\"http://k.olc.tw/elections/candidates/index/{$election['Election']['id']}\" target=\"_blank\">{$path}</a>： ";
                    $cLinks = array();
                    foreach ($candidates as $candidate) {
                        $cLinks[] = "<a href=\"'http://k.olc.tw/elections/candidates/view/{$candidate['Candidate']['id']}\" target=\"_blank\">{$candidate['Candidate']['name']}({$candidate['Candidate']['party']})</a>";
                    }
                    $line .= implode(', ', $cLinks) . "</li>\n";
                    fputs($fh, $line);
                }
            }
        }
        fclose($fh);
    }

    public function quota_export()
    {
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
        foreach ($elections as $election) {
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
            foreach ($nodes as $node) {
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

    public function quota_import()
    {
        $targets = array('縣市議員', '直轄市議員', '鄉鎮市民代表', '直轄市山地原住民區民代表');
        $importPath = __DIR__ . '/data/2014_quota';
        $sql = array(
            'UPDATE elections SET quota = 1;'
        );
        foreach ($targets as $target) {
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
