<?php
if (!isset($url)) {
    $url = array();
}
?>
<div id="ElectionsAdminIndex">
    <h2>選舉區</h2>
    <p>&nbsp;</p>
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
        <div class="row">
            <div class="list-group col-md-8 col-md-offset-2">
                <?php foreach ($items as $item): ?>
                    <?php
                    $arrowRight = '<i class="glyphicon glyphicon-chevron-right pull-right"></i>';
                    if(!empty($item['Area'])) {
                        $item['Election']['name'] .= '<br /><span class="text-muted">' . implode(', ', $item['Area']) . '</span>';
                    }
                    if ($item['Election']['rght'] - $item['Election']['lft'] === 1) {
                        echo $this->Html->link($item['Election']['name'] . $arrowRight, array('controller' => 'candidates', 'action' => 'index', $item['Election']['id']), array('class' => 'list-group-item', 'escape' => false));
                    } else {
                        echo $this->Html->link($item['Election']['name'] . $arrowRight, array('action' => 'index', $item['Election']['id']), array('class' => 'list-group-item', 'escape' => false));
                    }
                    ?>
                <?php endforeach ?>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="pull-right btn-group">
            <?php
            echo $this->Html->link('本頁 API', '/api/elections/index/' . $parentId, array('class' => 'btn btn-default', 'target' => '_blank'));
            ?>
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