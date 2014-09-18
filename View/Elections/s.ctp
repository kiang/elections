<?php

$jsonResult = array();
if (!empty($result)) {
    foreach ($result AS $item) {
        $jsonResult[] = array(
            'id' => $item['Election']['id'],
            'lft' => $item['Election']['lft'],
            'rght' => $item['Election']['rght'],
            'label' => $item['Election']['name'],
            'value' => $item['Election']['name'],
        );
    }
}
echo json_encode($jsonResult);
