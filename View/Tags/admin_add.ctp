<div class="tags form">
    <?php echo $this->Form->create('Tag'); ?>
    <legend><?php echo __('Add tag', true); ?></legend>
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
    <?php echo $this->Form->end(); ?>
</div>