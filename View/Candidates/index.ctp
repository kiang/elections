<div class="row" id="CandidatesAdminIndex">
    <div class="col-md-12">
        <?php
        $currentElection = array();
        if (!empty($parents)) {
            $c = array();
            echo '<h1 class="text-info">';
            foreach ($parents AS $parent) {
                echo $parent['Election']['name'] . '&nbsp;';
                if ($parent['Election']['rght'] - $parent['Election']['lft'] != 1) {
                    $this->Html->addCrumb($parent['Election']['name'], '/elections/index/' . $parent['Election']['id']);
                } else {
                    $this->Html->addCrumb($parent['Election']['name'], '/candidates/index/' . $parent['Election']['id']);
                }
            }
            echo '候選人</h1>';
            if(!empty($areas)) {
                echo '<br /><span class="text-muted">' . implode(', ', $areas) . '</span>';
            }
            echo '<p>&nbsp;</p>';
        } else {
            echo $this->Html->tag('h1', '候選人');
        }

        if (!empty($electionId)) {
        ?>
            <div class="alert alert-success">
                漏了候選人嗎？立即
                <?php echo $this->Html->link('新增候選人', array('action' => 'add', $electionId)); ?>。
            </div>
        <?php
        }
        ?>
        <div class="pull-right btn-group">
            <?php
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
    <?php
    if (!empty($currentElection['population_electors'])) {
        echo '<div class="col-md-12">';
        echo '<blockquote>';
        $quota = "名額： {$currentElection['quota']}";
        if (!empty($currentElection['quota_women'])) {
            $quota .= "<br>婦女保障： {$currentElection['quota_women']}";
        }
        echo "<br>{$quota}<br>選舉人： {$currentElection['population_electors']}<br>人口： {$currentElection['population']}";
        echo '</blockquote>';
        echo '</div>';
    }
    ?>
    <div class="clearfix"></div>
    <div class="paginator-wrapper col-md-12">
        <?php echo $this->element('paginator'); ?>
    </div>
    <?php
    if (!empty($items)) {
        $candidateCount = 0;
        foreach ($items AS $candidate) {
            ++$candidateCount;
            ?><div class="col-md-2 col-sm-6 col-xs-6">
                <div class="candidates-box">
                    <?php
                    if (intval($candidate['Candidate']['stage']) === 2) {
                        echo '<span class="ribbon">當選</span>';
                    }
                    ?>
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
                                echo $this->Html->para(null, $candidate['Candidate']['no'] . '號');
                            }
                            if(!empty($candidate['Candidate']['vote_count'])) {
                                echo $this->Html->para(null, $candidate['Candidate']['vote_count']);
                            }
                            if (intval($candidate['Candidate']['stage']) === 0) {
                                echo '<p class="text-muted">未登記</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
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