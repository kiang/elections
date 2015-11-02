<div class="members form">
    <?php echo $this->Form->create('Member'); ?>
    <legend><?php echo __('New Member', true); ?></legend>
    <?php
    echo $this->Form->input('group_id', array('div' => 'form-group', 'class' => 'form-control'));
    echo $this->Form->input('username', array('div' => 'form-group', 'class' => 'form-control'));
    echo $this->Form->input('password', array('div' => 'form-group', 'class' => 'form-control'));
    $options = array(
        'Y' => 'Y',
        'N' => 'N'
        );
    $attr = array(
        'class' => '',
        'label' => false,
        'type' => 'radio',
        'default'=> 0,
        'legend' => false,
        'before' => '<div class="radio"><label>',
        'after' => '</label></div>',
        'separator' => '</label></div><div class="radio"><label>',
        'options' => $options
        );
    echo '<strong>User Status</strong>';
    echo $this->Form->input('user_status', $attr);
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
