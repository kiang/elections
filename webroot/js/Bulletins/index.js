$(function () {
    $('a.bulletinIndexButton').on('click', function () {
        location.href = base_url + $('input#bulletinIndexKeyword').val();
    });
})