<?php

/*
 * 此次選舉動員206,406人次之工作人員，在全台設置15,582處投開票所，成立368個選務作業中心
 * http://web.cec.gov.tw/files/13-1000-29440.php?Lang=zh-tw
 * 
 * 1月16日選舉投開票日，全國投開票所有1萬5582處，包含重要2583處、次要2620處、一般1萬379處，警政署已編排1萬5618名警力、協勤民力2萬2708名協助維護安全
 * http://www.cna.com.tw/news/asoc/201601150195-1.aspx
 */
$json = json_decode(file_get_contents(__DIR__ . '/7.json'), true);
/*
 * Array
  (
  [provinceno] => 09
  [provincename] => 福建省
  [cityno] => 007
  [cityname] => 連江縣
  [areano] => 010
  [areaname] => 南竿鄉
  [voteplacenumber] => 0001
  [type] => 2 (1=一般、2=原住民)
  [villageno] => 001
  [villagename] => 介壽村
  [description] => 全村
  [recsecurity] =>
  [name] => 第一投(開)票所
  [addressprovince] => 09
  [addresscity] => 007
  [addrcityname] => 連江縣
  [addressarea] => 010
  [addrareaname] => 南竿鄉
  [addressvillage] => 001
  [addrvillagename] => 介壽村
  [addresslin] =>
  [addressroad] => 374-2號
  [officephone] => 083622316-21
  )
 */
$tmpPath = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/tmp/place';
if (!file_exists($tmpPath)) {
    mkdir($tmpPath, 0777, true);
}
$config = require $tmpPath . '/config.php';
$places = array();
foreach ($json['投開票所'] AS $place) {
    if ($place['type'] === '1') {
        if (isset($place['addressvillage']) && !preg_match('/[0-9]+/', $place['addressvillage'])) {
            $place['addrvillagename'] = $place['addressvillage'];
        }
        if (!isset($place['addrvillagename'])) {
            $place['addrvillagename'] = '';
        }
        if (!isset($place['recsecurity'])) {
            $place['recsecurity'] = '';
        }
        $code = "{$place['provinceno']}{$place['cityno']}{$place['areano']}{$place['voteplacenumber']}";
        if (!isset($places[$code])) {
            $name = "{$place['name']}";
            $village = "{$place['cityname']}{$place['areaname']}{$place['villagename']}";
            $address = "{$place['addrcityname']}{$place['addrareaname']}{$place['addrvillagename']}{$place['addressroad']}";
            $targetAddress = $address;
            $pos = strpos($targetAddress, '號');
            if (false !== $pos) {
                $targetAddress = substr($targetAddress, 0, $pos) . '號';
            }
            $tmpFile = $tmpPath . '/' . $targetAddress;
            if (!file_exists($tmpFile)) {
                error_log("processing: {$targetAddress}");
                $apiUrl = 'http://addr.tgos.nat.gov.tw/addrws/v30/QueryAddr.asmx/QueryAddr?' . http_build_query(array(
                            'oAPPId' => $config['tgos']['APPID'], //應用程式識別碼(APPId)
                            'oAPIKey' => $config['tgos']['APIKey'], // 應用程式介接驗證碼(APIKey)
                            'oAddress' => $targetAddress, //所要查詢的門牌位置
                            'oSRS' => 'EPSG:4326', //回傳的坐標系統
                            'oFuzzyType' => '2', //模糊比對的代碼
                            'oResultDataType' => 'JSON', //回傳的資料格式
                            'oFuzzyBuffer' => '0', //模糊比對回傳門牌號的許可誤差範圍
                            'oIsOnlyFullMatch' => 'false', //是否只進行完全比對
                            'oIsLockCounty' => 'true', //是否鎖定縣市
                            'oIsLockTown' => 'true', //是否鎖定鄉鎮市區
                            'oIsLockVillage' => 'false', //是否鎖定村里
                            'oIsLockRoadSection' => 'false', //是否鎖定路段
                            'oIsLockLane' => 'false', //是否鎖定巷
                            'oIsLockAlley' => 'false', //是否鎖定弄
                            'oIsLockArea' => 'false', //是否鎖定地區
                            'oIsSameNumber_SubNumber' => 'true', //號之、之號是否視為相同
                            'oCanIgnoreVillage' => 'true', //找不時是否可忽略村里
                            'oCanIgnoreNeighborhood' => 'true', //找不時是否可忽略鄰
                            'oReturnMaxCount' => '0', //如為多筆時，限制回傳最大筆數
                ));
                file_put_contents($tmpFile, file_get_contents($apiUrl));
            }
            $content = file_get_contents($tmpFile);
            $pos = strpos($content, '{');
            $posEnd = strrpos($content, '}') + 1;
            $jsonResult = json_decode(substr($content, $pos, $posEnd - $pos), true);
            $lat = $lng = 0.0;
            $formattedAddress = '';
            if (isset($jsonResult['AddressList'][0])) {
                $lat = $jsonResult['AddressList'][0]['Y'];
                $lng = $jsonResult['AddressList'][0]['X'];
                $formattedAddress = $jsonResult['AddressList'][0]['FULL_ADDR'];
            }
            $places[$code] = array(
                $name, $village, $address, $formattedAddress, $lat, $lng
            );
        }
    }
}
ksort($places);
$fh = fopen(__DIR__ . '/place.csv', 'w');
fputcsv($fh, array(
    '名稱', '所在村里', '住址', '座標住址', '座標緯度', '座標經度'
));
foreach ($places AS $place) {
    fputcsv($fh, $place);
}