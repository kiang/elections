<?php

class CecShell extends AppShell {

    public $uses = array('Election');
    public $electionList = array();

    public function main() {
        $this->final2014();
    }

    public function final2014() {
        $this->treeList($this->Election->find('threaded', array(
                    'fields' => array('id', 'name', 'parent_id', 'lft', 'rght'),
                    'conditions' => array('parent_id IS NOT NULL'),
        )));
        $eCandidates = array();
        $candidates = $this->Election->Candidate->find('all', array(
            'conditions' => array('Candidate.active_id IS NULL'),
            'fields' => array('id', 'election_id', 'name'),
        ));
        foreach ($candidates AS $candidate) {
            if (!isset($eCandidates[$candidate['Candidate']['election_id']])) {
                $eCandidates[$candidate['Candidate']['election_id']] = array();
            }
            $eCandidates[$candidate['Candidate']['election_id']][$candidate['Candidate']['name']] = $candidate['Candidate']['id'];
        }
        $repoPath = '/home/kiang/public_html/db.cec.gov.tw';
        $fh = fopen($repoPath . '/elections.csv', 'r');
        $pairs = array(
            $this->utf8(hexdec('E000')) => '𦰡',
            $this->utf8(hexdec('E001')) => '𥕢',
            $this->utf8(hexdec('E006')) => '塭',
            $this->utf8(hexdec('E007')) => '壳',
            $this->utf8(hexdec('E008')) => '磘',
            $this->utf8(hexdec('E01A')) => '硘',
            $this->utf8(hexdec('E01C')) => '嵵',
            $this->utf8(hexdec('E01D')) => '廍',
            $this->utf8(hexdec('E01F')) => '獇',
            $this->utf8(hexdec('E02A')) => '欍',
            $this->utf8(hexdec('E023')) => '爗',
            $this->utf8(hexdec('E028')) => '鍀',
            $this->utf8(hexdec('E411')) => '塗',
            $this->utf8(hexdec('E00F')) => '䅿',
            '槊榔里' => '槺榔里',
            '双湖村' => '雙湖村',
            '双潭村' => '雙潭村',
            '台子村' => '臺子村',
            '徦' => '厦',
        );
        $codeLoaded = array();
        while ($line = fgetcsv($fh)) {
            if (substr($line[0], 0, 4) === '2014') {
                if (!isset($codeLoaded[$line[0]])) {
                    $codeLoaded[$line[0]] = true;
                } else {
                    continue;
                }
                $parts = explode(' > ', $line[1]);
                $parts[0] = str_replace(array('2014-103年', '選舉'), array('', ''), $parts[0]);
                switch ($parts[0]) {
                    case '直轄市市議員':
                        $parts[0] = '直轄市議員';
                        break;
                    case '直轄市里長':
                        $parts[0] = '村里長';
                        break;
                    case '直轄市區長':
                        $parts[0] = '直轄市山地原住民區長';
                        break;
                    case '直轄市區民代表':
                        $parts[0] = '直轄市山地原住民區民代表';
                        break;
                }
                $eFh = fopen($repoPath . '/elections/' . $line[0] . '.csv', 'r');
                /*
                 * Array
                  (
                  [0] => 地區
                  [1] => 姓名
                  [2] => 號次
                  [3] => 性別
                  [4] => 出生年次
                  [5] => 推薦政黨
                  [6] => 得票數
                  [7] => 得票率
                  [8] => 當選註記
                  [9] => 是否現任
                  )
                 */
                fgets($eFh, 2048);
                while ($eLine = fgetcsv($eFh, 2048)) {
                    if (in_array($parts[0], array('鄉鎮市民代表', '直轄市山地原住民區民代表'))) {
                        $eLine[0] = str_replace(array('選區'), array('選舉區'), $eLine[0]);
                    }
                    $eLine[0] = strtr($eLine[0], $pairs);
                    $electionKey = "{$parts[0]}{$eLine[0]}";
                    $electionId = '';
                    if (isset($this->electionList[$electionKey])) {
                        $electionId = $this->electionList[$electionKey];
                    }
                    $candidateId = '';
                    if (isset($eCandidates[$electionId][$eLine[1]])) {
                        $candidateId = $eCandidates[$electionId][$eLine[1]];
                    }
                    if (empty($candidateId) && !empty($electionId)) {
                        $c = preg_split('/(?<!^)(?!$)/u', $eLine[1]);
                        $maxCount = 0;
                        $guessResult = "\n";
                        foreach ($eCandidates[$electionId] AS $cName => $cId) {
                            $cc = preg_split('/(?<!^)(?!$)/u', $cName);
                            $r = array_intersect($c, $cc);
                            $rCount = count($r);
                            if ($rCount > 2 && $rCount > $maxCount) {
                                $maxCount = $rCount;
                                $candidateId = $cId;
                            }
                        }
                    }
                    if (!empty($candidateId)) {
                        echo "saving {$eLine[0]}{$eLine[1]}\n";
                        $this->Election->Candidate->id = $candidateId;
                        $this->Election->Candidate->save(array('Candidate' => array(
                                'name' => $eLine[1],
                                'stage' => ($eLine[8] === '*' || $eLine[8] === '!') ? '2' : '1',
                                'no' => $eLine[2],
                                'gender' => ($eLine[3] === '男') ? 'm' : 'f',
                                'vote_count' => $eLine[6],
                                'is_present' => ($eLine[9] === '是') ? '1' : '0',
                        )));
                    } else {
                        print_r($eCandidates[$electionId]);
                        print_r($eLine);
                        $candidateId = $this->in('ID?');
                        if (!empty($candidateId)) {
                            echo "saving {$eLine[0]}{$eLine[1]}\n";
                            $this->Election->Candidate->id = $candidateId;
                            $this->Election->Candidate->save(array('Candidate' => array(
                                    'name' => $eLine[1],
                                    'stage' => ($eLine[8] === '*') ? '2' : '1',
                                    'no' => $eLine[2],
                                    'gender' => ($eLine[3] === '男') ? 'm' : 'f',
                                    'vote_count' => $eLine[6],
                                    'is_present' => ($eLine[9] === '是') ? '1' : '0',
                            )));
                        }
                    }
                }
            }
        }
    }

