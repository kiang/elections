<?php if (!empty($newsLinks)) { ?>
    <div class="col-md-12 newsLinksBlock">
        <h3>新聞集錦</h3>
        <hr />
        <div class="paging"><?php echo $this->element('paginator'); ?></div>
        <ul>
            <?php
            foreach ($newsLinks AS $newsLink) {
                $linkKeyword = $linkKeywords[$newsLink['LinksKeyword']['Keyword_id']];
                $newsLink['LinksKeyword']['summary'] = str_replace($linkKeyword, " <span style=\"color: #cc00cc; font-weight: 900;\">{$linkKeyword}</span> ", $newsLink['LinksKeyword']['summary']);
                echo '<li>';
                echo '<h4>' . $this->Html->link($newsLink['Link']['title'], $newsLink['Link']['url'], array('target' => '_blank')) . '</h4>';
                echo '<span class="pull-right">' . $newsLink['Link']['created'] . '</span>';
                echo '<br />' . $newsLink['LinksKeyword']['summary'];
                echo '</li>';
            }
            ?>
        </ul>
        <div class="paging"><?php echo $this->element('paginator'); ?></div>
        <script>
            $(function () {
                $('.newsLinksBlock .paging a').click(function () {
                    $('div.newsLinksBlock').parent().load(this.href);
                    return false;
                });
            })
        </script>
    </div>
<?php } ?>