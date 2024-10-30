;(function ( $, $document, $window ) {


    /**
     * Money Ad Class
     * @param settings
     * @constructor
     */
    MoneyAd = function ( settings ) {

        this.config = $.extend({

            adSoldId: null, // ad sold id
            adId: null, // ad id

            mode: 'live', // live|preview
            ajaxUrl: '', // wp ajax url
            nonce: '', // wp nonce

            showAfter: 1, // show ad after 1 seconds
            type: 'image', // image|video|audio|custom
            complexity: 'simple',
            timer: 10,
            action: 'close', // close|gotolink
            goToLink: '',

            shadow: 'on',
            responsive: 'off',
            position: 'center',
            width: 'auto',
            height: 'auto',
            top: 'auto',
            bottom: 'auto',
            left: 'auto',
            right: 'auto',

        }, settings );

        this.getAdOutput();
    };


    /**
     * Class Methods
     */
    MoneyAd.prototype = {

        getAdOutput: function() {

            var Class = this;

            $.ajax({
                url: Class.config.ajaxUrl,
                type: 'post',
                data: {
                    action: 'money_front_ad_output',
                    nonce: Class.config.nonce,
                    ad_id: Class.config.adId
                },
                success: function ( response ) {

                    if( response.hasOwnProperty('errors') ) return false;

                    if( Class.config.mode === 'front' ){
                        setTimeout(function () {

                            Class.displayAd.call( Class, response.output );
                            Class.updateStatsAfterDisplay.call( Class );

                        }, parseFloat( Class.config.showAfter ) * 1000 );
                    }
                    else if( Class.config.mode === 'preview' ) {
                        Class.displayAd.call( Class, response.output );
                    }

                }
            });

        },

        displayAd: function ( output ) {

            var Class = this;

            $('body').append( Class.setShadow( output ) );
            Class.ad = $( '#money-ad-' + Class.config.adId );

            // help when loading image, video, audio
            setTimeout(function () {

                Class.setSize();
                Class.setPosition();
                Class.setTimer();
                Class.setEvents();

            }, 20 );

        },

        updateStatsAfterDisplay: function () {

            var Class = this;

            $.ajax({
                url: Class.config.ajaxUrl,
                type: 'post',
                data: {
                    action: 'money_front_ad_update_stats',
                    nonce: Class.config.nonce,
                    ad_id: Class.config.adSoldId
                }
            });
        },

        setShadow: function ( output ) {

            if( this.config.shadow === 'on' ){
                output = '<div class="money-ad-shadow fullSizeShadow-ad">' + output + '</div>';
            }
            else{
                output = '<div class="money-ad-shadow noShadow-ad">' + output + '</div>';
            }

            return output;
        },

        setSize: function () {
            if( this.config.type === 'video' && this.config.width === 'auto' ){
                this.config.width = '700px';
                this.config.height = 'auto';
            }
            else if( this.config.type === 'audio' && this.config.width === 'auto' ){
                this.config.width = '300px';
                this.config.height = 'auto';
            }

            this.ad.css({
                'width': this.config.width,
                'height': this.config.height,
            });
        },

        setPosition: function () {
            var Class = this;

            // center
            if( Class.config.position == 'center' ){
                Class.ad.css({
                    'top': '50%',
                    'left': '50%',
                    'bottom': 'auto',
                    'right': 'auto',
                    'margin-left': '-' + ( parseInt( Class.ad.width() ) / 2) + 'px',
                    'margin-top': '-' + ( parseInt( Class.ad.height() ) / 2) + 'px',
                });
            }

            // Custom
            else{
                Class.ad.css({
                    'top': Class.config.top,
                    'left': Class.config.left,
                    'right': Class.config.right,
                    'bottom': Class.config.bottom,
                    'margin-left': '',
                    'margin-top': '',
                });
            }
        },

        setTimer: function () {
            if( this.config.complexity === 'simple' ) return false;

            var Class = this;
            var $tempTimer = this.config.timer;
            var $timerFunc = setInterval( function () {

                if( $tempTimer == 0 ){
                    clearInterval( $timerFunc );
                    Class.ad.trigger('mn:timerEnded');
                }
                else{
                    $tempTimer = $tempTimer - 1;
                    $('.money-ad-timer span', Class.ad ).text( $tempTimer );
                }

            }, 1000 );

        },

        setEvents: function () {

            var Class = this;

            // When timer ends
            Class.ad.on('mn:timerEnded', function () {

                if( Class.config.complexity == 'simple' ) return false;

                if( Class.config.action != 'close' ){
                    window.location = Class.config.goToLink;
                }

                $('.money-ad-timer', Class.ad ).replaceWith( '<button class="money-ad-close">x</button>' )
            });

            // close button
            $document.on('click', '.money-ad-close', function ( event ) {
                event.preventDefault();

                var shadow = Class.ad.parent();

                shadow.fadeOut( 150, function () {
                    shadow.remove();
                });
            });

            // Window Resize
            var timer;
            var oldAdWidth = Class.ad.width();
            $window.resize(function () {
                clearTimeout( timer );
                timer = setTimeout( function () {

                    // Responsive
                    if( Class.config.responsive === 'on' && oldAdWidth > $window.width() ) {
                        Class.responsive( true );
                    }
                    else if( $window.width() > oldAdWidth ) {
                        Class.responsive( false );
                    }

                }, 40 );

            }).resize();

            // Update Click Stat
            if( Class.config.mode === 'front' ){
                $( '.money-ad-url, .money-ad-label a', Class.ad ).click( function () {

                    $.ajax({
                        url: Class.config.ajaxUrl,
                        type: 'post',
                        data: {
                            action: 'money_front_ad_update_clicks_stat',
                            nonce: Class.config.nonce,
                            ad_id: Class.config.adSoldId
                        }
                    });

                });
            }

        },

        responsive: function ( isResponsive ) {
            if ( isResponsive ) {
                this.ad.addClass('responsive-ad');
            }
            else {
                this.ad.removeClass('responsive-ad');
            }
            this.setPosition();
        }

    };


    /**
     * Execute
     */
    var moneyAd;
    if( typeof mnObjAd !== 'undefined' ) {
        moneyAd = new MoneyAd( mnObjAd );
    }


    /**
     * Open Ad by clicking on a button
     */
    $document.on( 'click', '.mnad', function ( event ) {
        event.preventDefault();

        var self = $(this);
        var adId = self.attr('id').split('-')[1];
        var buttonText = self.text();

        self.text( buttonText + ' ...' );

        $.ajax({
            url: mnObjFront.ajaxurl,
            type: 'post',
            data: {
                action: 'money_front_get_ad_js_params',
                nonce: mnObjFront.nonce,
                ad_id: adId
            },
            success: function ( response ) {

                if( response.hasOwnProperty('errors') ) return false;

                if( response.hasOwnProperty('actionLink') ){
                    window.location = response.actionLink;
                }
                else if( typeof moneyAd !== 'undefined' ){
                    $('.money-ad-shadow').remove();
                }

                moneyAd = new MoneyAd( response.adParams );
                self.text( buttonText );
            }
        });

    });

})( jQuery, jQuery(document), jQuery(window) );