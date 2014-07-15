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
    <table class="table table-bordered" id="CandidatesAdminIndexTable">
        <thead>
            <tr>
                <th>候選人</th>
                <th>選區</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($items as $item) {
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td><?php
                        echo $this->Html->link($item['Candidate']['name'], array('action' => 'view', $item['Candidate']['id']));
                        ?></td>
                    <td><?php
                        $c = array();
                        foreach ($item['Election'] AS $e) {
                            if ($e['Election']['rght'] - $e['Election']['lft'] != 1) {
                                $c[] = $this->Html->link($e['Election']['name'], array('controller' => 'elections', 'action' => 'index', $e['Election']['id']));
                            } else {
                                $c[] = $this->Html->link($e['Election']['name'], array('action' => 'index', $e['Election']['id']));
                            }
                        }
                        echo implode(' > ', $c);
                        ?></td>
                </tr>
            <?php } // End of foreach ($items as $item) {   ?>
        </tbody>
    </table>
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