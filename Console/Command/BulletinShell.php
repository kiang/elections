<?php

class BulletinShell extends AppShell {

    public $uses = array('Election');

    public function main() {
        $csvMap = array(
            '市議員' => array(
                '縣市議員',
                '直轄市議員',
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
            '縣議員' => array(
                '縣市議員',
            ),
            '鄉鎮市民代表' => array(
                '鄉鎮市民代表',
            ),
            '鄉鎮市長' => array(
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
            '原住民區民代表' => array(
                '直轄市山地原住民區民代表',
            ),
            '原住民區長' => array(
                '直轄市山地原住民區長',
            ),
            '縣長' => array(
                '縣市長',
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

                if ($electionName !== '鄉鎮市民代表') {
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
        $bulletinFile = TMP . 'bulletin_103.csv';
        if (!file_exists($bulletinFile)) {
            file_put_contents($bulletinFile, file_get_contents('https://github.com/kiang/bulletin.cec.gov.tw/raw/master/Console/Command/data/bulletin_103.csv'));
        }
        $ebMap = array();
        $fh = fopen($bulletinFile, 'r');
        while ($line = fgetcsv($fh, 2048)) {
            $parts = explode(' > ', $line[0]);
            if ($parts[0] === '桃園市1') {
                continue;
            }
            $scopeId = false;
            foreach ($csvMap[$parts[1]] AS $csvKey) {
                $csvKey = $parts[0] . $csvKey;
                if (isset($dbMap[$csvKey])) {
                    $scopeId = $dbMap[$csvKey];
                }
            }
            if (false === $scopeId) {
                echo "not found\n";
                print_r($line);
                exit();
            } else {
                $txtFile = "/home/kiang/public_html/bulletin.cec.gov.tw/Console/Command/data/txt_103/{$line[2]}.csv";
                $txt = '';
                if (file_exists($txtFile)) {
                    $txt = file_get_contents($txtFile);
                }
                $electionId = array();
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
                            if (false === $subKeyMatched && false !== strpos($txt, $subKeys[$scopeId][$nodeId])) {
                                $subKeyMatched = true;
                            }
                            if ($subKeyMatched) {
                                $ebMap[$nodeId] = $currentNodeMatched;
                            }
                        } else {
                            $ebMap[$nodeId] = $currentNodeMatched;
                        }
                    }
                }
            }
        }
        fclose($fh);
        $fh = fopen(TMP . 'bulletin.sql', 'w');
        foreach ($ebMap AS $eId => $bId) {
            fputs($fh, "UPDATE elections SET bulletin_key = '{$bId}' WHERE id = '{$eId}';");
        }
        fclose($fh);
    }

}
