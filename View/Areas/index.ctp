<div class="pull-right btn-group">
    <?php
    foreach ($rootNodes AS $rootNodeId => $rootNodeName) {
        echo $this->Html->link($rootNodeName, '/areas/index/' . $rootNodeId, array('class' => 'btn btn-default'));
    }
    ?>
</div>
<?php
if (!empty($parents)) {
    foreach ($parents AS $parent) {
        $this->Html->addCrumb($parent['Area']['name'], array('action' => 'index', $parent['Area']['id']));
    }
}
?>
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
                    $quota .= "<br>婦女保障：{$election['AreasElection']['quota_women']}";
                }
                echo "{$quota}<br>選舉人：{$election['AreasElection']['population_electors']}<br>人口：{$election['AreasElection']['population']}";
                echo '</blockquote>';
                if (!empty($election['AreasElection']['bulletin_key'])) {
                    echo $this->Html->link('選舉公報', '/bulletins/view/' . $election['AreasElection']['bulletin_key'], array('class' => 'btn btn-primary pull-right col-md-1'));
                    echo '<div class="clearfix"></div>';
                    echo '<p>&nbsp;</p>';
                }

                if (!empty($election['Candidate'])) {
                    $candidateCount = 0;
                    foreach ($election['Candidate'] AS $candidate) {
                        ++$candidateCount;
                        ?>
                        <div class="col-md-2 col-xs-6">
                            <div class="candidates-box">
                                <?php
                                    if (intval($candidate['Candidate']['stage']) === 2) {
                                        echo '<span class="ribbon">當選</span>';
                                    }
                                ?>
                                <div class="thumbnail">
                                    <div class="candidate-image-wrapper">
                                        <a href="<?php echo $this->Html->url('/candidates/view/' . $candidate['Candidate']['id']); ?>">
                                            <?php
                                            if (empty($candidate['Candidate']['image'])) {
                                                echo $this->Html->image('candidate-not-found.jpg', array('class' => 'candidate-image'));
                                            } else {
                                                echo $this->Html->image('../media/' . $candidate['Candidate']['image'], array('class' => 'candidate-image'));
                                            }
                                            ?>
                                        </a>
                                    </div>
                                    <div class="caption">
                                        <?php
                                        echo $this->Html->link(
                                            $this->Html->tag('h3', $candidate['Candidate']['name']),
                                            '/candidates/view/' . $candidate['Candidate']['id'],
                                            array('escape' => false)
                                            );
                                        echo $this->Html->para(null, $candidate['Candidate']['party']);
                                        if(!empty($candidate['Candidate']['no'])) {
                                            echo $this->Html->para(null, $candidate['Candidate']['no'] . '號');
                                        }
                                        if (intval($candidate['Candidate']['stage']) === 0) {
                                            echo '<p class="text-muted">未登記</p>';
                                        }
                                        ?>
                                    </div>
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
                    echo '<span class="text-muted">目前沒有候選人資料</span>';
                }
            }
        }
        echo '<div class="clearfix"></div>';
        echo $this->Html->link('本頁 API', '/api/elections/index/' . $parentId, array('class' => 'btn btn-default pull-right', 'target' => '_blank'));
        ?>
    </div>
</div>