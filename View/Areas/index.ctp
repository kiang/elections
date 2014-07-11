<div id="AreasAdminIndex">
    <h3>行政區</h3>
    <?php
    if (!empty($parents)) {
        foreach ($parents AS $parent) {
            $this->Html->addCrumb($parent['Area']['name'], array('action' => 'index', $parent['Area']['id']));
        }
        echo $this->Html->getCrumbs();
    }
    echo '<div class="clearfix"></div>';
    foreach ($items as $item) {
        echo $this->Html->link($item['Area']['name'], array('action' => 'index', $item['Area']['id']), array('class' => 'btn btn-default'));
    }
    echo '<hr />';
    if (!empty($elections)) {
        foreach ($elections AS $election) {
            $c = array();
            foreach ($election['Election'] AS $e) {
                $c[] = $e['Election']['name'];
            }
            echo '<h3>' . implode(' > ', $c);
            echo $this->Html->link('新增候選人', array('controller' => 'candidates', 'action' => 'add', $election['AreasElection']['Election_id']), array('class' => 'btn btn-primary pull-right'));
            echo '</h3>';
            if (!empty($election['Candidate'])) {
                foreach ($election['Candidate'] AS $candidate) {
                    ?><div class="col-md-2" style="text-align: center;">
                        <?php echo $this->Html->image('candidate-not-found.jpg', array('style' => 'width: 100px;')); ?>
                        <br /><?php echo $candidate['Candidate']['name']; ?>
                    </div><?php
                }
            } else {
                echo ' ~ 目前沒有候選人資料 ~ ';
            }
            echo '<div class="clearfix"></div>';
        }
    }
    ?>
</div>