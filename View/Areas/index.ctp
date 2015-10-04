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
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills">
                <?php foreach ($items as $item): ?>
                    <li>
                        <?php echo $this->Html->link($item['Area']['name'], array('action' => $areaMethod, $item['Area']['id']), array('class' => 'code' . $item['Area']['code'])); ?>
                    </li>
                <?php endforeach ?>
            </ul>
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

                    echo '<hr>';
                    echo '<div class="col-md-12">';
                    echo '<ol class="breadcrumb breadcrumb-title">';
                    $i = 0;

                    foreach ($c AS $key => $value) {
                        ++$i;
                        if ($i === count($c)) {
                            echo $this->Html->tag(
                                'li',
                                $this->Html->link($value, '/candidates/index/' . $cLinkId),
                                array('class' => 'active')
                            );
                        } else {
                            echo $this->Html->tag(
                                'li',
                                $value,
                                array('class' => 'text-muted')
                            );
                        }
                    }
                    echo '</ol>';
                    echo '<blockquote>';
                    $quota = "名額：{$election['AreasElection']['quota']}";
                    if (!empty($election['AreasElection']['quota_women'])) {
                        $quota .= " / 婦女保障：{$election['AreasElection']['quota_women']}";
                    }
                    echo "{$quota} / 選舉人：{$election['AreasElection']['population_electors']} / 人口：{$election['AreasElection']['population']}";
                    echo '</blockquote>';
                    echo '</div>';
                    if (!empty($election['AreasElection']['bulletin_key'])) {
                        echo $this->Html->link('選舉公報', '/bulletins/view/' . $election['AreasElection']['bulletin_key'], array('class' => 'btn btn-primary pull-right col-md-1'));
                    }
                    
                    echo '<div class="clearfix"></div>';
                    if (!empty($election['Candidate'])) {
                        foreach ($election['Candidate'] AS $candidate) {
                            ?><div class="col-md-2 col-xs-6 candidate-<?php echo $candidate['Candidate']['stage']; ?>" style="text-align: center;">
                                <div class="thumbnail">
                                    <a href="<?php echo $this->Html->url('/candidates/view/' . $candidate['Candidate']['id']); ?>">
                                        <?php
                                        if (empty($candidate['Candidate']['image'])) {
                                            echo $this->Html->image('candidate-not-found.jpg', array('style' => 'width: 100px'));
                                        } else {
                                            echo $this->Html->image('../media/' . $candidate['Candidate']['image'], array('style' => 'width: 100px; height: 100px'));
                                        }
                                        ?>
                                    </a>
                                    <div class="caption">
                                        <?php
                                            echo $this->Html->tag('h3', $candidate['Candidate']['name']);
                                            echo $candidate['Candidate']['party'];
                                            if(!empty($candidate['Candidate']['no'])) {
                                                echo $candidate['Candidate']['no'] . '號';
                                            }
                                        ?>
                                    </div>
                                </div>
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
</div>