<?php

App::uses('Helper', 'View');

class OlcHelper extends AppHelper {

    public $stages = array(
        '0' => '未登記',
        '1' => '已登記',
        '2' => '已當選',
    );
    public $party = array(
        '無' => '無',
        '無黨籍及未經政黨推薦' => '無',
        '' => '無',
        '中國國民黨' => '國',
        '民主進步黨' => '民',
        '中華統一促進黨' => '中統',
        '國' => '國',
        '中國青年黨' => '中青',
        '教科文預算保障e聯盟' => '教',
        '無黨團結聯盟' => '無團',
        '無政黨' => '無',
        '勞動黨' => '勞',
        '台灣團結聯盟' => '台聯',
        '親民黨' => '親',
        '新黨' => '新',
        '無黨籍' => '無',
        '民進黨' => '民',
        '人民民主陣線' => '民陣',
        '樹黨' => '樹',
        '基進側翼' => '基',
        '國民進步黨' => '國進',
        '綠黨' => '綠',
        '全民廉政無黨聯盟' => '全廉',
        '天宙和平統一家庭黨' => '天',
        '台灣民族黨' => '台民',
        '台灣第一民族黨' => '台一',
        '華聲黨' => '華',
        '人民最大黨' => '人',
        '中華民主向日葵憲政改革聯盟' => '民向',
        '台灣主義黨' => '台主',
        '臺灣建國黨' => '建國',
        '聯合黨' => '聯',
        '三等國民公義人權自救黨' => '三',
        '大道人民黨' => '大',
        '臺灣團結聯盟' => '台聯',
        '國民黨' => '國',
        '無黨' => '無',
        '台灣國民會議' => '台國',
        '公民黨' => '公民',
        '制憲聯盟' => '制憲',
        '第三社會黨' => '第三',
        '台灣農民黨' => '台農',
        '紅黨' => '紅',
        '客家黨' => '客家',
        '中華民國臺灣基本法連線' => '基法',
        '健保免費連線' => '健保',
        '建國黨' => '建國',
        '中國民眾黨' => '民眾',
        '農民黨' => '農民',
        '張亞中等150人聯盟' => '張盟',
        '王廷興等20人聯盟' => '王盟',
        '正黨' => '正',
        '全國民主非政黨聯盟' => '非盟',
        '民主聯盟' => '民聯',
        '大道慈悲濟世黨' => '大道',
        '綠色本土清新黨' => '綠本',
        '全民的黨' => '全民',
        '連署' => '連署',
        '新國家連線' => '新國',
        '中國台灣原住民黨' => '台原',
        '中華台商愛國黨' => '台商',
        '民主自由黨' => '民自',
        '洪運忠義黨' => '洪忠',
        '世界和平黨' => '和平',
        '工教聯盟' => '工教',
        '臺灣慧行志工黨' => '慧行',
        '大中華統一陣線' => '一陣',
        '台灣吾黨' => '台吾',
        '國家民主黨' => '民主',
        '先進黨' => '先進',
        '保護台灣大聯盟' => '保台',
        '台灣民意黨' => '民意',
        '台灣建國聯盟' => '台建',
        '中國婦女黨' => '中婦',
        '中國忠義黨' => '中忠',
        '社會改革黨' => '社改',
        '台灣國民黨' => '國民',
    );

}
