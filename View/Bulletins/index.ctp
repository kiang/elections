<div id="BulletinsIndex">
    <h2>選舉公報</h2>
    <p>&nbsp;</p>
    <div class="col-md-8 col-md-offset-2">
        <form action="#">
            <div class="input-group">
                <?php
                echo $this->Form->input('keyword', array(
                    'type' => 'text',
                    'label' => false,
                    'value' => $keyword,
                    'id' => 'bulletinIndexKeyword',
                    'class' => 'form-control col-md-4',
                ));
                ?>
                <span class="input-group-btn">
                    <a href="#" class="btn btn-primary bulletinIndexButton">搜尋選舉公報</a>
                </span>
            </div>
        </form>
    </div>
    <div class="clearfix"></div>
    <p>&nbsp;</p>
    <div class="paginator-wrapper col-md-12"><?php echo $this->element('paginator'); ?></div>
    <div class="clearfix"></div>
    <p class="pull-right"><span class="badge">&nbsp;</span>&nbsp;中的數字表示選區數量</p>
    <div class="clearfix"></div>
    <div class="list-group">
        <?php
        foreach ($bulletins as $bulletin) {
            echo $this->Html->link(
                $bulletin['Bulletin']['name'] .
                '<i class="glyphicon glyphicon-chevron-right pull-right"></i>&nbsp;'.
                '<span class="badge">' . $bulletin['Bulletin']['count_elections'] .  '</span>',
                array('action' => 'view', $bulletin['Bulletin']['id']),
                array('class' => 'list-group-item', 'escape' => false)
            );
        } ?>P
    </div>
    <div class="paginator-wrapper col-md-12"><?php echo $this->element('paginator'); ?></div>
</div>
<script>
    var base_url = '<?php echo $this->Html->url('/bulletins/index/'); ?>';
</script>
<?php echo $this->Html->script('Bulletins/index.js', array('inline' => false, 'block' => 'scriptBottom')); ?>