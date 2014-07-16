<div id="CandidatesAdminReview">
    <div class="row">
        <h1>審核提供資料</h1>
        <div class="btn-group">
            <?php echo $this->Html->link('通過', "/admin/candidates/review/{$submitted['Candidate']['id']}/yes", array('class' => 'btn btn-default')); ?>
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col-md-6">
            現有資料：
            <pre><?php print_r($original); ?></pre>
        </div>
        <div class="col-md-6">
            提供資料：
            <pre><?php print_r($submitted); ?></pre>
        </div>
    </div>
</div>