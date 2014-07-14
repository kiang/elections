<?php
if (!isset($url)) {
    $url = array();
}
?>
<div id="ElectionsAdminIndex">
    <h2>選舉區</h2>
    <div class="clearfix"></div>
    <?php
    if (!empty($parents)) {
        foreach ($parents AS $parent) {
            if ($parent['Election']['rght'] - $parent['Election']['lft'] !== 1) {
                $this->Html->addCrumb($parent['Election']['name'], array(
                    'action' => 'index', $parent['Election']['id'])
                );
            } else {
                $this->Html->addCrumb($parent['Election']['name'], array(
                    'controller' => 'candidates',
                    'action' => 'index', $parent['Election']['id'])
                );
            }
        }
        echo $this->Html->getCrumbs();
    }
    ?>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <table class="table table-bordered" id="ElectionsAdminIndexTable">
        <thead>
            <tr>
                <th>選區名稱</th>
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
                    <td><?php
                        echo $item['Election']['name'];
                        ?></td>
                    <td class="actions">
                        <?php
                        if ($item['Election']['rght'] - $item['Election']['lft'] === 1) {
                            echo ' ' . $this->Html->link('候選人清單', array('controller' => 'candidates', 'action' => 'index', $item['Election']['id']));
                            echo ' ' . $this->Html->link('新增候選人', array('controller' => 'candidates', 'action' => 'add', $item['Election']['id']));
                        } else {
                            echo ' ' . $this->Html->link('下一層', array('action' => 'index', $item['Election']['id']));
                        }
                        ?>
                    </td>
                </tr>
            <?php } // End of foreach ($items as $item) {  ?>
        </tbody>
    </table>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <div id="ElectionsAdminIndexPanel"></div>
    <script type="text/javascript">
        //<![CDATA[
        $(function() {
            $('#ElectionsAdminIndexTable th a, #ElectionsAdminIndex div.paging a').click(function() {
                $('#ElectionsAdminIndex').parent().load(this.href);
                return false;
            });
<?php
if (!empty($op)) {
    $remoteUrl = $this->Html->url(array('action' => 'habtmSet', $parentId, $foreignModel, $foreignId));
    ?>
                $('#ElectionsAdminIndexTable input.habtmSet').click(function() {
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