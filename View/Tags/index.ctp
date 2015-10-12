<h2>候選人分類</h2>
<p>&nbsp;</p>
<div class="list-group col-md-8 col-md-offset-2">
<?php
foreach ($tags AS $tag) {
    echo $this->Html->link(
    	"{$tag['Tag']['name']}".
    	'<i class="glyphicon glyphicon-chevron-right pull-right"></i>&nbsp;'.
    	'<span class="badge">' . $tag['Tag']['count'] . '</span>',
    	'/candidates/tag/' . $tag['Tag']['id'],
    	array(
    		'class' => 'list-group-item',
    		'escape' => false
    	)
    );
}
?>
</div>
<div class="paginator-wrapper col-md-12"><?php echo $this->element('paginator'); ?></div>