<?php
/*
Plugin Name: Own Text Links
Plugin URI: http://www.wpmanage.com/own-text-links/
Description: Create your own internal or external links in your content automaticaly.
Version: 1.2
Author: Ujog Raul
Author URI: http://www.wpmanage.com

	Copyright (c) 2011
*/

if (!defined('TLK_PLUGIN_NAME'))
    define('TLK_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
	
if (!defined('TLK_PLUGIN_BASE'))
    define('TLK_PLUGIN_BASE', plugin_basename(__FILE__));

if (!defined('TLK_PLUGIN_URL'))
    define('TLK_PLUGIN_URL', WP_PLUGIN_URL . '/' . TLK_PLUGIN_NAME);
	
if (!defined('TLK_PLUGIN_DIR'))
    define('TLK_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . TLK_PLUGIN_NAME);
	
if (!defined('TLK_VERSION_KEY'))
    define('TLK_VERSION_KEY', 'TLK_version');

if (!defined('TLK_VERSION_NUM'))
    define('TLK_VERSION_NUM', '1.2');

///////////////////////////////////DB///////////////////////////////////////

function create_mylink_db(){
  global $wpdb;
 
  $sql = "CREATE TABLE " . $wpdb->prefix ."mytext_link (
	  id int(9) unsigned NOT NULL AUTO_INCREMENT,
	  time datetime NOT NULL,
	  word varchar(255) CHARACTER SET utf8 NOT NULL,
      link varchar(255) CHARACTER SET utf8 NOT NULL,
  	  expire datetime NOT NULL,
  	  views int(9) NOT NULL,
      PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=$wpdb->charset AUTO_INCREMENT=0;";
  require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
  dbDelta($sql);
  add_option("TLK_VERSION", TLK_VERSION_KEY);
}
register_activation_hook(__FILE__,'create_mylink_db');

///////////////////////////////////CSS///////////////////////////////////////

function tlk__stylesheet() {
	$myStyleUrl = TLK_PLUGIN_URL . '/css/tlk-style.css';
	$myStyleFile = TLK_PLUGIN_DIR . '/css/tlk-style.css';
	if ( file_exists($myStyleFile) ) {
		wp_register_style('revStyleSheet', $myStyleUrl);
		wp_register_style('tlk-jqueryui-css', "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css");
		wp_enqueue_style( 'revStyleSheet');
		wp_enqueue_style( 'tlk-jqueryui-css');	
	}
}
///////////////////////////////////JS////////////////////////////////////////

function tlk_js() {
	wp_enqueue_script ( 'tlk_jquery_date_js' , TLK_PLUGIN_URL . '/js/jquery-ui.min.js' , array ( 'jquery' ) , '1.4' , true );
	wp_enqueue_script ( 'tlk_date_js' , TLK_PLUGIN_URL . '/js/jquery-ui-timepicker.js' , array ( 'tlk_jquery_date_js' ) , '1.4' , true );
	wp_enqueue_script('tlk_js', TLK_PLUGIN_URL . '/js/tlkjs.js', array('jquery'), '1.0', true);

}

////////////////////////////////////MENU////////////////////////////////////

add_action('admin_menu', 'my_tlk_menu');
function my_tlk_menu() {

 	$tlk_page = add_menu_page('Own Text Links', 'Own Text Links', 'manage_options','tlk-link', 'my_add_new', TLK_PLUGIN_URL. '/images/icon.png' );
	$tlk_edit =  add_submenu_page('tlk-link', 'New/Edit', 'New/Edit', 'manage_options', 'tlk-link', 'my_add_new');
	add_submenu_page('tlk-link', 'Settings', 'Settings', 'manage_options', 'tlk-settings', 'my_add_set');
	

  add_action('admin_print_styles-'. $tlk_edit, 'tlk__stylesheet');
  add_action('admin_print_scripts-'. $tlk_edit, 'tlk_js');	
}

////////////////////////////////////INIT////////////////////////////////////////

function tlk_set_links($links) {
        array_unshift($links, '<a class="edit" href="admin.php?page=tlk-settings">Settings</a>');
        return $links;
}

add_filter('plugin_action_links_'.TLK_PLUGIN_BASE, 'tlk_set_links', 10, 2 );

////////////////////////////////////SETTINGS////////////////////////////////////
function my_add_set(){
	if(isset($_GET['update']) && !empty($_GET['update'])){
		if(isset($_POST['tlk_css'])){ update_option('tlk_css', $_POST['tlk_css']);}
		if(isset($_POST['tlk_max'])){ update_option('tlk_max', $_POST['tlk_max']);}
		if(isset($_POST['tlk_posts'])){update_option('tlk_typ', implode(",",$_POST['tlk_posts']));}
		if(isset($_POST['tlk_rss'])){ update_option('tlk_rss', $_POST['tlk_rss']);} else {  update_option('tlk_rss', '');}
		if(isset($_POST['tlk_rel'])){ update_option('tlk_rel', $_POST['tlk_rel']);} else {  update_option('tlk_rel', '');}
		if(isset($_POST['tlk_tar'])){ update_option('tlk_tar', $_POST['tlk_tar']);} else {  update_option('tlk_tar', '');}
		if(isset($_POST['tlk_exc'])){ update_option('tlk_exc', $_POST['tlk_exc']);}
	}
	$tlk_css = get_option('tlk_css');
	$tlk_max = get_option('tlk_max');
	$tlk_typ = explode(",",get_option('tlk_typ'));
	$tlk_rss = get_option('tlk_rss');
	$tlk_rel = get_option('tlk_rel');
	$tlk_tar = get_option('tlk_tar');
	$tlk_exc = get_option('tlk_exc');
	?>	
	<div class="wrap">
	<h2>Settings</h2>
	<form action="admin.php?page=tlk-settings&update=true" method="post">
	<table class="widefat fixed">
	<tr valign="top">
	<th scope="row" width="250px">Filter in all:</th>
	<td><input name="tlk_posts[]" type="checkbox" value="Posts" <?php if(in_array("Posts", $tlk_typ)) echo 'checked="checked"'; ?> /><label> Posts </label>
    	<input name="tlk_posts[]" type="checkbox" value="Pages" <?php if(in_array("Pages", $tlk_typ)) echo 'checked="checked"'; ?>/><label> Pages </label>
        <input name="tlk_posts[]" type="checkbox" value="Custom posts" <?php if(in_array("Custom posts", $tlk_typ)) echo 'checked="checked"'; ?>/><label> Custom posts </label>
    </td>
	</tr>	
    <tr valign="top">
	<th scope="row" width="250px">Process RSS feeds:</th>
	<td><input name="tlk_rss" type="checkbox" value="rss" <?php if(!empty($tlk_rss)) echo 'checked="checked"'; ?>/><label> RSS </label></td>
	</tr>
    <tr valign="top">
	<th scope="row" width="250px">Link Relationship Attribute:</th>
	<td><input name="tlk_rel" type="checkbox" value="rel" <?php if(!empty($tlk_rel)) echo 'checked="checked"'; ?>/><label> rel="nofollow" </label></td>
	</tr>
    <tr valign="top">
	<th scope="row" width="250px">Opens the linked keyword in a new window or tab:</th>
	<td><input name="tlk_tar" type="checkbox" value="tar" <?php if(!empty($tlk_tar)) echo 'checked="checked"'; ?>/><label> target="_blank" </label></td>
	</tr>	
    <tr valign="top">
	<th scope="row" width="250px">Custom CSS class:</th>
	<td>
   		<input type="text" name="tlk_css" value="<?php echo $tlk_css;?>" size="20px"/> <br /><small>Custom class for your link</small>
    </td>
	</tr>
     <tr valign="top">
	<th scope="row" width="250px">Maximum Links per Key/Word <br/>on Post/Page:</th>
	<td>
   		<input type="text" name="tlk_max" value="<?php echo $tlk_max;?>" size="20px"/> <br /><small>0 or empty for no limit</small>
    </td>
	</tr>
     <tr valign="top">
	<th scope="row" width="450px">Exclude posts/pages <br/>(id, slug or name) - use comma:</th>
	<td>
   		<input type="text" name="tlk_exc" value="<?php echo $tlk_exc;?>" size="40px"/> <br /><small>Ex: 222, new-article-name</small>
    </td>
	</tr>
    </table>
	<p class="submit">
	<input class="button-primary" type="submit" name="submit" value="Save" />
	</p>
	</form>
	<a href="http://www.wpmanage.com" target="_blank" style=" float:right">PSD to Wordpress Theme &rarr;</a>
	</div>
<?php
}
////////////////////////////////////Add New / Edit////////////////////////////////////
function tlk_url($cm){
		if(!preg_match('/([0-9A-Z]){3}+[.]([0-9A-Z])+[.]([0-9A-Z]){2}/i' ,$cm) && $cm) {
			return true;
 		}
	}
function tlk_URLfix($url)
		{			
				if ( strpos( $url, "http://" ) === false ) {
					$url = str_replace('www.', 'http://www.', $url);
				}
				return $url;
	
		} 
function my_add_new(){
	global $wpdb;
	$tlk_txti = "Key/text name";
	$tlk_lnki = "Target link";
	$tlk_dati = "Expire in: 07/19/2011 12:00";
	
	if(isset($_GET['edit']) && !empty($_GET['edit'])){
		$tlk_txt = isset($_POST['tlk_txt_'])  ? trim($_POST['tlk_txt_']) :  $tlk_txti;
		$tlk_lnk = isset($_POST['tlk_link_']) ? trim($_POST['tlk_link_']) : $tlk_lnki;
		$tlk_dat = isset($_POST['tlkdate_']) ? trim($_POST['tlkdate_']) : $tlk_dati;
	}
	else{
		$tlk_txti = $tlk_txt = isset($_POST['tlk_txt'])  ? trim($_POST['tlk_txt']) : $tlk_txti;
		$tlk_lnki = $tlk_lnk = isset($_POST['tlk_link']) ? trim($_POST['tlk_link']) : $tlk_lnki;
		$tlk_dati = $tlk_dat = isset($_POST['tlkdate']) ? trim($_POST['tlkdate']) : $tlk_dati;
	}
	
	if(isset($_POST['tlk_txt']) || isset($_POST['tlk_txt_'])){
		$form_err = "";
		if ($tlk_txt == "Word/text name"){
			$form_err = "Please complete your key or text<br/>";
		}
		if ($tlk_lnk=="Target link" || tlk_url($tlk_lnk)){
			$form_err .= "Please complete your target link<br/>";
		}
		if ($tlk_dat == "Expire in: 07/19/2011 12:00"){
			$form_err .= "Please complete a expiration date<br/>";
		}
		if(!isset($_GET['edit'])){
			$isword = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix ."mytext_link WHERE word = '".$tlk_txt."'");
			if(!empty($isword)){
				$form_err .= "This key already exist";
			}
		}
		if(!empty($form_err)) echo '<div class="error">'.$form_err.' </div>';
		else if (empty($form_err)){
			if(isset($_POST['tlk_txt'])){
				$time = date('Y-m-d H:i:s');
				//$tlk_dat = str_replace("/" , "-" , $tlk_dat);
				$unx = strtotime($tlk_dat);
				$exp = date('Y-m-d H:i:s', $unx);
				
				$sql  = "INSERT INTO " . $wpdb->prefix ."mytext_link";
				$sql .= " 	(id, time, word, link, expire, views) VALUES 
							('','".$time."','".$tlk_txt."','".$tlk_lnk."','".$exp."','0')";
				$wpdb->query($sql);
				 echo '<div class="updated">Your key was added </div>';
			}
			else if(isset($_POST['tlk_txt_'])){
				$unx = strtotime($tlk_dat);
				$exp = date('Y-m-d H:i:s', $unx);
				
				$sql  = "UPDATE " .$wpdb->prefix ."mytext_link 
				SET word = '".$tlk_txt."', link = '".$tlk_lnk."', expire = '".$exp."'
		        WHERE ID = ".$_POST['tlk_id_']."";		
				$wpdb->query($sql);
				 echo '<div class="updated">Your changes was made</div>';
			}
		}
		
	}
	
	if(isset($_GET['del']) && !empty($_GET['del'])){
		$wpdb->query("DELETE FROM ".$wpdb->prefix ."mytext_link WHERE id = '".$_GET['del']."'");
	}
	
	$posttags = get_tags();
	if ($posttags) {
	$taguri = '<option value="" class="seltit">Select key from Tags </option>';	
	  foreach($posttags as $tag) {
		$taguri .=  '<option value="'.$tag->name.'">'.$tag->name.'</option>'; 
	  }
	}
	
	
	?>	
	<div class="wrap">
    <h2>Add/Edit Text Links</h2>
    <div id="tlkadd">
    	<form action="admin.php?page=tlk-link" method="post">
        <select onchange="seltag(this.value)">
        	<?php echo $taguri; ?>
        </select>
        <input type="text" name="tlk_txt" size="30px" onfocus="if(this.value=='Key/text name') this.value=''; this.className='write'" onblur="if(this.value=='') {this.value = this.defaultValue; this.className='default';} else this.className='nowrite'" value="<?php _e($tlk_txti); ?>" id="thekey"/>
        <input type="text" name="tlk_link" size="60px" onfocus="if(this.value=='Target link') this.value=''; this.className='write'" onblur="if(this.value=='') {this.value = this.defaultValue; this.className='default';} else this.className='nowrite'" value="<?php _e($tlk_lnki); ?>"/>
        <input type="text" name="tlkdate" id="tlkdate" size="24px" onfocus="if(this.value=='Expire in: 07/19/2012 00:00') this.value=''; this.className='write'" onblur="if(this.value=='') {this.value = this.defaultValue; this.className='default';} else this.className='nowrite'" value="<?php _e($tlk_dati); ?>"/>
       
        <input class="button-primary" type="submit" name="submit" value="Add New" />
       
        </form>
    </div>
       <form action="admin.php?page=tlk-link&edit=ok" method="post">
    <table class="widefat">
    
    <thead>
        <tr>
            <th>Key/Name</th>
            <th>URL</th>
            <th>Valability Until</th>
            <th>Views</th>
            <th>Change</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
          <th>Key/Name</th>
          <th>URL</th>
          <th>Valability Until</th>
          <th>Views</th>
          <th>Change</th>
        </tr>
    </tfoot>
    <tbody>
<?php
	$tlk_datas = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."mytext_link ORDER BY `time` DESC");
	if(!empty($tlk_datas)){
	foreach($tlk_datas as $tlk)
		{
		$unx = strtotime( $tlk->expire);
		$expired = ($unx < time()) ? $expired = ' class="expired"' : "";
		$exp = date('m-d-Y H:i', $unx);
		$exp = str_replace("-" , "/" , $exp);
		$tlktab='<tr id="key'.$tlk->id.'"><td class="key">'.$tlk->word.'</td><td>'.str_replace('rel="nofollow"', 'rel="nofollow" target="_blank"', make_clickable($tlk->link)).'</td><td '.$expired.'>'.$exp.'</td><td>'.$tlk->views.'</td><td><a href="javascript: tlk_editkey('.$tlk->id.')">Edit</a> | <a href="admin.php?page=tlk-link&del='.$tlk->id.'">Delete</a></td></tr>';
		echo $tlktab;
		}
	}else{
		echo '<tr><td colspan="5"> You don\'t have any key. Add new one.</td></tr>';
	}
?>
    </tbody>
    </table>
    	</form>
    <a href="http://www.wpmanage.com" target="_blank" style=" float:right; margin-top:10px">PSD to Wordpress Theme &rarr;</a>
	</div>
<?php
}
////////////////////////////////////CONTENT////////////////////////////////////

