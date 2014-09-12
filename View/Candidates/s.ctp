<?php

$jsonResult = array();
if (!empty($result)) {
    foreach ($result AS $item) {
        $jsonResult[] = array(
            'id' => $item['Candidate']['id'],
            'label' => "{$item['Candidate']['name']} - {$item['jobTitle']}",
            'value' => $item['Candidate']['name'],
        );
    }
}
echo json_encode($jsonResult);
