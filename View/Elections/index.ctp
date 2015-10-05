<?php
if (!isset($url)) {
    $url = array();
}
?>
<div id="ElectionsAdminIndex">
    <h2>選舉區</h2>
    <div class="col-md-12">
        <div class="pull-right btn-group">
            <?php
            echo $this->Html->link('本頁 API', '/api/elections/index/' . $parentId, array('class' => 'btn btn-default', 'target' => '_blank'));
            ?>
        </div>
    </div>
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
    }
    ?>
    <div class="col-md-12">
        <div class="panel panel-default">
            <ul class="nav nav-pills">
                <?php foreach ($items as $item): ?>
                    <li>
                        <?php
                        if ($item['Election']['rght'] - $item['Election']['lft'] === 1) {
                            echo $this->Html->link($item['Election']['name'], array('controller' => 'candidates', 'action' => 'index', $item['Election']['id']));
                        } else {
                            echo $this->Html->link($item['Election']['name'], array('action' => 'index', $item['Election']['id']));
                        }
                        
                        ?>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
    <div id="ElectionsAdminIndexPanel"></div>
    <script>
        $(function () {
            $('#ElectionsAdminIndexTable th a, #ElectionsAdminIndex div.paging a').click(function () {
                $('#ElectionsAdminIndex').parent().load(this.href);
                return false;
            });
        <?php
        if (!empty($op)) {
            $remoteUrl = $this->Html->url(array('action' => 'habtmSet', $parentId, $foreignModel, $foreignId));
        ?>
                $('#ElectionsAdminIndexTable input.habtmSet').click(function () {
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
    </script>
</div>