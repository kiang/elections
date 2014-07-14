<div id="CandidatesAdminAdd">
    <div class="row">
        <h1><?php
            echo $title_for_layout;
            if (false !== strpos($referer, $this->Html->url('/', true))) {
                echo $this->Html->link('回上頁', $referer, array('class' => 'btn btn-default pull-right'));
            }
            ?></h1><hr />
    </div>
    <?php
    $url = array();
    echo $this->Form->create('Candidate', array('type' => 'file', 'url' => array($electionId)));
    echo $this->Form->input('Candidate.name', array(
        'label' => '姓名',
        'div' => 'form-group col-md-2',
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
        'value' => '1980-06-30',
        'div' => 'form-group col-md-2',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.image', array(
        'type' => 'file',
        'label' => '照片',
        'div' => 'form-group col-md-7',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.party', array(
        'label' => '政黨',
        'div' => 'form-group col-md-2',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.contacts_phone', array(
        'label' => '電話',
        'div' => 'form-group col-md-5',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.contacts_fax', array(
        'label' => '傳真',
        'div' => 'form-group col-md-5',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.contacts_email', array(
        'label' => '信箱',
        'div' => 'form-group col-md-12',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.contacts_address', array(
        'label' => '住址',
        'div' => 'form-group col-md-12',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.links', array(
        'type' => 'textarea',
        'label' => '網址',
        'div' => 'form-group col-md-12',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.education', array(
        'type' => 'textarea',
        'label' => '學歷',
        'div' => 'form-group col-md-12',
        'class' => 'form-control',
    ));
    echo $this->Form->input('Candidate.experience', array(
        'type' => 'textarea',
        'label' => '經歷',
        'div' => 'form-group col-md-12',
        'class' => 'form-control',
    ));
    echo $this->Form->input('CandidatesElection.platform', array(
        'type' => 'textarea',
        'label' => '政見',
        'div' => 'form-group col-md-12',
        'class' => 'form-control',
    ));
    echo $this->Form->end(__('Submit', true));
    ?>
    <script>
        $(function() {
            $('input#CandidateBirth').datepicker({
                dateFormat: 'yy-mm-dd',
                changeYear: true
            });
        });
    </script>
</div>