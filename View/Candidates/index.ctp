<div id="CandidatesAdminIndex">
    <h2>候選人</h2>
    <div class="clearfix"></div>
    <?php
    if (!empty($parents)) {
        foreach ($parents AS $parent) {
            if ($parent['Election']['rght'] - $parent['Election']['lft'] !== 1) {
                $this->Html->addCrumb($parent['Election']['name'], array(
                    'controller' => 'elections',
                    'action' => 'index', $parent['Election']['id'])
                );
            } else {
                $this->Html->addCrumb($parent['Election']['name'], array(
                    'action' => 'index', $parent['Election']['id'])
                );
            }
        }
    }
    if (!empty($electionId)) {
        $this->Html->addCrumb('新增候選人', array(
            'action' => 'add', $electionId)
        );
    }
    ?>
    <div class="col-md-12"><?php echo $this->Html->getCrumbs(); ?></div>
    <div class="paging col-md-4"><?php echo $this->element('paginator'); ?></div>
    <div class="col-md-4"><?php
        echo $this->Form->create('Candidate', array('url' => $url, 'class' => 'form-inline'));
        echo $this->Form->input('keyword', array(
            'div' => 'form-group',
            'value' => $keyword,
            'label' => false,
        ));
        echo '<div class="btn-group">';
        echo $this->Form->submit('搜尋', array('div' => false, 'class' => 'btn btn-primary'));
        echo $this->Form->button('清除', array('div' => false, 'class' => 'btn btn-default btn-clean-form'));
        echo '</div>';
        echo $this->Form->end();
        ?></div>
    <div class="clearfix"></div>
    <?php
    if (!empty($items)) {
        foreach ($items AS $candidate) {
            ?><div class="col-md-2 btn btn-default" style="text-align: center;">
                <a href="<?php echo $this->Html->url('/candidates/view/' . $candidate['Candidate']['id']); ?>">
                    <?php
                    if (empty($candidate['Candidate']['image'])) {
                        echo $this->Html->image('candidate-not-found.jpg', array('style' => 'width: 100px; border: 0px;'));
                    } else {
                        echo $this->Html->image('../media/' . $candidate['Candidate']['image'], array('style' => 'width: 100px; border: 0px;'));
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
    <script>
        $(function() {
            $('button.btn-clean-form').click(function() {
                $('input#CandidateKeyword').val('');
                $('form#CandidateIndexForm').submit();
            });
        });
    </script>
</div>