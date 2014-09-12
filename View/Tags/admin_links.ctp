<h3><?php echo $tag['Tag']['name']; ?></h3>
<div class="container">
    <input type="text" id="tagCandidate" class="form-control" placeholder="新增候選人到這個標籤" />
</div>
<div class="container">
    <?php
    foreach ($tag['Candidate'] AS $candidate) {
        echo '<div class="col-md-2">';
        echo $this->Html->link($candidate['name'], '/candidates/view/' . $candidate['id'], array('target' => '_blank', 'class' => 'btn btn-default'));
        echo $this->Html->link('[X]', array('action' => 'link_delete', $candidate['CandidatesTag']['id']), array('class' => 'btn btn-default btn-link-delete'));
        echo '</div>';
    }
    ?>
</div>
<script type="text/javascript">
    //<![CDATA[
    $(function() {
        $('input#tagCandidate').autocomplete({
            source: '<?php echo $this->Html->url('/candidates/s/'); ?>',
            select: function(event, ui) {
                $.get('<?php echo $this->Html->url('/admin/tags/link_add/' . $tag['Tag']['id']); ?>/' + ui.item.id, {}, function() {
                    $('div#viewContent').load('<?php echo $this->Html->url('/admin/tags/links/' . $tag['Tag']['id']); ?>');
                });
            }
        });
        $('a.btn-link-delete').click(function() {
            $.get(this.href, {}, function() {
                $('div#viewContent').load('<?php echo $this->Html->url('/admin/tags/links/' . $tag['Tag']['id']); ?>');
            });
            return false;
        });
    });
    //]]>
</script>