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
<div class="row" id="CandidatesAdminIndex">
    <div class="col-md-12">
        <h1>候選人</h1>
        <div class="pull-right btn-group">
            <?php
            if (!empty($electionId)) {
                echo $this->Html->link('新增候選人', array('action' => 'add', $electionId), array('class' => 'btn btn-primary'));
            }
            if (!empty($currentElection['bulletin_key'])) {
                echo $this->Html->link('選舉公報', '/bulletins/view/' . $currentElection['bulletin_key'], array('class' => 'btn btn-primary'));
            }
            if (!empty($electionId)) {
                echo $this->Html->link('本頁 API', '/api/elections/candidates/' . $electionId, array('class' => 'btn btn-default', 'target' => '_blank'));
            }
            ?>
        </div>
    </div>
    <p>&nbsp;</p>
    <div class="col-md-12">
        <!-- <?php echo $this->Html->getCrumbList(array('class' => 'breadcrumb breadcrumb-title')); ?> -->
    </div>
    <?php
    if (!empty($currentElection['population_electors'])) {
        echo '<div class="col-md-8">';
        $quota = "名額： {$currentElection['quota']}";
        if (!empty($currentElection['quota_women'])) {
            $quota .= " / 婦女保障： {$currentElection['quota_women']}";
        }
        echo " &nbsp; &nbsp; ( {$quota} / 選舉人： {$currentElection['population_electors']} / 人口： {$currentElection['population']} )";
        echo '</div>';
    }
    ?>
    <div class="clearfix"></div>
    <div class="paginator-wrapper col-md-12">
        <?php echo $this->element('paginator'); ?>
    </div>
    <?php
    if (!empty($items)) {
        foreach ($items AS $candidate) {
            ?><div class="col-md-2">
                <div class="thumbnail">
                    <div class="candidate-image-wrapper">
                        <a href="<?php echo $this->Html->url('/candidates/view/' . $candidate['Candidate']['id']); ?>">
                            <?php
                            if (empty($candidate['Candidate']['image'])) {
                                echo $this->Html->image('candidate-not-found.jpg', array('class' => 'candidate-image'));
                            } else {
                                echo $this->Html->image('../media/' . $candidate['Candidate']['image'], array('class' => 'candidate-image'));
                            }
                            ?>
                        </a>
                    </div>
                    <div class="caption">
                        <?php
                        echo $this->Html->link(
                            $this->Html->tag('h3', $candidate['Candidate']['name']),
                            '/candidates/view/' . $candidate['Candidate']['id'],
                            array('escape' => false)
                            );
                        echo $this->Html->para(null, $candidate['Candidate']['party']);
                        if(!empty($candidate['Candidate']['no'])) {
                            echo '<br>';
                            echo $this->Html->para(null, $candidate['Candidate']['no'] . '號');
                        }
                        ?>
                    </div>
                </div>
            </div><?php
        }
    } else {
        echo ' ~ 目前沒有候選人資料 ~ ';
    }
    ?>
    <div class="clearfix"></div>
    <div class="paginator-wrapper col-md-12">
        <?php echo $this->element('paginator'); ?>
    </div>
    <?php if (!empty($electionId)) { ?>
        <div id="vanilla-comments"></div>
        <script type="text/javascript">
            var vanilla_forum_url = '<?php echo $this->Html->url('/../talk'); ?>',
                vanilla_identifier = '<?php echo $electionId; ?>',
                vanilla_url = '<?php echo $this->Html->url('/candidates/index/' . $electionId, true); ?>';

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