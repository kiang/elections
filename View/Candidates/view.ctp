<div id="CandidatesView">
    <h3><?php echo __('View Candidates', true); ?></h3><hr />
    <div class="col-md-12">
        <div class="col-md-2">Elections</div>
        <div class="col-md-9"><?php
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
        <div class="col-md-9"><?php
            if ($this->data['Candidate']['name']) {

                echo $this->data['Candidate']['name'];
            }
?>&nbsp;
        </div>
    </div>
    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('Candidates List', true), array('action' => 'index')); ?> </li>
        </ul>
    </div>
    <div id="CandidatesViewPanel"></div>
    <script type="text/javascript">
        //<![CDATA[
        $(function() {
            $('a.CandidatesViewControl').click(function() {
                $('#CandidatesViewPanel').parent().load(this.href);
                return false;
            });
        });
        //]]>
    </script>
</div>