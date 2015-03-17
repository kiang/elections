<div id="ElectionsAdminAdd">
    <?php echo $this->Form->create('Election', array('type' => 'file')); ?>
    <div class="Elections form">
        <fieldset>
            <legend><?php
                echo __('Add Elections', true);
                ?></legend>
            <?php
            echo $this->Form->input('Election.name', array(
                'type' => 'text',
                'label' => '名稱',
                'div' => 'form-group',
                'class' => 'form-control',
            ));
            echo $this->Form->input('Election.population', array(
                'type' => 'text',
                'label' => '選區人口',
                'value' => '0',
                'div' => 'form-group',
                'class' => 'form-control',
            ));
            echo $this->Form->input('Election.population_electors', array(
                'type' => 'text',
                'label' => '選舉人數量',
                'value' => '0',
                'div' => 'form-group',
                'class' => 'form-control',
            ));
            echo $this->Form->input('Election.quota', array(
                'type' => 'text',
                'label' => '選區名額',
                'value' => '0',
                'div' => 'form-group',
                'class' => 'form-control',
            ));
            echo $this->Form->input('Election.quota_women', array(
                'type' => 'text',
                'label' => '女性保障名額',
                'value' => '0',
                'div' => 'form-group',
                'class' => 'form-control',
            ));
            echo $this->Form->input('Election.bulletin_key', array(
                'type' => 'text',
                'label' => '公報uuid',
                'div' => 'form-group',
                'class' => 'form-control',
            ));
            echo $this->Form->input('Election.keywords', array(
                'type' => 'text',
                'label' => '關鍵字',
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