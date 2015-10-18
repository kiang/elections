<div class="row">
    <div class="col-md-12">
        <h1>
            <?php
            if (!empty($parents)) {
                foreach ($parents AS $parent) {
                    echo $parent['Election']['name'] . '&nbsp;';
                    if ($parent['Election']['rght'] - $parent['Election']['lft'] != 1) {
                        $this->Html->addCrumb($parent['Election']['name'], '/elections/index/' . $parent['Election']['id']);
                    } else {
                        $this->Html->addCrumb($parent['Election']['name'], '/candidates/index/' . $parent['Election']['id']);
                    }
                }
                echo '候選人';
            }
            ?>
        </h1>
        <p>&nbsp;</p>
        <div class="alert alert-success">
            候選人資料錯誤嗎？立即
            <?php echo $this->Html->link('編輯', array('action' => 'edit', $candidate['Candidate']['id'])); ?>。
        </div>
        <?php
        $meta = array();
        if (!empty($candidate['Election']['quota'])) {
            $meta[] = "名額： {$candidate['Election']['quota']}<br>";
        }
        if (!empty($candidate['Election']['quota_women'])) {
            $meta[] = "婦女保障： {$candidate['Election']['quota_women']}<br>";
        }
        if (!empty($candidate['Election']['population_electors'])) {
            $meta[] = "選舉人： {$candidate['Election']['population_electors']}<br>";
        }
        if (!empty($candidate['Election']['population'])) {
            $meta[] = "人口： {$candidate['Election']['population']}<br>";
        }
        if (!empty($meta)) {
            $meta = implode('', $meta);
            echo "<blockquote>{$meta}</blockquote>";
        }
        ?>
    </div>
    <?php if (!empty($candidate['Election']['Area'])) { ?>
    <div class="col-md-12 label-list">
        行政區：
        <?php
        foreach ($candidate['Election']['Area'] AS $area) {
            echo $this->Html->link(
                $this->Html->tag('span', $area['name'], array('class' => 'label label-default')) . '&nbsp;',
                '/areas/index/' . $area['id'],
                array('escape' => false)
            );
        }
        ?>
    </div>
    <?php } ?>
    <?php if (!empty($candidate['Tag'])) { ?>
    <div class="col-md-12 label-list" style="margin-top: 1em">
        分類：
        <?php
        foreach ($candidate['Tag'] AS $tag) {
            echo $this->Html->link(
                $this->Html->tag('span', $tag['name'], array('class' => 'label label-default')) . '&nbsp;',
                '/candidates/tag/' . $tag['id'],
                array('escape' => false)
            );
        }
        ?>
    </div>
    <?php } ?>
