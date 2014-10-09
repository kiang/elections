<?php
if (!empty($parents)) {
    foreach ($parents AS $parent) {
        $this->Html->addCrumb($parent['Area']['name'], array('action' => 'index', $parent['Area']['id']));
    }
}
?>

<div id="AreasAdminIndex">
    <h3><?php echo $this->Html->link('行政區', '/areas/' . $areaMethod, array('class' => '')); ?></h3>
    <div class="col-md-12">
        <div class="pull-right btn-group">
            <?php
            echo $this->Html->link('本頁 API', '/api/elections/index/' . $parentId, array('class' => 'btn btn-default', 'target' => '_blank'));
            ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <ul class="nav nav-pills">
                    <?php foreach ($items as $item): ?>
                        <li>
                        <?php echo $this->Html->link($item['Area']['name'], array('action' => $areaMethod, $item['Area']['id']), array('class' => 'code' . $item['Area']['code']));?>
                        </li>
                    <?php endforeach ?>

                </ul>

            </div>

        </div>
        <div class="col-md-12">

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

                    echo '<hr />';
                    echo '<div class="col-md-8">';
                    echo $this->Html->link(implode(' > ', $c), '/candidates/index/' . $cLinkId);
                    echo " &nbsp; &nbsp; ( 選舉人： {$election['AreasElection']['population_electors']} / 人口： {$election['AreasElection']['population']} )";
                    echo '</div>';
                    echo $this->Html->link("新增 {$eType} 候選人", array('controller' => 'candidates', 'action' => 'add', $election['AreasElection']['Election_id']), array('class' => 'btn btn-primary pull-right col-md-2'));
                    echo '<div class="clearfix"></div>';
                    if (!empty($election['Candidate'])) {
                        foreach ($election['Candidate'] AS $candidate) {
                            ?><div class="col-md-2 candidate-<?php echo $candidate['Candidate']['stage']; ?>" style="text-align: center;">
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