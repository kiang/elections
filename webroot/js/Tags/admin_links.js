$(function () {
    $('input#tagCandidate').autocomplete({
        source: base_url + '/candidates/s/',
        select: function (event, ui) {
            $.get(base_url + '/admin/tags/link_add/' + currentTagId + '/' + ui.item.id, {}, function () {
                $('div#viewContent').load(base_url + '/admin/tags/links/' + currentTagId);
            });
        }
    });
    $('a.btn-link-delete').click(function () {
        $.get(this.href, {}, function () {
            $('div#viewContent').load(base_url + '/admin/tags/links/' + currentTagId);
        });
        return false;
    });
});