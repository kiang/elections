<?php

$jsonResult = array();
if (!empty($result)) {
    foreach ($result AS $item) {
        $jsonResult[] = array(
            'id' => $item['Area']['id'],
            'lft' => $item['Area']['lft'],
            'rght' => $item['Area']['rght'],
            'label' => $item['Area']['name'],
            'value' => $item['Area']['name'],
        );
    }
}
echo json_encode($jsonResult);
