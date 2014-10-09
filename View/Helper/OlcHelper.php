<?php

App::uses('Helper', 'View');

class OlcHelper extends AppHelper {

    public $stages = array(
        '0' => '未登記',
        '1' => '已登記',
        '2' => '已當選',
    );

}
