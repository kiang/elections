function dialogFull(linkObject, title) {
    if ($('#dialogFull').length == 0) {
        $('body').append('<div id="dialogFull"></div>');
        $('#dialogFull').dialog( {
            autoOpen : false,
            width : 950,
            close: function(event, ui) {
                location.reload();
            }
        });
    }
    if (typeof title == 'undefined') {
        title = linkObject.innerHTML || linkObject.textContent;
    }
    $('.modal .modal-body').load(linkObject.href, null, function() {
        $('.modal .modal-title').text(title);
        $('.modal').modal('show');
    });
}

function dialogMessage(message) {
    if ($('#dialogMessage').length == 0) {
        $('body').append('<div id="dialogMessage"></div>');
        $('#dialogMessage').html(message).dialog( {
            title: 'Message',
            autoOpen : true,
            width : 300
        });
    } else {
        $('#dialogMessage').html(message).dialog('open');
    }
}