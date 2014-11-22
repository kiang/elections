<div class="bulletins form">
    <?php
    echo $this->Form->create('Bulletin');
    echo $this->Form->input('id', array('label' => 'ID', 'type' => 'text', 'class' => 'form-control'));
    echo $this->Form->input('name', array('label' => 'Name', 'class' => 'form-control'));
    echo $this->Form->input('source', array('label' => 'Source', 'class' => 'form-control'));
    ?>
    <?php echo $this->Form->end(__('Submit', true)); ?>
</div>