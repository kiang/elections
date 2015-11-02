<div class="groups form">
    <?php echo $this->Form->create('Group', array('url' => array($parentId))); ?>
    <fieldset>
        <legend><?php echo __('Add group', true); ?></legend>
        <?php
        echo $this->Form->input('name', array('label' => __('Name', true), 'div' => 'form-group', 'class' => 'form-control'));
        echo $this->Html->tag(
            'button',
            'Submit',
            array(
                'class' => 'btn btn-primary form-control',
                'type' => 'submit'
            )
        );
        ?>
    </fieldset>
    <?php echo $this->Form->end(); ?>
</div>
<div class="actions">
    <ul>
        <li><?php echo $this->Html->link(__('List', true), array('action' => 'index')); ?></li>
    </ul>
</div>
