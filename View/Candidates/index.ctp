<div id="CandidatesIndex">
    <h2><?php echo __('Candidates', true); ?></h2>
    <div class="clear actions">
        <ul>
        </ul>
    </div>
    <p>
        <?php
        $url = array();

        if (!empty($foreignId) && !empty($foreignModel)) {
            $url = array($foreignModel, $foreignId);
        }

        echo $this->Paginator->counter(array(
            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?></p>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <table class="table table-bordered" id="CandidatesIndexTable">
        <thead>
            <tr>
                <?php if (empty($scope['Candidate.Election_id'])): ?>
                    <th><?php echo $this->Paginator->sort('Candidate.Election_id', 'Elections', array('url' => $url)); ?></th>
                <?php endif; ?>

                <th><?php echo $this->Paginator->sort('Candidate.name', 'Name', array('url' => $url)); ?></th>
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
                    <?php if (empty($scope['Candidate.Election_id'])): ?>
                        <td><?php
                if (empty($item['Election']['id'])) {
                    echo '--';
                } else {
                    echo $this->Html->link($item['Election']['id'], array(
                        'controller' => 'elections',
                        'action' => 'view',
                        $item['Election']['id']
                    ));
                }
                        ?></td>
                    <?php endif; ?>

                    <td><?php
                    echo $item['Candidate']['name'];
                    ?></td>
                    <td class="actions">
                        <?php echo $this->Html->link(__('View', true), array('action' => 'view', $item['Candidate']['id']), array('class' => 'CandidatesIndexControl')); ?>
                    </td>
                </tr>
            <?php }; // End of foreach ($items as $item) {  ?>
        </tbody>
    </table>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <div id="CandidatesIndexPanel"></div>
    <script type="text/javascript">
        //<![CDATA[
        $(function() {
            $('#CandidatesIndexTable th a, div.paging a, a.CandidatesIndexControl').click(function() {
                $('#CandidatesIndex').parent().load(this.href);
                return false;
            });
        });
        //]]>
    </script>
</div>