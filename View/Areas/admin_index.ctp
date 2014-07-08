<?php
if (!isset($url)) {
    $url = array();
}
?>
<div id="AreasAdminIndex">
    <h2><?php echo __('Areas', true); ?></h2>
    <div class="btn-group">
        <?php echo $this->Html->link(__('Add', true), array('action' => 'add'), array('class' => 'btn dialogControl')); ?>
    </div>
    <div><?php
        echo $this->Paginator->counter(array(
            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?></div>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <table class="table table-bordered" id="AreasAdminIndexTable">
        <thead>
            <tr>
                <?php
                if (!empty($op)) {
                    echo '<th>&nbsp;</th>';
                }
                ?>

                <th><?php echo $this->Paginator->sort('Area.parent_id', 'Parent', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Area.name', 'Name', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Area.lft', 'Left', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Area.rght', 'Right', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Area.is_area', 'Is Area?', array('url' => $url)); ?></th>
                <th class="actions"><?php echo __('Action', true); ?></th>
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
                    <?php
                    if (!empty($op)) {
                        echo '<td>';
                        $options = array('value' => $item['Area']['id'], 'class' => 'habtmSet');
                        if ($item['option'] == 1) {
                            $options['checked'] = 'checked';
                        }
                        echo $this->Form->checkbox('Set.' . $item['Area']['id'], $options);
                        echo '<div id="messageSet' . $item['Area']['id'] . '"></div></td>';
                    }
                    ?>

                    <td><?php
                    echo $item['Area']['parent_id'];
                    ?></td>
                    <td><?php
                    echo $item['Area']['name'];
                    ?></td>
                    <td><?php
                    echo $item['Area']['lft'];
                    ?></td>
                    <td><?php
                    echo $item['Area']['rght'];
                    ?></td>
                    <td><?php
                    echo $item['Area']['is_area'];
                    ?></td>
                    <td class="actions">
                        <?php echo $this->Html->link(__('View', true), array('action' => 'view', $item['Area']['id']), array('class' => 'dialogControl')); ?>
                        <?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $item['Area']['id']), array('class' => 'dialogControl')); ?>
                        <?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $item['Area']['id']), null, __('Delete the item, sure?', true)); ?>
                    </td>
                </tr>
            <?php } // End of foreach ($items as $item) {  ?>
        </tbody>
    </table>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <div id="AreasAdminIndexPanel"></div>
    <script type="text/javascript">
        //<![CDATA[
        $(function() {
            $('#AreasAdminIndexTable th a, #AreasAdminIndex div.paging a').click(function() {
                $('#AreasAdminIndex').parent().load(this.href);
                return false;
            });
<?php
if (!empty($op)) {
    $remoteUrl = $this->Html->url(array('action' => 'habtmSet', $foreignModel, $foreignId));
    ?>
                $('#AreasAdminIndexTable input.habtmSet').click(function() {
                    var remoteUrl = '<?php echo $remoteUrl; ?>/' + this.value + '/';
                    if (this.checked == true) {
                        remoteUrl = remoteUrl + 'on';
                    } else {
                        remoteUrl = remoteUrl + 'off';
                    }
                    $('div#messageSet' + this.value) . load(remoteUrl);
                });
    <?php
}
?>
    });
    //]]>
    </script>
</div>