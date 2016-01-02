<?php

foreach (glob(__DIR__ . '/raw/*/*.txt') AS $txtFile) {
    $targetFile = str_replace(array('/raw/', '.txt'), array('/sum/', '.json'), $txtFile);
    $info = pathinfo($targetFile);
    if (!file_exists($info['dirname'])) {
        mkdir($info['dirname'], 0777, true);
    }
    $json = array();
    $items = explode("\t", file_get_contents($txtFile));
    foreach ($items AS $item) {
        if (!isset($json[$item])) {
            $json[$item] = 1;
        } else {
            ++$json[$item];
        }
    }
    arsort($json);
    file_put_contents($targetFile, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}