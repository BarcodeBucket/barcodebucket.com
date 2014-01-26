// vim: set tabstop=4 shiftwidth=4 expandtab cindent:
$(document).ready(function() {
    $('#home-form').submit(function() {
        var barcode = $('#barcode').val();

        if(barcode !== '') {
            jQuery
                .ajax({
                    type: 'get',
                    dataType: 'json',
                    url: '/legacy.php/barcode/' + barcode,
                    success: function (data) {  
                        $('#barcode-list')
                            .prepend('<li>' + data.gtin + '</li>')
                            .children()
                            .first()
                            .hide()
                            .fadeIn(500)
                        ;
                    },
                    statusCode: {
                        404: function() {
                            $('#barcode-list')
                                .prepend('<li class="error">Invalid barcode!</li>')
                                .children()
                                .first()
                                .delay(1000)
                                .fadeOut(500)
                            ;
                        }
                    }
                });
            ;
        }

        return false;
    });
});
