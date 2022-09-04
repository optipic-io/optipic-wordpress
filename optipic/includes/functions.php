<?php

function optipic_version() {
    return '1.27.2';
}

function optipic_change_content($content) {

    $settings = optipic_get_settings();

    /*echo '<pre>';
    var_dump($settings);
    echo '</pre>';
    die('-------------');*/

    if($settings['autoreplace_active'] && $settings['site_id']) {
        require_once __DIR__.'/optipic-cdn-php/ImgUrlConverter.php';
        $converterOptiPic = new \optipic\cdn\ImgUrlConverter($settings);
        $content = $converterOptiPic->convertHtml($content);
    }
    
    if( current_user_can('editor') || current_user_can('administrator') ){
        $content = str_replace('</body>', '<script src="https://optipic.io/api/cp/stat?domain='.$_SERVER["HTTP_HOST"].'&sid='.$settings['site_id'].'&cms=wordpress&stype=cdn&mode=public&version='.optipic_version().'"></script></body>', $content);
    }

    return $content;
}

function optipic_get_settings() {

    $optipicSettings = get_option('optipic_options');

//    echo '<pre>';
//    var_dump($optipicSettings);
//    echo '</pre>';
//    die('-------------');

    $optipicSiteID = (!empty($optipicSettings['cdn_site_id']))? $optipicSettings['cdn_site_id']: '';
    $autoreplaceActive = (!empty($optipicSettings['cdn_autoreplace_active']))? $optipicSettings['cdn_autoreplace_active']: '';
    //$domains = $optipicSettings['domains'];
    
    $cdnDomain = (!empty($optipicSettings['cdn_domain']))? $optipicSettings['cdn_domain']: '';
    
    $settings = array(
        'site_id' => $optipicSiteID,
        'autoreplace_active' => ($autoreplaceActive=='Y'),
        'cdn_domain' => $cdnDomain,
    );
    
    foreach(array('domains', 'exclusions_url', 'whitelist_img_urls', 'srcset_attrs') as $attrName) {
        $list = array();
        $attrVal = (!empty($optipicSettings[$attrName]))? $optipicSettings[$attrName]: '';
        foreach(explode("\n", $attrVal) as $val) {
            $val = trim($val);
            if($val) {
                $list[] = $val;
            }
        }
        $settings[$attrName] = $list;
    }
    
    return $settings;
}

function optipic_build_img_url($localUrl, $params=array()) {
    $dir = optipic_get_current_url_dir();
    
    $schema = "//";
    
    if(!isset($params['site_id'])) {
        $settings = optipic_get_settings();
        $params['site_id'] = $settings['site_id'];
    }
    
    if(isset($params['url_schema'])) {
        if($params['url_schema']=='http') {
            $schema = "http://";
        }
        elseif($params['url_schema']=='https') {
            $schema = "https://";
        }
    }
    
        
    if($params['site_id']) {
        if(!strlen(trim($localUrl)) || stripos($localUrl, 'cdn.optipic.io')!==false) {
            return $localUrl;
        }
        /*elseif(stripos($localUrl, 'http://')===0) {
            return $localUrl;
        }
        elseif(stripos($localUrl, 'https://')===0) {
            return $localUrl;
        }
        elseif(stripos($localUrl, '//')===0) {
            return $localUrl;
        }*/
        else {
            $siteUrlLength = strlen(get_site_url());

            // убираем адрес сайта из начала URL (для http)
            if(stripos($localUrl,'http')===0) {
                //$protocol = defined('HTTPS') ? 'https' : 'http';
                $localUrl = substr($localUrl, $siteUrlLength);
            }
            // убираем адрес сайта из начала URL (для https)
            elseif(stripos($localUrl, 'https')===0) {
                //$protocol = defined('HTTPS') ? 'https' : 'http';
                $localUrl = substr($localUrl, $siteUrlLength);
            }
            
            // если URL не абсолютный - приводим его к абсолютному
            if(substr($localUrl, 0, 1)!='/') {
                $localUrl = $dir.$localUrl;
            }
            
            $url = $schema.'cdn.optipic.io/site-'.$params['site_id'];
            if(isset($params['q'])) {
                $url .= '/optipic-q='.$params['q'];
            }
            if(isset($params['maxw'])) {
                $url .= '/optipic-maxw='.$params['maxw'];
            }
            if(isset($params['maxh'])) {
                $url .= '/optipic-maxh='.$params['maxh'];
            }
            
            $url .= $localUrl;
            
            return $url;
            
            //return '<img'.$matches[1].'src='.$quoteSymbol.'//cdn.optipic.io/site-'.$settings['site_id'].$url.$quoteSymbol.$matches[3].'>';
        }
    }
    // Если URL 
    else {
        return $localUrl;
    }
    
    
}

function optipic_get_current_url_dir() {
    return substr($_SERVER['REQUEST_URI'], 0, strripos($_SERVER['REQUEST_URI'], '/')+1);
}

?>