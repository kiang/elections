<div id="AreasAdminIndex">
    <h2><?php echo __('Areas', true); ?></h2>
    <div class="btn-group">
        <?php echo $this->Html->link('新增', array('action' => 'add', $parentId), array('class' => 'btn dialogControl')); ?>
    </div>
    <div class="clearfix"></div>
    <?php
    if (!empty($parents)) {
        $this->Html->addCrumb('最上層', array('action' => 'index'));
        foreach ($parents AS $parent) {
            $this->Html->addCrumb($parent['Area']['name'], array('action' => 'index', $parent['Area']['id']));
        }
        echo $this->Html->getCrumbs();
    }
    if (!empty($elections)) {
        echo '<ul>';
        foreach ($elections AS $election) {
            $c = array();
            foreach ($election['Election'] AS $parent) {
                $c[] = $this->Html->link($parent['Election']['name'], array('controller' => 'elections', 'action' => 'index', $parent['Election']['id']));
            }
            echo '<li>' . implode(' > ', $c) . '</li>';
        }
        echo '</ul>';
    }
    ?>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <table class="table table-bordered" id="AreasAdminIndexTable">
        <thead>
            <tr>
                <?php
                if (!empty($op)) {
                    echo '<th>&nbsp;</th>';
                }
                ?>
                <th><?php echo $this->Paginator->sort('Area.ivid', 'Ivid', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Area.code', 'Code', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Area.name', 'Name', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Area.is_area', 'Is Area?', array('url' => $url)); ?></th>
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
                        echo $item['Area']['ivid'];
                        ?></td>
                    <td><?php
                        echo $item['Area']['code'];
                        ?></td>
                    <td><?php
                        echo $this->Html->link($item['Area']['name'], array('action' => 'index', $item['Area']['id']));
                        ?></td>
                    <td><?php
                        echo $item['Area']['is_area'];
                        ?></td>
                    <td class="actions">
                        <?php echo $this->Html->link(__('View', true), array('action' => 'view', $item['Area']['id']), array('class' => 'dialogControl')); ?>
                        <?php echo $this->Html->link('編輯', array('action' => 'edit', $item['Area']['id']), array('class' => 'dialogControl')); ?>
                        <?php echo $this->Html->link('刪除', array('action' => 'delete', $item['Area']['id']), null, '確定刪除？'); ?>
                    </td>
                </tr>
            <?php } // End of foreach ($items as $item) {   ?>
        </tbody>
    </table>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <div id="AreasAdminIndexPanel"></div>
    <script type="text/javascript">
        //<![CDATA[
        $(function() {
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
                    $('div#messageSet' + this.value).load(remoteUrl);
                });
    <?php
}
?>
        });
        //]]>
    </script>
</div>