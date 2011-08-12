<?php
/**
 * @package TimesToCome_Tripwire_Plugin
 * @version 2.0
**/
/*
Plugin Name: TimesToCome Tripwire Plugin
Version: 2.0
Plugin URI:  http://herselfswebtools.com/2008/06/wordpress-plugin-tripwire-3rd-of-three-part-security-plugin-set.html
Description: Security plugin for Wordpress Part 3 of 3 part set
Author: Linda MacPhee-Cobb
Author URI: http://timestocome.com
*/

//********************************************************************************************************************
// 1.1 fixes admin menu options for 3.0
// 2.0 improves user interface and cleans up code  Aug 2011  
//*******************************************************************************************************************	
	

    /*  Copyright 2011 Linda MacPhee-Cobb  (email : timestocome@gmail.com)
     
     This program is free software; you can redistribute it and/or modify
     it under the terms of the GNU General Public License, version 2, as 
     published by the Free Software Foundation.
     
     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.
     
     You should have received a copy of the GNU General Public License
     along with this program; if not, write to the Free Software
     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
     */

    	

	
	
//  -----  user page ------------
function ttc_tripwire_add_menu_page()
{
	add_options_page( 'Tripwire logs', 'Tripwire logs', 'manage_options', 'TripwireLogs', 'ttc_add_user_tripwire_menu');	
}

	
	
	
// display to user
function ttc_add_user_tripwire_menu()
{
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	

	$go_back = 0;
	
	// how far back in time are we going?
	print "<table<tr><td>";
	print "<form method=\"post\">";
	print "<strong>Number of days to check 1-99: </strong>";
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
	print "<br><strong> Checking last :: " . ( $go_back/$one_day) ." days </strong>";
    
    $row_count = 1;
    
	if ( $go_back > 0 ){
		print "<table><tr><td><strong>File Name</strong></td><td><strong>Date updated</strong></td></tr>";
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
                            
                            if ( $row_count == 0 ){ $row_count = 1; }else{ $row_count = 0; }
                            
                            if ( $row_count == 1 ){
                                
                                print "<tr><td><font color=\"#008800\"> $file_name </td>";
                                
                                $date_changed = filemtime( $file_name );
                                $pretty_date = date( "F j, Y g:i a", $date_changed);
                                print "<td><font color=\"#008800\"> ::: $pretty_date</td></tr>" ;
                                
                            }else{
                                
                                print "<tr><td><font color=\"#444444\"> $file_name </td>";
                                
                                $date_changed = filemtime( $file_name );
                                $pretty_date = date( "F j, Y g:i a", $date_changed);
                                print "<td><font color=\"#444444\"> ::: $pretty_date</td></tr>" ;
                            }
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