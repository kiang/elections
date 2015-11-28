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
        $this->candidate2016();
    }

    public function candidate2016() {
        $json = json_decode(file_get_contents(__DIR__ . '/data/2016_candidates.json'), true);
        /*
         * 2016-01 總統(副總統) - 55085e00-a45c-4143-af8a-2f916ab936af
         * 2016-01 立法委員 全國[不分區政黨] - 5508642a-7e4c-41bf-a0cd-23d86ab936af
         * 2016-01 立法委員 全國[山原] - 5508641c-f4e0-4b63-8354-28956ab936af
         * 2016-01 立法委員 全國[平原] - 55086422-2e64-4827-9712-23d86ab936af
         * 2016-01 立法委員 - 55085e1a-c494-40e0-ba31-2f916ab936af
         */
        $dbCandidates = $this->Election->Candidate->find('all', array(
            'conditions' => array(
                'Candidate.active_id IS NULL',
                'Candidate.election_id' => '55085e00-a45c-4143-af8a-2f916ab936af',
            ),
            'fields' => array('id', 'stage', 'name'),
        ));
        foreach ($json['總統'] AS $candidate) {
            foreach ($dbCandidates AS $dbCandidate) {
                for ($i = 0; $i < 2; $i++) {
                    if ($dbCandidate['Candidate']['name'] === $candidate['name'][$i] && $dbCandidate['Candidate']['stage'] != 1) {
                        $this->Election->Candidate->id = $dbCandidate['Candidate']['id'];
                        $this->Election->Candidate->save(array('Candidate' => array(
                                'stage' => '1',
                        )));
                    }
                }
            }
        }
        $election = $this->Election->find('first', array(
            'conditions' => array(
                'id' => '55085e1a-c494-40e0-ba31-2f916ab936af',
            ),
        ));
        $dbCandidates = $this->Election->Candidate->find('all', array(
            'conditions' => array(
                'Candidate.active_id IS NULL',
                'Election.lft >' => $election['Election']['lft'],
                'Election.rght <' => $election['Election']['rght'],
            ),
            'fields' => array('id', 'stage', 'name'),
            'contain' => array('Election'),
        ));
        foreach (array_keys($json['立法委員']['不分區']) AS $candidate) {
            $dbCandidateFound = false;
            foreach ($dbCandidates AS $dbCandidate) {
                if (false !== strpos($dbCandidate['Candidate']['name'], $candidate)) {
                    $dbCandidateFound = true;
                    if ($dbCandidate['Candidate']['stage'] != 1) {
                        $this->Election->Candidate->id = $dbCandidate['Candidate']['id'];
                        $this->Election->Candidate->save(array('Candidate' => array(
                                'stage' => '1',
                        )));
                    }
                }
            }
            if (false === $dbCandidateFound) {
                echo $candidate;
            }
        }
        foreach ($json['立法委員']['山地原住民'] AS $candidate) {
            $dbCandidateFound = false;
            foreach ($dbCandidates AS $dbCandidate) {
                if (false !== strpos($dbCandidate['Candidate']['name'], $candidate['name'])) {
                    $dbCandidateFound = true;
                    if ($dbCandidate['Candidate']['stage'] != 1) {
                        $this->Election->Candidate->id = $dbCandidate['Candidate']['id'];
                        $this->Election->Candidate->save(array('Candidate' => array(
                                'stage' => '1',
                        )));
                    }
                }
            }
            if (false === $dbCandidateFound) {
                echo $candidate['name'] . "\n";
            }
        }
        foreach ($json['立法委員']['平地原住民'] AS $candidate) {
            $dbCandidateFound = false;
            foreach ($dbCandidates AS $dbCandidate) {
                if (false !== strpos($dbCandidate['Candidate']['name'], $candidate['name'])) {
                    $dbCandidateFound = true;
                    if ($dbCandidate['Candidate']['stage'] != 1) {
                        $this->Election->Candidate->id = $dbCandidate['Candidate']['id'];
                        $this->Election->Candidate->save(array('Candidate' => array(
                                'stage' => '1',
                        )));
                    }
                }
            }
            if (false === $dbCandidateFound) {
                echo $candidate['name'] . "\n";
            }
        }
        foreach ($json['立法委員']['區域'] AS $area => $areaCandidates) {
            foreach ($areaCandidates AS $eCandidates) {
                foreach ($eCandidates AS $candidate) {
                    $dbCandidateFound = false;
                    foreach ($dbCandidates AS $dbCandidate) {
                        if (false !== strpos($dbCandidate['Candidate']['name'], $candidate['name'])) {
                            $dbCandidateFound = true;
                            if ($dbCandidate['Candidate']['stage'] != 1) {
                                $this->Election->Candidate->id = $dbCandidate['Candidate']['id'];
                                $this->Election->Candidate->save(array('Candidate' => array(
                                        'stage' => '1',
                                )));
                            }
                        }
                    }
                    if (false === $dbCandidateFound) {
                        echo $candidate['name'] . "\n";
                    }
                }
            }
        }
    }

    public function fb_links() {
        $fh = fopen(TMP . 'fb_links', 'r');
        $tasks = array();
        while ($line = fgetcsv($fh, 2048, "\t")) {
            $line[0] = trim($line[0]);
            $pos = strpos($line[0], '?');
            if (false !== $pos && false === strpos($line[0], 'profile.php')) {
                $line[0] = substr($line[0], 0, $pos);
            }
            $pos = strpos($line[0], '/photos/');
            if (false !== $pos) {
                $line[0] = substr($line[0], 0, $pos);
            }
            if (!empty($line[0])) {
                $line[0] = 'http://www' . substr($line[0], strpos($line[0], '.'));
                $candidateId = substr($line[4], strrpos($line[4], '/') + 1);
                if (!isset($tasks[$candidateId])) {
                    $tasks[$candidateId] = array();
                }
                $tasks[$candidateId][$line[0]] = true;
            }
        }
        fclose($fh);
        foreach ($tasks AS $candidateId => $links) {
            $candidate = $this->Election->Candidate->read(array('id', 'links'), $candidateId);
            $toSave = false;
            foreach ($links AS $link => $b) {
                if (false === strpos($candidate['Candidate']['links'], $link)) {
                    $toSave = true;
                    $candidate['Candidate']['links'] .= '\\n臉書 ' . $link;
                }
            }
            if ($toSave) {
                $this->Election->Candidate->save(array('Candidate' => array(
                        'id' => $candidate['Candidate']['id'],
                        'links' => $candidate['Candidate']['links'],
                )));
            }
        }
    }

    public function reps_fix() {
        $root = $this->Election->find('first', array(
            'conditions' => array('name' => '鄉鎮市民代表'),
        ));
        $counties = $this->Election->find('list', array(
            'conditions' => array(
                'parent_id' => $root['Election']['id'],
            ),
            'fields' => array('name', 'id'),
        ));
        $towns = $townAreas = array();
        foreach ($counties AS $cName => $cId) {
            $towns[$cName] = $this->Election->find('list', array(
                'conditions' => array(
                    'parent_id' => $cId,
                ),
                'fields' => array('name', 'id'),
            ));
        }
        foreach ($this->areas['counties'] AS $c) {
            if (in_array($c['name'], array('基隆市', '新竹市', '嘉義市'))) {
                continue;
            }
            $townAreas[$c['name']] = array();
            foreach ($this->areas['towns'][$c['id']] AS $t) {
                $townAreas[$c['name']][$t['name']] = array();
                $townAreas[$c['name']][$t['name']]['id'] = $t['id'];
                $townAreas[$c['name']][$t['name']]['cunlis'] = array();
                foreach ($this->areas['cunlis'][$t['id']] AS $l) {
                    $townAreas[$c['name']][$t['name']]['cunlis'][$l['name']] = $l['id'];
                }
            }
        }

        $repsContent = file_get_contents(__DIR__ . '/data/reps.txt');
        $blocks = explode("\n\n", $repsContent);
        $checkStack = array();
        foreach ($blocks AS $block) {
            $lines = explode("\n", $block);
            $firstLine = false;
            $county = '';
            foreach ($lines AS $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }
                $cols = explode('|', $line);
                if (count($cols) !== 2) {
                    print_r($cols);
                    exit();
                }
                if (false === $firstLine) {
                    $county = $cols[0];
                    $town = $cols[1];
                    $firstLine = true;
                } else {
                    $area = $cols[0];
                    $maps = explode('、', $cols[1]);
                    $data = array(
                        'parent_id' => $towns[$county][$town],
                        'name' => $area,
                    );
                    $pElectionId = $this->Election->field('id', $data);
                    if (count($maps) === 1 && ($maps[0] != $town)) {
                        $this->Election->AreasElection->deleteAll(array(
                            'Area_id' => $townAreas[$county][$town]['id'],
                            'Election_id' => $pElectionId,
                        ));
                        $item = $maps[0];
                        if (isset($townAreas[$county][$town]['cunlis'][$item])) {
                            $this->Election->AreasElection->create();
                            $this->Election->AreasElection->save(array(
                                'AreasElection' => array(
                                    'Area_id' => $townAreas[$county][$town]['cunlis'][$item],
                                    'Election_id' => $pElectionId,
                                )
                            ));
                        }
                    }
                }
            }
        }
    }

    public function reps() {
        $rNode = $this->Election->find('first', array(
            'conditions' => array('name' => '鄉鎮市長'),
        ));
        $cNodes = $this->Election->find('list', array(
            'conditions' => array(
                'parent_id' => $rNode['Election']['id'],
                'name' => array('基隆市', '新竹市', '嘉義市'),
            ),
            'fields' => array('id', 'id'),
        ));
        foreach ($cNodes AS $cNodeId) {
            $this->Election->AreasElection->deleteAll(array(
                'Election_id' => $cNodeId,
            ));
            $this->Election->delete($cNodeId);
        }
        $root = $this->Election->find('first', array(
            'conditions' => array('name' => '鄉鎮市民代表'),
        ));
        $counties = $this->Election->find('list', array(
            'conditions' => array(
                'parent_id' => $root['Election']['id'],
            ),
            'fields' => array('name', 'id'),
        ));
        $towns = $townAreas = array();
        foreach ($counties AS $cName => $cId) {
            if (in_array($cName, array('基隆市', '新竹市', '嘉義市'))) {
                $this->Election->deleteAll(array('parent_id' => $cId));
                $this->Election->delete($cId);
                continue;
            }
            $towns[$cName] = $this->Election->find('list', array(
                'conditions' => array(
                    'parent_id' => $cId,
                ),
                'fields' => array('name', 'id'),
            ));
            continue;
            $this->Election->AreasElection->deleteAll(array(
                'Election_id' => $towns[$cName],
            ));
        }
        foreach ($this->areas['counties'] AS $c) {
            if (in_array($c['name'], array('基隆市', '新竹市', '嘉義市'))) {
                continue;
            }
            $townAreas[$c['name']] = array();
            foreach ($this->areas['towns'][$c['id']] AS $t) {
                $townAreas[$c['name']][$t['name']] = array();
                $townAreas[$c['name']][$t['name']]['id'] = $t['id'];
                $townAreas[$c['name']][$t['name']]['cunlis'] = array();
                foreach ($this->areas['cunlis'][$t['id']] AS $l) {
                    $townAreas[$c['name']][$t['name']]['cunlis'][$l['name']] = $l['id'];
                }
            }
        }

        $repsContent = file_get_contents(__DIR__ . '/data/reps.txt');
        $blocks = explode("\n\n", $repsContent);
        $checkStack = array();
        foreach ($blocks AS $block) {
            $lines = explode("\n", $block);
            $firstLine = false;
            $county = '';
            foreach ($lines AS $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }
                $cols = explode('|', $line);
                if (count($cols) !== 2) {
                    print_r($cols);
                    exit();
                }
                if (false === $firstLine) {
                    $county = $cols[0];
                    $town = $cols[1];
                    $firstLine = true;
                } else {
                    $area = $cols[0];
                    $maps = explode('、', $cols[1]);
                    $data = array(
                        'parent_id' => $towns[$county][$town],
                        'name' => $area,
                    );
                    $pElectionId = $this->Election->field('id', $data);
                    if (empty($pElectionId)) {
                        $this->Election->create();
                        $this->Election->save(array('Election' => $data));
                        $pElectionId = $this->Election->getInsertID();
                    }
                    if ((count($maps) === 1) && ($maps[0] === $town)) {
                        if (isset($townAreas[$county][$town])) {
                            $this->Election->AreasElection->create();
                            $this->Election->AreasElection->save(array(
                                'AreasElection' => array(
                                    'Area_id' => $townAreas[$county][$town]['id'],
                                    'Election_id' => $pElectionId,
                                )
                            ));
                        }
                    } else {
                        foreach ($maps AS $item) {
                            if (isset($townAreas[$county][$town]['cunlis'][$item])) {
                                $this->Election->AreasElection->create();
                                $this->Election->AreasElection->save(array(
                                    'AreasElection' => array(
                                        'Area_id' => $townAreas[$county][$town]['cunlis'][$item],
                                        'Election_id' => $pElectionId,
                                    )
                                ));
                            }
                        }
                    }
                }
            }
        }
    }

    public function fix2014() {
        $toFixStack = array('縣市議員' => array(
                '臺東縣' => array(
                    '第01選區[區域]' => array('臺東市', '蘭嶼鄉'),
                    '第02選區[區域]' => array('卑南鄉', '延平鄉'),
                    '第03選區[區域]' => array('東河鄉', '成功鎮', '長濱鄉'),
                    '第04選區[區域]' => array('鹿野鄉', '關山鎮', '海端鄉', '池上鄉'),
                    '第05選區[區域]' => array('太麻里鄉', '金峰鄉', '達仁鄉', '大武鄉'),
                    '第06選區[區域]' => array('綠島鄉'),
                    '第07選區[平地]' => array('臺東市'),
                    '第08選區[平地]' => array('卑南鄉', '太麻里鄉', '金峰鄉', '達仁鄉', '大武鄉', '蘭嶼鄉'),
                    '第09選區[平地]' => array('鹿野鄉', '延平鄉', '關山鎮', '海端鄉', '池上鄉'),
                    '第10選區[平地]' => array('東河鄉', '綠島鄉', '成功鎮', '長濱鄉'),
                    '第11選區[山地]' => array('延平鄉', '卑南鄉', '東河鄉', '成功鎮', '長濱鄉'),
                    '第12選區[山地]' => array('海端鄉', '鹿野鄉', '關山鎮', '池上鄉'),
                    '第13選區[山地]' => array('金峰鄉', '太麻里鄉', '臺東市', '綠島鄉'),
                    '第14選區[山地]' => array('達仁鄉', '大武鄉'),
                    '第15選區[山地]' => array('蘭嶼鄉'),
                ),
                '新竹市' => array(
                    '第01選區[區域]' => array('東門里', '榮光里', '成功里', '育賢里', '中正里', '親仁里', '文華里', '復中里', '三民里', '公園里', '東園里', '東山里', '東勢里', '光復里', '前溪里', '水源里', '千甲里', '綠水里', '埔頂里', '仙宮里', '龍山里', '新莊里', '仙水里', '金山里', '建功里', '光明里', '立功里', '軍功里', '武功里', '豐功里', '科園里', '關東里', '建華里', '錦華里', '復興里'),
                    '第02選區[區域]' => array('南門里', '關帝里', '南市里', '福德里', '振興里', '新興里', '竹蓮里', '南大里', '寺前里', '下竹里', '頂竹里', '光鎮里', '高峰里', '柴橋里', '新光里', '湖濱里', '明湖里'),
                    '第03選區[區域]' => array('客雅里', '育英里', '曲溪里', '西雅里', '南勢里', '大鵬里', '西門里', '仁德里', '潛園里', '中央里', '崇禮里', '石坊里', '興南里', '台溪里'),
                    '第04選區[區域]' => array('北門里', '中興里', '大同里', '中山里', '長和里', '新民里', '民富里', '水田里', '文雅里', '光田里', '士林里', '福林里', '古賢里', '湳雅里', '舊社里', '武陵里', '南寮里', '舊港里', '康樂里', '港北里', '中寮里', '海濱里', '磐石里', '新雅里', '光華里', '金華里', '境福里', '金竹里', '湳中里', '金雅里'),
                    '第05選區[區域]' => array('頂埔里', '中埔里', '埔前里', '牛埔里', '樹下里', '浸水里', '虎林里', '虎山里', '港南里', '大庄里', '美山里', '朝山里', '東香里', '香山里', '香村里', '海山里', '鹽水里', '內湖里', '南港里', '中隘里', '南隘里', '大湖里', '茄苳里', '頂福里'),
                    '第06選區[平地]' => array('新竹市'),
                ),
                '金門縣' => array(
                    '第01選區[區域]' => array('金城鎮', '金寧鄉', '烏坵鄉'),
                    '第02選區[區域]' => array('金湖鎮', '金沙鎮'),
                    '第03選區[區域]' => array('烈嶼鄉'),
                ),
        ));
        $this->Election->deleteAll(array(
            'parent_id IS NULL', 'name IS NULL',
        ));
        foreach ($toFixStack AS $eType => $eStack) {
            $election = $this->Election->find('first', array(
                'conditions' => array(
                    'Election.name' => $eType,
                ),
            ));
            foreach ($eStack AS $county => $eAreas) {
                $eCounty = $this->Election->find('first', array(
                    'conditions' => array(
                        'Election.parent_id' => $election['Election']['id'],
                        'Election.name' => $county,
                    ),
                ));
                $eCountyArea = $this->Election->Area->find('first', array(
                    'conditions' => array(
                        'Area.name' => $county,
                    ),
                ));
                $areaList = $this->Election->Area->find('list', array(
                    'conditions' => array(
                        'Area.lft >=' => $eCountyArea['Area']['lft'],
                        'Area.rght <=' => $eCountyArea['Area']['rght'],
                    ),
                    'fields' => array('name', 'id'),
                ));
                $eAreaDbList = $this->Election->find('all', array(
                    'conditions' => array(
                        'Election.parent_id' => $eCounty['Election']['id'],
                    ),
                ));
                $eAreaDbList = Set::combine($eAreaDbList, '{n}.Election.name', '{n}.Election');
                $eAreaDbNames = array_combine(array_keys($eAreaDbList), array_keys($eAreaDbList));

                foreach ($eAreas AS $eArea => $targetAreas) {
                    if (!isset($eAreaDbList[$eArea])) {
                        echo "{$eArea}\n";
                        $this->Election->create();
                        if (!$this->Election->save(array('Election' => array(
                                        'parent_id' => $eCounty['Election']['id'],
                                        'name' => $eArea,
                            )))) {
                            print_r($eCounty);
                            exit();
                        }
                        $savedArea = $this->Election->read();
                        $eAreaDbList[$eArea] = $savedArea['Election'];
                    }
                    if (isset($eAreaDbNames[$eArea])) {
                        unset($eAreaDbNames[$eArea]);
                    }

                    $links = $this->Election->AreasElection->find('list', array(
                        'conditions' => array('Election_id' => $eAreaDbList[$eArea]['id']),
                        'fields' => array('Area_id', 'id'),
                    ));
                    foreach ($targetAreas AS $targetArea) {
                        if (isset($links[$areaList[$targetArea]])) {
                            unset($links[$areaList[$targetArea]]);
                        } elseif (isset($areaList[$targetArea])) {
                            $this->Election->AreasElection->create();
                            $this->Election->AreasElection->save(array('AreasElection' => array(
                                    'Election_id' => $eAreaDbList[$eArea]['id'],
                                    'Area_id' => $areaList[$targetArea],
                            )));
                        } else {
                            echo "{$targetArea}\n";
                        }
                    }
                    foreach ($links AS $linkId) {
                        $this->Election->AreasElection->delete($linkId);
                    }
                }
                if (!empty($eAreaDbNames)) {
                    foreach ($eAreaDbNames AS $eAreaDbName) {
                        $this->Election->delete($eAreaDbList[$eAreaDbName]['id']);
                    }
                }
            }
        }
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
                    $jsonData = json_decode(file_get_contents(__DIR__ . '/data/v20101101TxB2.json'), true);
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
