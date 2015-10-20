<div class="tags form">
    <?php echo $this->Form->create('Tag'); ?>
    <fieldset>
        <legend><?php echo __('Edit tag', true); ?></legend>
        <?php
        echo $this->Form->input('id');
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