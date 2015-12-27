<?php
/*
 * api doc( ./doc.pdf ): http://2016.cec.gov.tw/upload/file/2015-11-18/e0c73593-c229-4f5d-80d9-c62691286fd9/aad62a286d8d77f92fe8eebff1f3af7e.pdf
 */
for($i = 1; $i <=8; $i++) {
    file_put_contents(__DIR__ . '/' . $i . '.json', file_get_contents('http://2016.cec.gov.tw/opendata/cec2016/getJson?dataType=' . $i));
}
