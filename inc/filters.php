<?php


/**
 * Embed audio/video
 * @param $html
 * @return mixed
 *
 * Support :
 * uploaded audio/video
 * youtube
 * vimeo
 * soundcloud
 * mixcloud
 *
 */
function moneyEmbedFilter( $url, $type ){
        
    $pattern = '`src=["\']([^"\']+)`'; // get the iframe src url

    // youtube
    if( strpos( $url, 'youtu' ) !== false ){

        $html = wp_oembed_get( $url );
        preg_match( $pattern, $html, $match );

        if( strpos( $match[1], '?' ) !== false ){
            $url = $match[1] . '&autoplay=1&loop=1&enablejsapi=1';
        }
        else{
            $url = $match[1] . '?autoplay=1&loop=1&enablejsapi=1';
        }

        $html = str_replace( $match[1], $url, $html );

        return '<div class="money-embed-container">'.$html.'</div>';
    }

    // vimeo
    elseif( strpos( $url, 'vimeo' ) !== false ){

        $html = wp_oembed_get( $url );
        preg_match( $pattern, $html, $match );

        if( strpos( $match[1], '?' ) !== false ){
            $parts = explode( '?', $match[1] );
            $url = $parts[0] . '?autoplay=1&loop=1&title=0&byline=0&portrait=0';
        }
        else{
            $url = $match[1] . '?autoplay=1&loop=1&title=0&byline=0&portrait=0';
        }

        $html = str_replace( $match[1], $url, $html );
        return '<div class="money-embed-container">'.$html.'</div>';
    }

    // soundcloud
    elseif ( strpos( $url, 'soundcloud' ) !== false ){

        $html = wp_oembed_get( $url );
        preg_match( $pattern, $html, $match );

        if( strpos( $match[1], '?' ) !== false ){
            $url = $match[1] . '&auto_play=true';
        }
        else{
            $url = $match[1] . '?auto_play=true';
        }

        $html = str_replace( $match[1], $url, $html );
        return '<div class="money-embed-container">'.$html.'</div>';
    }

    // mixcloud
    elseif ( strpos( $url, 'mixcloud' ) !== false ){

        $html = wp_oembed_get( $url );
        preg_match( $pattern, $html, $match );

        if( strpos( $match[1], '?' ) !== false ){
            $url = $match[1] . '&autoplay=1';
        }
        else{
            $url = $match[1] . '?autoplay=1';
        }

        $html = str_replace( $match[1], $url, $html );
        return '<div class="money-embed-container">'.$html.'</div>';
    }

    // uploaded video / audio
    else{

        if( $type === 'video' ){
            return '<div class="money-embed-container"><video src="'.$url.'" loop autoplay></video></div>';
        }
        else{
            return '<audio src="'.$url.'" loop autoplay controls></audio>';
        }

    }

}
add_filter('money_embed', 'moneyEmbedFilter', 10, 2 );