</div>
<p>&nbsp;</p>
<div class="row">
    <div class="col-md-12 stage-<?php echo $candidate['Candidate']['stage']; ?> profile-text">
        <div class="btn-group pull-right">
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
            ?>
        </div>
        <div class="clearfix"></div>
        <p>&nbsp;</p>
        <?php
        if (empty($candidate['Candidate']['image'])) {
            echo $this->Html->image('candidate-not-found.jpg', array('class' => 'img-thumbnail img-rounded candidate-image'));
        } else {
            echo $this->Html->image('../media/' . $candidate['Candidate']['image'], array('class' => 'img-thumbnail img-rounded candidate-image large'));
        }
        echo $this->Html->tag('h2', $candidate['Candidate']['name']);
        if (!empty($candidate['Candidate']['no'])) {
            echo '<p>';
            echo "登記為&nbsp;<strong style=\"font-size: 1.1em\">{$candidate['Candidate']['no']}</strong>&nbsp;號&nbsp;";
            if (!empty($candidate['Candidate']['vote_count'])) {
                echo "&nbsp;<span class=\"text-muted\">|</span>&nbsp;得票數&nbsp;<strong style=\"font-size: 1.1em\">{$candidate['Candidate']['vote_count']}</strong>";
            }
            echo '</p>';
        }
        ?>
    </div>
    <hr>
    <div class="profile-desp-box col-md-6 col-md-offset-3 well">
        <div class="row">
            <?php echo $this->Html->image('profile-icon/gender.svg'); ?>
            <div class="profile-desp-title col-md-4">性別</div>
            <div class="col-md-8">
                <?php
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
            </div>
        </div>
    </div>
    <div class="profile-desp-box col-md-6 col-md-offset-3 well">
        <div class="row">
            <?php echo $this->Html->image('profile-icon/phone.svg'); ?>
            <div class="profile-desp-title col-md-4">電話</div>
            <div class="col-md-8">
                <?php
                if(!empty( $candidate['Candidate']['contacts_phone'])) {
                    echo $candidate['Candidate']['contacts_phone'];
                } else {
                    echo '<span class="text-muted">無資料</span>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="profile-desp-box col-md-6 col-md-offset-3 well">
        <div class="row">
            <?php echo $this->Html->image('profile-icon/fax.svg'); ?>
            <div class="profile-desp-title col-md-4">傳真</div>
            <div class="col-md-8">
                <?php
                if(!empty( $candidate['Candidate']['contacts_fax'])) {
                    echo $candidate['Candidate']['contacts_fax'];
                } else {
                    echo '<span class="text-muted">無資料</span>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="profile-desp-box col-md-6 col-md-offset-3 well">
        <div class="row">
            <?php echo $this->Html->image('profile-icon/mail.svg'); ?>
            <div class="profile-desp-title col-md-4">信箱</div>
            <div class="col-md-8">
                <?php
                if(!empty( $candidate['Candidate']['contacts_email'])) {
                    echo $candidate['Candidate']['contacts_email'];
                } else {
                    echo '<span class="text-muted">無資料</span>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="profile-desp-box col-md-6 col-md-offset-3 well">
        <div class="row">
            <?php echo $this->Html->image('profile-icon/location.svg'); ?>
            <div class="profile-desp-title col-md-4">服務處</div>
            <div class="col-md-8">
                <?php
                if(!empty( $candidate['Candidate']['contacts_address'])) {
                    echo $candidate['Candidate']['contacts_address'];
                } else {
                    echo '<span class="text-muted">無資料</span>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="profile-desp-box col-md-6 col-md-offset-3 well">
        <div class="row">
            <?php echo $this->Html->image('profile-icon/political.svg'); ?>
            <div class="profile-desp-title col-md-4">政黨</div>
            <div class="col-md-8">
                <?php
                if(!empty( $candidate['Candidate']['party'])) {
                    echo $candidate['Candidate']['party'];
                } else {
                    echo '<span class="text-muted">無資料</span>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="profile-desp-box col-md-6 col-md-offset-3 well">
        <div class="row">
            <?php echo $this->Html->image('profile-icon/birthday.svg'); ?>
            <div class="profile-desp-title col-md-4">生日</div>
            <div class="col-md-8">
                <?php
                if(!empty( $candidate['Candidate']['birth'])) {
                    echo $candidate['Candidate']['birth'];
                } else {
                    echo '<span class="text-muted">無資料</span>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="profile-desp-box col-md-6 col-md-offset-3 well">
        <div class="row">
            <?php echo $this->Html->image('profile-icon/education.svg'); ?>
            <div class="profile-desp-title col-md-4">教育程度</div>
            <div class="col-md-8">
                <?php
                if(!empty( $candidate['Candidate']['education_level'])) {
                    echo $candidate['Candidate']['education_level'];
                } else {
                    echo '<span class="text-muted">無資料</span>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="profile-desp-box col-md-6 col-md-offset-3 well">
        <div class="row">
            <?php echo $this->Html->image('profile-icon/birthday.svg'); ?>
            <div class="profile-desp-title col-md-4">出生地</div>
            <div class="col-md-8">
                <?php
                if(!empty( $candidate['Candidate']['birth_place'])) {
                    echo $candidate['Candidate']['birth_place'];
                } else {
                    echo '<span class="text-muted">無資料</span>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="profile-desp-box col-md-6 col-md-offset-3 well">
        <div class="row">
            <?php echo $this->Html->image('profile-icon/id.svg'); ?>
            <div class="profile-desp-title col-md-4">英文姓名</div>
            <div class="col-md-8">
                <?php
                if(!empty( $candidate['Candidate']['name_english'])) {
                    echo $candidate['Candidate']['name_english'];
                } else {
                    echo '<span class="text-muted">無資料</span>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="profile-desp-box col-md-6 col-md-offset-3 well">
        <div class="row">
            <?php echo $this->Html->image('profile-icon/vote.svg'); ?>
            <div class="profile-desp-title col-md-4">參選狀態</div>
            <div class="col-md-8">
                <?php echo $this->Olc->stages[$candidate['Candidate']['stage']]; ?>
            </div>
        </div>
    </div>
    <div class="profile-desp-box col-md-6 col-md-offset-3 well">
        <div class="row">
            <?php echo $this->Html->image('profile-icon/ballot.svg'); ?>
            <div class="profile-desp-title col-md-4">是否現任</div>
            <div class="col-md-8">
                <?php
                if(!empty( ($candidate['Candidate']['is_present'] == 1) ? '是' : '否')) {
                    echo ($candidate['Candidate']['is_present'] == 1) ? '是' : '否';
                } else {
                    echo '<span class="text-muted">無資料</span>';
                }
                ?>
            </div>
        </div>
    </div>
    <?php if (!empty($candidate['Candidate']['platform'])) { ?>
    <div class="col-md-12">
        <div class="well well-lg"> 
            <strong>政見</strong>
            <hr>
            <?php echo str_replace('\\n', '<br />', $candidate['Candidate']['platform']); ?>
        </div>
    </div>
    <?php } ?>
    <?php if (!empty($candidate['Candidate']['links'])) { ?>
    <div class="col-md-12">
        <div class="well well-lg"> 
            <strong>相關連結</strong>
            <hr>
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
    <?php } ?>
    <?php if (!empty($candidate['Candidate']['education'])) { ?>
    <div class="col-md-12">
        <div class="well well-lg"> 
            <strong>經歷</strong>
            <hr>
            <?php echo nl2br(str_replace('\\n', '<br>', $candidate['Candidate']['experience'])); ?>
        </div>
    </div>
    <?php } ?>
    <?php if (!empty($candidate['Candidate']['education'])) { ?>
    <div class="col-md-12">
        <div class="well well-lg"> 
            <strong>學歷</strong>
            <hr>
            <?php echo nl2br(str_replace('\\n', '<br />', $candidate['Candidate']['education'])); ?>
        </div>
    </div>
    <?php } ?>
    <div class="clearfix"></div>
    <div class="pull-right">
        <?php
        echo $this->Html->link('本頁 API', '/api/candidates/view/' . $candidate['Candidate']['id'], array('class' => 'btn btn-default', 'target' => '_blank'));
        ?>
    </div>
    <div class="clearfix"></div>
    <p>&nbsp;</p>
    <div class="pull-right text-muted" style="text-align: right">Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a>, <a href="http://www.flaticon.com/authors/zurb" title="Zurb">Zurb</a>, <a href="http://www.flaticon.com/authors/dave-gandy" title="Dave Gandy">Dave Gandy</a>, <a href="http://www.flaticon.com/authors/ocha" title="OCHA">OCHA</a>, <a href="http://www.flaticon.com/authors/google" title="Google">Google</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a></div>
    <?php if (Configure::read('debug') === 0) { ?>
        <div id="vanilla-comments"></div>
        <script type="text/javascript">
            var vanilla_forum_url = '<?php echo $this->Html->url('/../talk'); ?>';
            var vanilla_identifier = '<?php echo $candidate['Candidate']['id']; ?>';
            var vanilla_url = '<?php echo $this->Html->url('/candidates/view/' . $candidate['Candidate']['id'], true); ?>';
            (function () {
                var vanilla = document.createElement('script');
                vanilla.type = 'text/javascript';
                var timestamp = new Date().getTime();
                vanilla.src = vanilla_forum_url + '/js/embed.js';
                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(vanilla);
            })();
        </script>
        <?php } ?>
    </div>