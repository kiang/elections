<div id="AreasAdminIndex">
    <h2><?php echo __('Areas', true); ?></h2>
    <div class="clearfix"></div>
    <?php
    if (!empty($parents)) {
        $this->Html->addCrumb('最上層', array('action' => 'index'));
        foreach ($parents AS $parent) {
            $this->Html->addCrumb($parent['Area']['name'], array('action' => 'index', $parent['Area']['id']));
        }
        echo $this->Html->getCrumbs();
    }
    if (!empty($elections)) {
        echo '<ul>';
        foreach ($elections AS $election) {
            $c = array();
            foreach ($election['Election'] AS $parent) {
                $c[] = $this->Html->link($parent['Election']['name'], array('controller' => 'elections', 'action' => 'index', $parent['Election']['id']));
            }
            echo '<li>' . implode(' > ', $c) . '</li>';
        }
        echo '</ul>';
    }
    $i = 0;
    foreach ($items as $item) {
        echo $this->Html->link($item['Area']['name'], array('action' => 'index', $item['Area']['id']), array('class' => 'btn btn-default'));
    } // End of foreach ($items as $item) {   
    ?>
</div>