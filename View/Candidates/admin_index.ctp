<div id="CandidatesAdminIndex">
    <h2><?php echo __('Candidates', true); ?></h2>
    <div class="btn-group">
        <?php
        if (!empty($electionId)) {
            echo $this->Html->link('新增', array('action' => 'add', $electionId), array('class' => 'btn btn-default'));
        }
        echo $this->Html->link('審核', array('action' => 'submits'), array('class' => 'btn btn-default'));
        ?>
    </div>
    <div class="clearfix"></div>
    <?php
    if (!empty($parents)) {
        $this->Html->addCrumb('最上層', array('controller' => 'elections', 'action' => 'index'));
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
        echo $this->Html->getCrumbs();
    }
    ?>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <table class="table table-bordered" id="CandidatesAdminIndexTable">
        <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Candidate.name', '姓名', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Candidate.created', '建立時間', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Candidate.modified', '更新時間', array('url' => $url)); ?></th>
                <th class="actions">操作</th>
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
                    <td><?php echo $item['Candidate']['name']; ?></td>
                    <td><?php echo $item['Candidate']['created']; ?></td>
                    <td><?php echo $item['Candidate']['modified']; ?></td>
                    <td class="actions">
                        <?php echo $this->Html->link('檢視', array('action' => 'view', $item['Candidate']['id']), array('class' => 'btn btn-default')); ?>
                        <?php echo $this->Html->link('編輯', array('action' => 'edit', $item['Candidate']['id']), array('class' => 'btn btn-default')); ?>
                        <?php
                        if (Configure::read('loginMember.group_id') == 1) {
                            echo $this->Html->link('刪除', array('action' => 'delete', $item['Candidate']['id']), array('class' => 'btn btn-default'), '確定刪除？');
                        }
                        ?>
                    </td>
                </tr>
            <?php } // End of foreach ($items as $item) {   ?>
        </tbody>
    </table>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <div id="CandidatesAdminIndexPanel"></div>
    <script type="text/javascript">
        //<![CDATA[
        $(function () {
            $('#CandidatesAdminIndexTable th a, #CandidatesAdminIndex div.paging a').click(function () {
                $('#CandidatesAdminIndex').parent().load(this.href);
                return false;
            });
        });
        //]]>
    </script>
</div>