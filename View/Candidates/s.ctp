<?php

$jsonResult = array();
if (!empty($result)) {
    foreach ($result AS $item) {
        $party = $item['Candidate']['party'];
        if(isset($this->Olc->party[$item['Candidate']['party']])) {
            $party = $this->Olc->party[$item['Candidate']['party']];
        }
        if(!empty($item['Candidate']['no'])) {
            $item['Candidate']['name'] = $item['Candidate']['no'] . '號 ' . $item['Candidate']['name'] . ' (' . $this->Olc->party[$item['Candidate']['party']] . ')';;
        } else {
            $item['Candidate']['name'] = $item['Candidate']['name'] . ' (' . $this->Olc->party[$item['Candidate']['party']] . ')';;
        }
        $jsonResult[] = array(
            'id' => $item['Candidate']['id'],
            'label' => "{$item['Candidate']['name']} - {$item['jobTitle']}",
            'value' => $item['Candidate']['name'],
        );
    }
}
echo json_encode($jsonResult);
