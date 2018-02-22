<div id="CandidatesAdminIndex">
    <h2><?php echo __('Candidates', true); ?></h2>
    <div class="btn-group">
        <?php
        echo $this->Html->link('回清單', array('action' => 'index'), array('class' => 'btn btn-info'));
        ?>
    </div>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <?php echo $this->Form->create(); ?>
    <table class="table table-bordered" id="CandidatesAdminIndexTable">
        <thead>
            <tr>
                <th><a href="#" id="candidateCheckAll">勾選</a></th>
                <th>選區</th>
                <th>姓名</th>
                <th>建立時間</th>
                <th class="actions">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($items as $item) {
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td><input type="checkbox" class="candidateCheck" name="data[Candidate][id][]" value="<?php echo $item['Candidate']['id']; ?>" />
                    </td>
                    <td><?php
                        echo implode(' > ', $item['Election']);
                        ?></td>
                    <td><?php
                        echo $item['Candidate']['name'];
                        ?></td>
                    <td><?php
                        echo $item['Candidate']['created'];
                        ?></td>
                    <td class="actions">
                        <?php echo $this->Html->link('審核', array('action' => 'review', $item['Candidate']['id'])); ?>
                        <?php echo $this->Html->link('編輯', array('action' => 'edit', $item['Candidate']['id'], 'submits'), array('class' => 'dialogControl')); ?>
                        <?php
                        if (Configure::read('loginMember.group_id') == 1) {
                            echo $this->Html->link('刪除', array('action' => 'delete', $item['Candidate']['id'], 'submits'), null, '確定刪除？');
                        }
                        ?>
                    </td>
                </tr>
            <?php } // End of foreach ($items as $item) {   ?>
        </tbody>
    </table>
    <input type="submit" value="刪除選擇項目" class="btn btn-primary" />
    <?php echo $this->Form->end(); ?>
    <div class="paging"><?php echo $this->element('paginator'); ?></div>
    <script>
        $(function () {
            $('a#candidateCheckAll').click(function () {
                var currentChecked = false;
                var looped = false;
                $('input.candidateCheck').each(function () {
                    if (false === looped) {
                        looped = true;
                        currentChecked = $(this).prop('checked') ? false : true;
                    }
                    $(this).prop('checked', currentChecked);
                });
                return false;
            });
        })
    </script>
</div>