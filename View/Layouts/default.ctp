<!DOCTYPE html>
<html lang="zh-TW">
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
        echo $this->Html->meta('viewport','width=device-width, initial-scale=1.0');
        echo $this->Html->css('jquery-ui');
        echo $this->Html->css('bootstrap.min');
        echo $this->Html->css('default');
        ?>
    </head>
    <body>
        <nav class="navbar navbar-static-top navbar-inverse">
            <div class="navbar-inner">
                <div class="container">
                    <div class="navbar-header">
                        <?php echo $this->Html->link('選舉黃頁', '/', array('class' => 'navbar-brand')); ?>
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="navbar-collapse">
                        <ul class="nav navbar-nav">
                            <li><?php echo $this->Html->link('行政區', '/areas', array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link('選舉區', '/elections', array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link('候選人', '/candidates', array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link('分類', '/tags', array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link('選舉公報', '/bulletins', array('class' => '')); ?></li>
                        </ul>
                        <form action="#" class="navbar-form navbar-right">
                            <div class="input-group">
                                <?php
                                echo $this->Form->input('Candidate.keyword', array(
                                    'div' => false,
                                    'label' => false,
                                    'placeholder' => '候選人姓名搜尋',
                                    'class' => 'form-control'
                                ));
                                echo $this->Form->input('Election.keyword', array(
                                    'div' => false,
                                    'label' => false,
                                    'placeholder' => '選舉區搜尋',
                                    'class' => 'form-control',
                                    'style' => 'display: none'
                                ));
                                ?>
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">候選人搜尋 <span class="caret"></span></button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li data-type="Candidate" data-desp="候選人搜尋"><a href="#">候選人</a></li>
                                        <li data-type="Election" data-desp="選舉區搜尋"><a href="#">選舉區</a></li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- <div class="pull-right submitCount" style="color: #f2f2f2"></div> -->
                </div>
            </div>
        </nav>
        <div class="container">
            <div class="row">
                <?php echo $this->Html->getCrumbList(array('class' => 'breadcrumb breadcrumb-title')); ?>
            </div>

            <div class="row">
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
            <div class="row">
                <?php if (Configure::read('debug') === 0 && empty($groupId)) { ?>
                    <ins class="adsbygoogle"
                         style="display:inline-block;width:728px;height:90px"
                         data-ad-client="ca-pub-5571465503362954"
                         data-ad-slot="3499306028"></ins>
                <?php } ?>
                <hr>
                <div id="fb-root"></div>
                <script>
                    (function (d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id))
                            return;
                        js = d.createElement(s);
                        js.id = id;
                        js.src = "//connect.facebook.net/zh_TW/sdk.js#xfbml=1&appId=1393405437614114&version=v2.3";
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));
                </script>
                <div class="col-md-6">
                    <div class="fb-page" data-href="https://www.facebook.com/k.olc.tw" data-width="500" data-hide-cover="true" data-show-facepile="true" data-show-posts="false" data-colorscheme="dark"></div>
                </div>
                <div class="col-md-6">
                    <div class="fb-page" data-href="https://www.facebook.com/g0v.tw" data-width="500" data-hide-cover="true" data-show-facepile="true" data-show-posts="false" data-colorscheme="dark"></div>
                </div>
            </div>
        </div>

        <footer class="navbar-bottom navbar navbar-inverse">
            <div class="container">
                <div class="row">
                    <ul>
                        <li>
                            <?php echo $this->Html->link('江明宗 . 政 . 路過', 'http://k.olc.tw/', array('target' => '_blank')); ?>
                        </li>
                        <li>
                            <?php echo $this->Html->link('關於選舉黃頁', '/pages/about'); ?>
                        </li>
                        <li>
                            <?php echo $this->Html->link('免責聲明', '/pages/notice'); ?>
                        </li>
                        <?php
                            if (!Configure::read('loginMember.id')) {
                                echo $this->Html->tag(
                                    'li',
                                    $this->Html->link('登入', '/members/login')
                                );
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </footer>
        <?php
        // echo $this->element('sql_dump');
        echo $scripts_for_layout;
        echo $this->Html->script('jquery');
        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('bootstrap.min');
        echo $this->Html->script('olc');
        ?>
        <script>
            $(function () {
                $('.navbar-form .dropdown-menu li').on('click', function (e) {
                    $('.navbar-form .form-control').hide();
                    var type = $(this).data('type'),
                        desp = $(this).data('desp');

                    $('#' + type + 'Keyword').show();
                    $('.navbar-form .dropdown-toggle').html(desp + '&nbsp;<span class="caret"></span>');
                });

                $('a.dialogControl').click(function () {
                    dialogFull(this);
                    return false;
                });
                $('#CandidateKeyword').autocomplete({
                    source: '<?php echo $this->Html->url('/candidates/s/'); ?>',
                    select: function (event, ui) {
                        location.href = '<?php echo $this->Html->url('/candidates/view/'); ?>' + ui.item.id;
                    },
                    messages: {
                        noResults: '',
                        results: function() {}
                    }
                });
                $('#ElectionKeyword').autocomplete({
                    source: '<?php echo $this->Html->url('/elections/s/'); ?>',
                    select: function (event, ui) {
                        if (ui.item.rght - ui.item.lft === 1) {
                            location.href = '<?php echo $this->Html->url('/candidates/index/'); ?>' + ui.item.id;
                        } else {
                            location.href = '<?php echo $this->Html->url('/elections/index/'); ?>' + ui.item.id;
                        }

                    },
                    messages: {
                        noResults: '',
                        results: function() {}
                    }
                });
            });
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
        <?php if (Configure::read('debug') === 0 && empty($groupId)) { ?>
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        <?php } ?>
    </body>
</html>