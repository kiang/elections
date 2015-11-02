<?php echo $this->Form->create('Bulletin'); ?>
<div class="bulletins form">
    <?php
    echo $this->Form->input('id', array('label' => 'ID', 'type' => 'text', 'class' => 'form-control'));
    echo $this->Form->input('name', array('label' => 'Name', 'class' => 'form-control'));
    echo $this->Form->input('source', array('label' => 'Source', 'class' => 'form-control'));
    echo $this->Html->tag(
        'button',
        'Submit',
        array(
            'class' => 'btn btn-primary',
            'type' => 'submit'
        )
    );
    ?>
</div>
<?php echo $this->Form->end(); ?>