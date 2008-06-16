<?php

/*
Plugin Name: TimesToCome Tripwire Plugin
Version: 1.0
Plugin URI:  http://herselfswebtools.com/2008/06/wordpress-plugin-tripwire-3rd-of-three-part-security-plugin-set.html
Description: Security plugin for Wordpress Part 3 of 3 part set
Author: Linda MacPhee-Cobb
Author URI: http://timestocome.com
*/




function ttc_list_files(){}

//  -----  user page ------------
function ttc_tripwire_add_menu_page()
{
			if ( function_exists('add_management_page')){
				add_management_page( 'Tripwire logs', 'Tripwire logs', 8, 'Tripwire Logs', 'ttc_add_user_tripwire_menu');
			}
}

// display to user
function ttc_add_user_tripwire_menu()
{
	$go_back = 0;
	
	// how far back in time are we going?
	print "<table<tr><td>";
	print "<form method=\"post\">";
	print "Number of days to check 1-99: ";
	print "</td><td><input type=\"text\" name=\"days\" maxlength=\"2\" size=\"2\">";
	print "</td><td><input type=\"submit\" value=\"Check Files\">";
	print "</form>";
	print "</td></tr></table>";


	// info we need
	$date = time();						// current date+time
	$one_day = 86400;					// number of seconds in one day
	$days = $_POST['days'];				// user selected number of days back to check files
	$dir_count = 0;						// init loop
	$directories_to_read[$dir_count] = "../";		// plugins run from wp-admin so bounce up a directory
	$i = 0;								// loop counter
			
	$go_back = $one_day * $days;
	print "<br> Go back :: " . ( $go_back/$one_day) ." days ";

	if ( $go_back > 0 ){
		print "<table><tr><td>File Name</td><td>Date updated</td></tr>";
		$diff = $date - $go_back;
		
		while ( $i <= $dir_count ){

			$current_directory = $directories_to_read[$i];
		
			// get file info
			$read_path = opendir( $directories_to_read[$i] );
			while ( $file_name = readdir( $read_path)){
				if (( $file_name != '.' )&&( $file_name != '..' )){

					if ( is_dir( $current_directory . "/"  . $file_name ) == "dir" ){
						// need to grab files from each directory all the way down to leaves
						$d_file_name = "$current_directory" . "$file_name";
						$dir_count++;
						$directories_to_read[$dir_count] = $d_file_name . "/";
					}else{
					
						$file_name = "$current_directory" . "$file_name";								
						// if time modified newer than x days print - else skip


						if ( (filemtime( $file_name)) > $diff  ){
							print "<tr><td> $file_name </td>";
						
							$date_changed = filemtime( $file_name );
							$pretty_date = date( "F j, Y g:i a", $date_changed);
							print  "<td> ::: $pretty_date</td></tr>" ;
							
						}
					}
				}
			}
			closedir ( $read_path );
			$i++;	
		
		}
		
			print "</table>";	
			
	} // if go_back > 0 )			
					
						
}

add_action( 'admin_menu', 'ttc_tripwire_add_menu_page' );   //add admin menu for user interaction

?>