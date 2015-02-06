<div id="AreasAdminView">
    <h3><?php echo __('View Areas', true); ?></h3><hr />
    <div class="col-md-12">

        <div class="col-md-2">Parent</div>
        <div class="col-md-9">&nbsp;<?php
            echo $this->data['Area']['parent_id'];
            ?>&nbsp;
        </div>
        <div class="col-md-2">Ivid</div>
        <div class="col-md-9">&nbsp;<?php
            echo $this->data['Area']['ivid'];
            ?>&nbsp;
        </div>
        <div class="col-md-2">Code</div>
        <div class="col-md-9">&nbsp;<?php
            echo $this->data['Area']['code'];
            ?>&nbsp;
        </div>
        <div class="col-md-2">Name</div>
        <div class="col-md-9">&nbsp;<?php
            echo $this->data['Area']['name'];
            ?>&nbsp;
        </div>
        <div class="col-md-2">Is Area?</div>
        <div class="col-md-9">&nbsp;<?php
            echo $this->data['Area']['is_area'];
            ?>&nbsp;
        </div>
    </div>
    <hr />
    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link('刪除', array('action' => 'delete', $this->Form->value('Area.id')), null, '確定刪除？'); ?></li>
            <li><?php echo $this->Html->link(__('Areas List', true), array('action' => 'index')); ?> </li>
            <li><?php echo $this->Html->link(__('View Related Elections', true), array('controller' => 'elections', 'action' => 'index', 'Area', $this->data['Area']['id']), array('class' => 'AreasAdminViewControl')); ?></li>
            <li><?php echo $this->Html->link(__('Set Related Elections', true), array('controller' => 'elections', 'action' => 'index', 'Area', $this->data['Area']['id'], 'set'), array('class' => 'AreasAdminViewControl')); ?></li>
        </ul>
    </div>
    <div id="AreasAdminViewPanel"></div>
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