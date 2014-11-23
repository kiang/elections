<div class="container">
    <div class="row">
        <h1><?php
            if (!empty($parents)) {
                $c = array();
                foreach ($parents AS $parent) {
                    if ($parent['Election']['rght'] - $parent['Election']['lft'] != 1) {
                        $c[] = $this->Html->link($parent['Election']['name'], '/elections/index/' . $parent['Election']['id']);
                    } else {
                        $c[] = $this->Html->link($parent['Election']['name'], '/candidates/index/' . $parent['Election']['id']);
                    }
                }
                echo implode(' > ', $c);
            }
            ?></h1>
        <div class="col-md-12">
            <div class="pull-right btn-group">
                <?php
                echo $this->Html->link('編輯', array('action' => 'edit', $this->data['Candidate']['id']), array('class' => 'btn btn-default'));
                echo $this->Html->link('本頁 API', '/api/candidates/view/' . $this->data['Candidate']['id'], array('class' => 'btn btn-default', 'target' => '_blank'));
                echo $this->Html->link('相關公司', 'http://gcis.nat.g0v.tw/name/' . $this->data['Candidate']['name'], array('class' => 'btn btn-default', 'target' => '_blank'));
                echo $this->Html->link('相關法人', 'http://foundations.olc.tw/directors/index/' . $this->data['Candidate']['name'], array('class' => 'btn btn-default', 'target' => '_blank'));
                if (Configure::read('loginMember.group_id') === '1') {
                    echo $this->Html->link('管理', array('action' => 'edit', $this->data['Candidate']['id'], 'admin' => true), array('class' => 'btn btn-default'));
                }
                if (false !== strpos($referer, $this->Html->url('/', true))) {
                    echo $this->Html->link('回上頁', $referer, array('class' => 'btn btn-default'));
                }
                if (!empty($this->data['Election'][0]['bulletin_key'])) {
                    echo $this->Html->link('選舉公報', '/bulletins/view/' . $this->data['Election'][0]['bulletin_key'], array('class' => 'btn btn-primary'));
                }
                ?>
            </div>
            <?php
            $quota = "名額： {$this->data['Election'][0]['quota']}";
            if (!empty($this->data['Election'][0]['quota_women'])) {
                $quota .= " / 婦女保障： {$this->data['Election'][0]['quota_women']}";
            }
            echo " &nbsp; &nbsp; ( {$quota} / 選舉人： {$this->data['Election'][0]['population_electors']} / 人口： {$this->data['Election'][0]['population']} )";
            ?>
        </div>
        <div class="col-md-6">
            行政區：
            <?php
            foreach ($this->data['Election'][0]['Area'] AS $area) {
                echo $this->Html->link($area['name'], '/areas/index/' . $area['id'], array('class' => 'btn btn-default'));
            }
            ?>
        </div>
        <div class="col-md-6">
            <?php if (!empty($this->data['Tag'])) { ?>
                分類：
                <?php
                foreach ($this->data['Tag'] AS $tag) {
                    echo $this->Html->link($tag['name'], '/candidates/tag/' . $tag['id'], array('class' => 'btn btn-default'));
                }
            }
            ?>
        </div>
        <div class="clearfix"></div>
        <hr />
    </div>
    <div class="row">
        <div class="col-md-12 candidateMainBlock">
            <div class="col-md-12 candidate-<?php echo $this->data['Candidate']['stage']; ?>">
                <div class="col-md-6">
                    <?php
                    if (empty($this->data['Candidate']['image'])) {
                        echo $this->Html->image('candidate-not-found.jpg', array('style' => 'width: 200px; border: 0px;'));
                    } else {
                        echo $this->Html->image('../media/' . $this->data['Candidate']['image'], array('style' => 'width: 200px; border: 0px;'));
                    }
                    ?>
                </div>
                <div class="col-md-6">
                    <h2><?php echo $this->data['Candidate']['name']; ?></h2>
                    <?php echo $this->Olc->stages[$this->data['Candidate']['stage']]; ?>
                </div>
            </div>
            <div class="col-md-12">
                <br />
                <div class="well well-lg"> 
                    <div class="row">
                        <div class="col-sm-4">

                        </div>
                        <div class="col-sm-8">
                            <ul>
                                <li>電話：<?php echo $this->data['Candidate']['contacts_phone']; ?></li>
                                <li>傳真：<?php echo $this->data['Candidate']['contacts_fax']; ?></li>
                                <li>信箱：<?php echo $this->data['Candidate']['contacts_email']; ?></li>
                                <li>服務處：<?php echo $this->data['Candidate']['contacts_address']; ?></li>
                                <li>政黨：<?php echo $this->data['Candidate']['party']; ?></li>
                                <li>生日：<?php echo $this->data['Candidate']['birth']; ?></li>
                                <li>性別：<?php
                                    $gender = strtolower($this->data['Candidate']['gender']);
                                    switch ($gender) {
                                        case 'f':
                                            echo '女';
                                            break;
                                        case 'm':
                                            echo '男';
                                            break;
                                        default:
                                            echo '未設定';
                                    }
                                    ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!empty($this->data['Election'][0]['CandidatesElection']['platform'])) { ?>
                <div class="col-md-12">
                    <div class="well well-lg"> 
                        <div class="row">
                            <strong>政見</strong> <hr />
                            <?php echo str_replace('\\n', '<br />', $this->data['Election'][0]['CandidatesElection']['platform']); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (!empty($this->data['Candidate']['links'])) { ?>
                <div class="col-md-12">
                    <div class="well well-lg"> 
                        <div class="row">
                            <strong>相關連結</strong> <hr />
                            <?php
                            $lines = explode('\\n', $this->data['Candidate']['links']);
                            foreach ($lines AS $line) {
                                $pos = strrpos($line, 'http');
                                if (false !== $pos) {
                                    $title = trim(substr($line, 0, $pos));
                                    $url = html_entity_decode(trim(substr($line, $pos)));
                                    if (empty($title)) {
                                        $title = $url;
                                    }
                                    if (!empty($url)) {
                                        echo '<a href="' . $url . '" target="_blank">' . $title . '</a><br />';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (!empty($this->data['Candidate']['education'])) { ?>
                <div class="col-md-12">
                    <div class="well well-lg"> 
                        <div class="row">
                            <strong>經歷</strong> <hr />
                            <?php echo nl2br(str_replace('\\n', '<br />', $this->data['Candidate']['experience'])); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (!empty($this->data['Candidate']['education'])) { ?>
                <div class="col-md-12">
                    <div class="well well-lg"> 
                        <div class="row">
                            <strong>學歷</strong> <hr />
                            <?php echo nl2br(str_replace('\\n', '<br />', $this->data['Candidate']['education'])); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="col-md-7 candidateNewsBlock" style="display: none;">
        </div>
    </div>
    <div id="vanilla-comments"></div>
    <script type="text/javascript">
        var vanilla_forum_url = '<?php echo $this->Html->url('/../talk'); ?>'; // Required: the full http url & path to your vanilla forum
        var vanilla_identifier = '<?php echo $this->data['Candidate']['id']; ?>'; // Required: your unique identifier for the content being commented on
        var vanilla_url = '<?php echo $this->Html->url('/candidates/view/' . $this->data['Candidate']['id'], true); ?>'; // Current page's url
        (function () {
            var vanilla = document.createElement('script');
            vanilla.type = 'text/javascript';
            var timestamp = new Date().getTime();
            vanilla.src = vanilla_forum_url + '/js/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(vanilla);
        })();
        $(function () {
            $.get('<?php echo $this->Html->url('/candidates/links/' . $this->data['Candidate']['id']); ?>', {}, function (pageBlock) {
                if (pageBlock !== '') {
                    $('div.candidateNewsBlock').html(pageBlock).show();
                    $('div.candidateMainBlock').removeClass('col-md-12').addClass('col-md-5');
                }
            });
        })
    </script>
</div><!--/container-->