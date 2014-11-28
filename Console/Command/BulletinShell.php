<?php

class BulletinShell extends AppShell {

    public $uses = array('Election');

    public function main() {
        $this->bulletinMapImport();
    }

    public function bulletinMapImport() {
        $bulletinTasks = $this->Election->Bulletin->find('list', array(
            'conditions' => array(
                'Bulletin.count_elections' => 0,
            ),
            'fields' => array('id', 'id'),
        ));
        $fh = fopen(__DIR__ . '/data/bulletin_map.csv', 'r');
        fgets($fh, 512);
        while ($line = fgetcsv($fh, 2048)) {
            if (isset($line[1]) && isset($bulletinTasks[$line[1]]) && !empty($line[2])) {
                $line[2] = explode('|', $line[2]);
                foreach ($line[2] AS $electionId) {
                    $this->Election->BulletinsElection->create();
                    $this->Election->BulletinsElection->save(array('BulletinsElection' => array(
                            'Bulletin_id' => $line[1],
                            'Election_id' => $electionId,
                    )));
                }
                $this->Election->Bulletin->id = $line[1];
                $this->Election->Bulletin->saveField('count_elections', count($line[2]));
            }
        }
    }

    public function import_ptec() {
        $bulletinFile = '/home/kiang/public_html/bulletin.cec.gov.tw/Console/Command/data/bulletin_103_ptec.csv';
        $dbBulletins = $this->Election->Bulletin->find('list', array(
            'fields' => array('id', 'count_elections'),
        ));
        $townNodes = $this->Election->find('list', array(
            'conditions' => array(
                'Election.parent_id' => '53c020a0-f5a4-4054-836f-5c5aacb5b862',
            ),
            'fields' => array('name', 'id'),
        ));
        $townRepNodes = $this->Election->find('list', array(
            'conditions' => array(
                'Election.parent_id' => '53c020ce-0798-4ae9-af49-5c5aacb5b862',
            ),
            'fields' => array('name', 'id'),
        ));
        foreach ($townRepNodes AS $name => $id) {
            $list = $this->Election->find('list', array(
                'conditions' => array(
                    'Election.parent_id' => $id,
                ),
                'fields' => array('name', 'id'),
            ));
            $townRepNodes[$name] = array();
            foreach ($list AS $lName => $lId) {
                $lName = preg_replace('/[^0-9]/', '', $lName);
                $townRepNodes[$name][$lName] = $lId;
            }
        }
        $fh = fopen($bulletinFile, 'r');
        while ($line = fgetcsv($fh, 2048)) {
            if (!isset($dbBulletins[$line[2]])) {
                $this->Election->Bulletin->create();
                $this->Election->Bulletin->save(array('Bulletin' => array(
                        'id' => $line[2],
                        'name' => $line[0],
                        'source' => $line[1],
                )));
                $dbBulletins[$line[2]] = 0;
            }
            if (isset($dbBulletins[$line[2]]) && $dbBulletins[$line[2]] == 0) {
                if (false !== strpos($line[0], '代表選舉')) {
                    $line[0] = str_replace(array('103', '第一', '第二', '第三', '第四', '第五'), array('', '01', '02', '03', '04', '05'), $line[0]);
                    //鄉鎮市民代表
                    foreach ($townRepNodes AS $town => $nodes) {
                        foreach ($nodes AS $node => $id) {
                            if (false !== strpos($line[0], $town) && false !== strpos($line[0], $node)) {
                                $this->Election->BulletinsElection->create();
                                $this->Election->BulletinsElection->save(array('BulletinsElection' => array(
                                        'Election_id' => $id,
                                        'Bulletin_id' => $line[2],
                                )));
                                $this->Election->Bulletin->updateAll(array(
                                    'Bulletin.count_elections' => 'Bulletin.count_elections + 1',
                                    'Bulletin.modified' => 'now()',
                                        ), array('Bulletin.id' => $line[2]));
                                $this->Election->updateAll(array(
                                    'Election.bulletin_key' => "'{$line[2]}'"
                                        ), array(
                                    'Election.id' => $id,
                                    'OR' => array(
                                        'Election.bulletin_key !=' => $line[2],
                                        'Election.bulletin_key IS NULL',
                                    ),
                                ));
                            }
                        }
                    }
                } else {
                    //鄉鎮市長
                    $nodeMatched = false;
                    foreach ($townNodes AS $town => $id) {
                        if (false !== strpos($line[0], $town)) {
                            $this->Election->BulletinsElection->create();
                            $this->Election->BulletinsElection->save(array('BulletinsElection' => array(
                                    'Election_id' => $id,
                                    'Bulletin_id' => $line[2],
                            )));
                            $this->Election->Bulletin->updateAll(array(
                                'Bulletin.count_elections' => 'Bulletin.count_elections + 1',
                                'Bulletin.modified' => 'now()',
                                    ), array('Bulletin.id' => $line[2]));
                            $this->Election->updateAll(array(
                                'Election.bulletin_key' => "'{$line[2]}'"
                                    ), array(
                                'Election.id' => $id,
                                'OR' => array(
                                    'Election.bulletin_key !=' => $line[2],
                                    'Election.bulletin_key IS NULL',
                                ),
                            ));
                        }
                    }
                }
            }
        }
        fclose($fh);
    }

