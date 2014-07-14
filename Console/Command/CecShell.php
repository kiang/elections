<?php

class CecShell extends AppShell {

    public $uses = array();

    public function main() {
        $this->v20100601C1D2();
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
                        if (!isset($result[$county][$zone[1]])) {
                            $result[$county][$zone[1]] = array(
                                'areas' => array(),
                                'candidates' => array(),
                            );
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
                                $subArea = explode('選區', trim(strip_tags($subFields[0])));
                                $result[$county][$zone[1]]['areas'][] = $subArea[1];
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

}
