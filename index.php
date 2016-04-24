<?php
/**
 * @package Grafana
 * @version 1.0
 */
/*
Plugin Name: Grafana
Plugin URI: https://wordpress.org/plugins/grafana/
Description: This plugin allows easy integration of Grafana dashboards and panels into a Wordpress site.
Author: Tyler Mitchell	
Version: 1.0
Author URI: http://makedatauseful.com/
*/

// TODO: Get grafana base url from WP 
$gr_site = "http://carversvillefarm.landstream.net/grafana/";
// TODO: Get user/pwd from WP config form
$username = "cville";
$password = "deepsoil1";
$gr_db_uri = "db/watchtower-1";
$gr_panel_id = 17;

function grafana_curl($gr_url){
	$userpwd = $GLOBALS['username'].":".$GLOBALS['password'];
	$ch = curl_init($gr_url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json'
	));
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//	curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_USERPWD, $userpwd);
	$result = curl_exec($ch);
//	$status_code = curl_getinfo($ch, CURLINFO_HEADER_OUT);
//	echo $status_code;
	curl_close($ch);
	//echo $gr_url;
	return json_decode($result);
}

function grafana_get_dashboards($gr_site){
	// i.e. http://carversvillefarm.landstream.net/grafana/api/search
	$db_list = grafana_curl($gr_site."api/search");
	return $db_list;
}

function grafana_get_dashboard($gr_site, $gr_db){
	// i.e. http://carversvillefarm.landstream.net/grafana/api/dashboards/db/watchtower-1
	$gr_raw_db_details = grafana_curl($gr_site."api/dashboards/".$gr_db);
	$gr_panel_list = $gr_raw_db_details_parsed;
	return $gr_raw_db_details; 
}

function grafana_get_panels($gr_db_details){
	foreach ($gr_db_details as $panel){
		$gr_panel_list.append(array(
								"name" => "a",
								"id"=>"n"		
							));
	}
	return $gr_panel_list;
}

function grafana_get_chart($gr_site, $gr_db_uri, $gr_panel_id){
	// i.e. <iframe src="http://carversvillefarm.landstream.net/grafana/dashboard-solo/db/watchtower-1?panelId=17" ></iframe>
	// Optional add time frame: &from=1445622189441&to=1445975302778
	$gr_iframe_url = $gr_site . "dashboard-solo/" . $gr_db_uri . "?panelId=" . $gr_panel_id;
	return "<iframe src=\"" . $gr_iframe_url . "\"></iframe>";
}

function grafana_plugin_menu(){
	add_options_page( 'Grafana Plugin Options', 'Grafana Plugin', 'manage_options', 'wp-grafana-id', 'wp_grafana_options');
}

function wp_grafana_options(){

    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // variables for the field and option names 
    $gr_base_url_name = 'gr_base_url';
    $gr_auth_user_name = 'gr_auth_user';
    $gr_auth_pwd_name = 'gr_auth_pwd';
    
    $hidden_field_name = 'gr_submit_hidden';

    // Read in existing option value from database
    $url_val = get_option( $gr_base_url_name );
    $user_val = get_option( $gr_auth_user_name );
    $pwd_val = get_option( $gr_auth_pwd_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $url_val = $_POST[ $gr_base_url_name ];
        $user_val = $_POST[ $gr_auth_user_name ];
        $pwd_val = $_POST[ $gr_auth_pwd_name ];

        // Save the posted value in the database
        update_option( $gr_base_url_name, $url_val );
        update_option( $gr_auth_user_name, $user_val );
        update_option( $gr_auth_pwd_name, $pwd_val );

        // Put a "settings saved" message on the screen

	?>
	<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
	<?php
    }

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Menu Test Plugin Settings', 'menu-test' ) . "</h2>";

    // settings form
    
    ?>

	<form name="form1" method="post" action="">
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
	
	<p><?php _e("Grafana Base Url:", 'menu-test' ); ?> 
	<input type="text" name="<?php echo $gr_base_url_name; ?>" value="<?php echo $url_val; ?>" size="120"></p>
	<p><?php _e("HTTP Auth Username:", 'menu-test' ); ?> 	
	<input type="text" name="<?php echo $gr_auth_user_name; ?>" value="<?php echo $user_val; ?>" size="20"></p>
	<p><?php _e("HTTP Auth Password:", 'menu-test' ); ?> 
	<input type="password" name="<?php echo $gr_auth_pwd_name; ?>" value="<?php echo $pwd_val; ?>" size="20">
	</p><hr />
	
	<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
	</p>
	
	</form>
	</div>
	
	<?php
	 
}

add_action( 'admin_menu', 'grafana_plugin_menu' );

// Get saved option values
// echo "<center>".get_option('gr_base_url')."</center>";
// echo "<center>".get_option('gr_auth_user')."</center>";
// echo "<center>".get_option('gr_auth_pwd')."</center>";

// Testing functions ...
function test(){
	foreach (grafana_get_dashboards($gr_site) as $db){
		
		if ($db->uri === $GLOBALS['gr_db_uri']){
			echo "<li id=".$db->id."><b>".$db -> uri."</b></li>";
			$gr_panels = json_encode(grafana_get_dashboard($gr_site,$db->uri)->dashboard->rows[0]->panels[17]);
			echo $gr_panels;
		}
		else {
			echo "<li id=".$db->id.">".$db -> uri."</li>";
		}
	}
	
	echo grafana_get_chart($gr_site, $gr_db_uri, $gr_panel_id);
}
// TODO: Set shortcode action
//add_action( 'admin_notices', 'hello_dolly' );
//add_action( 'admin_head', 'dolly_css' );

