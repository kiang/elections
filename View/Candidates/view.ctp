<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-info"><?php
                if (!empty($parents)) {
                    foreach ($parents AS $parent) {
                        echo $parent['Election']['name'] . '&nbsp;';
                    }
                    echo '候選人';
                }
                ?></h1>
            <p>&nbsp;</p>
            <div class="alert alert-success">
                候選人資料錯誤嗎？立即
                <?php echo $this->Html->link('編輯', array('action' => 'edit', $candidate['Candidate']['id'])); ?>。
            </div>
            <div class="pull-right btn-group">
                <?php
                echo $this->Html->link('參選記錄', array('action' => 'name', $candidate['Candidate']['name']), array('class' => 'btn btn-default'));
                echo $this->Html->link('相關公司', 'http://gcis.nat.g0v.tw/name/' . $candidate['Candidate']['name'], array('class' => 'btn btn-default', 'target' => '_blank'));
                echo $this->Html->link('相關法人', 'http://foundations.olc.tw/directors/index/' . $candidate['Candidate']['name'], array('class' => 'btn btn-default', 'target' => '_blank'));
                if (Configure::read('loginMember.group_id') === '1') {
                    echo $this->Html->link('管理', array('action' => 'edit', $candidate['Candidate']['id'], 'admin' => true), array('class' => 'btn btn-default'));
                }
                if (!empty($candidate['Election']['bulletin_key'])) {
                    echo $this->Html->link('選舉公報', '/bulletins/view/' . $candidate['Election']['bulletin_key'], array('class' => 'btn btn-primary'));
                }
                echo $this->Html->link('本頁 API', '/api/candidates/view/' . $candidate['Candidate']['id'], array('class' => 'btn btn-default', 'target' => '_blank'));
                ?>
            </div>
            <?php
            $meta = array();
            if (!empty($candidate['Election']['quota'])) {
                $meta[] = "名額： {$candidate['Election']['quota']}";
            }
            if (!empty($candidate['Election']['quota_women'])) {
                $meta[] = "婦女保障： {$candidate['Election']['quota_women']}";
            }
            if (!empty($candidate['Election']['population_electors'])) {
                $meta[] = "選舉人： {$candidate['Election']['population_electors']}";
            }
            if (!empty($candidate['Election']['population'])) {
                $meta[] = "人口： {$candidate['Election']['population']}";
            }
            if (!empty($meta)) {
                $meta = implode(' / ', $meta);
                echo "<h3>{$meta}</h3>";
            }
            ?>
        </div>
        <?php if (!empty($candidate['Election']['Area'])) { ?>
            <div class="col-md-6">
                行政區：
                <?php
                foreach ($candidate['Election']['Area'] AS $area) {
                    echo $this->Html->link($area['name'], '/areas/index/' . $area['id'], array('class' => 'btn btn-default btn-sm'));
                }
                ?>
            </div>
        <?php } ?>
        <div class="col-md-6">
            <?php if (!empty($candidate['Tag'])) { ?>
                分類：
                <?php
                foreach ($candidate['Tag'] AS $tag) {
                    echo $this->Html->link($tag['name'], '/candidates/tag/' . $tag['id'], array('class' => 'btn btn-default'));
                }
            }
            ?>
        </div>
        <div class="clearfix"></div>
        <hr />
    </div>
    <div class="row candidateMainBlock">
        <div class="col-md-12">
            <h2><?php
                if (!empty($candidate['Candidate']['no'])) {
                    echo "{$candidate['Candidate']['no']}號 ";
                }
                echo $candidate['Candidate']['name'];
                ?></h2>
            <?php
            echo $this->Olc->stages[$candidate['Candidate']['stage']];
            if (!empty($candidate['Candidate']['vote_count'])) {
                echo "<br />得票數： {$candidate['Candidate']['vote_count']}";
            }
            ?>
        </div>
        <div class="col-md-12 candidate-<?php echo $candidate['Candidate']['stage']; ?> well row">
            <div class="col-md-6">
                <?php
                if (empty($candidate['Candidate']['image'])) {
                    echo $this->Html->image('candidate-not-found.jpg', array('class' => 'img-thumbnail', 'style' => 'width: 200px;'));
                } else {
                    echo $this->Html->image('../media/' . $candidate['Candidate']['image'], array('class' => 'img-thumbnail', 'style' => 'width: 200px;'));
                }
                ?>
            </div>
            <div class="col-md-6">
                <dl class="dl-horizontal profile-list">
                    <dt>性別：</dt>
                    <dd><?php
                        $gender = strtolower($candidate['Candidate']['gender']);
                        switch ($gender) {
                            case 'f':
                                echo '女';
                                break;
                            case 'm':
                                echo '男';
                                break;
                            default:
                                echo '<span class="text-muted">無資料</span>';
                        }
                        ?>
                    </dd>
                    <dt>電話：</dt>
                    <dd>
                        <?php
                        if(!empty( $candidate['Candidate']['contacts_phone'])) {
                            echo $candidate['Candidate']['contacts_phone'];
                        } else {
                            echo '<span class="text-muted">無資料</span>';
                        }
                        ?>
                    </dd>
                    <dt>傳真：</dt>
                    <dd>
                        <?php
                        if(!empty( $candidate['Candidate']['contacts_fax'])) {
                           echo $candidate['Candidate']['contacts_fax'];
                       } else {
                           echo '<span class="text-muted">無資料</span>';
                       }
                           ?>
                    </dd>
                    <dt>信箱：</dt>
                    <dd>
                        <?php
                        if(!empty( $candidate['Candidate']['contacts_email'])) {
                            echo $candidate['Candidate']['contacts_email'];
                        } else {
                            echo '<span class="text-muted">無資料</span>';
                        }
                       ?>
                    </dd>
                    <dt>服務處：</dt>
                    <dd>
                        <?php
                        if(!empty( $candidate['Candidate']['contacts_address'])) {
                            echo $candidate['Candidate']['contacts_address'];
                        } else {
                            echo '<span class="text-muted">無資料</span>';
                        }
                           ?>
                    </dd>
                    <dt>政黨：</dt>
                    <dd>
                        <?php
                        if(!empty( $candidate['Candidate']['party'])) {
                            echo $candidate['Candidate']['party'];
                        } else {
                            echo '<span class="text-muted">無資料</span>';
                        }
                        ?>
                    </dd>
                    <dt>生日：</dt>
                    <dd>
                        <?php
                        if(!empty( $candidate['Candidate']['birth'])) {
                            echo $candidate['Candidate']['birth'];
                        } else {
                            echo '<span class="text-muted">無資料</span>';
                        }
                        ?>
                    </dd>
                    <dt>教育程度：</dt>
                    <dd>
                        <?php
                        if(!empty( $candidate['Candidate']['education_level'])) {
                            echo $candidate['Candidate']['education_level'];
                        } else {
                            echo '<span class="text-muted">無資料</span>';
                        }
                        ?>
                    </dd>
                    <dt>出生地：</dt>
                    <dd>
                        <?php
                        if(!empty( $candidate['Candidate']['birth_place'])) {
                            echo $candidate['Candidate']['birth_place'];
                        } else {
                            echo '<span class="text-muted">無資料</span>';
                        }
                        ?>
                    </dd>
                    <dt>英文姓名：</dt>
                    <dd>
                        <?php
                        if(!empty( $candidate['Candidate']['name_english'])) {
                            echo $candidate['Candidate']['name_english'];
                        } else {
                            echo '<span class="text-muted">無資料</span>';
                        }
                        ?>
                    </dd>
                    <dt>是否現任：</dt>
                    <dd>
                        <?php
                        if(!empty( ($candidate['Candidate']['is_present'] == 1) ? '是' : '否')) {
                            echo ($candidate['Candidate']['is_present'] == 1) ? '是' : '否';
                        } else {
                            echo '<span class="text-muted">無資料</span>';
                        }
                        ?>
                    </dd>
                </dl>
            </div>
        </div>
        <?php if (!empty($candidate['Candidate']['platform'])) { ?>
            <div class="col-md-12">
                <div class="well well-lg"> 
                    <div class="row">
                        <strong>政見</strong> <hr />
                        <?php echo str_replace('\\n', '<br />', $candidate['Candidate']['platform']); ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if (!empty($candidate['Candidate']['links'])) { ?>
            <div class="col-md-12">
                <div class="well well-lg"> 
                    <div class="row">
                        <strong>相關連結</strong> <hr />
                        <?php
                        $lines = explode('\\n', $candidate['Candidate']['links']);
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
        <?php if (!empty($candidate['Candidate']['education'])) { ?>
            <div class="col-md-12">
                <div class="well well-lg"> 
                    <div class="row">
                        <strong>經歷</strong> <hr />
                        <?php echo nl2br(str_replace('\\n', '<br />', $candidate['Candidate']['experience'])); ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if (!empty($candidate['Candidate']['education'])) { ?>
            <div class="col-md-12">
                <div class="well well-lg"> 
                    <div class="row">
                        <strong>學歷</strong> <hr />
                        <?php echo nl2br(str_replace('\\n', '<br />', $candidate['Candidate']['education'])); ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php if (Configure::read('debug') === 0) { ?>
        <div id="vanilla-comments"></div>
        <script type="text/javascript">
            var vanilla_forum_url = '<?php echo $this->Html->url('/../talk'); ?>'; // Required: the full http url & path to your vanilla forum
            var vanilla_identifier = '<?php echo $candidate['Candidate']['id']; ?>'; // Required: your unique identifier for the content being commented on
            var vanilla_url = '<?php echo $this->Html->url('/candidates/view/' . $candidate['Candidate']['id'], true); ?>'; // Current page's url
            (function () {
                var vanilla = document.createElement('script');
                vanilla.type = 'text/javascript';
                var timestamp = new Date().getTime();
                vanilla.src = vanilla_forum_url + '/js/embed.js';
                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(vanilla);
            })();
        </script>
    <?php } ?>
</div><!--/container-->