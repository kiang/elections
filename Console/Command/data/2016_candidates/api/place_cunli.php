<?php

/*
 * http://2016.cec.gov.tw/cms/5709531
 * 
 * 選舉人總數 18,782,991
 * 國行使選舉權人人數 2,420
 * 此次選舉動員206,406人次之工作人員，在全台設置15,582處投開票所，成立368個選務作業中心
 * 
 * (18782991+2420)÷15582 ~= 1206
 * 
 */
$fh = fopen(__DIR__ . '/place.csv', 'r');
/*
 * Array
  (
  [0] => 投票所代號
  [1] => 名稱
  [2] => 村里代碼
  [3] => 村里
  [4] => 村里人口
  [5] => 村里選舉人
  [6] => 村里長
  [7] => 政黨
  [8] => 住址
  [9] => 座標住址
  [10] => 座標緯度
  [11] => 座標經度
  )
 */
fgetcsv($fh, 2048);
$cunli = array();
while ($line = fgetcsv($fh, 2048)) {
    if (!isset($cunli[$line[2]])) {
        $cunli[$line[2]] = array(
            'code' => $line[2],
            'name' => $line[3],
            'voters' => $line[5],
            'party' => $line[7],
            'count' => 0,
        );
    }
    ++$cunli[$line[2]]['count'];
}
$fh = fopen(__DIR__ . '/place_cunli.csv', 'w');
fputcsv($fh, array(
    '村里代碼', '村里', '選舉人', '里長政黨', '投票所數量', '平均',
));
foreach ($cunli AS $k => $v) {
    $v['avg'] = round($v['voters'] / $v['count']);
    fputcsv($fh, $v);
}