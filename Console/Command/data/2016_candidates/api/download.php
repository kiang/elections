<?php
for($i = 1; $i <=8; $i++) {
    file_put_contents(__DIR__ . '/' . $i . '.json', file_get_contents('http://2016.cec.gov.tw/opendata/cec2016/getJson?dataType=' . $i));
}
