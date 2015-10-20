<div class="bulletins form">
    <?php echo $this->Form->create('Bulletin'); ?>
    <legend><?php echo __('Add bulletin', true); ?></legend>
    <?php
    echo $this->Form->input('name', array('label' => 'Name', 'div' => 'form-group', 'class' => 'form-control'));
    echo $this->Form->input('source', array('label' => 'Source', 'div' => 'form-group', 'class' => 'form-control'));
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