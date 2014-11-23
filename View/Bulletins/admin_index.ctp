<div id="BulletinsAdminIndex">
    <h2><?php echo __('Bulletins', true); ?></h2>
    <div class="btn-group">
        <?php
        echo $this->Html->link(__('Add', true), array('action' => 'add'), array('class' => 'btn'));
        ?>
    </div>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <table class="table table-bordered" id="BulletinsAdminIndexTable">
        <tr>
            <th><?php echo $this->Paginator->sort('id', 'ID'); ?></th>
            <th><?php echo $this->Paginator->sort('name', 'Name'); ?></th>
            <th><?php echo $this->Paginator->sort('count_elections', 'Count'); ?></th>
            <th class="actions"><?php __('Actions'); ?></th>
        </tr>
        <?php
        $i = 0;
        foreach ($bulletins as $bulletin):
            $class = null;
            if ($i++ % 2 == 0) {
                $class = ' class="altrow"';
            }
            ?>
            <tr<?php echo $class; ?>>
                <td>
                    <?php echo $bulletin['Bulletin']['id']; ?>
                </td>
                <td>
                    <?php echo $bulletin['Bulletin']['name']; ?>
                </td>
                <td>
                    <?php echo $bulletin['Bulletin']['count_elections']; ?>
                </td>
                <td class="actions">
                    <?php echo $this->Html->link(__('Links', true), array('action' => 'links', $bulletin['Bulletin']['id'])); ?>
                    <?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $bulletin['Bulletin']['id']), array('class' => 'dialogControl')); ?>
                    <?php
                    if (Configure::read('loginMember.group_id') == 1) {
                        echo $this->Html->link(__('Delete', true), array('action' => 'delete', $bulletin['Bulletin']['id']), null, __('Delete the item, sure?', true));
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <div id="BulletinsAdminIndexPanel"></div>
    <?php
    echo $this->Html->scriptBlock('
$(function() {
    $(\'#BulletinsAdminIndexTable th a, #BulletinsAdminIndex div.paging a\').click(function() {
        $(\'#BulletinsAdminIndex\').parent().load(this.href);
        return false;
    });
});
');
    ?>
</div>
