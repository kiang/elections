<?php
$currentElection = array();
if (!empty($parents)) {
    foreach ($parents AS $parent) {
        if ($parent['Election']['rght'] - $parent['Election']['lft'] !== 1) {
            $this->Html->addCrumb($parent['Election']['name'], array(
                'controller' => 'elections',
                'action' => 'index', $parent['Election']['id'])
            );
        } else {
            $currentElection = $parent['Election'];
            $this->Html->addCrumb($parent['Election']['name'], array(
                'action' => 'index', $parent['Election']['id'])
            );
        }
    }
}
?>
<div id="CandidatesAdminIndex">
    <h2>候選人</h2>
    <div class="col-md-12">
        <div class="pull-right btn-group">
            <?php
            if (!empty($electionId)) {
                echo $this->Html->link('本頁 API', '/api/elections/candidates/' . $electionId, array('class' => 'btn btn-default', 'target' => '_blank'));
            }
            if (!empty($currentElection['bulletin_key'])) {
                echo $this->Html->link('選舉公報', '/bulletins/view/' . $currentElection['bulletin_key'], array('class' => 'btn btn-primary'));
            }
            ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php
    if (!empty($electionId)) {
        $this->Html->addCrumb('新增候選人', array(
            'action' => 'add', $electionId)
        );
    }
    ?>
    <div class="col-md-12"><?php echo $this->Html->getCrumbs(); ?></div>
    <div class="col-md-8">
        <?php
        if (!empty($currentElection['population_electors'])) {
            $quota = "名額： {$currentElection['quota']}";
            if (!empty($currentElection['quota_women'])) {
                $quota .= " / 婦女保障： {$currentElection['quota_women']}";
            }
            echo " &nbsp; &nbsp; ( {$quota} / 選舉人： {$currentElection['population_electors']} / 人口： {$currentElection['population']} )";
        }
        ?>
    </div>
    <div class="paging col-md-4"><?php echo $this->element('paginator'); ?></div>
    <div class="clearfix"></div>
    <?php
    if (!empty($items)) {
        foreach ($items AS $candidate) {
            ?><div class="col-md-2">
                <a class="thumbnail text-center candidate-<?php echo $candidate['Candidate']['stage']; ?>" href="<?php echo $this->Html->url('/candidates/view/' . $candidate['Candidate']['id']); ?>">
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
    <?php if (!empty($electionId)) { ?>
        <div id="vanilla-comments"></div>
        <script type="text/javascript">
            var vanilla_forum_url = '<?php echo $this->Html->url('/../talk'); ?>'; // Required: the full http url & path to your vanilla forum
            var vanilla_identifier = '<?php echo $electionId; ?>'; // Required: your unique identifier for the content being commented on
            var vanilla_url = '<?php echo $this->Html->url('/candidates/index/' . $electionId, true); ?>'; // Current page's url
            (function () {
                var vanilla = document.createElement('script');
                vanilla.type = 'text/javascript';
                var timestamp = new Date().getTime();
                vanilla.src = vanilla_forum_url + '/js/embed.js';
                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(vanilla);
            })();
        </script>
    <?php } ?>
</div>