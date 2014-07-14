<?php

class ImportShell extends AppShell {

    public $areas = array(
        'code2id' => array(),
        'municipalities' => array(),
        'counties' => array(),
        'towns' => array(),
        'cunlis' => array(),
    );
    public $uses = array('Election');

    public function main() {
        $this->areas();
        $this->elections();
    }

    public function elections() {
        $electionParent = $this->Election->field('id', array('name' => '2014'));
        if (empty($electionParent)) {
            $this->Election->create();
            $this->Election->save(array('Election' => array(
                    'name' => '2014',
            )));
            $electionParent = $this->Election->getInsertID();
        }
        $subTypesDb = $this->Election->find('list', array(
            'conditions' => array('parent_id' => $electionParent),
            'fields' => array('name', 'id'),
        ));
        $subTypes = array(
            '直轄市長', '直轄市議員', '直轄市山地原住民區長', '直轄市山地原住民區民代表',
            '縣市長', '縣市議員', '鄉鎮市長', '鄉鎮市民代表', '村里長'
        );
        foreach ($subTypes AS $subType) {
            if (!isset($subTypesDb[$subType])) {
                $this->Election->create();
                $this->Election->save(array('Election' => array(
                        'parent_id' => $electionParent,
                        'name' => $subType,
                )));
                $subTypesDb[$subType] = $this->Election->getInsertID();
            }
        }

        foreach ($subTypes AS $subType) {
            switch ($subType) {
                case '直轄市長':
                    $dbList = $this->Election->find('list', array(
                        'conditions' => array(
                            'parent_id' => $subTypesDb[$subType],
                        ),
                        'fields' => array('name', 'id'),
                    ));
                    foreach ($this->areas['municipalities'] AS $c) {
                        if (!isset($dbList[$c['name']])) {
                            $this->Election->create();
                            if ($this->Election->save(array('Election' => array(
                                            'parent_id' => $subTypesDb[$subType],
                                            'name' => $c['name'],
                                )))) {
                                $this->Election->AreasElection->create();
                                $this->Election->AreasElection->save(array('AreasElection' => array(
                                        'Election_id' => $this->Election->getInsertID(),
                                        'Area_id' => $c['id'],
                                )));
                            }
                        }
                    }
                    break;
                case '縣市長':
                    $dbList = $this->Election->find('list', array(
                        'conditions' => array(
                            'parent_id' => $subTypesDb[$subType],
                        ),
                        'fields' => array('name', 'id'),
                    ));
                    foreach ($this->areas['counties'] AS $c) {
                        if (!isset($dbList[$c['name']])) {
                            $this->Election->create();
                            if ($this->Election->save(array('Election' => array(
                                            'parent_id' => $subTypesDb[$subType],
                                            'name' => $c['name'],
                                )))) {
                                $this->Election->AreasElection->create();
                                $this->Election->AreasElection->save(array('AreasElection' => array(
                                        'Election_id' => $this->Election->getInsertID(),
                                        'Area_id' => $c['id'],
                                )));
                            }
                        }
                    }
                    break;
                case '鄉鎮市長':
                    $cDbList = $this->Election->find('list', array(
                        'conditions' => array(
                            'parent_id' => $subTypesDb[$subType],
                        ),
                        'fields' => array('name', 'id'),
                    ));
                    foreach ($this->areas['counties'] AS $c) {
                        if (!isset($cDbList[$c['name']])) {
                            $this->Election->create();
                            $this->Election->save(array('Election' => array(
                                    'parent_id' => $subTypesDb[$subType],
                                    'name' => $c['name'],
                            )));
                            $cDbList[$c['name']] = $this->Election->getInsertID();
                        }
                        $tDbList = $this->Election->find('list', array(
                            'conditions' => array(
                                'parent_id' => $cDbList[$c['name']],
                            ),
                            'fields' => array('name', 'id'),
                        ));
                        foreach ($this->areas['towns'][$c['id']] AS $t) {
                            if (!isset($tDbList[$t['name']])) {
                                $this->Election->create();
                                if ($this->Election->save(array('Election' => array(
                                                'parent_id' => $cDbList[$c['name']],
                                                'name' => $t['name'],
                                    )))) {
                                    $this->Election->AreasElection->create();
                                    $this->Election->AreasElection->save(array('AreasElection' => array(
                                            'Election_id' => $this->Election->getInsertID(),
                                            'Area_id' => $t['id'],
                                    )));
                                }
                            }
                        }
                    }
                    break;
                case '鄉鎮市民代表':
                    $cDbList = $this->Election->find('list', array(
                        'conditions' => array(
                            'parent_id' => $subTypesDb[$subType],
                        ),
                        'fields' => array('name', 'id'),
                    ));
                    foreach ($this->areas['counties'] AS $c) {
                        if (!isset($cDbList[$c['name']])) {
                            $this->Election->create();
                            $this->Election->save(array('Election' => array(
                                    'parent_id' => $subTypesDb[$subType],
                                    'name' => $c['name'],
                            )));
                            $cDbList[$c['name']] = $this->Election->getInsertID();
                        }
                        $tDbList = $this->Election->find('list', array(
                            'conditions' => array(
                                'parent_id' => $cDbList[$c['name']],
                            ),
                            'fields' => array('name', 'id'),
                        ));
                        foreach ($this->areas['towns'][$c['id']] AS $t) {
                            if (!isset($tDbList[$t['name']])) {
                                $this->Election->create();
                                if ($this->Election->save(array('Election' => array(
                                                'parent_id' => $cDbList[$c['name']],
                                                'name' => $t['name'],
                                    )))) {
                                    $this->Election->AreasElection->create();
                                    $this->Election->AreasElection->save(array('AreasElection' => array(
                                            'Election_id' => $this->Election->getInsertID(),
                                            'Area_id' => $t['id'],
                                    )));
                                }
                            }
                        }
                    }
                    break;
                case '直轄市議員':
                    $dbList = $this->Election->find('all', array(
                        'conditions' => array(
                            'parent_id' => $subTypesDb[$subType],
                        ),
                    ));
                    $dbList = Set::combine($dbList, '{n}.Election.name', '{n}');
                    $jsonData = json_decode(file_get_contents(__DIR__ . '/data/v20101101TxC2.json'), true);
                    $tData = json_decode(file_get_contents(__DIR__ . '/data/v20091201TxC2.json'), true);
                    foreach ($this->areas['municipalities'] AS $c) {
                        if (!isset($dbList[$c['name']])) {
                            $this->Election->create();
                            if ($this->Election->save(array('Election' => array(
                                            'parent_id' => $subTypesDb[$subType],
                                            'name' => $c['name'],
                                )))) {
                                $dbList[$c['name']] = $this->Election->read();
                            }
                        }
                        $subDbList = $this->Election->find('all', array(
                            'conditions' => array(
                                'parent_id' => $dbList[$c['name']]['Election']['id'],
                            ),
                        ));
                        $subDbList = Set::combine($subDbList, '{n}.Election.name', '{n}');

                        if (isset($jsonData[$c['name']])) {
                            foreach ($jsonData[$c['name']] AS $cityZone => $cityAreas) {
                                $cityZoneName = "{$cityZone}[{$cityAreas['type']}]";
                                if (!isset($subDbList[$cityZoneName])) {
                                    $this->Election->create();
                                    if ($this->Election->save(array('Election' => array(
                                                    'parent_id' => $dbList[$c['name']]['Election']['id'],
                                                    'name' => $cityZoneName,
                                        )))) {
                                        $subDbList[$cityZoneName] = $this->Election->read();
                                    }
                                }
                                foreach ($this->areas['towns'][$c['id']] AS $t) {
                                    if (in_array($t['name'], $cityAreas['areas'])) {
                                        if (empty($this->Election->AreasElection->field('id', array(
                                                            'Area_id' => $t['id'],
                                                            'Election_id' => $subDbList[$cityZoneName]['Election']['id'],
                                                )))) {
                                            $this->Election->AreasElection->create();
                                            $this->Election->AreasElection->save(array('AreasElection' => array(
                                                    'Area_id' => $t['id'],
                                                    'Election_id' => $subDbList[$cityZoneName]['Election']['id'],
                                            )));
                                        }
                                    }
                                }
                            }
                        } elseif (isset($tData[$c['name']])) {
                            foreach ($tData[$c['name']] AS $cityZone => $cityAreas) {
                                $cityZoneName = "{$cityZone}[{$cityAreas['type']}]";
                                if (!isset($subDbList[$cityZoneName])) {
                                    $this->Election->create();
                                    if ($this->Election->save(array('Election' => array(
                                                    'parent_id' => $dbList[$c['name']]['Election']['id'],
                                                    'name' => $cityZoneName,
                                        )))) {
                                        $subDbList[$cityZoneName] = $this->Election->read();
                                    }
                                }
                                $sCityAreas = array();
                                foreach ($cityAreas['areas'] AS $cArea) {
                                    $cAreaName = mb_substr($cArea, 0, 3, 'utf-8');
                                    $cAreaName = str_replace(array('蘆竹鄉', '楊梅鎮'), array('蘆竹市', '楊梅市'), $cAreaName);
                                    $sCityAreas[$cAreaName] = true;
                                }
                                foreach ($this->areas['towns'][$c['id']] AS $t) {
                                    if (isset($sCityAreas[$t['name']])) {
                                        if (empty($this->Election->AreasElection->field('id', array(
                                                            'Area_id' => $t['id'],
                                                            'Election_id' => $subDbList[$cityZoneName]['Election']['id'],
                                                )))) {
                                            $this->Election->AreasElection->create();
                                            $this->Election->AreasElection->save(array('AreasElection' => array(
                                                    'Area_id' => $t['id'],
                                                    'Election_id' => $subDbList[$cityZoneName]['Election']['id'],
                                            )));
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
                case '直轄市山地原住民區長':
                    $stack = array(
                        '新北市' => array('烏來區' => true),
                        '桃園縣' => array('復興鄉' => true),
                        '臺中市' => array('和平區' => true),
                        '高雄市' => array('桃源區' => true, '那瑪夏區' => true, '茂林區' => true),
                    );
                    $dbList = $this->Election->find('all', array(
                        'conditions' => array(
                            'parent_id' => $subTypesDb[$subType],
                        ),
                    ));
                    $dbList = Set::combine($dbList, '{n}.Election.name', '{n}');
                    foreach ($this->areas['municipalities'] AS $c) {
                        if (isset($stack[$c['name']])) {
                            if (!isset($dbList[$c['name']])) {
                                $this->Election->create();
                                if ($this->Election->save(array('Election' => array(
                                                'parent_id' => $subTypesDb[$subType],
                                                'name' => $c['name'],
                                    )))) {
                                    $dbList[$c['name']] = $this->Election->read();
                                }
                            }

                            foreach ($this->areas['towns'][$c['id']] AS $t) {
                                if (isset($stack[$c['name']][$t['name']])) {
                                    $subDbList = $this->Election->find('list', array(
                                        'conditions' => array(
                                            'parent_id' => $dbList[$c['name']]['Election']['id'],
                                        ),
                                        'fields' => array('name', 'id'),
                                    ));
                                    if (!isset($subDbList[$t['name']])) {
                                        $this->Election->create();
                                        if ($this->Election->save(array('Election' => array(
                                                        'parent_id' => $dbList[$c['name']]['Election']['id'],
                                                        'name' => $t['name'],
                                            )))) {
                                            $subDbList[$t['name']] = $this->Election->getInsertID();
                                        }
                                    }

                                    if (empty($this->Election->AreasElection->field('id', array(
                                                        'Area_id' => $t['id'],
                                                        'Election_id' => $subDbList[$t['name']],
                                            )))) {
                                        $this->Election->AreasElection->create();
                                        $this->Election->AreasElection->save(array('AreasElection' => array(
                                                'Area_id' => $t['id'],
                                                'Election_id' => $subDbList[$t['name']],
                                        )));
                                    }
                                }
                            }
                        }
                    }
                    break;
                case '直轄市山地原住民區民代表':
                    $stack = array(
                        '新北市' => array(
                            '烏來區' => array(
                                '第01選舉區' => array('忠治里' => true),
                                '第02選舉區' => array('烏來里' => true, '孝義里' => true),
                                '第03選舉區' => array('信賢里' => true),
                                '第04選舉區' => array('福山里' => true),
                            ),
                        ),
                        '桃園縣' => array(
                            '復興鄉' => array(
                                '第01選舉區' => array('三民村' => true, '澤仁村' => true, '霞雲村' => true, '義盛村' => true),
                                '第02選舉區' => array('長興村' => true, '奎輝村' => true, '羅浮村' => true),
                                '第03選舉區' => array('高義村' => true, '三光村' => true, '華陵村' => true),
                            ),
                        ),
                        '臺中市' => array(
                            '和平區' => array(
                                '第01選舉區' => array('南勢里' => true, '天輪里' => true, '博愛里' => true),
                                '第02選舉區' => array('中坑里' => true, '自由里' => true, '達觀里' => true),
                                '第03選舉區' => array('平等里' => true, '梨山里' => true),
                            ),
                        ),
                        '高雄市' => array(
                            '桃源區' => array(
                                '第01選舉區' => array('梅山里' => true, '拉芙蘭里' => true, '復興里' => true, '勤和里' => true, '桃源里' => true, '高中里' => true),
                                '第02選舉區' => array('建山里' => true, '寶山里' => true),
                            ),
                            '那瑪夏區' => array(
                                '第01選舉區' => array('達卡努瓦里' => true),
                                '第02選舉區' => array('瑪雅里' => true, '南沙魯里' => true),
                            ),
                            '茂林區' => array(
                                '第01選舉區' => array('多納里' => true, '萬山里' => true),
                                '第02選舉區' => array('茂林里' => true),
                            ),
                        ),
                    );
                    $dbList = $this->Election->find('all', array(
                        'conditions' => array(
                            'parent_id' => $subTypesDb[$subType],
                        ),
                    ));
                    $dbList = Set::combine($dbList, '{n}.Election.name', '{n}');
                    foreach ($this->areas['municipalities'] AS $c) {
                        if (isset($stack[$c['name']])) {
                            if (!isset($dbList[$c['name']])) {
                                $this->Election->create();
                                if ($this->Election->save(array('Election' => array(
                                                'parent_id' => $subTypesDb[$subType],
                                                'name' => $c['name'],
                                    )))) {
                                    $dbList[$c['name']] = $this->Election->read();
                                }
                            }

                            foreach ($this->areas['towns'][$c['id']] AS $t) {
                                if (isset($stack[$c['name']][$t['name']])) {
                                    $subDbList = $this->Election->find('list', array(
                                        'conditions' => array(
                                            'parent_id' => $dbList[$c['name']]['Election']['id'],
                                        ),
                                        'fields' => array('name', 'id'),
                                    ));
                                    if (!isset($subDbList[$t['name']])) {
                                        $this->Election->create();
                                        if ($this->Election->save(array('Election' => array(
                                                        'parent_id' => $dbList[$c['name']]['Election']['id'],
                                                        'name' => $t['name'],
                                            )))) {
                                            $subDbList[$t['name']] = $this->Election->getInsertID();
                                        }
                                    }

                                    $nextDbList = $this->Election->find('list', array(
                                        'conditions' => array(
                                            'parent_id' => $subDbList[$t['name']],
                                        ),
                                        'fields' => array('name', 'id'),
                                    ));

                                    foreach ($stack[$c['name']][$t['name']] AS $tZone => $tCunlis) {
                                        if (!isset($nextDbList[$tZone])) {
                                            $this->Election->create();
                                            if ($this->Election->save(array('Election' => array(
                                                            'parent_id' => $subDbList[$t['name']],
                                                            'name' => $tZone,
                                                )))) {
                                                $nextDbList[$tZone] = $this->Election->getInsertID();
                                            }
                                        }

                                        foreach ($this->areas['cunlis'][$t['id']] AS $l) {
                                            if (isset($tCunlis[$l['name']])) {
                                                if (empty($this->Election->AreasElection->field('id', array(
                                                                    'Area_id' => $l['id'],
                                                                    'Election_id' => $nextDbList[$tZone],
                                                        )))) {
                                                    $this->Election->AreasElection->create();
                                                    $this->Election->AreasElection->save(array('AreasElection' => array(
                                                            'Area_id' => $l['id'],
                                                            'Election_id' => $nextDbList[$tZone],
                                                    )));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
                case '縣市議員':
                    $dbList = $this->Election->find('all', array(
                        'conditions' => array(
                            'parent_id' => $subTypesDb[$subType],
                        ),
                    ));
                    $dbList = Set::combine($dbList, '{n}.Election.name', '{n}');
                    $tData = json_decode(file_get_contents(__DIR__ . '/data/v20091201TxC2.json'), true);
                    foreach ($this->areas['counties'] AS $c) {
                        if (!isset($dbList[$c['name']])) {
                            $this->Election->create();
                            if ($this->Election->save(array('Election' => array(
                                            'parent_id' => $subTypesDb[$subType],
                                            'name' => $c['name'],
                                )))) {
                                $dbList[$c['name']] = $this->Election->read();
                            }
                        }

                        $subDbList = $this->Election->find('all', array(
                            'conditions' => array(
                                'parent_id' => $dbList[$c['name']]['Election']['id'],
                            ),
                        ));
                        $subDbList = Set::combine($subDbList, '{n}.Election.name', '{n}');

                        if (isset($tData[$c['name']])) {
                            foreach ($tData[$c['name']] AS $cityZone => $cityAreas) {
                                $cityZoneName = "{$cityZone}[{$cityAreas['type']}]";
                                if (!isset($subDbList[$cityZoneName])) {
                                    $this->Election->create();
                                    if ($this->Election->save(array('Election' => array(
                                                    'parent_id' => $dbList[$c['name']]['Election']['id'],
                                                    'name' => $cityZoneName,
                                        )))) {
                                        $subDbList[$cityZoneName] = $this->Election->read();
                                    }
                                }
                                foreach ($this->areas['towns'][$c['id']] AS $t) {
                                    foreach ($cityAreas['areas'] AS $areaLine) {
                                        if (false !== strpos($areaLine, $t['name'])) {
                                            //link with town
                                            if (empty($this->Election->AreasElection->field('id', array(
                                                                'Area_id' => $t['id'],
                                                                'Election_id' => $subDbList[$cityZoneName]['Election']['id'],
                                                    )))) {
                                                $this->Election->AreasElection->create();
                                                $this->Election->AreasElection->save(array('AreasElection' => array(
                                                        'Area_id' => $t['id'],
                                                        'Election_id' => $subDbList[$cityZoneName]['Election']['id'],
                                                )));
                                            }

                                            foreach ($this->areas['cunlis'][$t['id']] AS $l) {
                                                if (isset($l['name']) && false !== strpos($areaLine, $l['name'])) {
                                                    //link with cunli
                                                    if (empty($this->Election->AreasElection->field('id', array(
                                                                        'Area_id' => $l['id'],
                                                                        'Election_id' => $subDbList[$cityZoneName]['Election']['id'],
                                                            )))) {
                                                        $this->Election->AreasElection->create();
                                                        $this->Election->AreasElection->save(array('AreasElection' => array(
                                                                'Area_id' => $l['id'],
                                                                'Election_id' => $subDbList[$cityZoneName]['Election']['id'],
                                                        )));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
                case '村里長':
                    $cDbList = $this->Election->find('list', array(
                        'conditions' => array(
                            'parent_id' => $subTypesDb[$subType],
                        ),
                        'fields' => array('name', 'id'),
                    ));
                    $bigC = array_merge($this->areas['counties'], $this->areas['municipalities']);
                    foreach ($bigC AS $c) {
                        if (!isset($cDbList[$c['name']])) {
                            $this->Election->create();
                            $this->Election->save(array('Election' => array(
                                    'parent_id' => $subTypesDb[$subType],
                                    'name' => $c['name'],
                            )));
                            $cDbList[$c['name']] = $this->Election->getInsertID();
                        }
                        $tDbList = $this->Election->find('list', array(
                            'conditions' => array(
                                'parent_id' => $cDbList[$c['name']],
                            ),
                            'fields' => array('name', 'id'),
                        ));
                        foreach ($this->areas['towns'][$c['id']] AS $t) {
                            if (!isset($tDbList[$t['name']])) {
                                $this->Election->create();
                                $this->Election->save(array('Election' => array(
                                        'parent_id' => $cDbList[$c['name']],
                                        'name' => $t['name'],
                                )));
                                $tDbList[$t['name']] = $this->Election->getInsertID();
                            }
                            $lDbList = $this->Election->find('list', array(
                                'conditions' => array(
                                    'parent_id' => $tDbList[$t['name']],
                                ),
                                'fields' => array('name', 'id'),
                            ));
                            if (isset($this->areas['cunlis'][$t['id']])) {
                                foreach ($this->areas['cunlis'][$t['id']] AS $l) {
                                    if (!isset($lDbList[$l['name']])) {
                                        $this->Election->create();
                                        if ($this->Election->save(array('Election' => array(
                                                        'parent_id' => $tDbList[$t['name']],
                                                        'name' => $l['name'],
                                            )))) {
                                            $this->Election->AreasElection->create();
                                            $this->Election->AreasElection->save(array('AreasElection' => array(
                                                    'Election_id' => $this->Election->getInsertID(),
                                                    'Area_id' => $l['id'],
                                            )));
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }

    public function areas() {
        $areaParent = $this->Election->Area->find('first', array(
            'conditions' => array('name' => '2014'),
        ));
        if (empty($areaParent)) {
            $this->Election->Area->create();
            $this->Election->Area->save(array('Area' => array(
                    'name' => '2014',
                    'is_area' => '0',
            )));
            $areaParent = $this->Election->Area->read();
        }

        $this->areas['code2id'] = $this->Election->Area->find('list', array(
            'fields' => array('code', 'id'),
            'conditions' => array(
                'lft >' => $areaParent['Area']['lft'],
                'rght <' => $areaParent['Area']['rght'],
            ),
        ));

        $fh = fopen(__DIR__ . '/data/villages.csv', 'r');
        fgetcsv($fh, 2048);
        while ($line = fgetcsv($fh, 2048)) {
            $countyCode = substr($line[1], 0, -7);
            $townCode = substr($line[1], 0, -4);
            $cunliCode = $line[1];
            if (!isset($this->areas['code2id'][$countyCode])) {
                $this->Election->Area->create();
                $this->Election->Area->save(array('Area' => array(
                        'parent_id' => $areaParent['Area']['id'],
                        'name' => $line[4],
                        'is_area' => '1',
                        'ivid' => $line[6],
                        'code' => $countyCode,
                )));
                $this->areas['code2id'][$countyCode] = $this->Election->Area->getInsertID();
            }
            if (!isset($this->areas['code2id'][$townCode])) {
                $this->Election->Area->create();
                $this->Election->Area->save(array('Area' => array(
                        'parent_id' => $this->areas['code2id'][$countyCode],
                        'name' => $line[3],
                        'is_area' => '1',
                        'ivid' => $line[7],
                        'code' => $townCode,
                )));
                $this->areas['code2id'][$townCode] = $this->Election->Area->getInsertID();
            }
            if (!isset($this->areas['code2id'][$cunliCode])) {
                $this->Election->Area->create();
                $this->Election->Area->save(array('Area' => array(
                        'parent_id' => $this->areas['code2id'][$townCode],
                        'name' => $line[2],
                        'is_area' => '1',
                        'ivid' => $line[0],
                        'code' => $cunliCode,
                )));
                $this->areas['code2id'][$cunliCode] = $this->Election->Area->getInsertID();
            }
        }

        $dbAreas = $this->Election->Area->find('all', array(
            'conditions' => array(
                'lft >' => $areaParent['Area']['lft'],
                'rght <' => $areaParent['Area']['rght'],
            ),
        ));

        foreach ($dbAreas AS $dbArea) {
            switch (strlen($dbArea['Area']['ivid'])) {
                case 3:
                    switch ($dbArea['Area']['ivid']) {
                        case 'TPE':
                        case 'KHH':
                        case 'TPQ':
                        case 'TXG':
                        case 'TNN':
                        case 'TAO':
                            $this->areas['municipalities'][$dbArea['Area']['id']] = $dbArea['Area'];
                            break;
                        default:
                            $this->areas['counties'][$dbArea['Area']['id']] = $dbArea['Area'];
                            break;
                    }
                    break;
                case 7:
                    if (!isset($this->areas['towns'][$dbArea['Area']['parent_id']])) {
                        $this->areas['towns'][$dbArea['Area']['parent_id']] = array();
                    }
                    $this->areas['towns'][$dbArea['Area']['parent_id']][$dbArea['Area']['id']] = $dbArea['Area'];
                    break;
                default:
                    if (!isset($this->areas['cunlis'][$dbArea['Area']['parent_id']])) {
                        $this->areas['cunlis'][$dbArea['Area']['parent_id']] = array();
                    }
                    $this->areas['cunlis'][$dbArea['Area']['parent_id']][$dbArea['Area']['id']] = $dbArea['Area'];
            }
        }
    }

}
