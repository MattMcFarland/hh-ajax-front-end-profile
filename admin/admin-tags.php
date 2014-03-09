<?php
/**
 * Admin page stuff
 *
 * Created by PhpStorm.
 * Date: 3/9/14
 * Time: 12:34 AM
 */


/**
 * Creates the backend dashboard menu item.
 * @uses add_submenu_page
 */
function register_clear_avatar_page() {
    add_submenu_page( 'users.php', 'Clear Avatars', 'Clear Avatars', 'manage_options', 'clear-avatars-page', 'view_clear_avatar_page' );
}
add_action('admin_menu', 'register_clear_avatar_page');

/**
 * A very basic page - specifically designed to reset all avatars.
 * @uses get_users
 * @uses delete_user_meta
 * @uses hh_download_gravatar
 * @uses get_the_author_meta
 * @uses get_avatar
 * @uses get_avatar_url
 */
function view_clear_avatar_page() {
    if (isset($_POST["clear_all_avatars"]))  {
        $users = get_users(array('meta_key'=>'profile_pic'));
        echo '<table>';
        echo '<thead>';
        echo '<th>User ID</th>';
        echo '<th>Username</th>';
        echo '<th>Gravatar</th>';
        echo '<th>Avatar</th>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($users as $user) {
            delete_user_meta($user->ID,'profile_pic');
            hh_download_gravatar($user->ID);
            $pic_url = get_the_author_meta( 'profile_pic', $user->ID );
            $gravatar_url = get_avatar_url(get_avatar($user->ID),'30');
            echo '<tr>';
            echo '<td>['.$user->ID.']</td>';
            echo '<td>'.$user->user_nicename.'</td>';
            echo '<td><img style="max-width:30px;" src="'.$gravatar_url.'"/></td>';
            echo '<td><img style="max-width:30px;" src="'.$pic_url.'"/></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        die();
    }

    echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
    echo '<h2>Clear User Avatars</h2>';
    echo '</div>';
    echo '<p>Use this simple tool to clear all avatars, this is not reversable</p>';
    echo '<form name="hh_admin_clear_avatars" action="" method="post">';
    echo '<input type="checkbox" name="clear_all_avatars">I understand this is not reversable';
    echo '<br/><br/><button type=submit">Do it!</button>';
    echo '<script>document.hh_admin_clear_avatars.reset();</script>';
    echo '</form>';
}
