<?php
App::import('Vendor', 'php-diff', array('file' => 'php-diff/lib/Diff.php'));
App::import('Vendor', 'php-diff-inline', array('file' => 'php-diff/lib/Diff/Renderer/Html/Inline.php'));

// Options for generating the diff
$options = array(
        //'ignoreWhitespace' => true,
        //'ignoreCase' => true,
);

// Initialize the diff class
$diff = new Diff(explode("\n", print_r($original, true)), explode("\n", print_r($submitted, true)), $options);
$renderer = new Diff_Renderer_Html_Inline;
?>
<style>
    body {
        background: #fff;
        font-family: Arial;
        font-size: 12px;
    }
    .Differences {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        empty-cells: show;
    }

    .Differences thead th {
        text-align: left;
        border-bottom: 1px solid #000;
        background: #aaa;
        color: #000;
        padding: 4px;
    }
    .Differences tbody th {
        text-align: right;
        background: #ccc;
        width: 4em;
        padding: 1px 2px;
        border-right: 1px solid #000;
        vertical-align: top;
        font-size: 13px;
    }

    .Differences td {
        padding: 1px 2px;
        font-family: Consolas, monospace;
        font-size: 13px;
    }

    .DifferencesSideBySide .ChangeInsert td.Left {
        background: #dfd;
    }

    .DifferencesSideBySide .ChangeInsert td.Right {
        background: #cfc;
    }

    .DifferencesSideBySide .ChangeDelete td.Left {
        background: #f88;
    }

    .DifferencesSideBySide .ChangeDelete td.Right {
        background: #faa;
    }

    .DifferencesSideBySide .ChangeReplace .Left {
        background: #fe9;
    }

    .DifferencesSideBySide .ChangeReplace .Right {
        background: #fd8;
    }

    .Differences ins, .Differences del {
        text-decoration: none;
    }

    .DifferencesSideBySide .ChangeReplace ins, .DifferencesSideBySide .ChangeReplace del {
        background: #fc0;
    }

    .Differences .Skipped {
        background: #f7f7f7;
    }

    .DifferencesInline .ChangeReplace .Left,
    .DifferencesInline .ChangeDelete .Left {
        background: #fdd;
    }

    .DifferencesInline .ChangeReplace .Right,
    .DifferencesInline .ChangeInsert .Right {
        background: #dfd;
    }

    .DifferencesInline .ChangeReplace ins {
        background: #9e9;
    }

    .DifferencesInline .ChangeReplace del {
        background: #e99;
    }

    pre {
        width: 100%;
        overflow: auto;
    }
</style>
<div id="CandidatesAdminReview">
    <div class="row">
        <h1>審核提供資料</h1>
        <div class="btn-group">
            <?php echo $this->Html->link('通過', "/admin/candidates/review/{$submittedId}/yes", array('class' => 'btn btn-default')); ?>
        </div>
    </div>
    <hr />
    <div class="row">
        <?php echo $diff->render($renderer); ?>
    </div>
</div>