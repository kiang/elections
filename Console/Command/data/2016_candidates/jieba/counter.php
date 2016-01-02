<?php

$counter = array();
foreach (glob(__DIR__ . '/raw/*/*.txt') AS $txtFile) {
    $items = explode("\t", file_get_contents($txtFile));
    foreach ($items AS $item) {
        if (!isset($counter[$item])) {
            $counter[$item] = 1;
        } else {
            ++$counter[$item];
        }
    }
}
arsort($counter);

print_r($counter);