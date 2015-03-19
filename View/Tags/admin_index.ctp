<div id="TagsAdminIndex">
    <h2><?php echo __('Tags', true); ?></h2>
    <div class="btn-group">
        <?php
        echo $this->Html->link('新增', array('action' => 'add'), array('class' => 'btn btn-default'));
        ?>
    </div>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <table class="table table-bordered" id="TagsAdminIndexTable">
        <tr>
            <th><?php echo $this->Paginator->sort('name', '名稱'); ?></th>
            <th><?php echo $this->Paginator->sort('created', '建立時間'); ?></th>
            <th class="actions"><?php __('Actions'); ?></th>
        </tr>
        <?php
        $i = 0;
        foreach ($tags as $tag):
            $class = null;
            if ($i++ % 2 == 0) {
                $class = ' class="altrow"';
            }
            ?>
            <tr<?php echo $class; ?>>
                <td>
                    <?php echo $tag['Tag']['name']; ?>
                </td>
                <td>
                    <?php echo $tag['Tag']['created']; ?>
                </td>
                <td class="actions">
                    <?php echo $this->Html->link('連結', array('action' => 'links', $tag['Tag']['id']), array('class' => 'btn btn-default')); ?>
                    <?php echo $this->Html->link('編輯', array('action' => 'edit', $tag['Tag']['id']), array('class' => 'btn btn-default dialogControl')); ?>
                    <?php
                        if (Configure::read('loginMember.group_id') == 1) {
                            echo $this->Html->link('刪除', array('action' => 'delete', $tag['Tag']['id']), array('class' => 'btn btn-default'), '確定刪除？');
                    }
                     ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <div id="TagsAdminIndexPanel"></div>
    <?php
    echo $this->Html->scriptBlock('
$(function() {
    $(\'#TagsAdminIndexTable th a, #TagsAdminIndex div.paging a\').click(function() {
        $(\'#TagsAdminIndex\').parent().load(this.href);
        return false;
    });
});
');
    ?>
</div>