    public function dbFix() {
        $eTree = Hash::nest($this->Election->children('53c021ec-e11c-49be-b6c8-5c5aacb5b862', false, array('id', 'parent_id', 'name')));
        $elections = array();
        foreach ($eTree AS $town) {
            foreach ($town['children'] AS $cunli) {
                $elections[$cunli['Election']['id']] = "{$town['Election']['name']} > {$cunli['Election']['name']}";
            }
        }
        $bulletins = $this->Election->Bulletin->find('all', array(
            'conditions' => array(
                'count_elections' => 0,
                'name like' => '%雲林縣 > 村里長%',
            ),
        ));
        foreach ($bulletins AS $bulletin) {
            $bulletinMatched = false;
            foreach ($elections AS $electionId => $filter) {
                if (false === $bulletinMatched && false !== strpos($bulletin['Bulletin']['name'], $filter)) {
                    $bulletinMatched = true;
                    if ($this->Election->Bulletin->save(array('Bulletin' => array(
                                    'id' => $bulletin['Bulletin']['id'],
                                    'count_elections' => 1,
                        )))) {
                        $this->Election->save(array('Election' => array(
                                'id' => $electionId,
                                'bulletin_key' => $bulletin['Bulletin']['id'],
                        )));
                        $this->Election->BulletinsElection->create();
                        $this->Election->BulletinsElection->save(array('BulletinsElection' => array(
                                'Election_id' => $electionId,
                                'Bulletin_id' => $bulletin['Bulletin']['id'],
                        )));
                    }
                }
            }
        }
    }

    public function import() {
        $bulletinFile = '/home/kiang/public_html/bulletin.cec.gov.tw/Console/Command/data/bulletin_103.csv';
        $dbBulletins = $this->Election->Bulletin->find('list', array(
            'fields' => array('id', 'id'),
        ));
        $fh = fopen($bulletinFile, 'r');
        while ($line = fgetcsv($fh, 2048)) {
            if (!isset($dbBulletins[$line[2]])) {
                $this->Election->Bulletin->create();
                $this->Election->Bulletin->save(array('Bulletin' => array(
                        'id' => $line[2],
                        'name' => $line[0],
                        'source' => $line[1],
                )));
                $dbBulletins[$line[2]] = 0;
            }
        }
        fclose($fh);
    }

