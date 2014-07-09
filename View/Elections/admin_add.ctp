<div id="ElectionsAdminAdd">
        <?php echo $this->Form->create('Election', array('type' => 'file')); ?>
    <div class="Elections form">
        <fieldset>
            <legend><?php
                echo __('Add Elections', true);
                ?></legend>
            <?php
            echo $this->Form->input('Election.name', array(
                'label' => 'Name',
                'div' => 'form-group',
                'class' => 'form-control',
            ));
            ?>
        </fieldset>
    </div>
        <?php
    echo $this->Form->end(__('Submit', true));
    ?>
</div>