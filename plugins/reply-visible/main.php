<?php
/*
Plugin Name: reply-visible
Plugin URI: https://github.com/Carseason
Description: 评论可见
Version: 1.0.0
Author: Carseason
Author URI: https://github.com/Carseason
*/

declare(strict_types=1);
\defined('\\AUTH_KEY') || \http_response_code(500) && die;
add_filter('the_content', 'Carseaosn_VisibleMethod');
function Carseaosn_VisibleMethod(string $content):string{
    if ( is_user_logged_in() ){
        global $current_user;
        $user = $current_user;
        $userId = $current_user->ID;
        $postsId  = get_the_ID();
        if (!Carseaosn_DBExistCommentsAndUser($postsId,$userId)){
            $nickname = $current_user->display_name;
            $content =  Carseaosn_CommentPrivateHTML($nickname);
        }
    }else{
        $content =  Carseaosn_CommentPrivateHTML("游客");
    }
    return $content;
}
function Carseaosn_CommentPrivateHTML(string $nickname):string{
    return '<p style="display:block;width:100%;font-size:1rem;color:#191919;"><span style="color:#f00;cursor:pointer;">'.$nickname.'</span> 如果您要查看本帖隐藏内容请先 <span style="color:#f00;cursor:pointer;">评论</span>!</p>';
}
function Carseaosn_DBExistCommentsAndUser(int $postsId, int $userId):bool{
    global $wpdb;
    $tableName = $wpdb->prefix."comments";
    $sqlRow = $wpdb->prepare( 
        "select * from `".$tableName."` where comment_post_ID = %d and  user_id = %d limit 1;",
        $postsId,
        $userId,
    );
    $data = $wpdb->get_row($sqlRow);
    return $data->comment_ID > 0;
}