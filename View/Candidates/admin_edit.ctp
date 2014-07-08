<div id="CandidatesAdminEdit">
    <?php echo $this->Form->create('Candidate', array('type' => 'file')); ?>
    <div class="Candidates form">
        <fieldset>
            <legend><?php
                echo __('Edit Candidates', true);
                ?></legend>
            <?php
            echo $this->Form->input('Candidate.id');
            foreach ($belongsToModels AS $key => $model) {
                echo $this->Form->input('Candidate.' . $model['foreignKey'], array(
                    'type' => 'select',
                    'label' => $model['label'],
                    'options' => $$key,
                    'div' => 'form-group',
                    'class' => 'form-control',
                ));
            }
            echo $this->Form->input('Candidate.name', array(
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