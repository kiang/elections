<div id="CandidatesAdminIndex">
    <h2><?php echo $tag['Tag']['name']; ?> :: 候選人</h2>
    <div class="clearfix"></div>
    <div class="col-md-12"><?php echo $this->Html->getCrumbs(); ?></div>
    <div class="paging col-md-4"><?php echo $this->element('paginator'); ?></div>
    <div class="pull-right btn-group">
        <?php echo $this->Html->link('照片', '/candidates/tag/' . $tag['Tag']['id'], array('class' => 'btn btn-default')); ?>
        <?php echo $this->Html->link('清單', '/candidates/tag_list/' . $tag['Tag']['id'], array('class' => 'btn btn-primary')); ?>
    </div>
    <div class="clearfix"></div>
    <?php
    if (!empty($items)) {
        ?>        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>候選人</th>
                    <th>政黨</th>
                    <th>選區</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($items AS $candidate) {
                    ?>
                    <tr>
                        <td class="candidate-<?php echo $candidate['Candidate']['stage']; ?>">
                            <a href="<?php echo $this->Html->url('/candidates/view/' . $candidate['Candidate']['id']); ?>"><?php
                                if (!empty($candidate['Candidate']['no'])) {
                                    echo $candidate['Candidate']['no'] . '號 ';
                                }
                                echo $candidate['Candidate']['name'];
                                ?></a></td>
                        <td>
                            <?php
                            echo $candidate['Candidate']['party'];
                            ?></td>
                        <td><?php echo implode(' > ', $candidate['Election']); ?></td>
                    </tr>
                    <?php
                }
                ?>

            </tbody>
        </table>
        <?php
    } else {
        echo ' ~ 目前沒有候選人資料 ~ ';
    }
    ?>
    <div class="clearfix"></div>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <div id="vanilla-comments"></div>
    <script type="text/javascript">
        var vanilla_forum_url = '<?php echo $this->Html->url('/../talk'); ?>'; // Required: the full http url & path to your vanilla forum
        var vanilla_identifier = '<?php echo $tag['Tag']['id']; ?>'; // Required: your unique identifier for the content being commented on
        var vanilla_url = '<?php echo $this->Html->url('/candidates/tag/' . $tag['Tag']['id'], true); ?>'; // Current page's url
        (function () {
            var vanilla = document.createElement('script');
            vanilla.type = 'text/javascript';
            var timestamp = new Date().getTime();
            vanilla.src = vanilla_forum_url + '/js/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(vanilla);
        })();
    </script>
</div>