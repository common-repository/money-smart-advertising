;(function ( $, $document, $window ) {
    
    var ajaxurl = $('#mn-ajaxurl').val();
    var nonce = $('#mn-nonce').val();
    var adID = $('#mn-adid').val();
    var siteurl = $('#mn-siteurl');
    var bar = $('#mn-sidebar');
    var sectionsWrap = $('.mn-sections-wrap');
    var iframe = $('#mn-iframe');
    var iframeWrap = $('#mn-iframe-wrap');
    var loading = $('#mn-loading');
    var errorsWrap = $('#mn-errors');

    // options
    var oCurrency = $( 'select[name="currency"]', bar );
    var oContentType = $( 'input[name="content_type"]', bar );
    var mediaBtn = $( '#mn-open-wp-media', bar );
    var oStrategy = $( 'input[name="strategy_type"]', bar );
    var oDisplayWhen = $( 'input[name="display_when"]', bar );
    var oDisplayOn = $( 'input[name="display_on"]', bar );
    var oComplexity = $( 'input[name="complexity"]', bar );
    var oAction = $( 'input[name="advanced_action"]', bar );
    var oEnableBtn = $( '.mn-op-enable', bar );
    var oStylePosition = $( 'select[name="style_position"]', bar );

    // Set sections height
    sectionsWrap.height( $window.height() - 82 );


    /**
     * Set iframe url
     * @param previewUrl
     * @param callback
     */
    var setIframeUrl = function ( previewUrl, callback ) {

        // run callback when page load
        $('#mn-iframe').off('load').on('load', function () {
            callback();
        });

        // Set the url
        iframe.attr( 'src', previewUrl );
    };


    /**
     * Display errors to screen
     * @param errors
     */
    var displayErrors = function ( errors ) {
        errorsWrap.find('ul').html('');

        for( var key in errors ){
            errorsWrap.find('ul').append( '<li>' + errors[ key ] + '</li>' );
        }

        errorsWrap.removeClass('mn-hide');
    };


    /**
     * return prepared data for ajax save
     * @returns object
     */
    var getOptionData = function () {

        var data = {
            action: 'money_save_option',
            nonce: nonce,
            ad_id: adID
        };

        for ( var i = 0; i < arguments.length; i++) {
            data[ arguments[i].attr('name') ] = arguments[i].val();
        }

        return data;
    };


    /**
     * Save option by ajax
     */
    var setTimer;
    var saveOption = function ( data, refresh ) {

        clearTimeout( setTimer );
        setTimer = setTimeout( function () {

            errorsWrap.addClass('mn-hide');

            // display loading layer
            if( loading.is('.mn-hide') && refresh ){
                loading.removeClass('mn-hide');
            }
            
            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: data,
                success: function ( response ) {

                    if( response.hasOwnProperty('errors') ){
                        displayErrors( response.errors );
                        loading.addClass('mn-hide');
                    }

                    else if( refresh ){
                        setIframeUrl( response.previewUrl, function () {
                            loading.addClass('mn-hide');
                        });
                    }

                }
            });
        }, 600 );

    };


    /** Events **/

    $('.mn-responsive-icons .dashicons', bar).click(function () {

        var self = $(this);

        if( ! self.is('.active') && self.is('.dashicons-desktop, .dashicons-tablet, .dashicons-smartphone') ){

            if( self.is('.dashicons-desktop') ){
                $('.dashicons-tablet, .dashicons-smartphone', bar).removeClass('active');
                iframeWrap.removeClass('tablet smartphone');
            }
            else if( self.is('.dashicons-tablet') ){
                $('.dashicons-desktop, .dashicons-smartphone', bar).removeClass('active');
                iframeWrap.removeClass('smartphone');
                iframeWrap.addClass('tablet');
            }
            else{
                $('.dashicons-desktop, .dashicons-tablet', bar).removeClass('active');
                iframeWrap.removeClass('table');
                iframeWrap.addClass('smartphone');
            }

            self.addClass('active');
        }
        else{

            if( self.is('.dashicons-arrow-left-alt2') ){
                bar.addClass('mn-collapse');
                iframeWrap.addClass('desktop');
            }
            else{
                bar.removeClass('mn-collapse');
                iframeWrap.removeClass('desktop');
            }

        }

    });

    $( '.mn-section h3', bar ).click(function () {
        var parent = $(this).parent();

        if( parent.is('.active') ){
            parent.removeClass('active');
        }
        else{
            parent.addClass('active').siblings().removeClass('active');
        }
    });

    $('input[type="text"], textarea', bar).on( 'input', function () {
        saveOption( getOptionData( $(this) ), true );
    });

    oCurrency.on( 'change', function () {
        saveOption( getOptionData( oCurrency ), false );
    });

    oContentType.on( 'click', function () {
        saveOption( getOptionData( $(this) ), true );
    });

    mediaBtn.on( 'click', function ( event ) {
        event.preventDefault();

        var targetInput = $( 'textarea[name="content"]', bar );
        var contentType = $( 'input[name="content_type"]:checked', bar ).val();
        var args = {
            title: mnObjAdmin.media_uploader_select,
            button: {
                text: mnObjAdmin.media_uploader_select
            },
            multiple: false,  // Set to true to allow multiple files to be selected
            library: {
                order: 'ASC',

                // [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo',
                // 'id', 'post__in', 'menuOrder' ]
                orderby: 'date',

                // mime type. e.g. 'image', 'image/jpeg'
                type: contentType,

                // Searches the attachment title.
                search: null,

                // Attached to a specific post (ID).
                uploadedTo: null
            }
        };
        var uploader = wp.media( args );

        // When an attachment is added
        uploader.on( 'select', function() {

            var selectionCollection = uploader.state().get('selection').first().toJSON();
            targetInput.val( selectionCollection.url );

            saveOption( getOptionData( targetInput ), true );
        });

        uploader.open();
    });

    oStrategy.on( 'click', function () {
        var self = $( this );
        var parent = self.parents('li');
        var oStrategyVals = $( 'input[name="strategy_value"]', bar );

        oStrategyVals.val('0');

        saveOption( getOptionData( self ), false );
    });

    oDisplayWhen.on('click', function () {

        var self = $( this );
        var parent = self.parents('li');

        if( self.val() === 'page_load' ){
            parent.find('pre, p').addClass('mn-hide');
            $('.mn-item-display-on').removeClass('mn-hide');
        }
        else{
            parent.find('pre, p').removeClass('mn-hide');
            $('.mn-item-display-on').addClass('mn-hide');
        }

        saveOption( getOptionData( self ), false );
    });

    oDisplayOn.on('click', function () {
        var self = $( this );
        var idsWrap = $( '#mn-display-on-ids', bar );

        if( self.val() === 'all' || self.val() === 'homepage' ){
            idsWrap.addClass('mn-hide');
        }
        else if( self.val() === 'pages' || self.val() === 'posts' ) {
            idsWrap.removeClass('mn-hide');
            idsWrap.find('select').addClass('mn-hide');
            idsWrap.find('select[name="'+self.val()+'"]').removeClass('mn-hide');
        }

        saveOption( getOptionData( self ), true );
    });

    $('#mn-display-on-ids select', bar ).on('change', function () {
        var self = $(this);
        var parent = self.parent();
        var hiddenInput = parent.find('input[name="display_on_'+self.attr('name')+'"]');
        var selectedValues = self.val();

        hiddenInput.val( selectedValues.toString() );
        saveOption( getOptionData( hiddenInput ), true );
    });

    oComplexity.on('click', function () {
        var self = $( this );

        if( self.val() === 'advanced' ){
            $('.mn-item-display-time').removeClass('mn-hide');
            $('.mn-item-action').removeClass('mn-hide');
            $('.mn-item-action-text').removeClass('mn-hide');
        }
        else{
            $('.mn-item-display-time').addClass('mn-hide');
            $('.mn-item-action').addClass('mn-hide');
            $('.mn-item-action-text').addClass('mn-hide');
        }

        saveOption( getOptionData( self ), true );
    });

    oAction.on('click', function () {
        var self = $( this );
        var parent = self.parents('li');

        if( self.val() === 'close' ){
            parent.find('input[name="advanced_url"]').addClass('mn-hide');
        }
        else{
            parent.find('input[name="advanced_url"]').removeClass('mn-hide');
        }

        saveOption( getOptionData( self ), true );
    });

    oEnableBtn.on('click', function () {

        var self = $(this);

        if( self.is(':checked') ){
            self.val('on');
        }
        else{
            self.val('off');
        }

        saveOption( getOptionData( self ), true );
    });

    oStylePosition.on('change', function () {
        saveOption( getOptionData( oStylePosition ), true );

        if( oStylePosition.val() === 'center' ){
            $( '.mn-op-custom-position', bar ).addClass('mn-hide');
        }
        else{
            $( '.mn-op-custom-position', bar ).removeClass('mn-hide');
        }
    });

})( jQuery, jQuery(document), jQuery( window ) );