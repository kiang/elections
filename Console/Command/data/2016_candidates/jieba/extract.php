<?php

/*
 * before executing under Ubuntu 15.04, you must have the following packages installed
 * 
 * sudo apt-get install python-setuptools
 * sudo easy_install jieba
 * 
 * ref: https://github.com/fxsjy/jieba
 */
$rawBase = __DIR__ . '/raw';
$apiPath = dirname(__DIR__) . '/api/';
if (!file_exists($rawBase)) {
    mkdir($rawBase, 0777, true);
}
$items = array('　', '&nbsp;', '<BR>', '(', ')', '&lt;', '&gt;', '<', '>', '，',
    '、', '。', '.', '：', '「', '」', '；', '（', '）', '！', ',', '～', mb_convert_encoding('&#x2029;', 'UTF-8', 'HTML-ENTITIES'),
    '●', '】', '【', '《', '》', '』', '『', '－', ':', '-', '—', '˙', '〝', '〞',
    '‧', '＝', '!', '？', '~', '．', '▓', '〉', '%', '/', '／', '〈', '+', '…');
for ($i = 3; $i <= 5; $i++) {
    $json = json_decode(file_get_contents($apiPath . '/' . $i . '.json'), true);
    $key = key($json);
    foreach ($json[$key] AS $candidate) {
        if (isset($candidate['cityname'])) {
            $cityPath = $rawBase . '/' . $candidate['cityname'];
            if (!file_exists($cityPath)) {
                mkdir($cityPath, 0777, true);
            }
            $targetFile = $cityPath . '/' . $candidate['sessionname'] . '_' . $candidate['candidatename'] . '.txt';
        } else {
            $sessionPath = $rawBase . '/' . $candidate['sessionname'];
            if (!file_exists($sessionPath)) {
                mkdir($sessionPath, 0777, true);
            }
            if (isset($candidate['RecPartyName_1'])) {
                $targetFile = $sessionPath . '/' . $candidate['RecPartyName_1'] . '_' . $candidate['candidatename'] . '.txt';
            } elseif (isset($candidate['recpartyname_1'])) {
                $targetFile = $sessionPath . '/' . $candidate['recpartyname_1'] . '_' . $candidate['candidatename'] . '.txt';
            } else {
                $targetFile = $sessionPath . '/' . $candidate['recpartyname'] . '_' . $candidate['candidatename'] . '.txt';
            }
        }
        $platform = '';
        if (isset($candidate['politics'])) {
            $platform = $candidate['politics'];
        } elseif (isset($candidate['rptpolitics'])) {
            $platform = $candidate['rptpolitics'];
        }
        if (!empty($platform)) {
            $platform = str_replace($items, array(' '), $platform);
            $platform = preg_replace('/[\\s]+/i', ' ', $platform);
            $lines = array();
            exec('/usr/bin/python ' . __DIR__ . '/extract.py ' . $platform, $lines);
            file_put_contents($targetFile, $lines[0]);
        }
    }
}