    public function matchBulletin() {
        $csvMap = array(
            '市議員' => array(
                '縣市議員',
                '直轄市議員',
            ),
            '市議員選舉公報' => array(
                '縣市議員',
                '直轄市議員',
            ),
            '市長選舉公報' => array(
                '縣市長',
                '直轄市長',
            ),
            '市長' => array(
                '縣市長',
                '直轄市長',
            ),
            '里長' => array(
                '村里長',
            ),
            '村里長' => array(
                '村里長',
            ),
            '里長選舉公報' => array(
                '村里長',
            ),
            '縣議員' => array(
                '縣市議員',
            ),
            '鄉鎮市民代表' => array(
                '鄉鎮市民代表',
            ),
            '鄉市民代表' => array(
                '鄉鎮市民代表',
            ),
            '鄉鎮市長' => array(
                '鄉鎮市長',
            ),
            '鄉市長' => array(
                '鄉鎮市長',
            ),
            '直轄市議員' => array(
                '直轄市議員',
            ),
            '直轄市長' => array(
                '縣市長',
                '直轄市長',
            ),
            '1-市長' => array(
                '直轄市長',
            ),
            '2、市議員' => array(
                '縣市議員',
                '直轄市議員',
            ),
            '2-市議員' => array(
                '縣市議員',
                '直轄市議員',
            ),
            '議員' => array(
                '縣市議員',
                '直轄市議員',
            ),
            '原住民區民代表' => array(
                '直轄市山地原住民區民代表',
            ),
            '4-原住民區民代表' => array(
                '直轄市山地原住民區民代表',
            ),
            '原住民區長' => array(
                '直轄市山地原住民區長',
            ),
            '3-原住民區長' => array(
                '直轄市山地原住民區長',
            ),
            '縣長' => array(
                '縣市長',
            ),
            '01縣長' => array(
                '縣市長',
            ),
            '02縣議員' => array(
                '縣市議員',
            ),
            '03鄉鎮市長' => array(
                '鄉鎮市長',
            ),
            '04鄉鎮市民代表' => array(
                '鄉鎮市民代表',
            ),
            '05村里長' => array(
                '村里長',
            ),
            '村里長1' => array(
                '村里長',
            ),
            '市議員選舉公報資料夾' => array(
                '縣市議員',
                '直轄市議員',
            ),
            '市長選舉公報資料夾' => array(
                '縣市長',
                '直轄市長',
            ),
        );
        $rootNode = $this->Election->find('first', array(
            'conditions' => array('Election.name' => '2014'),
        ));
        $electionTypes = $this->Election->find('list', array(
            'conditions' => array('Election.parent_id' => $rootNode['Election']['id']),
            'fields' => array('id', 'name'),
        ));
        $dbMap = $nodes = $subKeys = array();
        foreach ($electionTypes AS $electionId => $electionName) {
            $cities = $this->Election->find('all', array(
                'conditions' => array('Election.parent_id' => $electionId),
            ));
            foreach ($cities AS $city) {
                $cityKey = "{$city['Election']['name']}{$electionName}";
                $dbMap[$cityKey] = $city['Election']['id'];

                if ($electionName !== '鄉鎮市民代表' && $electionName !== '村里長') {
                    $nodes[$city['Election']['id']] = $this->Election->find('list', array(
                        'conditions' => array(
                            'Election.lft >=' => $city['Election']['lft'],
                            'Election.rght <=' => $city['Election']['rght'],
                            'Election.rght - Election.lft = 1',
                        ),
                        'fields' => array('id', 'name'),
                    ));
                } else {
                    $towns = $this->Election->find('list', array(
                        'conditions' => array('parent_id' => $city['Election']['id']),
                        'fields' => array('id', 'name'),
                    ));
                    $nodes[$city['Election']['id']] = $subKeys[$city['Election']['id']] = array();
                    $lNodes = $this->Election->find('all', array(
                        'conditions' => array(
                            'Election.lft >=' => $city['Election']['lft'],
                            'Election.rght <=' => $city['Election']['rght'],
                            'Election.rght - Election.lft = 1',
                        ),
                        'fields' => array('id', 'parent_id', 'name'),
                    ));
                    foreach ($lNodes AS $lNode) {
                        $nodes[$city['Election']['id']][$lNode['Election']['id']] = $lNode['Election']['name'];
                        $subKeys[$city['Election']['id']][$lNode['Election']['id']] = $towns[$lNode['Election']['parent_id']];
                    }
                }
                foreach ($nodes[$city['Election']['id']] AS $k => $v) {
                    $qPos = strpos($v, '[');
                    if (false !== $qPos) {
                        $nodes[$city['Election']['id']][$k] = substr($v, 0, strpos($v, '選'));
                    }
                }
            }
        }

        /*
         * prefix provides records that fixed manually, just skip and merge the result
         */
        $prefixes = array();
        $pFh = fopen(__DIR__ . '/data/bulletin_map_prefix.csv', 'r');
        fgets($pFh, 512);
        while ($line = fgetcsv($pFh, 2048)) {
            /*
             * 0 - name from bulletin
             * 1 - bulletin key
             * 2 - election id separated by |
             */
            $prefixes[$line[1]] = explode('|', $line[2]);
        }
        fclose($pFh);

        $bulletinFile = '/home/kiang/public_html/bulletin.cec.gov.tw/Console/Command/data/bulletin_103.csv';
        $ebMap = $bulletins = array();
        $fh = fopen($bulletinFile, 'r');
        while ($line = fgetcsv($fh, 2048)) {
            if (isset($prefixes[$line[2]])) {
                $bulletins[$line[2]] = array(
                    'line' => $line,
                    'elections' => $prefixes[$line[2]],
                );
                foreach ($prefixes[$line[2]] AS $nodeId) {
                    $ebMap[$nodeId] = $line[2];
                }
                continue;
            }
            $parts = explode(' > ', $line[0]);
            if ($parts[0] === '桃園市1') {
                continue;
            }
            if ($parts[1] === '紙本公告') {
                foreach ($parts AS $pK => $pV) {
                    if ($pK > 1) {
                        $parts[$pK - 1] = $pV;
                    }
                }
            }
            end($parts);
            $partLastKey = key($parts);
            $scopeId = false;
            foreach ($csvMap[$parts[1]] AS $csvKey) {
                if (false === $scopeId) {
                    $csvKey = $parts[0] . $csvKey;
                    if (isset($dbMap[$csvKey])) {
                        $scopeId = $dbMap[$csvKey];
                    }
                }
            }
            if (false === $scopeId) {
                echo "not found\n";
                print_r($line);
                exit();
            } else {
                $bulletins[$line[2]] = array(
                    'line' => $line,
                    'elections' => array(),
                );
                $txtFile = "/home/kiang/public_html/bulletin.cec.gov.tw/Console/Command/data/txt_103/{$line[2]}.csv";
                $txt = '';
                if (file_exists($txtFile)) {
                    $txt = file_get_contents($txtFile);
                }
                if (mb_substr($line[0], -1) === '長') {
                    $line[0] = mb_substr($line[0], 0, -2, 'utf-8');
                }
                foreach ($nodes[$scopeId] AS $nodeId => $nodeName) {
                    $currentNodeMatched = false;
                    if (false !== strpos($line[0], $nodeName)) {
                        $currentNodeMatched = $line[2];
                    } else {
                        $newHaystack = str_replace(array('第'), array('第0'), $line[0]);
                        if (false !== strpos($newHaystack, $nodeName)) {
                            $currentNodeMatched = $line[2];
                        }
                    }
                    if (false === $currentNodeMatched) {
                        if (false !== strpos($txt, $nodeName)) {
                            $currentNodeMatched = $line[2];
                        }
                    }
                    if (false !== $currentNodeMatched) {
                        if (isset($subKeys[$scopeId][$nodeId])) {
                            $subKeyMatched = false;
                            if (false !== strpos($line[0], $subKeys[$scopeId][$nodeId])) {
                                $subKeyMatched = true;
                            }
                            if ($subKeyMatched) {
                                $ebMap[$nodeId] = $currentNodeMatched;
                            } else {
                                $currentNodeMatched = false;
                            }
                        } else {
                            $ebMap[$nodeId] = $currentNodeMatched;
                        }
                    }
                    if (false !== $currentNodeMatched) {
                        $bulletins[$line[2]]['elections'][] = $nodeId;
                    }
                }
                if (empty($bulletins[$line[2]]['elections']) && preg_match('/(村|里)/', $line[0]) && false === strpos($line[0], '背')
                ) {
                    foreach ($nodes[$scopeId] AS $nodeId => $nodeName) {
                        $nodeName = mb_substr($nodeName, 0, -1, 'utf-8');
                        if (false !== strpos($parts[$partLastKey], $nodeName) || false !== strpos($txt, $nodeName)) {
                            $ebMap[$nodeId] = $line[2];
                            $bulletins[$line[2]]['elections'][] = $nodeId;
                        }
                    }
                }
            }
        }
        fclose($fh);
        $fh = fopen(TMP . 'bulletin.sql', 'w');
        $bulletinKeys = $this->Election->find('list', array(
            'conditions' => array(
                'Election.bulletin_key IS NOT NULL'
            ),
            'fields' => array('Election.id', 'Election.bulletin_key'),
        ));
        foreach ($ebMap AS $eId => $bId) {
            if (isset($bulletinKeys[$eId]) && $bulletinKeys[$eId] !== $bId) {
                fputs($fh, "UPDATE elections SET bulletin_key = '{$bId}' WHERE id = '{$eId}';");
            }
        }
        fclose($fh);
        $bFh = fopen(__DIR__ . '/data/bulletin_map.csv', 'w');
        fputcsv($bFh, array(
            'bulletin_name',
            'bulletin_key',
            'election_id',
        ));
        foreach ($bulletins AS $bId => $data) {
            fputcsv($bFh, array(
                $data['line'][0],
                $bId,
                implode('|', $data['elections']),
            ));
        }
        fclose($bFh);
    }

}
