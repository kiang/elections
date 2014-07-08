<div id="AreasIndex">
    <h2><?php echo __('Areas', true); ?></h2>
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
    <table class="table table-bordered" id="AreasIndexTable">
        <thead>
            <tr>

                <th><?php echo $this->Paginator->sort('Area.parent_id', 'Parent', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Area.name', 'Name', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Area.lft', 'Left', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Area.rght', 'Right', array('url' => $url)); ?></th>
                <th><?php echo $this->Paginator->sort('Area.is_area', 'Is Area?', array('url' => $url)); ?></th>
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
                    echo $item['Area']['parent_id'];
                    ?></td>
                    <td><?php
                    echo $item['Area']['name'];
                    ?></td>
                    <td><?php
                    echo $item['Area']['lft'];
                    ?></td>
                    <td><?php
                    echo $item['Area']['rght'];
                    ?></td>
                    <td><?php
                    echo $item['Area']['is_area'];
                    ?></td>
                    <td class="actions">
                        <?php echo $this->Html->link(__('View', true), array('action' => 'view', $item['Area']['id']), array('class' => 'AreasIndexControl')); ?>
                    </td>
                </tr>
            <?php }; // End of foreach ($items as $item) {  ?>
        </tbody>
    </table>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <div id="AreasIndexPanel"></div>
    <script type="text/javascript">
        //<![CDATA[
        $(function() {
            $('#AreasIndexTable th a, div.paging a, a.AreasIndexControl').click(function() {
                $('#AreasIndex').parent().load(this.href);
                return false;
            });
        });
        //]]>
    </script>
</div>