    /*
     * 99年直轄市議員選舉（區域） 候選人得票數
     * http://db.cec.gov.tw/histQuery.jsp?voteCode=20101101T1B2&qryType=ctks
     * 
     * 99年直轄市議員選舉（平地） 候選人得票數
     * http://db.cec.gov.tw/histQuery.jsp?voteCode=20101101T2B2&qryType=ctks
     * 
     * 99年直轄市議員選舉（山地） 候選人得票數
     * http://db.cec.gov.tw/histQuery.jsp?voteCode=20101101T3B2&qryType=ctks
     */

    public function v20101101TxB2() {
        $result = array();
        foreach (array('20101101T1B2', '20101101T2B2', '20101101T3B2') AS $vType) {
            $tmpList = TMP . "cec/v{$vType}_list";
            $tmpPath = TMP . "cec/v{$vType}";
            if (!file_exists($tmpPath)) {
                mkdir($tmpPath, 0777, true);
            }
            if (!file_exists($tmpList)) {
                file_put_contents($tmpList, file_get_contents("http://db.cec.gov.tw/histQuery.jsp?voteCode={$vType}&qryType=ctks"));
            }
            $listPage = file_get_contents($tmpList);

            $pos = strpos($listPage, '<tr class="title">');
            $labelBlock = substr($listPage, $pos, strpos($listPage, '</tr', $pos) - $pos);
            $labels = explode('</td>', $labelBlock);
            foreach ($labels AS $k => $label) {
                if ($k !== 0) {
                    $labels[$k - 1] = trim(strip_tags($label));
                }
            }
            unset($labels[10]);
            unset($labels[9]);

            $pos = strpos($listPage, '<tr class="data">');
            $listPage = substr($listPage, $pos, strpos($listPage, '</table', $pos) - $pos);
            $blocks = explode('</tr>', $listPage);
            foreach ($blocks AS $block) {
                $fields = explode('</td>', $block);
                $fieldsCount = count($fields);
                switch ($fieldsCount) {
                    case 10:
                        unset($fields[9]);
                        foreach ($fields AS $k => $field) {
                            $fields[$k] = trim(strip_tags($field));
                        }
                        $result[$county][$zone[1]]['candidates'][] = array_combine($labels, $fields);
                        break;
                    case 11:
                        $fields[0] = explode('<a href="', $fields[0]);
                        $zone = explode('">', strip_tags($fields[0][1]));
                        $county = substr($zone[1], 0, strpos($zone[1], '第'));
                        $zone[1] = substr($zone[1], strlen($county));
                        if (!isset($result[$county])) {
                            $result[$county] = array();
                        }

                        switch ($vType) {
                            case '20101101T1B2':
                                if (!isset($result[$county][$zone[1]])) {
                                    $result[$county][$zone[1]] = array(
                                        'type' => '區域',
                                        'areas' => array(),
                                        'candidates' => array(),
                                    );
                                }
                                break;
                            case '20101101T2B2':
                                if (!isset($result[$county][$zone[1]])) {
                                    $result[$county][$zone[1]] = array(
                                        'type' => '平地',
                                        'areas' => array(),
                                        'candidates' => array(),
                                    );
                                }
                                break;
                            case '20101101T3B2':
                                if (!isset($result[$county][$zone[1]])) {
                                    $result[$county][$zone[1]] = array(
                                        'type' => '山地',
                                        'areas' => array(),
                                        'candidates' => array(),
                                    );
                                }
                                break;
                        }

                        $tmpSubPageFile = $tmpPath . '/' . md5($zone[0]);
                        if (!file_exists($tmpSubPageFile)) {
                            file_put_contents($tmpSubPageFile, file_get_contents('http://db.cec.gov.tw/' . $zone[0]));
                        }
                        $tmpSubPage = file_get_contents($tmpSubPageFile);

                        $pos = strpos($tmpSubPage, '<tr class="data">');
                        $tmpSubPage = substr($tmpSubPage, $pos, strpos($tmpSubPage, '</table', $pos) - $pos);
                        $subBlocks = explode('</tr>', $tmpSubPage);
                        foreach ($subBlocks AS $subBlock) {
                            $subFields = explode('</td>', $subBlock);
                            if (count($subFields) === 6) {
                                $subAreas = explode('<a href="', $subFields[0]);
                                $subAreas = explode('">', strip_tags($subAreas[1]));
                                $subAreas = explode('選區', $subAreas[1]);
                                $result[$county][$zone[1]]['areas'][] = $subAreas[1];
                            }
                        }

                        unset($fields[0]);

                        foreach ($fields AS $k => $field) {
                            $fields[$k - 1] = trim(strip_tags($field));
                        }
                        unset($fields[10]);
                        unset($fields[9]);
                        ksort($fields);
                        $result[$county][$zone[1]]['candidates'][] = array_combine($labels, $fields);
                        break;
                }
            }
        }
        file_put_contents(__DIR__ . '/data/v20101101TxB2.json', json_encode($result));
    }

