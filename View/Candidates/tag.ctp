<div id="CandidatesAdminIndex">
    <h2><?php echo $tag['Tag']['name']; ?> :: 候選人</h2>
    <div class="clearfix"></div>
    <div class="col-md-12"><?php echo $this->Html->getCrumbs(); ?></div>
    <div class="paging col-md-4"><?php echo $this->element('paginator'); ?></div>
    <div class="clearfix"></div>
    <?php
    if (!empty($items)) {
        foreach ($items AS $candidate) {
            ?><div class="col-md-2">
                <a class="thumbnail text-center" href="<?php echo $this->Html->url('/candidates/view/' . $candidate['Candidate']['id']); ?>">
                    <?php
                    if (empty($candidate['Candidate']['image'])) {
                        echo $this->Html->image('candidate-not-found.jpg', array('style' => 'width: 100px; border: 0px;'));
                    } else {
                        echo $this->Html->image('../media/' . $candidate['Candidate']['image'], array('style' => 'width: 100px; height: 100px; border: 0px;'));
                    }
                    ?>
                    <br /><?php echo $candidate['Candidate']['name']; ?>
                    <br /><?php echo $candidate['Election'][1]['Election']['name']; ?>
                </a>
            </div><?php
        }
    } else {
        echo ' ~ 目前沒有候選人資料 ~ ';
    }
    ?>
    <div class="clearfix"></div>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
</div>