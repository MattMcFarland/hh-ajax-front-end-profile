<?php

/**
 * Downloads offsite gravatar from social media sites (if appl) or gravatar.com and saves them as local.
 * This function WILL NOT WORK if you do not have imagemagick 6.x on your server and it is not in /ur/local/bin
 * As such, it is recommended you just use the /img/mystery.gif instead.
 *
 * @param $user_id
 * @param string $size
 *
 */
function hh_download_gravatar($user_id,$size='150') {

    //ImageMagick location
    $imagick = '/usr/local/bin/convert';

    /**
     * IF you do not have ImageMagick installed or not sure, then uncomment the following block of code.
     */
    /*
    $profile_pic = plugin_dir_url( __FILE__ ) . 'img/mystery.gif';
    update_user_meta($user_id,'profile_pic',$profile_pic);

    return;
    */
    $gravatar = get_avatar_url(get_avatar($user_id),'150');
    $site = parse_url($gravatar);
    if ($site['host'] !=='0.gravatar.com' && $site['host'] !=='1.gravatar.com') {
        $upload_dir =wp_upload_dir();
        $path = $upload_dir['path'];
        $process = $path.'/id_'.$user_id.'_pic';
        copy($gravatar, $process);
        $final = $process.'.jpg';
        exec('/usr/local/bin/convert -auto-orient -strip -interlace Plane -resize 150x150^ -gravity center -extent 150x150 -quality 62 -format jpg '.$process.' '.$final.' 2>&1',$error);
        $profile_pic = $upload_dir['url'].'/'.basename($final);
    } else {
        $profile_pic = plugin_dir_url(__FILE__). 'img/mystery.gif';
    }
    update_user_meta($user_id,'profile_pic',$profile_pic);
}

function get_avatar_url($get_avatar){
    preg_match("/src='(.*?)'/i", $get_avatar, $matches);
    return $matches[1];
}

function hh_get_profile_photo($user_id,$size){
    $pic_url = get_the_author_meta( 'profile_pic', $user_id );
    if ($pic_url =="") hh_download_gravatar($user_id );
    $errordir = plugin_dir_url( __FILE__ ) . 'img/mystery.gif';
    return '<img src="'.$pic_url.'" style="width:'.$size.'px; height:'.$size.'px" onerror="this.src=\''.$errordir.' \' ">';
}

function hh_profile_pic_reset($user_id) {
    hh_download_gravatar($user_id );
}

