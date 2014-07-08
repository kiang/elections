<div id="AreasView">
    <h3><?php echo __('View Areas', true); ?></h3><hr />
    <div class="col-md-12">

        <div class="col-md-2">Parent</div>
        <div class="col-md-9"><?php
            if ($this->data['Area']['parent_id']) {

                echo $this->data['Area']['parent_id'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Name</div>
        <div class="col-md-9"><?php
            if ($this->data['Area']['name']) {

                echo $this->data['Area']['name'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Left</div>
        <div class="col-md-9"><?php
            if ($this->data['Area']['lft']) {

                echo $this->data['Area']['lft'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Right</div>
        <div class="col-md-9"><?php
            if ($this->data['Area']['rght']) {

                echo $this->data['Area']['rght'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Is Area?</div>
        <div class="col-md-9"><?php
            if ($this->data['Area']['is_area']) {

                echo $this->data['Area']['is_area'];
            }
?>&nbsp;
        </div>
    </div>
    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('Areas List', true), array('action' => 'index')); ?> </li>
            <li><?php echo $this->Html->link(__('View Related Elections', true), array('controller' => 'elections', 'action' => 'index', 'Area', $this->data['Area']['id']), array('class' => 'AreasViewControl')); ?></li>
        </ul>
    </div>
    <div id="AreasViewPanel"></div>
    <script type="text/javascript">
        //<![CDATA[
        $(function() {
            $('a.AreasViewControl').click(function() {
                $('#AreasViewPanel').parent().load(this.href);
                return false;
            });
        });
        //]]>
    </script>
</div>