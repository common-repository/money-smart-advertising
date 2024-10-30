;(function ( $, $document ) {

    var dialog = $('.mn-dialog-shadow');
    var closeDialogBtn = $( '.mn-dialog-btn-cancel', dialog );
    var dialogErrors = $('.mn-dialog-errors', dialog );
    var panel = $('.mn-panel');
    var $nonce = mnObjAdmin.nonce;
    var $addNewBtn = $('#money-btn-add');


    /**
     * ajax function
     */
    var ajax = function ( el, data, callback ) {

        var gif = el.parent().find('.mn-ajax-loader');
        var callback = callback || function ( response ) {
                
                if( response.hasOwnProperty('errors') ){
                    displayErrors( response.errors, mnObjAdmin.dialog_errors_title );
                }
                else{
                    window.location.reload();
                }

                el.prop("disabled",false);
                gif.hide();
            };

        el.prop("disabled",true);
        gif.show();

        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: data,
            success: callback
        });

    };

    /**
     * Display errors
     */
    var displayErrors = function ( errors, title ) {

        dialog.find('header h3').text( title );
        dialogErrors.html('');

        for( var key in errors ){
            dialogErrors.append( '<li>' + errors[ key ] + '</li>' );
        }

        dialog.addClass('mn-dialog-shadow-open');
    };


    /**
     * Close Dialog
     */
    closeDialogBtn.click( function ( event ) {
        event.preventDefault();
        $('.mn-dialog-shadow').removeClass('mn-dialog-shadow-open');
    });


    /**
     * Ajax: Add New
     */
    $addNewBtn.on( 'click', function ( event ) {
        event.preventDefault();

        ajax( $addNewBtn, {
            action: 'money_add_new_zone',
            nonce: $nonce
        }, function ( response ) {
            if( response.hasOwnProperty('errors') ){
                displayErrors( response.errors, mnObjAdmin.dialog_errors_title );
                $addNewBtn.prop("disabled",false);
            }
            else if( response.hasOwnProperty('url') ) {
                window.location = response.url;
            }
        });

    });


    /**
     * Ajax: Clone Ad
     */
    $('.mn-panel-btn-clone', panel ).click( function ( event ) {
        event.preventDefault();

        var self = $(this);

        ajax( self, {
            action: 'money_clone_ad',
            nonce: $nonce,
            ad_id: self.data('id')
        });

    });


    /**
     * Ajax: Delete Ads
     */
    $('.mn-btn-delete-pending, .mn-btn-delete-active, .mn-btn-delete-zone', panel ).click( function ( event ) {
        event.preventDefault();

        var self = $(this);
        var confirmMessage = mnObjAdmin.delete_waiting_approval_confirm_text;
        var action = 'money_delete_sold_ad';

        if( self.is('.mn-btn-delete-active') ){
            confirmMessage = mnObjAdmin.delete_active_confirm_text;
        }
        else if( self.is('.mn-btn-delete-zone') ){
            confirmMessage = mnObjAdmin.delete_confirm_text;
            action = 'money_delete_demo_ad';
        }

        if ( confirm( confirmMessage ) == true ) {

            ajax( self, {
                action: action,
                nonce: $nonce,
                ad_id: self.data('id')
            });

        }

    });


    /**
     * Ajax: Approve Ad
     */
    $('.mn-btn-approve', panel ).click( function ( event ) {
        event.preventDefault();

        var self = $(this);

        ajax( self, {
            action: 'money_approve_ad',
            nonce: $nonce,
            ad_id: self.data('id')
        });

    });


    /**
     * Ajax: Plugin Setup
     */
    $( '.mn-dialog-btn-setup' ).click(function () {

        var self = $(this);
        var whatToDo = self.attr('data-whatToDo');

        ajax( self, {
            action: 'money_plugin_setup',
            nonce: $nonce,
            whatToDo: self.attr('data-whatToDo'),
            step: self.attr('data-step'),
            store_page_id: self.parents('.mn-dialog').find('select[name="ads_store_page"]').val()
        }, function ( response ) {
            window.location.reload();
        });

    });


    /**
     * Chart 'per' change
     */
    var chartPer = $( 'select[name="mn-chart-per"]' );
    var chartYear = $( 'select[name="mn-chart-year"]' );
    var chartMonth = $( 'select[name="mn-chart-month"]' );
    var chartWeek = $( 'select[name="mn-chart-week"]' );

    chartPer.change(function () {
        var url = $( 'input[name="mn-stat-url"]' ).val() + '&money-stats-type=' + $( 'input[name="mn-chart-type"]' ).val();

        if( chartPer.val() === 'year' ){
            window.location = url + '&money-stats-per=year#money-chart-container';
        }
        else if( chartPer.val() === 'month' ){
            window.location = url + '&money-stats-per=month&money-year=' + chartYear.val() + '#money-chart-container';
        }
        else if( chartPer.val() === 'week' ){
            window.location = url + '&money-stats-per=week&money-year=' + chartYear.val() + '&money-month=' + chartMonth.val() + '#money-chart-container';
        }

    });


    /**
     * Chart 'year' change
     */
    chartYear.change(function () {
        var url = $( 'input[name="mn-stat-url"]' ).val() + '&money-stats-type=' + $( 'input[name="mn-chart-type"]' ).val();
        window.location = url + '&money-stats-per=year&money-year=' + chartYear.val() + '#money-chart-container';
    });


    /**
     * Chart 'month' change
     */
    chartMonth.change(function () {
        var url = $( 'input[name="mn-stat-url"]' ).val() + '&money-stats-type=' + $( 'input[name="mn-chart-type"]' ).val();
        window.location = url + '&money-stats-per=month&money-year=' + chartYear.val() + '&money-month=' + chartMonth.val() + '#money-chart-container';
    });


    /**
     * Chart 'week' change
     */
    chartWeek.change(function () {
        var url = $( 'input[name="mn-stat-url"]' ).val() + '&money-stats-type=' + $( 'input[name="mn-chart-type"]' ).val();
        window.location = url + '&money-stats-per=week&money-year=' + chartYear.val() + '&money-month=' + chartMonth.val() + '&money-week=' + chartWeek.val() + '#money-chart-container';
    });

})( jQuery, jQuery(document) );