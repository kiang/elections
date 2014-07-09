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
                    break;
                case '直轄市山地原住民區長':
                    break;
                case '直轄市山地原住民區民代表':
                    break;
                case '縣市議員':
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
                    break;
            }
        }
        return;



        $accTypes = array();
        $fh = fopen('/home/kiang/public_html/suncy/list_new.csv', 'r');
        while ($line = fgetcsv($fh, 2048)) {
            $a = explode('擬參選人', $line[1]);
            $a[0] = substr($a[0], strpos($a[0], '年') + 3);
            $accTypes[$a[0]] = true;
        }
        print_r($accTypes);
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

        $fh = fopen('/home/kiang/public_html/cunli/villages.csv', 'r');
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
