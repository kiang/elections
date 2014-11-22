<h3>候選人分類</h3>
<div class="container">
    <?php
    foreach ($bulletins AS $bulletin) {
        echo $this->Html->link("{$bulletin['Bulletin']['name']} ({$bulletin['Bulletin']['count']})", '/elections/bulletin/' . $bulletin['Bulletin']['id'], array('class' => 'btn btn-default'));
    }
    ?>
</div>
<div class="paging"><?php echo $this->element('paginator'); ?></div>