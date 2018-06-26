<?php 
/*
Plugin Name: My Traffic Counter 
Description: A simple plugin to display the number of visitors on your website.
Version: 1.0
Author: Mohit Pradhan
Author URI: http://www.mohitpradhan.com
License: GPLv2
*/


class TrafficCounterPlugin_Widget extends WP_Widget
{
     
    function __construct()
	{
        parent::__construct(
         
            // base ID of the widget
            'trafficcounterplugin_widget',
             
            // name of the widget to be displayed under widgets
            __('My Traffic Counter Widget', 'TrafficCounterPlugin' ),
             
            // widget options to be displayed under widgets
            array (
                'description' => __('Simple widget to display the number of visitors on your website', 'traffic Counter')
            )
             
        );
    }
    
	//to display the plugin settings as a form in widgets
    function form($curr)
	{
        $defaultstyle = array(
        'textfontfamily' => 'Helvetica',
        'textcolor'=>'#000',
        'headingfontfamily'=>'Helvetica',
        'headingcolor'=>'#000'
        );
        
		$textfontfamily;
		$textcolor;
		$headingfontfamily;
		$headingcolor;
		
		if(isset($curr['textfontfamily']))
		{
			$textfontfamily = $curr['textfontfamily'];
		}
		else
		{
			$textfontfamily = $defaultstyle['textfontfamily'];
		}
		
		if(isset($curr['textcolor']))
		{
			$textcolor = $curr['textcolor'];
		}
		else
		{
			$textcolor = $defaultstyle['textcolor'];
		}
		
		if(isset($curr['headingfontfamily']))
		{
			$headingfontfamily = $curr['headingfontfamily'];
		}
		else
		{
			$headingfontfamily = $defaultstyle['headingfontfamily'];
		}
		
		if(isset($curr['headingcolor']))
		{
			$headingcolor = $curr['headingcolor'];
		}
		else
		{
			$headingcolor = $defaultstyle['headingcolor'];
		}
		
       ?>
            <p>
                <label for="<?php echo $this->get_field_id('headingfontfamily'); ?>">Heading Font:</label><br/>
                <select class="widefat" id="<?php echo $this->get_field_id('headingfontfamily'); ?>" name="<?php echo $this->get_field_name('headingfontfamily'); ?>">
				
                    <option <?php if($headingfontfamily == "Arial"){?> selected <?php }?> value="Arial">Arial</option>
                    <option <?php if($headingfontfamily == "Helvetica"){?> selected <?php }?> value="Helvetica">Helvetica</option>
                    <option <?php if($headingfontfamily == "sans-serif"){?> selected <?php }?> value="sans-serif">sans-serif</option>
                    <option <?php if($headingfontfamily == "Verdana"){?> selected <?php }?> value="Verdana">Verdana</option>
                    
                </select>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('headingcolor'); ?>">Heading Color:</label><br/>
                <input class="widefat" type="text" id="<?php echo $this->get_field_id('headingcolor'); ?>" name="<?php echo $this->get_field_name('headingcolor'); ?>" value="<?php echo esc_attr($headingcolor); ?>" >
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('textfontfamily'); ?>">Text Font:</label><br/>
                <select class="widefat" id="<?php echo $this->get_field_id('textfontfamily'); ?>" name="<?php echo $this->get_field_name('textfontfamily'); ?>">
                    
					<option <?php if($textfontfamily == "Arial"){?> selected <?php }?> value="Arial">Arial</option>
                    <option <?php if($textfontfamily == "Helvetica"){?> selected <?php }?> value="Helvetica">Helvetica</option>
                    <option <?php if($textfontfamily == "sans-serif"){?> selected <?php }?> value="sans-serif">sans-serif</option>
                    <option <?php if($textfontfamily == "Verdana"){?> selected <?php }?> value="Verdana">Verdana</option>
                </select>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('textcolor'); ?>">Text Color:</label><br/>
                <input class="widefat" type="text" id="<?php echo $this->get_field_id('textcolor'); ?>" name="<?php echo $this->get_field_name('textcolor'); ?>" value="<?php echo esc_attr($textcolor); ?>" >
            </p>

        <?php

    }
     
	 //function to update new style for the plugin
    function update($new_curr, $old_curr)
	{   
        $curr = $old_curr;
        var_dump($curr);
        $curr['textfontfamily'] = strip_tags($new_curr['textfontfamily']);
        $curr['textcolor'] = strip_tags($new_curr['textcolor']);
        $curr['headingfontfamily'] = strip_tags($new_curr['headingfontfamily']);
        $curr['headingcolor'] = strip_tags($new_curr['headingcolor']);

        return $curr;    
    }
     