    /*
     * 98年縣市議員選舉（區域） 候選人得票數
     * http://db.cec.gov.tw/histQuery.jsp?voteCode=20091201T1C2&qryType=ctks
     * 
     * 98年縣市議員選舉（平原） 候選人得票數
     * http://db.cec.gov.tw/histQuery.jsp?voteCode=20091201T2C2&qryType=ctks
     * 
     * 98年縣市議員選舉（山原） 候選人得票數
     * http://db.cec.gov.tw/histQuery.jsp?voteCode=20091201T3C2&qryType=ctks
     */

    public function v20091201TxC2() {
        $result = array();
        foreach (array('20091201T1C2', '20091201T2C2', '20091201T3C2') AS $vType) {
            $tmpList = TMP . "cec/v{$vType}_list";
            $tmpPath = TMP . "cec/v{$vType}";
            if (!file_exists($tmpPath)) {
                mkdir($tmpPath, 0777, true);
            }
            if (!file_exists($tmpList)) {
                file_put_contents($tmpList, file_get_contents("http://db.cec.gov.tw/histQuery.jsp?voteCode={$vType}&qryType=ctks"));
            }
            $listPage = file_get_contents($tmpList);

            $pos = strpos($listPage, '<tr class="title">');
            $labelBlock = substr($listPage, $pos, strpos($listPage, '</tr', $pos) - $pos);
            $labels = explode('</td>', $labelBlock);
            foreach ($labels AS $k => $label) {
                if ($k !== 0) {
                    $labels[$k - 1] = trim(strip_tags($label));
                }
            }
            unset($labels[10]);
            unset($labels[9]);

            $pos = strpos($listPage, '<tr class="data">');
            $listPage = substr($listPage, $pos, strpos($listPage, '</table', $pos) - $pos);
            $blocks = explode('</tr>', $listPage);
            foreach ($blocks AS $block) {
                $fields = explode('</td>', $block);
                $fieldsCount = count($fields);
                switch ($fieldsCount) {
                    case 10:
                        unset($fields[9]);
                        foreach ($fields AS $k => $field) {
                            $fields[$k] = trim(strip_tags($field));
                        }
                        $result[$county][$zone[1]]['candidates'][] = array_combine($labels, $fields);
                        break;
                    case 11:
                        $fields[0] = explode('<a href="', $fields[0]);
                        $zone = explode('">', strip_tags($fields[0][1]));
                        $county = substr($zone[1], 0, strpos($zone[1], '第'));
                        $zone[1] = substr($zone[1], strlen($county));
                        if (!isset($result[$county])) {
                            $result[$county] = array();
                        }

                        switch ($vType) {
                            case '20091201T1C2':
                                if (!isset($result[$county][$zone[1]])) {
                                    $result[$county][$zone[1]] = array(
                                        'type' => '區域',
                                        'areas' => array(),
                                        'candidates' => array(),
                                    );
                                }
                                break;
                            case '20091201T2C2':
                                if (!isset($result[$county][$zone[1]])) {
                                    $result[$county][$zone[1]] = array(
                                        'type' => '平地',
                                        'areas' => array(),
                                        'candidates' => array(),
                                    );
                                }
                                break;
                            case '20091201T3C2':
                                if (!isset($result[$county][$zone[1]])) {
                                    $result[$county][$zone[1]] = array(
                                        'type' => '山地',
                                        'areas' => array(),
                                        'candidates' => array(),
                                    );
                                }
                                break;
                        }

                        $tmpSubPageFile = $tmpPath . '/' . md5($zone[0]);
                        if (!file_exists($tmpSubPageFile)) {
                            file_put_contents($tmpSubPageFile, file_get_contents('http://db.cec.gov.tw/' . $zone[0]));
                        }
                        $tmpSubPage = file_get_contents($tmpSubPageFile);

                        $pos = strpos($tmpSubPage, '<tr class="data">');
                        $tmpSubPage = substr($tmpSubPage, $pos, strpos($tmpSubPage, '</table', $pos) - $pos);
                        $subBlocks = explode('</tr>', $tmpSubPage);
                        foreach ($subBlocks AS $subBlock) {
                            $subFields = explode('</td>', $subBlock);
                            if (count($subFields) === 6) {
                                $subAreas = explode('<a href="', $subFields[0]);
                                $subAreas = explode('">', strip_tags($subAreas[1]));
                                $subAreaPageFile = $tmpPath . '/' . md5($subAreas[0]);
                                if (!file_exists($subAreaPageFile)) {
                                    file_put_contents($subAreaPageFile, file_get_contents('http://db.cec.gov.tw/' . $subAreas[0]));
                                }
                                $subAreaPage = file_get_contents($subAreaPageFile);
                                $pos = strpos($subAreaPage, '<tr class="data">');
                                $subAreaPage = substr($subAreaPage, $pos, strpos($subAreaPage, '</table', $pos) - $pos);
                                $subAreaPageBlocks = explode('</tr>', $subAreaPage);
                                foreach ($subAreaPageBlocks AS $subAreaPageBlock) {
                                    $subAreaPageBlockFields = explode('</td>', $subAreaPageBlock);
                                    if (count($subAreaPageBlockFields) === 6) {
                                        $cunli = explode('選區', trim(strip_tags($subAreaPageBlockFields[0])));
                                        $result[$county][$zone[1]]['areas'][] = $cunli[1];
                                    }
                                }
                            }
                        }

                        unset($fields[0]);

                        foreach ($fields AS $k => $field) {
                            $fields[$k - 1] = trim(strip_tags($field));
                        }
                        unset($fields[10]);
                        unset($fields[9]);
                        ksort($fields);
                        $result[$county][$zone[1]]['candidates'][] = array_combine($labels, $fields);
                        break;
                }
            }
        }
        file_put_contents(__DIR__ . '/data/v20091201TxC2.json', json_encode($result));
    }

