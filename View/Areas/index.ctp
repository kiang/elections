<div id="AreasAdminIndex">
    <h3>行政區</h3>
    <?php
    if (!empty($parents)) {
        foreach ($parents AS $parent) {
            $this->Html->addCrumb($parent['Area']['name'], array('action' => 'index', $parent['Area']['id']));
        }
        echo $this->Html->getCrumbs();
    }
    ?>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked">
                <?php foreach ($items as $item): ?>
                    <li>
                    <?php echo $this->Html->link($item['Area']['name'], array('action' => 'index', $item['Area']['id']), array('class' => ''));?>
                    </li>
                <?php endforeach ?>

            </ul>

        </div>
        <div class="col-md-10">

            <?php
            if (!empty($elections)) {
                foreach ($elections AS $election) {
                    $c = array();
                    foreach ($election['Election'] AS $e) {
                        if (empty($e['Election']['parent_id'])) {
                            $eParentId = $e['Election']['id'];
                        } elseif ($e['Election']['parent_id'] === $eParentId) {
                            $eType = $e['Election']['name'];
                        }
                        $cLinkId = $e['Election']['id'];
                        $c[] = $e['Election']['name'];
                    }
                    echo '<h3>' . $this->Html->link(implode(' > ', $c), '/candidates/index/' . $cLinkId);
                    echo $this->Html->link("新增 {$eType} 候選人", array('controller' => 'candidates', 'action' => 'add', $election['AreasElection']['Election_id']), array('class' => 'btn btn-primary pull-right col-md-2'));
                    echo '</h3>';
                    if (!empty($election['Candidate'])) {
                        foreach ($election['Candidate'] AS $candidate) {
                            ?><div class="col-md-2" style="text-align: center;">
                                <a href="<?php echo $this->Html->url('/candidates/view/' . $candidate['Candidate']['id']); ?>">
                                    <?php
                                    if (empty($candidate['Candidate']['image'])) {
                                        echo $this->Html->image('candidate-not-found.jpg', array('style' => 'width: 100px; border: 0px;'));
                                    } else {
                                        echo $this->Html->image('../media/' . $candidate['Candidate']['image'], array('style' => 'width: 100px; height: 100px; border: 0px;'));
                                    }
                                    ?>
                                    <br /><?php echo $candidate['Candidate']['name']; ?>
                                </a>
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

    </div>
    <div class="clear"></div>

</div>