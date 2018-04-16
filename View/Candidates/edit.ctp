<div id="CandidatesEdit">
    <div class="row">
        <h1 class="text-info"><?php
            if (!empty($parents)) {
                echo '編輯&nbsp;';
                foreach ($parents AS $parent) {
                    echo $parent['Election']['name'] . '&nbsp;';
                }
                echo '候選人';
            }
            ?></h1>
            <div class="pull-right btn-group">
                <?php
                    echo $this->Html->link('免責聲明', '/pages/notice', array('target' => '_blank', 'class' => 'btn btn-primary'));
                    if (false !== strpos($referer, $this->Html->url('/', true))) {
                        echo $this->Html->link('回上頁', $referer, array('class' => 'btn btn-default pull-right'));
                    }
                ?>
            </div>
            <div class="clearfix"></div>
            <hr>
    </div>
    <?php
    $url = array();
    echo $this->Form->create('Candidate', array('type' => 'file', 'url' => array($candidateId)));
    echo $this->Form->input('Candidate.name', array(
        'label' => '姓名',
        'div' => 'form-group col-md-2',
        'class' => 'form-control',
        'placeholder' => '姓名是必填欄位',
    ));
    echo $this->Form->input('Candidate.no', array(
        'type' => 'text',
        'label' => '號次',
        'div' => 'form-group col-md-1',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.gender', array(
        'type' => 'select',
        'options' => array(
            'm' => '男',
            'f' => '女',
        ),
        'label' => '性別',
        'div' => 'form-group col-md-1',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.birth', array(
        'type' => 'text',
        'label' => '生日',
        'div' => 'form-group col-md-2',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.image', array(
        'type' => 'file',
        'label' => '照片（上傳後會自動壓縮為寬度、高度 512 畫素圖片，建議上傳前先自行剪裁）',
        'div' => 'form-group col-md-6',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.name_english', array(
        'label' => '英文姓名',
        'div' => 'form-group col-md-2',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.education_level', array(
        'label' => '教育程度',
        'div' => 'form-group col-md-4',
        'class' => 'form-control',
        'placeholder' => '例如： 專科 大學 碩士 博士',
    ));
    echo $this->Form->input('Candidate.birth_place', array(
        'label' => '出生地',
        'div' => 'form-group col-md-4',
        'class' => 'form-control',
        'placeholder' => '例如： 臺北市',
    ));
    echo $this->Form->input('Candidate.is_present', array(
        'label' => '是否現任',
        'type' => 'select',
        'options' => array(
            '0' => '否',
            '1' => '是',
        ),
        'div' => 'form-group col-md-2',
        'class' => 'form-control',
        'placeholder' => '例如： 臺北市',
    ));
    echo $this->Form->input('Candidate.party', array(
        'label' => '政黨',
        'div' => 'form-group col-md-2',
        'class' => 'form-control',
        'placeholder' => '例如： 全民最大黨',
    ));
    echo $this->Form->input('Candidate.contacts_phone', array(
        'label' => '電話',
        'div' => 'form-group col-md-5',
        'class' => 'form-control',
        'placeholder' => '例如： [服務處]06-2345678, [手機]0912-345678',
    ));
    echo $this->Form->input('Candidate.contacts_fax', array(
        'label' => '傳真',
        'div' => 'form-group col-md-5',
        'class' => 'form-control',
        'placeholder' => '例如： [助理]06-2345678, [服務處]07-5556666',
    ));
    echo $this->Form->input('Candidate.contacts_email', array(
        'label' => '信箱',
        'div' => 'form-group col-md-12',
        'class' => 'form-control',
        'placeholder' => '例如： [助理1]qqq@xxx.com, [助理2]yyy@xxx.com',
    ));
    echo $this->Form->input('Candidate.contacts_address', array(
        'label' => '住址',
        'div' => 'form-group col-md-12',
        'class' => 'form-control',
        'placeholder' => '主要是服務處地址，例如： [北區]xx路xx號, [東區]oo路oo號',
    ));
    echo $this->Form->input('Candidate.platform', array(
        'type' => 'textarea',
        'label' => '政見',
        'div' => 'form-group col-md-12',
        'class' => 'form-control',
        'placeholder' => '一行一筆政見資料，例如： 爭取勞工權益，協助勞工組織工會',
    ));
    echo $this->Form->input('Candidate.links', array(
        'type' => 'textarea',
        'label' => '網址或連結（像是個人部落格、臉書專頁、研究成果等等）',
        'div' => 'form-group col-md-12',
        'class' => 'form-control',
        'placeholder' => '一行一個網址，網址只接受 http 或 https 開頭，如果希望自訂網址文字可以加在前面用空白隔開，例如： "http://k.olc.tw" 或 "江明宗 . 政 . 路過 http://k.olc.tw"',
    ));
    echo $this->Form->input('Candidate.education', array(
        'type' => 'textarea',
        'label' => '學歷',
        'div' => 'form-group col-md-12',
        'class' => 'form-control',
        'placeholder' => '一行一筆學歷資料，例如： 台南市永福國民小學',
    ));
    echo $this->Form->input('Candidate.experience', array(
        'type' => 'textarea',
        'label' => '經歷',
        'div' => 'form-group col-md-12',
        'class' => 'form-control',
        'placeholder' => '一行一筆學歷資料，例如： 現任大台南市議會第1屆議員',
    ));
    ?>
    <div class="col-md-12">
    <div class="g-recaptcha" data-sitekey="6Lfc1PQSAAAAAKzbq1ArWgKkjuzMtuHcyT8DHi9q"></div>
    </div>
    <div class="clearfix"></div><?php
    echo $this->Html->tag(
        'button',
        '送出',
        array(
            'class' => 'btn btn-primary btn-block btn-lg',
            'type' => 'submit'
        )
    );
    echo $this->Form->end();
    ?>
</div>
<?php
$groupId = Configure::read('loginMember.group_id');
if($groupId == 0) {
    echo $this->Html->script('https://www.google.com/recaptcha/api.js', array('inline' => false));
}

echo $this->Html->script('Candidates/add.js', array('inline' => false, 'block' => 'scriptBottom'));