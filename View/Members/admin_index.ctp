<div id="MembersAdminIndex">
    <h2><?php echo __('Members', true); ?></h2>
    <div class="btn-group">
        <?php echo $this->Html->link('新增', array('action' => 'add'), array('class' => 'btn btn-default dialogControl')); ?>
        <?php echo $this->Html->link(__('Groups', true), array('controller' => 'groups'), array('class' => 'btn btn-default')); ?>
        <?php echo $this->Html->link(__('Generate ACOs', true), array('action' => 'acos'), array('class' => 'btn btn-default')); ?>
    </div>
    <?php
    echo 'Filter: ' . $this->Form->text('Member.filter', array(
        'id' => 'memberFilter',
        'value' => $keyword,
    ));
    ?>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <table class="table table-bordered" id="MembersAdminIndexTable">
        <tr>
            <th><?php echo $this->Paginator->sort(__('Id', true), 'id'); ?></th>
            <th><?php echo $this->Paginator->sort(__('Account', true), 'username'); ?></th>
            <th><?php echo $this->Paginator->sort(__('Status', true), 'user_status'); ?></th>
            <th><?php echo $this->Paginator->sort(__('Created time', true), 'created'); ?></th>
            <th><?php echo $this->Paginator->sort(__('Modified time', true), 'modified'); ?></th>
            <th class="actions">操作</th>
        </tr>
        <?php
        $i = 0;
        foreach ($members as $member):
            $class = null;
            if ($i++ % 2 == 0) {
                $class = ' class="altrow"';
            }
            ?>
            <tr<?php echo $class; ?>>
                <td>
                    <?php echo $member['Member']['id']; ?>
                </td>
                <td>
                    <?php echo $member['Member']['username']; ?>
                </td>
                <td>
                    <?php echo $member['Member']['user_status']; ?>
                </td>
                <td>
                    <?php echo $member['Member']['created']; ?>
                </td>
                <td>
                    <?php echo $member['Member']['modified']; ?>
                </td>
                <td class="actions">
                    <?php echo $this->Html->link('編輯', array('action' => 'edit', $member['Member']['id']), array('class' => 'btn btn-default dialogControl')); ?>
                    <?php echo $this->Html->link('刪除', array('action' => 'delete', $member['Member']['id']), array('class' => 'btn btn-default'), '確定刪除？'); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <div id="MembersAdminIndexPanel"></div>
    <?php
    $jsUri = $this->Html->url() . '/index';
    echo $this->Html->scriptBlock('
$(function() {
    $(\'#MembersAdminIndexTable th a, #MembersAdminIndex div.paging a\').click(function() {
        $(\'#MembersAdminIndex\').parent().load(this.href);
        return false;
    });
    $(\'#memberFilter\').autocomplete({
        delay: 1000,
        minLength: 0,
        search: function(event, ui) {
            var targetUri = \'' . $jsUri . '/keyword:\' + $(this).val();
            $(\'#MembersAdminIndex\').parent().load(encodeURI(targetUri));
            return false;
        }
    });
});
');
    ?>
</div>
