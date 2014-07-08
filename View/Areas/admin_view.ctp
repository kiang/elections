<div id="AreasAdminView">
    <h3><?php echo __('View Areas', true); ?></h3><hr />
    <div class="col-md-12">

        <div class="col-md-2">Parent</div>
        <div class="col-md-9">&nbsp;<?php
            if ($this->data['Area']['parent_id']) {

                echo $this->data['Area']['parent_id'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Name</div>
        <div class="col-md-9">&nbsp;<?php
            if ($this->data['Area']['name']) {

                echo $this->data['Area']['name'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Left</div>
        <div class="col-md-9">&nbsp;<?php
            if ($this->data['Area']['lft']) {

                echo $this->data['Area']['lft'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Right</div>
        <div class="col-md-9">&nbsp;<?php
            if ($this->data['Area']['rght']) {

                echo $this->data['Area']['rght'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Is Area?</div>
        <div class="col-md-9">&nbsp;<?php
            if ($this->data['Area']['is_area']) {

                echo $this->data['Area']['is_area'];
            }
?>&nbsp;
        </div>
    </div>
    <hr />
    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Area.id')), null, __('Delete the item, sure?', true)); ?></li>
            <li><?php echo $this->Html->link(__('Areas List', true), array('action' => 'index')); ?> </li>
            <li><?php echo $this->Html->link(__('View Related Elections', true), array('controller' => 'elections', 'action' => 'index', 'Area', $this->data['Area']['id']), array('class' => 'AreasAdminViewControl')); ?></li>
            <li><?php echo $this->Html->link(__('Set Related Elections', true), array('controller' => 'elections', 'action' => 'index', 'Area', $this->data['Area']['id'], 'set'), array('class' => 'AreasAdminViewControl')); ?></li>
        </ul>
    </div>
    <div id="AreasAdminViewPanel"></div>
<?php
echo $this->Html->scriptBlock('

');
?>
    <script type="text/javascript">
        //<![CDATA[
        $(function() {
            $('a.AreasAdminViewControl').click(function() {
                $('#AreasAdminViewPanel').parent().load(this.href);
                return false;
            });
        });
        //]]>
    </script>
</div>