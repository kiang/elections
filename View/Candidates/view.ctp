<div class="container">
    <div class="row">
        <h1><?php
        if (!empty($parents)) {
            $c = array();
            foreach ($parents AS $parent) {
                $c[] = $parent['Election']['name'];
            }
            echo implode(' > ', $c);
        }
        ?></h1><hr />
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
            <div class="col-md-6"><h2><?php echo $this->data['Candidate']['name']; ?></h2></div>
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
                            <li>性別：<?php echo $this->data['Candidate']['gender']; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="success">政見</div><?php echo nl2br($this->data['Election'][0]['CandidatesElection']['platform']); ?>
        <div class="success">經歷</div><?php echo nl2br($this->data['Candidate']['experience']); ?>
        <div class="success">學歷</div><?php echo nl2br($this->data['Candidate']['education']); ?>
    </div><!--/row-->
</div><!--/container-->