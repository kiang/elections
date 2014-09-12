<h3>候選人分類</h3>
<div class="container">
    <?php
    foreach ($tags AS $tag) {
        echo '<div class="col-md-2">';
        echo $this->Html->link("{$tag['Tag']['name']} ({$tag['Tag']['count']})", '/candidates/tag/' . $tag['Tag']['id'], array('class' => 'btn btn-default'));
        echo '</div>';
    }
    ?>
</div>