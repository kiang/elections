<?php
if (!empty($parents)) {
    foreach ($parents as $parent) {
        if ($parent['Election']['rght'] - $parent['Election']['lft'] !== 1) {
            $this->Html->addCrumb(
                $parent['Election']['name'],
                array(
                    'action' => 'index', $parent['Election']['id']
                )
            );
        } else {
            $this->Html->addCrumb($parent['Election']['name']);
        }
    }
}
?>
<div id="viewContent">
    <h3><?php echo $election['Election']['name']; ?></h3>
    <div class="container">
        <input type="text" id="electionArea" class="form-control" placeholder="新增行政區到這個選區" />
    </div>
    <div class="container">
        <?php
        foreach ($election['Area'] as $area) {
            echo '<div class="col-md-2">';
            echo $this->Html->link($area['name'], '/areas/index/' . $area['id'], array('target' => '_blank', 'class' => 'btn btn-default'));
            echo $this->Html->link('[X]', array('action' => 'link_delete', $area['AreasElection']['id']), array('class' => 'btn btn-default btn-link-delete'));
            echo '</div>';
        }
        ?>
    </div>
    <script>
        $(function() {
            $('#electionArea').autocomplete({
                source: '<?php echo $this->Html->url('/areas/s/'); ?>',
                select: function(event, ui) {
                    $.get('<?php echo $this->Html->url('/admin/elections/link_add/' . $election['Election']['id']); ?>/' + ui.item.id, {}, function(b) {
                        $('div#viewContent').load('<?php echo $this->Html->url('/admin/elections/links/' . $election['Election']['id']); ?>');
                    });
                }
            });
            $('a.btn-link-delete').click(function() {
                $.get(this.href, {}, function() {
                    $('div#viewContent').load('<?php echo $this->Html->url('/admin/elections/links/' . $election['Election']['id']); ?>');
                });
                return false;
            });
        });
    </script>
</div>