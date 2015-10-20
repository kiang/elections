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
    var currentTagId = '<?php echo $tag['Tag']['id']; ?>';
    //]]>
</script>
<?php echo $this->Html->script('Tags/admin_links.js', array('inline' => false, 'block' => 'scriptBottom')); ?>