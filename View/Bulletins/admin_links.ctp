<h3><?php echo $bulletin['Bulletin']['name']; ?></h3>
<ul>
    <?php if (!empty($bulletin['Bulletin']['source'])) { ?>
        <li><?php echo $this->Html->link('原始檔案', $bulletin['Bulletin']['source'], array('target' => '_blank')); ?></li>
    <?php } ?>
    <li><?php echo $this->Html->link('備份檔案', 'https://github.com/kiang/bulletin.cec.gov.tw/tree/master/Console/Command/data/pdf_103/' . $bulletin['Bulletin']['id'] . '.pdf', array('target' => '_blank')); ?></li>
    <li><?php echo $this->Html->link('網頁格式', 'http://k.olc.tw/bulletin/' . $bulletin['Bulletin']['id'] . '/' . $bulletin['Bulletin']['id'] . '.html', array('target' => '_blank')); ?></li>
</ul>
<div class="container">
    <input type="text" id="bulletinElection" class="form-control" placeholder="新增選區到這個選舉公報" />
</div>
<div class="container">
    <?php
    foreach ($bulletin['Election'] AS $election) {
        echo '<div class="col-md-2">';
        echo $this->Html->link($election['name'], '/candidates/index/' . $election['id'], array('target' => '_blank', 'class' => 'btn btn-default'));
        echo $this->Html->link('[X]', array('action' => 'link_delete', $election['BulletinsElection']['id']), array('class' => 'btn btn-default btn-link-delete'));
        echo '</div>';
    }
    ?>
</div>
<script type="text/javascript">
    //<![CDATA[
    $(function () {
        $('input#bulletinElection').autocomplete({
            source: '<?php echo $this->Html->url('/elections/s/'); ?>',
            select: function (event, ui) {
                $.get('<?php echo $this->Html->url('/admin/bulletins/link_add/' . $bulletin['Bulletin']['id']); ?>/' + ui.item.id, {}, function (b) {
                    $('div#viewContent').load('<?php echo $this->Html->url('/admin/bulletins/links/' . $bulletin['Bulletin']['id']); ?>');
                });
            }
        });
        $('a.btn-link-delete').click(function () {
            $.get(this.href, {}, function () {
                $('div#viewContent').load('<?php echo $this->Html->url('/admin/bulletins/links/' . $bulletin['Bulletin']['id']); ?>');
            });
            return false;
        });
    });
    //]]>
</script>