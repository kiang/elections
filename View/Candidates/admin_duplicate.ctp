<div class="container">
    <div class="row">
        <hr />
        <?php
        echo $this->Form->create('Candidate');
        echo $this->Form->input('Candidate.election_name', array(
            'type' => 'text',
            'label' => '複製到： ',
            'div' => 'form-group',
            'class' => 'form-control',
        ));
        echo $this->Form->hidden('Candidate.election_id');
        echo $this->Form->submit('送出', array('class' => 'btn btn-primary col-md-2'));
        echo $this->Form->end();
        ?>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="col-md-6">
                <?php
                if (empty($candidate['Candidate']['image'])) {
                    echo $this->Html->image('candidate-not-found.jpg', array('style' => 'width: 200px; border: 0px;'));
                } else {
                    echo $this->Html->image('../media/' . $candidate['Candidate']['image'], array('style' => 'width: 200px; border: 0px;'));
                }
                ?>
            </div>
            <div class="col-md-6">
                <h2><?php echo $candidate['Candidate']['name']; ?></h2>
                <?php echo $this->Olc->stages[$candidate['Candidate']['stage']]; ?>
            </div>
        </div>
        <div class="col-md-7">
            <div class="well well-lg"> 
                <div class="row">
                    <div class="col-sm-4">

                    </div>
                    <div class="col-sm-8">
                        <ul>
                            <li>電話：<?php echo $candidate['Candidate']['contacts_phone']; ?></li>
                            <li>傳真：<?php echo $candidate['Candidate']['contacts_fax']; ?></li>
                            <li>信箱：<?php echo $candidate['Candidate']['contacts_email']; ?></li>
                            <li>服務處：<?php echo $candidate['Candidate']['contacts_address']; ?></li>
                            <li>政黨：<?php echo $candidate['Candidate']['party']; ?></li>
                            <li>生日：<?php echo $candidate['Candidate']['birth']; ?></li>
                            <li>性別：<?php
                                $gender = strtolower($candidate['Candidate']['gender']);
                                switch ($gender) {
                                    case 'f':
                                        echo '女';
                                        break;
                                    case 'm':
                                        echo '男';
                                        break;
                                    default:
                                        echo '未設定';
                                }
                                ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="success">政見</div><?php echo str_replace('\\n', '<br />', $candidate['Candidate']['platform']); ?>
        <div class="success">相關連結</div><?php
        $lines = explode('\\n', $candidate['Candidate']['links']);
        foreach ($lines AS $line) {
            $pos = strrpos($line, 'http');
            $title = trim(substr($line, 0, $pos));
            $url = trim(substr($line, $pos));
            if (empty($title)) {
                $title = $url;
            }
            if (!empty($url)) {
                echo $this->Html->link($title, $url, array('target' => '_blank')) . '<br />';
            }
        }
        ?>
        <div class="success">經歷</div><?php echo str_replace('\\n', '<br />', $candidate['Candidate']['experience']); ?>
        <div class="success">學歷</div><?php echo str_replace('\\n', '<br />', $candidate['Candidate']['education']); ?>
    </div><!--/row-->
</div><!--/container-->
<script type="text/javascript">
    //<![CDATA[
    $(function () {
        $('input#CandidateElectionName').autocomplete({
            source: '<?php echo $this->Html->url('/elections/s/'); ?>',
            select: function (event, ui) {
                $('input#CandidateElectionId').val(ui.item.id);
            }
        });
    });
    //]]>
</script>