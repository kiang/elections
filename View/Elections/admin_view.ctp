<div id="ElectionsAdminView">
    <h3><?php echo __('View Elections', true); ?></h3><hr />
    <div class="col-md-12">

        <div class="col-md-2">Parent</div>
        <div class="col-md-9">&nbsp;<?php
            if ($this->data['Election']['parent_id']) {

                echo $this->data['Election']['parent_id'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Name</div>
        <div class="col-md-9">&nbsp;<?php
            if ($this->data['Election']['name']) {

                echo $this->data['Election']['name'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Left</div>
        <div class="col-md-9">&nbsp;<?php
            if ($this->data['Election']['lft']) {

                echo $this->data['Election']['lft'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Right</div>
        <div class="col-md-9">&nbsp;<?php
            if ($this->data['Election']['rght']) {

                echo $this->data['Election']['rght'];
            }
?>&nbsp;
        </div>
    </div>
    <hr />
    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Election.id')), null, __('Delete the item, sure?', true)); ?></li>
            <li><?php echo $this->Html->link(__('Elections List', true), array('action' => 'index')); ?> </li>
            <li><?php echo $this->Html->link(__('View Related Candidates', true), array('controller' => 'candidates', 'action' => 'index', 'Election', $this->data['Election']['id']), array('class' => 'ElectionsAdminViewControl')); ?></li>
            <li><?php echo $this->Html->link(__('View Related Areas', true), array('controller' => 'areas', 'action' => 'index', 'Election', $this->data['Election']['id']), array('class' => 'ElectionsAdminViewControl')); ?></li>
            <li><?php echo $this->Html->link(__('Set Related Areas', true), array('controller' => 'areas', 'action' => 'index', 'Election', $this->data['Election']['id'], 'set'), array('class' => 'ElectionsAdminViewControl')); ?></li>
        </ul>
    </div>
    <div id="ElectionsAdminViewPanel"></div>
<?php
echo $this->Html->scriptBlock('

');
?>
    <script type="text/javascript">
        //<![CDATA[
        $(function() {
            $('a.ElectionsAdminViewControl').click(function() {
                $('#ElectionsAdminViewPanel').parent().load(this.href);
                return false;
            });
        });
        //]]>
    </script>
</div>