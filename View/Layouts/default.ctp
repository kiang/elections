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
                    </ul>
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
            <div id="header">

                <div class="breadcrumb">
                    <?php echo $this->Html->getCrumbs() ?>
                </div>
            </div>

            <div id="content">
                <div class="btn-group">
                    <?php if ($this->Session->read('Auth.User.id')): ?>
                        <?php echo $this->Html->link('Elections', '/admin/elections', array('class' => 'btn')); ?>
                        <?php echo $this->Html->link('Areas', '/admin/areas', array('class' => 'btn')); ?>
                        <?php echo $this->Html->link('Candidates', '/admin/candidates', array('class' => 'btn')); ?>
                        <?php echo $this->Html->link('Tags', '/admin/tags', array('class' => 'btn')); ?>
                        <?php echo $this->Html->link('Members', '/admin/members', array('class' => 'btn')); ?>
                        <?php echo $this->Html->link('Groups', '/admin/groups', array('class' => 'btn')); ?>
                        <?php echo $this->Html->link('Logout', '/members/logout', array('class' => 'btn')); ?>
                    <?php endif; ?>
                    <?php
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
            <div id="footer" class="container">
                <hr />
                <?php echo $this->Html->link('江明宗 . 政 . 路過', 'http://k.olc.tw/', array('target' => '_blank')); ?>
                / <?php echo $this->Html->link('關於選舉黃頁', '/pages/about'); ?>
                <?php if (!$this->Session->read('Auth.User.id')): ?>
                    / <?php echo $this->Html->link('Login', '/members/login'); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
        echo $this->element('sql_dump');
        ?>
        <script type="text/javascript">
            //<![CDATA[
            $(function() {
                $('a.dialogControl').click(function() {
                    dialogFull(this);
                    return false;
                });
                $('input#CandidateKeyword').autocomplete({
                    source: '<?php echo $this->Html->url('/candidates/s/'); ?>',
                    select: function(event, ui) {
                        location.href = '<?php echo $this->Html->url('/candidates/view/'); ?>' + ui.item.id;
                    }
                });
            });
            //]]>
        </script>
    </body>
</html>