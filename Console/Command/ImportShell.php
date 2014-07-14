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
                    /*
                     * 新北市烏來區
                     * 桃園市復興區
                     * 臺中市和平區
                     * 高雄市桃源區
                     * 高雄市那瑪夏區
                     * 高雄市茂林區
                     */
                    break;
                case '直轄市山地原住民區民代表':
                    /*
                     * 新北市烏來區民代表會
                     * (一)第1選舉區：忠治里。
                      (二)第2選舉區：烏來里、孝義里。
                      (三)第3選舉區：信賢里。
                      (四)第4選舉區：福山里。
                     * 
                     * 桃園市復興區民代表會第 1 屆區民代表選舉區劃
                      分如下：
                      一、 第 1 選舉區：三民里、澤仁里、霞雲里、義盛里。
                      二、 第 2 選舉區：長興里、奎輝里、羅浮里。
                      三、 第 3 選舉區：高義里、三光里、華陵里。
                     * 
                     * 臺中市和平區民代表會第1屆代表選舉區劃分如下：
                      一、第1選舉區：南勢里、天輪里、博愛里。
                      二、第2選舉區：中坑里、自由里、達觀里。
                      三、第3選舉區：平等里、梨山里。
                     * 
                     * 高雄市桃源區、那瑪夏區、茂林區區民代表會第1屆代表選舉
                      區劃分如下：
                      一、桃源區
                      (1) 第1選舉區：梅山里、拉芙蘭里、復興里、勤和里、
                      桃源里、高中里。
                      (2) 第2選舉區：建山里、寶山里。
                      二、那瑪夏區
                      (1) 第1選舉區： 達卡努瓦里。
                      (2) 第2選舉區：瑪雅里、南沙魯里。
                      三、茂林區
                      (1) 第1選舉區：多納里、萬山里。
                      (2) 第2選舉區：茂林里。
                     */
                    break;
                case '縣市議員':
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

        foreach ($dbAreas AS $dbArea) {
            if (strlen($dbArea['Area']['ivid']) === 3) {
                switch ($dbArea['Area']['ivid']) {
                    case 'TPE':
                    case 'KHH':
                    case 'TPQ':
                    case 'TXG':
                    case 'TNN':
                    case 'TAO':
                        $oStack = array('山地原住民' => true, '平地原住民' => true);
                        foreach ($this->areas['towns'][$dbArea['Area']['id']] AS $t) {
                            if (isset($oStack[$t['name']])) {
                                unset($oStack[$t['name']]);
                            }
                        }
                        if (!empty($oStack)) {
                            foreach ($oStack AS $name => $o) {
                                $this->Election->Area->create();
                                $this->Election->Area->save(array('Area' => array(
                                        'parent_id' => $dbArea['Area']['id'],
                                        'name' => $name,
                                        'is_area' => '1',
                                        'ivid' => $dbArea['Area']['ivid'] . '-000',
                                        'code' => '',
                                )));
                                $r = $this->Election->Area->read();
                                $this->areas['towns'][$dbArea['Area']['id']][$this->Election->Area->getInsertID()] = $r['Area'];
                            }
                        }
                        break;
                }
            }
        }
    }

}
