;(function ( $, $document, $window ) {

    var store = $('.mn-store');
    var dialog = $('#mn-dialog-buyer-content');
    var errorsWrap = $( '#mn-dialog-errors', dialog );
    var closeDialogBtn = $( '.mn-dialog-btn-cancel' );
    var saveDialogBtn = $( '#mn-dialog-btn-save', dialog );
    var checkoutBtn = $( '.mn-btn-checkout' );
    var buyerUrl = $( '#mn-buyer-url', dialog );
    var buyerContent = $( '#mn-buyer-content', dialog );
    var buyerAd = $( '#mn-buyer-demo-id', dialog );


    /**
     * Window resize listener
     */
    $window.resize(function () {

        if( store.width() < 670 ){
            store.addClass('mn-store-responsive');
        }
        else{
            store.removeClass('mn-store-responsive');
        }

    }).resize();


    /**
     * Get Param from url
     * @param name
     * @param url
     * @returns {*}
     */
    function getReferrerUrl() {

        var url = window.location.href;

        url = url.replace(/money-mode=.+/g, "");
        url = url.replace(/&$/g, "");

        return url;
    }


    /**
     * Close Dialog
     */
    closeDialogBtn.click( function ( event ) {
        event.preventDefault();
        $('.mn-dialog-shadow').removeClass('mn-dialog-shadow-open');
    });


    /**
     * Buy now Button
     */
    checkoutBtn.click(function ( event ) {
        event.preventDefault();

        var self = $(this);
        var gif = self.parent().find('.mn-ajax-loader');

        if( self.is('.mn-ajax-active') ) return false;

        self.addClass('mn-ajax-active');
        gif.show();

        $.ajax({
            url: mnObjFront.ajaxurl,
            type: 'post',
            data: {
                action: 'money_paypal_checkout',
                nonce: mnObjFront.nonce,
                ad_id: self.data('demo_id'),
                referrer_url: getReferrerUrl()
            },
            success: function ( response ) {

                if( response.hasOwnProperty('errors') ){

                    if( response.errors.hasOwnProperty('userLogin') ){
                        $('#mn-dialog-login').addClass('mn-dialog-shadow-open');
                    }

                    // display errors
                    var errorStr = '';
                    for( var key in response.errors ){
                        errorStr = errorStr + response.errors[ key ] + '\n';
                    }

                    gif.hide();
                }

                else{
                    window.location = response.approval_url;
                }
                self.removeClass('mn-ajax-active');
            }
        });
    });


    /**
     * Save Buyer content
     */
    saveDialogBtn.click(function ( event ) {
        event.preventDefault();

        var gif = saveDialogBtn.find('img');

        // disable button
        saveDialogBtn.prop("disabled",true);
        gif.show();

        $.ajax({
            url: mnObjFront.ajaxurl,
            type: 'post',
            data: {
                action: 'money_save_buyer_content',
                nonce: mnObjFront.nonce,
                ad_id: $( '#mn-buyer-soldAd-id', dialog ).val(),
                buyer_url: $( '#mn-buyer-url', dialog ).val(),
                buyer_content: $( '#mn-buyer-content', dialog ).val()
            },
            success: function ( response ) {

                if( response.hasOwnProperty('errors') ){

                    errorsWrap.html('');
                    for( var key in response.errors ){
                        errorsWrap.append( '<li>' + response.errors[ key ] + '</li>' );
                    }

                    gif.hide();
                    saveDialogBtn.prop( "disabled", false );
                }

                else{
                    dialog.removeClass('mn-dialog-shadow-open');
                    $('#mn-dialog-success').addClass('mn-dialog-shadow-open');
                }

                gif.hide();
            }
        });
    });


})( jQuery, jQuery(document), jQuery( window ) );