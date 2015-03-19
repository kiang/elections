<?php
if (!empty($parents)) {
    foreach ($parents AS $parent) {
        if ($parent['Election']['rght'] - $parent['Election']['lft'] !== 1) {
            $this->Html->addCrumb($parent['Election']['name'], array(
                'action' => 'index', $parent['Election']['id'])
            );
        } else {
            $this->Html->addCrumb($parent['Election']['name']);
        }
    }
}
if (!empty($errors)) {
    ?><div class="alert alert-danger" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>
        找不到資料： <?php echo implode(' ', $errors); ?>
    </div><?php
}
?>
<h3><?php echo $election['Election']['name']; ?></h3>
<div class="container">
    <?php
    echo $this->Form->create();
    echo $this->Form->input('scope', array(
        'type' => 'text',
        'label' => '範圍',
        'div' => 'form-group',
        'class' => 'form-control',
    ));
    echo $this->Form->input('areas', array(
        'type' => 'textarea',
        'label' => '區域(半形空白與斷行區隔)',
        'div' => 'form-group',
        'class' => 'form-control',
    ));
    echo $this->Form->hidden('area_id');
    echo $this->Form->end('送出');
    ?>
</div>
<script type="text/javascript">
    //<![CDATA[
    $(function () {
        $('input#ElectionScope').autocomplete({
            source: '<?php echo $this->Html->url('/areas/s/'); ?>',
            select: function (e, ui) {
                $('input#ElectionAreaId').val(ui.item.id);
            }
        }).click(function () {
            $(this).select();
        });
    });
    //]]>
</script>