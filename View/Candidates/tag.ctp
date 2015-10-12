<div id="CandidatesAdminIndex">
    <h2><?php echo $tag['Tag']['name']; ?> 候選人</h2>
    <div class="col-md-12"><?php echo $this->Html->getCrumbs(); ?></div>
    <div class="paginator-wrapper col-md-12"><?php echo $this->element('paginator'); ?></div>
    <div class="pull-right btn-group">
        <?php echo $this->Html->link('照片', '/candidates/tag/' . $tag['Tag']['id'], array('class' => 'btn btn-primary')); ?>
        <?php echo $this->Html->link('清單', '/candidates/tag_list/' . $tag['Tag']['id'], array('class' => 'btn btn-default')); ?>
        <?php echo $this->Html->link('參選記錄', '/candidates/tag_name/' . $tag['Tag']['id'], array('class' => 'btn btn-default')); ?>
    </div>
    <div class="clearfix"></div>
    <p>&nbsp;</p>
    <?php
    if (!empty($items)) {
        $candidateCount = 0;
        foreach ($items AS $candidate) {
            ++$candidateCount;
            ?><div class="col-md-2 col-sm-4 col-xs-6">
                <a class="thumbnail text-center candidate-<?php echo $candidate['Candidate']['stage']; ?>" href="<?php echo $this->Html->url('/candidates/view/' . $candidate['Candidate']['id']); ?>">
                    <?php
                    if (empty($candidate['Candidate']['image'])) {
                        echo $this->Html->image('candidate-not-found.jpg', array('style' => 'width: 100px; border: 0px;'));
                    } else {
                        echo $this->Html->image('../media/' . $candidate['Candidate']['image'], array('style' => 'width: 100px; height: 100px; border: 0px;'));
                    }
                    ?>
                    <br><?php
                    if(!empty($candidate['Candidate']['no'])) {
                        echo $candidate['Candidate']['no'] . '號 ';
                    }
                    echo $candidate['Candidate']['name']; ?>
                    <br><?php echo $candidate['Election'][1]['Election']['name']; ?>
                </a>
            </div><?php
            if ($candidateCount >= 6) {
                echo '<div class="clearfix"></div>';
                $candidateCount = 0;
            }
        }
    } else {
        echo '<span class="text-muted">目前沒有候選人資料</span>';
    }
    ?>
    <div class="clearfix"></div>
    <div class="paginator-wrapper col-md-12"><?php echo $this->element('paginator'); ?></div>
    <div id="vanilla-comments"></div>
    <script type="text/javascript">
        var vanilla_forum_url = '<?php echo $this->Html->url('/../talk'); ?>'; // Required: the full http url & path to your vanilla forum
        var vanilla_identifier = '<?php echo $tag['Tag']['id']; ?>'; // Required: your unique identifier for the content being commented on
        var vanilla_url = '<?php echo $this->Html->url('/candidates/tag/' . $tag['Tag']['id'], true); ?>'; // Current page's url
        (function () {
            var vanilla = document.createElement('script');
            vanilla.type = 'text/javascript';
            var timestamp = new Date().getTime();
            vanilla.src = vanilla_forum_url + '/js/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(vanilla);
        })();
    </script>
</div>