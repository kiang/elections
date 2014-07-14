<?php

class CecShell extends AppShell {

    public $uses = array('Area');

    public function main() {
        $this->v20100601C1D2();
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
                //echo "{$fields[1][1]} - {$fields[1][0]}\n";
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
                            $result[$county][$town][$townArea][] = array_combine($labels, $subFields);
                            break;
                        case 11:
                            $townArea = trim(strip_tags($subFields[0]));
                            unset($subFields[0]);
                            if (!isset($result[$county][$town][$townArea])) {
                                $result[$county][$town][$townArea] = array();
                            }
                            foreach ($subFields AS $k => $subField) {
                                $subFields[$k - 1] = trim(strip_tags($subField));
                            }
                            unset($subFields[10]);
                            unset($subFields[9]);
                            ksort($subFields);
                            $result[$county][$town][$townArea][] = array_combine($labels, $subFields);
                            break;
                        default:
                    }
                }
            }
        }
        file_put_contents(__DIR__ . '/data/v20100601C1D2.json', json_encode($result));
    }

}
