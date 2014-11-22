<div class="bulletins form">
    <?php echo $this->Form->create('Bulletin'); ?>
    <fieldset>
        <legend><?php echo __('Add bulletin', true); ?></legend>
        <?php
        echo $this->Form->input('name', array('label' => 'Name', 'class' => 'form-control'));
        echo $this->Form->input('source', array('label' => 'Source', 'class' => 'form-control'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit', true)); ?>
</div>