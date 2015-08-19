<?php /*
Plugin Name: Choose Header File
Plugin URI: https://github.com/StephenWidom/choose-header
Description: Select which header file to use on which pages
Version: 0.1
Author: Stephen Widom
Author URI: http://stephenwidom.com
License: GPL
*/ 

add_action('add_meta_boxes','cd_meta_box_add');
function cd_meta_box_add(){
    add_meta_box('my-meta-box-id','Select a header file','cd_meta_box_cb','page','normal','high');
}

function cd_meta_box_cb($post){
	$files = get_header_files();
	$values = get_post_custom($post->ID);
	$selected = isset($values['my_meta_box_select']) ? esc_attr($values['my_meta_box_select'][0]) : '';
    wp_nonce_field('my_meta_box_nonce','meta_box_nonce');
    ?>
    <p>
        <label for="my_meta_box_select">Header</label>
        <select name="my_meta_box_select" id="my_meta_box_select">
        	<?php foreach($files as $file): ?>
        	<option value="<?php echo $file; ?>" <?php selected($selected,$file); ?>><?php echo $file; ?></option>
        	<?php endforeach; ?>
        </select>
    </p>
    <?php    
}

add_action('save_post','cd_meta_box_save');
function cd_meta_box_save($post_id){

    // Bail if we're doing an auto save
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
     
    // If our nonce isn't there, or we can't verify it, bail
    if(!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'],'my_meta_box_nonce')) return;
     
    // If our current user can't edit this post, bail
    if(!current_user_can('edit_post')) return;
         
	if(isset($_POST['my_meta_box_select']))	update_post_meta($post_id,'my_meta_box_select',esc_attr($_POST['my_meta_box_select']));

}

add_action('init','check_header_value');
function check_header_value(){

	// Grabbing current post ID
	$current_post_id = url_to_postid("http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

	global $headerfile;
	$headerfile = get_post_meta($current_post_id,'my_meta_box_select',true);
	$headerfile = str_replace('.php','',str_replace('-','',str_replace('header','',$headerfile)));
}

function get_custom_header_file(){
	global $headerfile;
	if($headerfile != ""){
		get_header($headerfile);
	} else {
		get_header();
	}
}

function get_header_files(){
	$directory = get_template_directory();
	$hidden = array( // List of files/directories we don't want to show up
		'..',
		'.',
		'.htaccess',
		'error_log',
		'index.php',
		'style.css'
	);
	$files = array_values(array_diff(scandir($directory),$hidden));

	foreach ($files as $key => $value){
		if(strpos($value,'header') !== 0) unset($files[$key]);
	}

	return $files;

}

?>