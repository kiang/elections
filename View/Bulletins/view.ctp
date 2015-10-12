<div class="col-md-12">
    <h3>選舉公報 - <?php echo $bulletin['Bulletin']['name']; ?></h3>
    <p>&nbsp;</p>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>相關選區候選人</h4>
        </div>
        <div class="panel-body">
            <?php
            foreach ($bulletin['Election'] AS $election) {
                echo $this->Html->link($election['name'], '/candidates/index/' . $election['id'], array('target' => '_blank', 'class' => 'btn btn-default')) . '&nbsp;';
            }
            ?>
        </div>
    </div>
    <p>&nbsp;</p>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>選舉公報</h4>
        </div>
        <div class="panel-body">
            <?php
            if (!empty($bulletin['Bulletin']['source'])) {
                echo $this->Html->link('原始檔案', $bulletin['Bulletin']['source'], array('target' => '_blank', 'class' => 'btn btn-default')) . '&nbsp;';
            }
            echo $this->Html->link('備份檔案', 'https://github.com/kiang/bulletin.cec.gov.tw/tree/master/Console/Command/data/pdf_103/' . $bulletin['Bulletin']['id'] . '.pdf', array('target' => '_blank', 'class' => 'btn btn-default')) . '&nbsp;';
            echo $this->Html->link('網頁格式', 'http://k.olc.tw/bulletin/' . $bulletin['Bulletin']['id'] . '/' . $bulletin['Bulletin']['id'] . '.html', array('target' => '_blank', 'class' => 'btn btn-default'));
            ?>
        </div>
    </div>
</div>