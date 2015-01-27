<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-TW">
    <head>
        <?php echo $this->Html->charset(); ?>
        <title><?php echo $title_for_layout; ?>選舉黃頁</title><?php
        $trailDesc = '選舉黃頁提供了各種發生在中華民國的選舉資訊，主要聚焦在各種候選人';
        if (!isset($desc_for_layout)) {
            $desc_for_layout = $trailDesc;
        } else {
            $desc_for_layout .= $trailDesc;
        }
        echo $this->Html->meta('icon');
        echo $this->Html->meta('description', $desc_for_layout);
        echo $this->Html->css('jquery-ui');
        echo $this->Html->css('bootstrap');
        echo $this->Html->css('default');
        echo $this->Html->css('blocks');
        echo $this->Html->script('jquery');
        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('bootstrap.min');
        echo $this->Html->script('olc');
        echo $scripts_for_layout;
        ?>
    </head>
    <body>
        <nav class="navbar navbar-static-top navbar-inverse">
            <div class="navbar-inner">
                <div class="container">
                    <?php echo $this->Html->link('選舉黃頁', '/', array('class' => 'navbar-brand')); ?>
                    <ul class="nav navbar-nav">
                        <li><?php echo $this->Html->link('行政區', '/areas', array('class' => '')); ?></li>
                        <li><?php echo $this->Html->link('選舉區', '/elections', array('class' => '')); ?></li>
                        <li><?php echo $this->Html->link('候選人', '/candidates', array('class' => '')); ?></li>
                        <li><?php echo $this->Html->link('分類', '/tags', array('class' => '')); ?></li>
                        <li><?php echo $this->Html->link('選舉公報', '/bulletins', array('class' => '')); ?></li>
                    </ul>
                    <div class="pull-right submitCount" style="color: #f2f2f2"></div>
                </div>
            </div>
        </nav>
        <div class="container">
            <div class="pull-right">
                <?php
                echo $this->Form->input('Candidate.keyword', array(
                    'div' => 'form-group',
                    'label' => false,
                    'placeholder' => '候選人姓名搜尋',
                    'class' => 'form-control col-md-4'
                ));
                ?>
            </div>
            <div class="pull-right">
                <?php
                echo $this->Form->input('Election.keyword', array(
                    'div' => 'form-group',
                    'label' => false,
                    'placeholder' => '選舉區搜尋',
                    'class' => 'form-control col-md-4'
                ));
                ?>
            </div>
            <div id="header">
                <div class="breadcrumb">
                    <?php echo $this->Html->getCrumbs() ?>
                </div>
            </div>

            <div id="content">
                <div class="btn-group">
                    <?php
                    $groupId = Configure::read('loginMember.group_id');
                    switch ($groupId) {
                        case '1':
                            echo $this->Html->link('Elections', '/admin/elections', array('class' => 'btn btn-default'));
                            echo $this->Html->link('Areas', '/admin/areas', array('class' => 'btn btn-default'));
                            echo $this->Html->link('Candidates', '/admin/candidates', array('class' => 'btn btn-default'));
                            echo $this->Html->link('Tags', '/admin/tags', array('class' => 'btn btn-default'));
                            echo $this->Html->link('Bulletins', '/admin/bulletins', array('class' => 'btn btn-default'));
                            echo $this->Html->link('Members', '/admin/members', array('class' => 'btn btn-default'));
                            echo $this->Html->link('Groups', '/admin/groups', array('class' => 'btn btn-default'));
                            break;
                        case '2':
                            echo $this->Html->link('Candidates', '/admin/candidates', array('class' => 'btn btn-default'));
                            echo $this->Html->link('Tags', '/admin/tags', array('class' => 'btn btn-default'));
                            echo $this->Html->link('Bulletins', '/admin/bulletins', array('class' => 'btn btn-default'));
                            break;
                    }
                    if (!empty($groupId)) {
                        echo $this->Html->link('Logout', '/members/logout', array('class' => 'btn btn-default'));
                    }
                    if (!empty($actions_for_layout)) {
                        foreach ($actions_for_layout as $title => $url) {
                            echo $this->Html->link($title, $url, array('class' => 'btn'));
                        }
                    }
                    ?>
                </div>

                <?php echo $this->Session->flash(); ?>
                <div id="viewContent"><?php echo $content_for_layout; ?></div>
            </div>
            <div class="clearfix"></div>
            <div id="footer" class="container">
                <hr />
                <?php echo $this->Html->link('江明宗 . 政 . 路過', 'http://k.olc.tw/', array('target' => '_blank')); ?>
                / <?php echo $this->Html->link('關於選舉黃頁', '/pages/about'); ?>
                / <?php echo $this->Html->link('免責聲明', '/pages/notice'); ?>
                <?php if (!Configure::read('loginMember.id')): ?>
                    / <?php echo $this->Html->link('Login', '/members/login'); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
        echo $this->element('sql_dump');
        ?>
        <script type="text/javascript">
            //<![CDATA[
            $(function () {
                $('a.dialogControl').click(function () {
                    dialogFull(this);
                    return false;
                });
                $('input#CandidateKeyword').autocomplete({
                    source: '<?php echo $this->Html->url('/candidates/s/'); ?>',
                    select: function (event, ui) {
                        location.href = '<?php echo $this->Html->url('/candidates/view/'); ?>' + ui.item.id;
                    }
                });
                $('input#ElectionKeyword').autocomplete({
                    source: '<?php echo $this->Html->url('/elections/s/'); ?>',
                    select: function (event, ui) {
                        if (ui.item.rght - ui.item.lft === 1) {
                            location.href = '<?php echo $this->Html->url('/candidates/index/'); ?>' + ui.item.id;
                        } else {
                            location.href = '<?php echo $this->Html->url('/elections/index/'); ?>' + ui.item.id;
                        }

                    }
                });
                $('div.submitCount').load('<?php echo $this->Html->url('/candidates/submits'); ?>');
            });
            //]]>
        </script>
        <?php if (Configure::read('debug') === 0) { ?>
            <script>
                (function (i, s, o, g, r, a, m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function () {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                            m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

                ga('create', 'UA-51256613-1', 'auto');
                ga('send', 'pageview');
                ga('set', 'contentGroup1', 'elections');

            </script>
        <?php } ?>
    </body>
</html>