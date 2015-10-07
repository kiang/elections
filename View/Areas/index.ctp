<?php
if (!empty($parents)) {
    foreach ($parents AS $parent) {
        $this->Html->addCrumb($parent['Area']['name'], array('action' => 'index', $parent['Area']['id']));
    }
}
?>

<div class="row">
    <div class="col-md-12">
    <h3><?php echo $this->Html->link('行政區', '/areas/' . $areaMethod, array('class' => '')); ?></h3>
        <div class="pull-right btn-group">
            <?php
            echo $this->Html->link('本頁 API', '/api/elections/index/' . $parentId, array('class' => 'btn btn-default', 'target' => '_blank'));
            ?>
        </div>
    </div>
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
                echo '<div class="clearfix"></div>';
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
                if (!empty($election['AreasElection']['bulletin_key'])) {
                    echo $this->Html->link('選舉公報', '/bulletins/view/' . $election['AreasElection']['bulletin_key'], array('class' => 'btn btn-primary pull-right col-md-1'));
                }

                if (!empty($election['Candidate'])) {
                    $candidateCount = 0;
                    foreach ($election['Candidate'] AS $candidate) {
                        ++$candidateCount;
                        ?>
                        <div class="col-md-2 col-xs-6 candidate-<?php echo $candidate['Candidate']['stage']; ?>">
                            <div class="thumbnail">
                                <a href="<?php echo $this->Html->url('/candidates/view/' . $candidate['Candidate']['id']); ?>">
                                    <?php
                                    if (empty($candidate['Candidate']['image'])) {
                                        echo $this->Html->image('candidate-not-found.jpg', array('class' => 'candidate-image'));
                                    } else {
                                        echo $this->Html->image('../media/' . $candidate['Candidate']['image'], array('class' => 'candidate-image'));
                                    }
                                    ?>
                                </a>
                                <div class="caption">
                                    <?php
                                    echo $this->Html->link(
                                        $this->Html->tag('h3', $candidate['Candidate']['name']),
                                        '/candidates/view/' . $candidate['Candidate']['id'],
                                        array('escape' => false)
                                        );
                                    echo $candidate['Candidate']['party'];
                                    if(!empty($candidate['Candidate']['no'])) {
                                        echo '<br>';
                                        echo $candidate['Candidate']['no'] . '號';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        if ($candidateCount === 6) {
                            echo '<div class="clearfix"></div>';
                            $candidateCount = 0;
                        }
                    }
                } else {
                    echo ' ~ 目前沒有候選人資料 ~ ';
                }
            }
        }
        ?>
    </div>
</div>