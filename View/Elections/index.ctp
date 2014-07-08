<div id="ElectionsIndex">
    <h2><?php echo __('Elections', true); ?></h2>
    <div class="clear actions">
        <ul>
        </ul>
    </div>
    <p>
        <?php
        $url = array();

        echo $this->Paginator->counter(array(
            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?></p>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <table class="table table-bordered" id="ElectionsIndexTable">
        <thead>
            <tr>

                <th><?php echo $this->Paginator->sort('Election.parent_id', 'Parent', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Election.name', 'Name', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Election.lft', 'Left', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Election.rght', 'Right', array('url' => $url)); ?></th>
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
                    echo $item['Election']['parent_id'];
                    ?></td>
                    <td><?php
                    echo $item['Election']['name'];
                    ?></td>
                    <td><?php
                    echo $item['Election']['lft'];
                    ?></td>
                    <td><?php
                    echo $item['Election']['rght'];
                    ?></td>
                    <td class="actions">
                        <?php echo $this->Html->link(__('View', true), array('action' => 'view', $item['Election']['id']), array('class' => 'ElectionsIndexControl')); ?>
                    </td>
                </tr>
            <?php }; // End of foreach ($items as $item) {  ?>
        </tbody>
    </table>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <div id="ElectionsIndexPanel"></div>
    <script type="text/javascript">
        //<![CDATA[
        $(function() {
            $('#ElectionsIndexTable th a, div.paging a, a.ElectionsIndexControl').click(function() {
                $('#ElectionsIndex').parent().load(this.href);
                return false;
            });
        });
        //]]>
    </script>
</div>