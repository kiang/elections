<div id="CandidatesAdminIndex">
    <h2><?php echo __('Candidates', true); ?></h2>
    <div class="btn-group">
        <?php
        echo $this->Html->link('回清單', array('action' => 'index'), array('class' => 'btn'));
        ?>
    </div>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <table class="table table-bordered" id="CandidatesAdminIndexTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Created</th>
                <th class="actions"><?php echo __('Action', true); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($items as $item) {
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td><?php
                        echo $item['Candidate']['name'];
                        ?></td>
                    <td><?php
                        echo $item['Candidate']['created'];
                        ?></td>
                    <td class="actions">
                        <?php echo $this->Html->link('審核', array('action' => 'review', $item['Candidate']['id'])); ?>
                        <?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $item['Candidate']['id']), array('class' => 'dialogControl')); ?>
                        <?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $item['Candidate']['id']), null, __('Delete the item, sure?', true)); ?>
                    </td>
                </tr>
            <?php } // End of foreach ($items as $item) {   ?>
        </tbody>
    </table>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
</div>