function tlk_filter($content){
	global $post, $wpdb;
	
	$r_key = array();
	$r_lnk = array();

	$tlk_css = get_option('tlk_css'); $r_css = (!empty($tlk_css)) ? ' class="'.$tlk_css.'"' : '';
	$tlk_max = get_option('tlk_max'); $r_max = (!empty($tlk_max)) ? (int)$tlk_max : -1;
	$tlk_typ = explode(",",get_option('tlk_typ'));
	$tlk_rel = get_option('tlk_rel');  $r_lre = ($tlk_rel=="rel") ? ' rel="nofollow"' : '';
	$tlk_tar = get_option('tlk_tar');  $r_tar = ($tlk_tar=="tar") ? ' target="_blank"' : '';
	$tlk_exc = explode(",",get_option('tlk_exc'));
	$tlk_rss = get_option('tlk_rss');
	
	foreach($tlk_typ as $tlk_ty){	
		if($tlk_ty=="Posts" && is_single($post->ID)){$is_page = true;} 
		if($tlk_ty=="Pages" && is_page($post->ID)){$is_page = true;} 
		if($tlk_ty=="Custom posts" && post_type_exists($post->ID)){$is_page = true;} 
		if(is_feed() && !empty($tlk_rss)){$is_page = true;}
	}

	if(!empty($tlk_exc[0])){
		foreach($tlk_exc as $tlk_ex){
			$tlk_ex =  trim($tlk_ex);
			if(is_single($tlk_ex)){$is_page = false; }
			if(is_page($tlk_ex)){$is_page = false;}
			if(post_type_exists($tlk_ex)){$is_page = false;}
		}

	}

	$res_tlk = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."mytext_link");
	if(!empty($res_tlk) && isset($is_page)){
		
		foreach($res_tlk as $result_tlk)
		{
			if ( strpos( $content, $result_tlk->word ) !== false && strtotime( $result_tlk->expire) > time()) {
			
				$is_tlk = 1;
				$r_key[] .= $result_tlk->word;
				$r_lnk[] .= $result_tlk->link;
				$r_cnt = (int)$result_tlk->views+1;
				$wpdb->query("UPDATE " .$wpdb->prefix ."mytext_link 
				SET views = '".$r_cnt."'
		        WHERE ID = ".$result_tlk->id."");						
				}
		}
	}
	$l = 0;
	
	if(isset($is_tlk) && isset($is_page)){
		foreach($r_key as $r_k)
		{
			$content = preg_replace ( '/(?!(?:[^<]+>))\b('.$r_k.')\b/is', '<a href="'.tlk_URLfix($r_lnk[$l]).'" '.$r_tar.$r_lre.$r_css.'>'.$r_k.'</a>',  $content, $r_max);				
			$l++;
		}
	}	
	return $content;
}
add_filter("the_content", "tlk_filter", 12);
?>