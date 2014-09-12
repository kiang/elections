<div id="TagsAdminIndex">
    <h2><?php echo __('Tags', true); ?></h2>
    <p>
        <?php
        echo $this->Paginator->counter(array(
            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?>
    </p>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <table class="table table-bordered" id="TagsAdminIndexTable">
        <tr>
            <th><?php echo $this->Paginator->sort(__('Id', true), 'id'); ?></th>
            <th><?php echo $this->Paginator->sort(__('Name', true), 'name'); ?></th>
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
                    <?php echo $tag['Tag']['id']; ?>
                </td>
                <td>
                    <?php echo $tag['Tag']['name']; ?>
                </td>
                <td class="actions">
                    <?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $tag['Tag']['id']), array('class' => 'dialogControl')); ?>
                    <?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $tag['Tag']['id']), null, __('Delete the item, sure?', true)); ?>
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
