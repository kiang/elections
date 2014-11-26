<?php

$jsonResult = array();
if (!empty($result)) {
    foreach ($result AS $item) {
        if(!empty($item['Candidate']['no'])) {
            $item['Candidate']['name'] = $item['Candidate']['no'] . '號 ' . $item['Candidate']['name'];
        }
        $jsonResult[] = array(
            'id' => $item['Candidate']['id'],
            'label' => "{$item['Candidate']['name']} - {$item['jobTitle']}",
            'value' => $item['Candidate']['name'],
        );
    }
}
echo json_encode($jsonResult);
