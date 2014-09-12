<div class="tags form">
    <?php echo $this->Form->create('Tag'); ?>
    <fieldset>
        <legend><?php echo __('Add tag', true); ?></legend>
        <?php
        echo $this->Form->input('name', array('label' => __('Name', true)));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit', true)); ?>
</div>