<div id="ElectionsView">
    <h3><?php echo __('View Elections', true); ?></h3><hr />
    <div class="col-md-12">

        <div class="col-md-2">Parent</div>
        <div class="col-md-9"><?php
            if ($this->data['Election']['parent_id']) {

                echo $this->data['Election']['parent_id'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Name</div>
        <div class="col-md-9"><?php
            if ($this->data['Election']['name']) {

                echo $this->data['Election']['name'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Left</div>
        <div class="col-md-9"><?php
            if ($this->data['Election']['lft']) {

                echo $this->data['Election']['lft'];
            }
?>&nbsp;
        </div>
        <div class="col-md-2">Right</div>
        <div class="col-md-9"><?php
            if ($this->data['Election']['rght']) {

                echo $this->data['Election']['rght'];
            }
?>&nbsp;
        </div>
    </div>
    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('Elections List', true), array('action' => 'index')); ?> </li>
            <li><?php echo $this->Html->link(__('View Related Candidates', true), array('controller' => 'candidates', 'action' => 'index', 'Election', $this->data['Election']['id']), array('class' => 'ElectionsViewControl')); ?></li>
            <li><?php echo $this->Html->link(__('View Related Areas', true), array('controller' => 'areas', 'action' => 'index', 'Election', $this->data['Election']['id']), array('class' => 'ElectionsViewControl')); ?></li>
        </ul>
    </div>
    <div id="ElectionsViewPanel"></div>
    <script type="text/javascript">
        //<![CDATA[
        $(function() {
            $('a.ElectionsViewControl').click(function() {
                $('#ElectionsViewPanel').parent().load(this.href);
                return false;
            });
        });
        //]]>
    </script>
</div>