    /*
     * 99年鄉鎮市民代表選舉 候選人得票數
     * http://db.cec.gov.tw/histQuery.jsp?voteCode=20100601C1D2&qryType=ctks
     */

    public function v20100601C1D2() {
        $tmpList = TMP . 'cec/v20100601C1D2_list';
        $tmpPath = TMP . 'cec/v20100601C1D2';
        if (!file_exists($tmpPath)) {
            mkdir($tmpPath, 0777, true);
        }
        if (!file_exists($tmpList)) {
            file_put_contents($tmpList, file_get_contents('http://db.cec.gov.tw/histQuery.jsp?voteCode=20100601C1D2&qryType=ctks'));
        }
        $listPage = file_get_contents($tmpList);
        $pos = strpos($listPage, '<tr class="data">');
        $listPage = substr($listPage, $pos, strpos($listPage, '</table', $pos) - $pos);
        $blocks = explode('</td>', $listPage);
        $result = array();
        foreach ($blocks AS $block) {
            $fields = explode('<a href="', $block);
            if (count($fields) === 1) {
                $county = str_replace('&nbsp;', '', trim(strip_tags($fields[0])));
                if (!empty($county)) {
                    if (!isset($result[$county])) {
                        $result[$county] = array();
                    }
                }
            } else {
                $fields[1] = explode('">', strip_tags($fields[1]));
                $town = $fields[1][1];
                if (!isset($result[$county][$town])) {
                    $result[$county][$town] = array();
                }
                $tmpSubPageFile = $tmpPath . '/' . md5($fields[1][0]);
                if (!file_exists($tmpSubPageFile)) {
                    file_put_contents($tmpSubPageFile, file_get_contents('http://db.cec.gov.tw/' . $fields[1][0]));
                }
                $tmpSubPage = file_get_contents($tmpSubPageFile);

                $pos = strpos($tmpSubPage, '<tr class="title">');
                $labelBlock = substr($tmpSubPage, $pos, strpos($tmpSubPage, '</tr', $pos) - $pos);
                $labels = explode('</td>', $labelBlock);
                foreach ($labels AS $k => $label) {
                    if ($k !== 0) {
                        $labels[$k - 1] = trim(strip_tags($label));
                    }
                }
                unset($labels[10]);
                unset($labels[9]);

                $pos = strpos($tmpSubPage, '<tr class="data">');
                $tmpSubPage = substr($tmpSubPage, $pos, strpos($tmpSubPage, '</table', $pos) - $pos);
                $subBlocks = explode('</tr>', $tmpSubPage);
                foreach ($subBlocks AS $subBlock) {
                    $subFields = explode('</td>', $subBlock);
                    $subFieldsCount = count($subFields);
                    switch ($subFieldsCount) {
                        case 10:
                            foreach ($subFields AS $k => $subField) {
                                $subFields[$k] = trim(strip_tags($subField));
                            }
                            unset($subFields[9]);
                            $result[$county][$town][$townArea]['candidates'][] = array_combine($labels, $subFields);
                            break;
                        case 11:
                            $townArea = trim(strip_tags($subFields[0]));
                            $townArea = substr($townArea, strpos($townArea, '第'));
                            unset($subFields[0]);
                            if (!isset($result[$county][$town][$townArea])) {
                                $result[$county][$town][$townArea] = array(
                                    'candidates' => array(),
                                );
                            }
                            foreach ($subFields AS $k => $subField) {
                                $subFields[$k - 1] = trim(strip_tags($subField));
                            }
                            unset($subFields[10]);
                            unset($subFields[9]);
                            ksort($subFields);
                            $result[$county][$town][$townArea]['candidates'][] = array_combine($labels, $subFields);
                            break;
                        default:
                    }
                }
            }
        }
        file_put_contents(__DIR__ . '/data/v20100601C1D2.json', json_encode($result));
    }

    public function treeList($elections, $prefix = '') {
        foreach ($elections AS $election) {
            $pos = strpos($election['Election']['name'], '[');
            $election['Election']['name'] = str_replace(array('里'), array('里'), $election['Election']['name']);

            if (false !== $pos) {
                $election['Election']['name'] = substr($election['Election']['name'], 0, $pos);
            }
            if ($election['Election']['rght'] - $election['Election']['lft'] === 1) {
                $this->electionList[$prefix . $election['Election']['name']] = $election['Election']['id'];
            } else {
                $this->treeList($election['children'], $prefix . $election['Election']['name']);
            }
        }
    }

    /*
     * from http://stackoverflow.com/questions/1805802/php-convert-unicode-codepoint-to-utf-8
     */

    public function utf8($num) {
        if ($num <= 0x7F)
            return chr($num);
        if ($num <= 0x7FF)
            return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
        if ($num <= 0xFFFF)
            return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        if ($num <= 0x1FFFFF)
            return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        return '';
    }

}
