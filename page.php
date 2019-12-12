/*
* put this content in your themes function.php file or in your cheild themes function.php files
* here code to update shared content on all sub site
*/
 add_action('save_post','wase87206_broadcast');

 function wase87206_broadcast($post_id) {
    $user = wp_get_current_user();
    $current_user_id=$user->ID;
    if(is_super_admin($current_user_id)){
        $post_id_update_array=array(7963); // this is condition if you don't want to upate all page, if you want only specific page content should update on all sub site then provide their page id
        $current_blog_id=get_current_blog_id();
        $mainurl = get_option('siteurl');
        $post = get_post($post_id);
        $current_post_id = get_the_ID(); // here to get current site blog id
        
        if(in_array($current_post_id,$post_id_update_array) && $current_blog_id==1){ //optional conditions
            $blogs = get_sites();
            foreach ($blogs as $blogid) {
                switch_to_blog($current_blog_id);
			  	$post = get_post($post_id); //here is code to get original post content
                $subsite_id = get_object_vars($blogid)["blog_id"];
                if($current_blog_id!=$subsite_id){
                    switch_to_blog($subsite_id);
                    $siteurl = get_option('siteurl');
                    // $post->post_content = str_replace($mainurl,$siteurl,$post->post_content); // fix links
                    // unhook this function so it doesn't loop infinitely
                	remove_action('save_post','wase87206_broadcast');
                    wp_update_post($post);
                    // re-hook this function
                	add_action('save_post','wase87206_broadcast');
                }
          }
          switch_to_blog($current_blog_id); // set context back to main site
        }
    }
}
