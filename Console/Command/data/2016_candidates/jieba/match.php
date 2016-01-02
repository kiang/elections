<?php

$candidates = array();
$match = array();

foreach (glob(__DIR__ . '/sum/*/*.json') AS $jsonFile) {
    $info = pathinfo($jsonFile);
    $prefix = substr($info['dirname'], strrpos($info['dirname'], '/') + 1);
    $key = $prefix . '/' . $info['filename'];
    $candidates[$key] = json_decode(file_get_contents($jsonFile), true);
}

foreach ($candidates AS $key1 => $items1) {
    foreach ($candidates AS $key2 => $items2) {
        if ($key1 !== $key2) {
            foreach ($items2 AS $word => $count) {
                if (isset($items1[$word])) {
                    if (!isset($match[$key1])) {
                        $match[$key1] = array();
                    }
                    if (!isset($match[$key1][$key2])) {
                        $match[$key1][$key2] = 1;
                    } else {
                        ++$match[$key1][$key2];
                    }
                }
            }
        }
    }
}

foreach ($match AS $key => $items) {
    arsort($match[$key]);
}

print_r($match);
