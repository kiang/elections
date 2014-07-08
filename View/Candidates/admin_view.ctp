<div id="CandidatesAdminView">
    <h3><?php echo __('View Candidates', true); ?></h3><hr />
    <div class="col-md-12">
        <div class="col-md-2">Elections</div>
        <div class="col-md-9">&nbsp;<?php
if (empty($this->data['Election']['id'])) {
    echo '--';
} else {
    echo $this->Html->link($this->data['Election']['id'], array(
        'controller' => 'elections',
        'action' => 'view',
        $this->data['Election']['id']
    ));
}
?></div>

        <div class="col-md-2">Name</div>
        <div class="col-md-9">&nbsp;<?php
            if ($this->data['Candidate']['name']) {

                echo $this->data['Candidate']['name'];
            }
?>&nbsp;
        </div>
    </div>
    <hr />
    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Candidate.id')), null, __('Delete the item, sure?', true)); ?></li>
            <li><?php echo $this->Html->link(__('Candidates List', true), array('action' => 'index')); ?> </li>
        </ul>
    </div>
    <div id="CandidatesAdminViewPanel"></div>
<?php
echo $this->Html->scriptBlock('

');
?>
    <script type="text/javascript">
        //<![CDATA[
        $(function() {
            $('a.CandidatesAdminViewControl').click(function() {
                $('#CandidatesAdminViewPanel').parent().load(this.href);
                return false;
            });
        });
        //]]>
    </script>
</div>