    function widget($args, $curr)
	{
        extract($curr);
        ?>

        <aside class="widget" id="visitor-counter">
            <h2 class="visitor-counter-heading" style="font-family:<?php echo $headingfontfamily ?>; color:<?php echo $headingcolor ?>;" >Website Visitors</h2>
        <div class="visitor-counter-content" style="color: <?php echo $textcolor ?>; font-family: <?php echo $textfontfamily ?>;" >
            <p>Total Visitors: <?php echo traffic_get_count('T') ?></p>
			<p>Today: <?php echo traffic_get_count('D') ?></p>
            <p>Yesterday: <?php echo traffic_get_count('Y') ?></p>
            <p>This Week: <?php echo traffic_get_count('W') ?></p>
            <p>This Month: <?php echo traffic_get_count('M') ?></p>
            <p>Currently Online: <?php echo traffic_get_count('C') ?></p>
        </div>
        </aside>
        
        <?php
    }
     
}

function traffic_counter_plugin_widget()
{
 
    register_widget('trafficCounterPlugin_Widget');
 
}
//action hook to add the plugin when the widgets page is loaded
add_action('widgets_init', 'traffic_counter_plugin_widget');


 function traffic_get_count($interval='D')
{
	/*using wpdp global object to access WordPress database. Reference: https://codex.wordpress.org/Class_Reference/wpdb*/
	
    global $wpdb;

    $table_name = $wpdb->prefix . "traffic_log_table";
    
	switch($interval)
	{
		case 'C':
				$condition = "`Time` > DATE_SUB(NOW(), INTERVAL 5 HOUR)";
				break;
		case 'T':
				$condition = "1";
				break;
		case 'D':
				$condition = "DATE(`Time`)=DATE(NOW())";
				break;
		case 'W':
				$condition = "WEEKOFYEAR(`Time`)=WEEKOFYEAR(NOW())";
				break;
		case 'M':
				$condition = "MONTH(`Time`)=MONTH(NOW())";
				break;
		case 'Y':
				$condition = "DATE(`Time`)=DATE(NOW() - INTERVAL 1 DAY)";
				break;
	}
	
    
    $sql = "SELECT COUNT(*) FROM " . $table_name . " WHERE ".$condition;

    $count = $wpdb -> get_var($sql);
   
    return $count;
}

//Logging a user
add_action('init','traffic_counter_log_user');

 function traffic_counter_log_user()
 {
	if(!traffic_counter_ip_exist($_SERVER['REMOTE_ADDR'])){

        global $wpdb;

        $table_name = $wpdb->prefix . "traffic_log_table";

        $sqlQuery = "INSERT INTO " . $table_name . " VALUES (NULL,'".$_SERVER['REMOTE_ADDR']."',NULL)";
        $sqlQueryResult = $wpdb -> get_results($sqlQuery);
    }
 }
 
 function traffic_counter_ip_exist($ip)
{
    global $wpdb;

    $table_name = $wpdb->prefix . "traffic_log_table";

    $sql = "SELECT COUNT(*) FROM " . $table_name . " WHERE IP='".$ip."' AND DATE(Time)='".date('Y-m-d')."'";

    $count = $wpdb -> get_var($sql);
   
    return $count;
}

global $traffic_db_version;
$traffic_db_version = "1";

//function to be executed when the plugin is installed
function traffic_counter_on_install()
{
	global $wpdb;
	global $traffic_db_version;
	
	//$traffic_log_table = $wpdb->prefix . "traffic_log";
	
	require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
	
	$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "traffic_log_table 
	(
		logID int(11) NOT NULL AUTO_INCREMENT,
		user_ip varchar(20) NOT NULL,
		Time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (logID)
	);
	";
	
	dbDelta($sql); //Modifies the database based on specified SQL statement
	
	add_option('traffic_db_version', $traffic_db_version);
	echo "mohit is here";
}
register_activation_hook( __FILE__, 'traffic_counter_on_install' );


//function to be executed when the plugin is uninstalled
function traffic_counter_on_uninstall(){

    global $wpdb;
    $traffic_log_table = $wpdb->prefix."traffic_log";
    //Delete any options that's stored also?
    delete_option('traffic_db_version');
    $wpdb->query("DROP TABLE IF EXISTS $traffic_log_table");
}


register_uninstall_hook( __FILE__, 'traffic_counter_on_uninstall' );

?>