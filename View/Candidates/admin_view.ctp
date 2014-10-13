<div class="container">
    <div class="row">
        <div class="col-md-3 btn-group">
            <?php
            echo $this->Html->link('編輯', array('action' => 'edit', $this->data['Candidate']['id']), array('class' => 'btn btn-default'));
            ?>
        </div>
        <div class="col-md-9">
            <div style="float: left;">
                keywords:    
            </div>
            <?php
            foreach ($this->data['Keyword'] AS $keyword) {
                echo ' <div class="btn-group col-md-2">';
                echo $this->Html->link($keyword['keyword'], array('action' => 'view', $this->data['Candidate']['id']), array('class' => 'btn btn-default'));
                echo $this->Html->link('x', array('action' => 'keyword_delete', $keyword['CandidatesKeyword']['id']), array('class' => 'btn btn-default'));
                echo '</div> ';
            }
            ?>
            <div style="float: left;">
                <form id="keywordForm" class="form-inline" method="post">
                    <div class="form-group">
                        <input type="text" name="keyword" />
                        <input type="submit" value="新增" />
                    </div>
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
        <hr />
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="col-md-6">
                <?php
                if (empty($this->data['Candidate']['image'])) {
                    echo $this->Html->image('candidate-not-found.jpg', array('style' => 'width: 200px; border: 0px;'));
                } else {
                    echo $this->Html->image('../media/' . $this->data['Candidate']['image'], array('style' => 'width: 200px; border: 0px;'));
                }
                ?>
            </div>
            <div class="col-md-6">
                <h2><?php echo $this->data['Candidate']['name']; ?></h2>
                <?php echo $this->Olc->stages[$this->data['Candidate']['stage']]; ?>
            </div>
        </div>
        <div class="col-md-7">
            <div class="well well-lg"> 
                <div class="row">
                    <div class="col-sm-4">

                    </div>
                    <div class="col-sm-8">
                        <ul>
                            <li>電話：<?php echo $this->data['Candidate']['contacts_phone']; ?></li>
                            <li>傳真：<?php echo $this->data['Candidate']['contacts_fax']; ?></li>
                            <li>信箱：<?php echo $this->data['Candidate']['contacts_email']; ?></li>
                            <li>服務處：<?php echo $this->data['Candidate']['contacts_address']; ?></li>
                            <li>政黨：<?php echo $this->data['Candidate']['party']; ?></li>
                            <li>生日：<?php echo $this->data['Candidate']['birth']; ?></li>
                            <li>性別：<?php
                                $gender = strtolower($this->data['Candidate']['gender']);
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
        <div class="success">政見</div><?php echo str_replace('\\n', '<br />', $this->data['Election'][0]['CandidatesElection']['platform']); ?>
        <div class="success">相關連結</div><?php
        $lines = explode('\\n', $this->data['Candidate']['links']);
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
        <div class="success">經歷</div><?php echo str_replace('\\n', '<br />', $this->data['Candidate']['experience']); ?>
        <div class="success">學歷</div><?php echo str_replace('\\n', '<br />', $this->data['Candidate']['education']); ?>
        <div class="success">版本</div><ul><?php
            foreach ($versions AS $version) {
                echo '<li>';
                if ($version['Candidate']['id'] === $this->data['Candidate']['id']) {
                    echo '* ';
                }
                echo $this->Html->link($version['Candidate']['created'], array('action' => 'view', $version['Candidate']['id']));
                echo '</li>';
            }
            ?></ul>
    </div><!--/row-->
</div><!--/container-->
<script>
    $(function () {
        $('form#keywordForm').submit(function () {
            $.post('<?php echo $this->Html->url('/admin/candidates/keyword_add/' . $this->data['Candidate']['id']); ?>', $(this).serializeArray(), function (postResult) {
                if (postResult === 'ok') {
                    $('div#viewContent').load('<?php echo $this->Html->url('/admin/candidates/view/' . $this->data['Candidate']['id']); ?>');
                }
            });
            return false;
        });
    })
</script>