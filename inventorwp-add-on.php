<?php

/*
Plugin Name: WP All Import - Inventor WP Add-On
Plugin URI: http://www.wpallimport.com/
Description: Supporting imports into Inventor WP.
Version: 1.1.1
Author: Soflyy
*/


include "rapid-addon.php";

include_once(ABSPATH.'wp-admin/includes/plugin.php');

/**
* Initialize add-on
**/

$inventorwp_addon = new RapidAddon('Inventor WP Add-On', 'inventorwp_addon');


/**
* Show fields based on post type
**/

$custom_type = false;

// Get import ID from URL or set to 'new'
$import_id = isset($_GET['id']) ? $_GET['id'] : 'new';

// Declaring $wpdb as global to access database
global $wpdb;

// Get values from import data table
$imports_table = $wpdb->prefix . 'pmxi_imports';

// Get import session from database based on import ID or 'new'
$import_options = $wpdb->get_row( $wpdb->prepare("SELECT options FROM $imports_table WHERE id = %d", $import_id), ARRAY_A );

// If this is an existing import load the custom post type from the array
if ( ! empty($import_options) )
{
	$import_options_arr = unserialize($import_options['options']);
	$custom_type = $import_options_arr['custom_type'];
}
else

// If this is a new import get the custom post type data from the current session
{
	$import_options = $wpdb->get_row( $wpdb->prepare("SELECT option_name, option_value FROM $wpdb->options WHERE option_name = %s", '_wpallimport_session_' . $import_id . '_'), ARRAY_A );				
	$import_options_arr = empty($import_options) ? array() : unserialize($import_options['option_value']);
	$custom_type = empty($import_options_arr['custom_type']) ? '' : $import_options_arr['custom_type'];		
}

// Get import session from database based on import ID or 'new'
switch ( $custom_type ) 
{
	case 'business':

		/** 
		* Business Post Type Fields
		**/
		
		// Banner fields
		
		$banner_fields = array(
			'banner_simple' => 'Simple',
			'banner_featured_image' => 'Featured Image',
			'banner_image' => array(
					'Custom Image',
					$inventorwp_addon->add_field('listing_banner_image', 'Custom Image', 'image' ),
							),
			'banner_video' => array(
					'Video',
					$inventorwp_addon->add_field('listing_banner_video', 'Video', 'file' ),
					$inventorwp_addon->add_field(
						'listing_banner_video_loop',
						'Loop',
						'radio',
						array(
							'' => 'No',
							'on' => 'Yes'
						),
						'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
					)
			),
		);
		
		/*
		 *  If "Inventor Google Map" plugin is active, add the "Google Map",
		 *   "Google Street view" & "Google Inside View" banner types to
		 *   the list of availble banner types.
		 */
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-google-map/inventor-google-map.php')) {
				$google_maps_banner_fields = array(
					'banner_map' => array(
						'Google Map',
						$inventorwp_addon->add_field('listing_banner_map_zoom', 'Zoom', 'text', null, 'Minimum value of zoom is 0. Maximum value depends on location (12-25).'),
						$inventorwp_addon->add_field(
							'listing_banner_map_type',
							'Map Type',
							'radio',
							array(
								'ROADMAP' => 'Roadmap',
								'SATELLITE' => 'Satellite',
								'HYBRID' => 'Hybrid'
							),
							'If \'Set with XPath\' is chosen, the value must be \'ROADMAP\' for \'Roadmap\', \'SATELLITE\' for \'Satellite\' or \'HYBRID\' for \'Hybrid\'.'
						),
						$inventorwp_addon->add_field(
							'listing_banner_map_marker',
							'Marker',
							'radio',
							array(
								'' => 'No',
								'on' => 'Yes'
							),
							'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
						)
					),
					'banner_street_view' => 'Google Street View',
					'banner_inside_view' => 'Google Inside View',
				);
				
				$banner_fields = array_merge($banner_fields, $google_maps_banner_fields);
			}
		}

		$inventorwp_addon->add_title('Banner');
		$inventorwp_addon->add_field(
		        'listing_banner',
		        'Banner Type',
		        'radio', 
				$banner_fields
		);

		// Video fields
		$inventorwp_addon->add_title('Video');
		$inventorwp_addon->add_field('listing_video', 'Video URL', 'text', null, 'URL of externally hosted video for embedding');

		// Opening hours
		$inventorwp_addon->add_title('Opening Hours', 'Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable time will work.');
		$inventorwp_addon->add_text('<table id="opening-hours" class="form-table">
									    <tbody>
									    	<tr>
									    		<td class="opening-hours-day">
									    			<div class="input">Monday</div>
									    		</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_mon', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_mon', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_mon', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
											<tr>
												<td class="opening-hours-day">
													<div class="input">Tuesday</div>
												</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_tues', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_tues', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_tues', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
											<tr>
												<td class="opening-hours-day">
													<div class="input">Wednesday</div>
												</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_weds', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_weds', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_weds', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
											<tr>
												<td class="opening-hours-day">
													<div class="input">Thursday</div>
												</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_thurs', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_thurs', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_thurs', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
											<tr>
												<td class="opening-hours-day">
													<div class="input">Friday</div>
												</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_fri', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_fri', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_fri', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
											<tr>
												<td class="opening-hours-day">
													<div class="input">Saturday</div>
												</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_sat', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_sat', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_sat', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
											<tr>
												<td class="opening-hours-day">
													<div class="input">Sunday</div>
												</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_sun', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_sun', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_sun', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
										</tbody>
									</table>', true);

		// Contact fields
		$inventorwp_addon->add_title('Contact');
		$inventorwp_addon->add_field('listing_email', 'Email', 'text');
		$inventorwp_addon->add_field('listing_phone', 'Phone', 'text');
		$inventorwp_addon->add_field('listing_website', 'Website', 'text', null, 'Use a full web address including http:// or https://');
		$inventorwp_addon->add_field('listing_person', 'Person', 'text');		
		$inventorwp_addon->add_field('listing_address', 'Address', 'text');

		// Social fields
		$inventorwp_addon->add_title('Social', 'Use full URL' );
		$inventorwp_addon->add_field('listing_facebook', 'Facebook', 'text');
		$inventorwp_addon->add_field('listing_twitter', 'Twitter', 'text');
		$inventorwp_addon->add_field('listing_google', 'Google+', 'text');
		$inventorwp_addon->add_field('listing_instagram', 'Instagram', 'text');
		$inventorwp_addon->add_field('listing_linkedin', 'LinkedIn', 'text');
		$inventorwp_addon->add_options(
		        null,
		        'Other Social Networks', 
		        array(
		                $inventorwp_addon->add_field('listing_vimeo', 'Vimeo', 'text'),
						$inventorwp_addon->add_field('listing_youtube', 'YouTube', 'text'),
						$inventorwp_addon->add_field('listing_dribbble', 'Dribbble', 'text'),
						$inventorwp_addon->add_field('listing_skype', 'Skype', 'text'),
						$inventorwp_addon->add_field('listing_foursquare', 'Foursquare', 'text'),
						$inventorwp_addon->add_field('listing_behance', 'Behance', 'text')
		        )
		);

		// Flags check boxes
		$inventorwp_addon->add_title('Flags');
		$inventorwp_addon->add_field(
			'listing_featured',
			'Featured',
			'radio',
				array(
					'0' => 'No',
					'on' => 'Yes'
					),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or \'0\' for \'No\'.'
			);

		$inventorwp_addon->add_field(
			'listing_reduced',
			'Reduced',
			'radio',
				array(
					'' => 'No',
					'on' => 'Yes'
						 ),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
			);

		// Location fields
		$inventorwp_addon->add_title('Location');
		$inventorwp_addon->add_field('listing_map_location_address', 'Map Location', 'text', null, 'Enter the full address of the location');
		$inventorwp_addon->add_field('listing_map_location_latitude', 'Map Location Latitude', 'text');
		$inventorwp_addon->add_field('listing_map_location_longitude', 'Map Location Longitude', 'text');

		$inventorwp_addon->add_options(
		    null,
		    'Advanced Location Options', 
		    array(	
		    		$inventorwp_addon->add_field('listing_map_location_polygon', 'Map Location Polygon', 'text', null, 'Enter encoded path. It can be constructed using Google\'s Interactive Polyline Encoder Utility'),
		            $inventorwp_addon->add_field(
			    		'listing_street_view',
			    		'Street View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_street_view_location_address', 'Street View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_street_view_location_latitude', 'Street View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_street_view_location_longitude', 'Street View Longitude', 'text' )
		       						 ),
			   					 )
						),
		            $inventorwp_addon->add_field(
			    		'listing_inside_view',
			    		'Inside View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_inside_view_location_address', 'Inside View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_inside_view_location_latitude', 'Inside View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_inside_view_location_longitude', 'Inside View Longitude', 'text' )
		       						 ),
			   					 )
						),
		    )
		);

		// Location Categories
		$inventorwp_addon->add_title('Location Categories', 'Use only one value for each field. The Parent Location field is required. Your location will not import correctly if it\'s left blank.');
		$inventorwp_addon->add_text('<div class="location-tax-wrap"><div class="location-tax lt-parent">', true);
		$inventorwp_addon->add_field('tmp_locations_parent', 'Location Parent', 'text', null, 'The top level location. This is often the country or state/province.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-child">', true);
		$inventorwp_addon->add_field('tmp_locations_child', 'Location Child', 'text', null, 'The second level location. This is often the state/province or city.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-sub-child">', true);
		$inventorwp_addon->add_field('tmp_locations_sub_child', 'Location Sub Child', 'text', null, 'The third level location. This is often the city or district/neighborhood.');
		$inventorwp_addon->add_text('</div></div><div style="clear:both">', true);

		// FAQs
		$inventorwp_addon->add_title('FAQ', 'The Question field is required. Your FAQ item will not import correctly if it\'s left blank.'); 
		$inventorwp_addon->add_text('<table class="seperator">
										<tr>
											<td>
												Seperator Character
											</td>
											<td>', true);
		$inventorwp_addon->add_field('faq_seperator_character', '', 'text', array("," => null));
		$inventorwp_addon->add_text('		</td>
											<td>', true);
		$inventorwp_addon->add_title(' ', 'Use this to choose the seperator used in your CSV file. For example, if your questions or answers are formatted like this: \'question 1, question 2, question 3\' then enter the character \',\' into the field.');
		$inventorwp_addon->add_text('		</td>
										</tr>
									</table>', true);
		$inventorwp_addon->add_field('listing_question', 'Question', 'text');
		$inventorwp_addon->add_field('listing_answer', 'Answer', 'text');

		// Branding
		$inventorwp_addon->add_title('Branding');
		$inventorwp_addon->add_field('listing_slogan', 'Slogan', 'text');
		$inventorwp_addon->add_field('listing_brand_color', 'Brand Color', 'text', null, 'Use Hex color codes');
		$inventorwp_addon->add_field('listing_logo', 'Logo', 'image' );

		// Listing Categories
		$inventorwp_addon->add_title('Listing Categories');
		$inventorwp_addon->add_text('<table class="seperator">
										<tr>
											<td>
												Seperator Character
											</td>
											<td>', true);
		$inventorwp_addon->add_field('listing_categories_seperator_character', '', 'text', array("," => null));
		$inventorwp_addon->add_text('		</td>
											<td>', true);
		$inventorwp_addon->add_title(' ', 'Use this to choose the seperator used in your CSV file. For example, if your listing categories are formatted like this: \'category 1, category 2, category 3\' then enter the character \',\' into the field.');
		$inventorwp_addon->add_text('		</td>
										</tr>
									</table>', true);
		$inventorwp_addon->add_field('listing_listing_category', 'Listing Categories', 'text');
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-statistics/inventor-statistics.php')) {
		
				$inventorwp_addon->add_title('Post Views');
				$inventorwp_addon->add_field('inventor_statistics_post_total_views', 'Post Views', 'text');
		
			}
		}

		break;

	case 'car':

		/** 
		* Car Post Type Fields
		**/

		// Details

		$inventorwp_addon->add_title('Details');
		// Taxonomy ONLY - NO post_meta associated
		$inventorwp_addon->add_field('tmp_engine_type', 'Engine Type', 'text');
		$inventorwp_addon->add_field('tmp_car_body_style', 'Body Style', 'text', null, 'Seperate multiple values with a comma. For example: value 1, value 2, value 3');
		// Taxonomy ONLY - NO post_meta associated
		$inventorwp_addon->add_field('tmp_transmission', 'Transmission', 'text');
		$inventorwp_addon->add_field('listing_car_model', 'Model', 'text');
		$inventorwp_addon->add_field('listing_car_year_manufactured', 'Year Manufactured', 'text');
		$inventorwp_addon->add_field('listing_car_mileage', 'Mileage', 'text');
		$inventorwp_addon->add_field(
		    'listing_car_condition',
		    'Condition',
		    'radio',
		    array(
		        'NEW' => 'New',
		        'USED' => 'Used'
		    ),
		    'If \'Set with XPath\' is chosen, the value must be either \'NEW\' or \'USED\'. Case sensitive.'
		);
		$inventorwp_addon->add_field(
		    'listing_car_leasing',
		    'Leasing Available',
		    'radio',
		    array(
		        'on' => 'Yes',
		        '' => 'No'
		    ),
		    'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
		);


		// Color

		// These are actually taxonomy fields. It's two seperate fields selected from one taxonomy. These are stored with custom fields but their values match the slug of the categories

		$inventorwp_addon->add_title('Color');
		$inventorwp_addon->add_field('tmp_car_color_interior', 'Interior Color', 'text', null, 'Seperate multiple values with a comma. For example: value 1, value 2, value 3');
		$inventorwp_addon->add_field('tmp_car_color_exterior', 'Exterior Color', 'text', null, 'Seperate multiple values with a comma. For example: value 1, value 2, value 3');

		// Banner fields
		
		$banner_fields = array(
			'banner_simple' => 'Simple',
			'banner_featured_image' => 'Featured Image',
			'banner_image' => array(
					'Custom Image',
					$inventorwp_addon->add_field('listing_banner_image', 'Custom Image', 'image' ),
							),
			'banner_video' => array(
					'Video',
					$inventorwp_addon->add_field('listing_banner_video', 'Video', 'file' ),
					$inventorwp_addon->add_field(
						'listing_banner_video_loop',
						'Loop',
						'radio',
						array(
							'' => 'No',
							'on' => 'Yes'
						),
						'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
					)
			),
		);
		
		/*
		 *  If "Inventor Google Map" plugin is active, add the "Google Map",
		 *   "Google Street view" & "Google Inside View" banner types to
		 *   the list of availble banner types.
		 */
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-google-map/inventor-google-map.php')) {
				$google_maps_banner_fields = array(
					'banner_map' => array(
						'Google Map',
						$inventorwp_addon->add_field('listing_banner_map_zoom', 'Zoom', 'text', null, 'Minimum value of zoom is 0. Maximum value depends on location (12-25).'),
						$inventorwp_addon->add_field(
							'listing_banner_map_type',
							'Map Type',
							'radio',
							array(
								'ROADMAP' => 'Roadmap',
								'SATELLITE' => 'Satellite',
								'HYBRID' => 'Hybrid'
							),
							'If \'Set with XPath\' is chosen, the value must be \'ROADMAP\' for \'Roadmap\', \'SATELLITE\' for \'Satellite\' or \'HYBRID\' for \'Hybrid\'.'
						),
						$inventorwp_addon->add_field(
							'listing_banner_map_marker',
							'Marker',
							'radio',
							array(
								'' => 'No',
								'on' => 'Yes'
							),
							'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
						)
					),
					'banner_street_view' => 'Google Street View',
					'banner_inside_view' => 'Google Inside View',
				);
				
				$banner_fields = array_merge($banner_fields, $google_maps_banner_fields);
			}
		}

		$inventorwp_addon->add_title('Banner');
		$inventorwp_addon->add_field(
		        'listing_banner',
		        'Banner Type',
		        'radio', 
				$banner_fields
		);

		// Video fields
		$inventorwp_addon->add_title('Video');
		$inventorwp_addon->add_field('listing_video', 'Video URL', 'text', null, 'URL of externally hosted video for embedding');

		// Price fields
		$inventorwp_addon->add_title('Price');
		$inventorwp_addon->add_options(
				$inventorwp_addon->add_field('listing_price', 'Price', 'text', null, 'In USD'),
				'Advanced Options',
				array(
						$inventorwp_addon->add_field('listing_price_prefix', 'Price Prefix', 'text', null, 'Any text shown before price (for example "from")'),
						$inventorwp_addon->add_field('listing_price_suffix', 'Price Suffix', 'text', null, 'Any text shown after price (for example "per night")'),
						$inventorwp_addon->add_field('listing_price_custom', 'Custom Text', 'text', null, 'Any text instead of numeric price (for example "by agreement"). Prefix and Suffix will be ignored')
					)
		);

		// Contact fields
		$inventorwp_addon->add_title('Contact');
		$inventorwp_addon->add_field('listing_email', 'Email', 'text');
		$inventorwp_addon->add_field('listing_phone', 'Phone', 'text');
		$inventorwp_addon->add_field('listing_website', 'Website', 'text', null, 'Use a full web address including http:// or https://');
		$inventorwp_addon->add_field('listing_person', 'Person', 'text');		
		$inventorwp_addon->add_field('listing_address', 'Address', 'text');

		// Social fields
		$inventorwp_addon->add_title('Social', 'Use full URL' );
		$inventorwp_addon->add_field('listing_facebook', 'Facebook', 'text');
		$inventorwp_addon->add_field('listing_twitter', 'Twitter', 'text');
		$inventorwp_addon->add_field('listing_google', 'Google+', 'text');
		$inventorwp_addon->add_field('listing_instagram', 'Instagram', 'text');
		$inventorwp_addon->add_field('listing_linkedin', 'LinkedIn', 'text');
		$inventorwp_addon->add_options(
		        null,
		        'Other Social Networks', 
		        array(
		                $inventorwp_addon->add_field('listing_vimeo', 'Vimeo', 'text'),
						$inventorwp_addon->add_field('listing_youtube', 'YouTube', 'text'),
						$inventorwp_addon->add_field('listing_dribbble', 'Dribbble', 'text'),
						$inventorwp_addon->add_field('listing_skype', 'Skype', 'text'),
						$inventorwp_addon->add_field('listing_foursquare', 'Foursquare', 'text'),
						$inventorwp_addon->add_field('listing_behance', 'Behance', 'text')
		        )
		);

		// Flags check boxes
		$inventorwp_addon->add_title('Flags');
		$inventorwp_addon->add_field(
			'listing_featured',
			'Featured',
			'radio',
				array(
					'0' => 'No',
					'on' => 'Yes'
					),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or \'0\' for \'No\'.'
			);

		$inventorwp_addon->add_field(
			'listing_reduced',
			'Reduced',
			'radio',
				array(
					'' => 'No',
					'on' => 'Yes'
						 ),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
			);


		// Location fields
		$inventorwp_addon->add_title('Location');
		$inventorwp_addon->add_field('listing_map_location_address', 'Map Location', 'text', null, 'Enter the full address of the location');
		$inventorwp_addon->add_field('listing_map_location_latitude', 'Map Location Latitude', 'text');
		$inventorwp_addon->add_field('listing_map_location_longitude', 'Map Location Longitude', 'text');

		$inventorwp_addon->add_options(
		    null,
		    'Advanced Location Options', 
		    array(	
		    		$inventorwp_addon->add_field('listing_map_location_polygon', 'Map Location Polygon', 'text', null, 'Enter encoded path. It can be constructed using Google\'s Interactive Polyline Encoder Utility'),
		            $inventorwp_addon->add_field(
			    		'listing_street_view',
			    		'Street View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_street_view_location_address', 'Street View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_street_view_location_latitude', 'Street View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_street_view_location_longitude', 'Street View Longitude', 'text' )
		       						 ),
			   					 )
						),
		            $inventorwp_addon->add_field(
			    		'listing_inside_view',
			    		'Inside View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_inside_view_location_address', 'Inside View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_inside_view_location_latitude', 'Inside View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_inside_view_location_longitude', 'Inside View Longitude', 'text' )
		       						 ),
			   					 )
						),
		    )
		);

		// Location Categories
		$inventorwp_addon->add_title('Location Categories', 'Use only one value for each field. The Parent Location field is required. Your location will not import correctly if it\'s left blank.');
		$inventorwp_addon->add_text('<div class="location-tax-wrap"><div class="location-tax lt-parent">', true);
		$inventorwp_addon->add_field('tmp_locations_parent', 'Location Parent', 'text', null, 'The top level location. This is often the country or state/province.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-child">', true);
		$inventorwp_addon->add_field('tmp_locations_child', 'Location Child', 'text', null, 'The second level location. This is often the state/province or city.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-sub-child">', true);
		$inventorwp_addon->add_field('tmp_locations_sub_child', 'Location Sub Child', 'text', null, 'The third level location. This is often the city or district/neighborhood.');
		$inventorwp_addon->add_text('</div></div><div style="clear:both">', true);

		// Listing Categories
		$inventorwp_addon->add_title('Listing Categories');
		$inventorwp_addon->add_text('<table class="seperator">
										<tr>
											<td>
												Seperator Character
											</td>
											<td>', true);
		$inventorwp_addon->add_field('listing_categories_seperator_character', '', 'text', array("," => null));
		$inventorwp_addon->add_text('		</td>
											<td>', true);
		$inventorwp_addon->add_title(' ', 'Use this to choose the seperator used in your CSV file. For example, if your listing categories are formatted like this: \'category 1, category 2, category 3\' then enter the character \',\' into the field.');
		$inventorwp_addon->add_text('		</td>
										</tr>
									</table>', true);
		$inventorwp_addon->add_field('listing_listing_category', 'Listing Categories', 'text');
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-statistics/inventor-statistics.php')) {
		
				$inventorwp_addon->add_title('Post Views');
				$inventorwp_addon->add_field('inventor_statistics_post_total_views', 'Post Views', 'text');
		
			}
		}

		break;

	case 'dating':

		/** 
		* Dating Post Type Fields
		**/

		// Details
		$inventorwp_addon->add_title('Details');
		$inventorwp_addon->add_field('tmp_dating_group', 'Dating Groups', 'text', null, 'Seperate multiple values with a comma. For example: value 1, value 2, value 3');
		$inventorwp_addon->add_field(
		    'listing_dating_gender',
		    'Gender',
		    'radio',
		    array(
		        'MALE' => 'Male',
		        'FEMALE' => 'Female',
		    ),
		    'If \'Set with XPath\' is chosen, the value must be either \'MALE\' or \'FEMALE\'. Case sensitive.'
		);
		$inventorwp_addon->add_field('listing_dating_age', 'Age', 'text');
		$inventorwp_addon->add_field('listing_weight', 'Weight', 'text');
		// Taxonomy ONLY - NO post_meta associated
		$inventorwp_addon->add_field('tmp_status', 'Status', 'text');
		// Taxonomy ONLY - NO post_meta associated
		$inventorwp_addon->add_field('tmp_eye_color', 'Eye Color', 'text');
		$inventorwp_addon->add_field('tmp_dating_interest', 'Interests', 'text', null, 'Seperate multiple values with a comma. For example: value 1, value 2, value 3');

		// Banner fields
		
		$banner_fields = array(
			'banner_simple' => 'Simple',
			'banner_featured_image' => 'Featured Image',
			'banner_image' => array(
					'Custom Image',
					$inventorwp_addon->add_field('listing_banner_image', 'Custom Image', 'image' ),
							),
			'banner_video' => array(
					'Video',
					$inventorwp_addon->add_field('listing_banner_video', 'Video', 'file' ),
					$inventorwp_addon->add_field(
						'listing_banner_video_loop',
						'Loop',
						'radio',
						array(
							'' => 'No',
							'on' => 'Yes'
						),
						'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
					)
			),
		);
		
		/*
		 *  If "Inventor Google Map" plugin is active, add the "Google Map",
		 *   "Google Street view" & "Google Inside View" banner types to
		 *   the list of availble banner types.
		 */
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-google-map/inventor-google-map.php')) {
				$google_maps_banner_fields = array(
					'banner_map' => array(
						'Google Map',
						$inventorwp_addon->add_field('listing_banner_map_zoom', 'Zoom', 'text', null, 'Minimum value of zoom is 0. Maximum value depends on location (12-25).'),
						$inventorwp_addon->add_field(
							'listing_banner_map_type',
							'Map Type',
							'radio',
							array(
								'ROADMAP' => 'Roadmap',
								'SATELLITE' => 'Satellite',
								'HYBRID' => 'Hybrid'
							),
							'If \'Set with XPath\' is chosen, the value must be \'ROADMAP\' for \'Roadmap\', \'SATELLITE\' for \'Satellite\' or \'HYBRID\' for \'Hybrid\'.'
						),
						$inventorwp_addon->add_field(
							'listing_banner_map_marker',
							'Marker',
							'radio',
							array(
								'' => 'No',
								'on' => 'Yes'
							),
							'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
						)
					),
					'banner_street_view' => 'Google Street View',
					'banner_inside_view' => 'Google Inside View',
				);
				
				$banner_fields = array_merge($banner_fields, $google_maps_banner_fields);
			}
		}

		$inventorwp_addon->add_title('Banner');
		$inventorwp_addon->add_field(
		        'listing_banner',
		        'Banner Type',
		        'radio', 
				$banner_fields
		);

		// Video fields
		$inventorwp_addon->add_title('Video');
		$inventorwp_addon->add_field('listing_video', 'Video URL', 'text', null, 'URL of externally hosted video for embedding');


		// Contact fields
		$inventorwp_addon->add_title('Contact');
		$inventorwp_addon->add_field('listing_email', 'Email', 'text');
		$inventorwp_addon->add_field('listing_phone', 'Phone', 'text');
		$inventorwp_addon->add_field('listing_website', 'Website', 'text', null, 'Use a full web address including http:// or https://');
		$inventorwp_addon->add_field('listing_person', 'Person', 'text');		
		$inventorwp_addon->add_field('listing_address', 'Address', 'text');

		// Social fields
		$inventorwp_addon->add_title('Social', 'Use full URL' );
		$inventorwp_addon->add_field('listing_facebook', 'Facebook', 'text');
		$inventorwp_addon->add_field('listing_twitter', 'Twitter', 'text');
		$inventorwp_addon->add_field('listing_google', 'Google+', 'text');
		$inventorwp_addon->add_field('listing_instagram', 'Instagram', 'text');
		$inventorwp_addon->add_field('listing_linkedin', 'LinkedIn', 'text');
		$inventorwp_addon->add_options(
		        null,
		        'Other Social Networks', 
		        array(
		                $inventorwp_addon->add_field('listing_vimeo', 'Vimeo', 'text'),
						$inventorwp_addon->add_field('listing_youtube', 'YouTube', 'text'),
						$inventorwp_addon->add_field('listing_dribbble', 'Dribbble', 'text'),
						$inventorwp_addon->add_field('listing_skype', 'Skype', 'text'),
						$inventorwp_addon->add_field('listing_foursquare', 'Foursquare', 'text'),
						$inventorwp_addon->add_field('listing_behance', 'Behance', 'text')
		        )
		);

		// Flags check boxes
		$inventorwp_addon->add_title('Flags');
		$inventorwp_addon->add_field(
			'listing_featured',
			'Featured',
			'radio',
				array(
					'0' => 'No',
					'on' => 'Yes'
					),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or \'0\' for \'No\'.'
			);

		$inventorwp_addon->add_field(
			'listing_reduced',
			'Reduced',
			'radio',
				array(
					'' => 'No',
					'on' => 'Yes'
						 ),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
			);


		// Location fields
		$inventorwp_addon->add_title('Location');
		$inventorwp_addon->add_field('listing_map_location_address', 'Map Location', 'text', null, 'Enter the full address of the location');
		$inventorwp_addon->add_field('listing_map_location_latitude', 'Map Location Latitude', 'text');
		$inventorwp_addon->add_field('listing_map_location_longitude', 'Map Location Longitude', 'text');

		$inventorwp_addon->add_options(
		    null,
		    'Advanced Location Options', 
		    array(	
		    		$inventorwp_addon->add_field('listing_map_location_polygon', 'Map Location Polygon', 'text', null, 'Enter encoded path. It can be constructed using Google\'s Interactive Polyline Encoder Utility'),
		            $inventorwp_addon->add_field(
			    		'listing_street_view',
			    		'Street View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_street_view_location_address', 'Street View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_street_view_location_latitude', 'Street View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_street_view_location_longitude', 'Street View Longitude', 'text' )
		       						 ),
			   					 )
						),
		            $inventorwp_addon->add_field(
			    		'listing_inside_view',
			    		'Inside View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_inside_view_location_address', 'Inside View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_inside_view_location_latitude', 'Inside View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_inside_view_location_longitude', 'Inside View Longitude', 'text' )
		       						 ),
			   					 )
						),
		    )
		);

		// Location Categories
		$inventorwp_addon->add_title('Location Categories', 'Use only one value for each field. The Parent Location field is required. Your location will not import correctly if it\'s left blank.');
		$inventorwp_addon->add_text('<div class="location-tax-wrap"><div class="location-tax lt-parent">', true);
		$inventorwp_addon->add_field('tmp_locations_parent', 'Location Parent', 'text', null, 'The top level location. This is often the country or state/province.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-child">', true);
		$inventorwp_addon->add_field('tmp_locations_child', 'Location Child', 'text', null, 'The second level location. This is often the state/province or city.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-sub-child">', true);
		$inventorwp_addon->add_field('tmp_locations_sub_child', 'Location Sub Child', 'text', null, 'The third level location. This is often the city or district/neighborhood.');
		$inventorwp_addon->add_text('</div></div><div style="clear:both">', true);

		// Listing Categories
		$inventorwp_addon->add_title('Listing Categories');
		$inventorwp_addon->add_text('<table class="seperator">
										<tr>
											<td>
												Seperator Character
											</td>
											<td>', true);
		$inventorwp_addon->add_field('listing_categories_seperator_character', '', 'text', array("," => null));
		$inventorwp_addon->add_text('		</td>
											<td>', true);
		$inventorwp_addon->add_title(' ', 'Use this to choose the seperator used in your CSV file. For example, if your listing categories are formatted like this: \'category 1, category 2, category 3\' then enter the character \',\' into the field.');
		$inventorwp_addon->add_text('		</td>
										</tr>
									</table>', true);
		$inventorwp_addon->add_field('listing_listing_category', 'Listing Categories', 'text');
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-statistics/inventor-statistics.php')) {
		
				$inventorwp_addon->add_title('Post Views');
				$inventorwp_addon->add_field('inventor_statistics_post_total_views', 'Post Views', 'text');
		
			}
		}

		break;

	case 'education':

		/** 
		* Education Post Type Fields
		**/

		// Details
		$inventorwp_addon->add_title('Details');
		// Taxonomy ONLY - NO post_meta associated
		$inventorwp_addon->add_field('tmp_education_level', 'Education Level', 'text');
		// Taxonomy ONLY - NO post_meta associated
		$inventorwp_addon->add_field('tmp_education_subject', 'Education Subject', 'text');
		$inventorwp_addon->add_field('listing_education_lector', 'Lector', 'text');

		// Date & Time Interval
		$inventorwp_addon->add_title('Date and Time Interval', 'Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.');
		$inventorwp_addon->add_field('listing_date', 'Date', 'text'); // Formatted as Unix time stamp
		$inventorwp_addon->add_field('listing_time_from', 'Time from', 'text');
		$inventorwp_addon->add_field('listing_time_to', 'Time to', 'text');

		// Banner fields
		
		$banner_fields = array(
			'banner_simple' => 'Simple',
			'banner_featured_image' => 'Featured Image',
			'banner_image' => array(
					'Custom Image',
					$inventorwp_addon->add_field('listing_banner_image', 'Custom Image', 'image' ),
							),
			'banner_video' => array(
					'Video',
					$inventorwp_addon->add_field('listing_banner_video', 'Video', 'file' ),
					$inventorwp_addon->add_field(
						'listing_banner_video_loop',
						'Loop',
						'radio',
						array(
							'' => 'No',
							'on' => 'Yes'
						),
						'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
					)
			),
		);
		
		/*
		 *  If "Inventor Google Map" plugin is active, add the "Google Map",
		 *   "Google Street view" & "Google Inside View" banner types to
		 *   the list of availble banner types.
		 */
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-google-map/inventor-google-map.php')) {
				$google_maps_banner_fields = array(
					'banner_map' => array(
						'Google Map',
						$inventorwp_addon->add_field('listing_banner_map_zoom', 'Zoom', 'text', null, 'Minimum value of zoom is 0. Maximum value depends on location (12-25).'),
						$inventorwp_addon->add_field(
							'listing_banner_map_type',
							'Map Type',
							'radio',
							array(
								'ROADMAP' => 'Roadmap',
								'SATELLITE' => 'Satellite',
								'HYBRID' => 'Hybrid'
							),
							'If \'Set with XPath\' is chosen, the value must be \'ROADMAP\' for \'Roadmap\', \'SATELLITE\' for \'Satellite\' or \'HYBRID\' for \'Hybrid\'.'
						),
						$inventorwp_addon->add_field(
							'listing_banner_map_marker',
							'Marker',
							'radio',
							array(
								'' => 'No',
								'on' => 'Yes'
							),
							'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
						)
					),
					'banner_street_view' => 'Google Street View',
					'banner_inside_view' => 'Google Inside View',
				);
				
				$banner_fields = array_merge($banner_fields, $google_maps_banner_fields);
			}
		}

		$inventorwp_addon->add_title('Banner');
		$inventorwp_addon->add_field(
		        'listing_banner',
		        'Banner Type',
		        'radio', 
				$banner_fields
		);


		// Video fields
		$inventorwp_addon->add_title('Video');
		$inventorwp_addon->add_field('listing_video', 'Video URL', 'text', null, 'URL of externally hosted video for embedding');

		// Contact fields
		$inventorwp_addon->add_title('Contact');
		$inventorwp_addon->add_field('listing_email', 'Email', 'text');
		$inventorwp_addon->add_field('listing_phone', 'Phone', 'text');
		$inventorwp_addon->add_field('listing_website', 'Website', 'text', null, 'Use a full web address including http:// or https://');
		$inventorwp_addon->add_field('listing_person', 'Person', 'text');		
		$inventorwp_addon->add_field('listing_address', 'Address', 'text');

		// Social fields
		$inventorwp_addon->add_title('Social', 'Use full URL' );
		$inventorwp_addon->add_field('listing_facebook', 'Facebook', 'text');
		$inventorwp_addon->add_field('listing_twitter', 'Twitter', 'text');
		$inventorwp_addon->add_field('listing_google', 'Google+', 'text');
		$inventorwp_addon->add_field('listing_instagram', 'Instagram', 'text');
		$inventorwp_addon->add_field('listing_linkedin', 'LinkedIn', 'text');
		$inventorwp_addon->add_options(
		        null,
		        'Other Social Networks', 
		        array(
		                $inventorwp_addon->add_field('listing_vimeo', 'Vimeo', 'text'),
						$inventorwp_addon->add_field('listing_youtube', 'YouTube', 'text'),
						$inventorwp_addon->add_field('listing_dribbble', 'Dribbble', 'text'),
						$inventorwp_addon->add_field('listing_skype', 'Skype', 'text'),
						$inventorwp_addon->add_field('listing_foursquare', 'Foursquare', 'text'),
						$inventorwp_addon->add_field('listing_behance', 'Behance', 'text')
		        )
		);

		// Price fields
		$inventorwp_addon->add_title('Price');
		$inventorwp_addon->add_options(
				$inventorwp_addon->add_field('listing_price', 'Price', 'text', null, 'In USD'),
				'Advanced Options',
				array(
						$inventorwp_addon->add_field('listing_price_prefix', 'Price Prefix', 'text', null, 'Any text shown before price (for example "from")'),
						$inventorwp_addon->add_field('listing_price_suffix', 'Price Suffix', 'text', null, 'Any text shown after price (for example "per night")'),
						$inventorwp_addon->add_field('listing_price_custom', 'Custom Text', 'text', null, 'Any text instead of numeric price (for example "by agreement"). Prefix and Suffix will be ignored')
					)
		);

		// Flags check boxes
		$inventorwp_addon->add_title('Flags');
		$inventorwp_addon->add_field(
			'listing_featured',
			'Featured',
			'radio',
				array(
					'0' => 'No',
					'on' => 'Yes'
					),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or \'0\' for \'No\'.'
			);

		$inventorwp_addon->add_field(
			'listing_reduced',
			'Reduced',
			'radio',
				array(
					'' => 'No',
					'on' => 'Yes'
						 ),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
			);

		// Location fields
		$inventorwp_addon->add_title('Location');
		$inventorwp_addon->add_field('listing_map_location_address', 'Map Location', 'text', null, 'Enter the full address of the location');
		$inventorwp_addon->add_field('listing_map_location_latitude', 'Map Location Latitude', 'text');
		$inventorwp_addon->add_field('listing_map_location_longitude', 'Map Location Longitude', 'text');

		$inventorwp_addon->add_options(
		    null,
		    'Advanced Location Options', 
		    array(	
		    		$inventorwp_addon->add_field('listing_map_location_polygon', 'Map Location Polygon', 'text', null, 'Enter encoded path. It can be constructed using Google\'s Interactive Polyline Encoder Utility'),
		            $inventorwp_addon->add_field(
			    		'listing_street_view',
			    		'Street View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_street_view_location_address', 'Street View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_street_view_location_latitude', 'Street View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_street_view_location_longitude', 'Street View Longitude', 'text' )
		       						 ),
			   					 )
						),
		            $inventorwp_addon->add_field(
			    		'listing_inside_view',
			    		'Inside View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_inside_view_location_address', 'Inside View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_inside_view_location_latitude', 'Inside View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_inside_view_location_longitude', 'Inside View Longitude', 'text' )
		       						 ),
			   					 )
						),
		    )
		);


		// Location Categories
		$inventorwp_addon->add_title('Location Categories', 'Use only one value for each field. The Parent Location field is required. Your location will not import correctly if it\'s left blank.');
		$inventorwp_addon->add_text('<div class="location-tax-wrap"><div class="location-tax lt-parent">', true);
		$inventorwp_addon->add_field('tmp_locations_parent', 'Location Parent', 'text', null, 'The top level location. This is often the country or state/province.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-child">', true);
		$inventorwp_addon->add_field('tmp_locations_child', 'Location Child', 'text', null, 'The second level location. This is often the state/province or city.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-sub-child">', true);
		$inventorwp_addon->add_field('tmp_locations_sub_child', 'Location Sub Child', 'text', null, 'The third level location. This is often the city or district/neighborhood.');
		$inventorwp_addon->add_text('</div></div><div style="clear:both">', true);

		// Listing Categories
		$inventorwp_addon->add_title('Listing Categories');
		$inventorwp_addon->add_text('<table class="seperator">
										<tr>
											<td>
												Seperator Character
											</td>
											<td>', true);
		$inventorwp_addon->add_field('listing_categories_seperator_character', '', 'text', array("," => null));
		$inventorwp_addon->add_text('		</td>
											<td>', true);
		$inventorwp_addon->add_title(' ', 'Use this to choose the seperator used in your CSV file. For example, if your listing categories are formatted like this: \'category 1, category 2, category 3\' then enter the character \',\' into the field.');
		$inventorwp_addon->add_text('		</td>
										</tr>
									</table>', true);
		$inventorwp_addon->add_field('listing_listing_category', 'Listing Categories', 'text');
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-statistics/inventor-statistics.php')) {
		
				$inventorwp_addon->add_title('Post Views');
				$inventorwp_addon->add_field('inventor_statistics_post_total_views', 'Post Views', 'text');
		
			}
		}

		break;

	case 'event':

		/** 
		* Event Post Type Fields
		**/

		// Details
		$inventorwp_addon->add_title('Details');
		// Taxonomy ONLY - NO post_meta associated
		$inventorwp_addon->add_field('tmp_event_type', 'Event Type', 'text');

		// Date & Time Interval
		$inventorwp_addon->add_title('Date and Time Interval', 'Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.');
		$inventorwp_addon->add_field('listing_date', 'Date', 'text');
		$inventorwp_addon->add_field('listing_time_from', 'Time from', 'text');
		$inventorwp_addon->add_field('listing_time_to', 'Time to', 'text');

		// Banner fields
		
		$banner_fields = array(
			'banner_simple' => 'Simple',
			'banner_featured_image' => 'Featured Image',
			'banner_image' => array(
					'Custom Image',
					$inventorwp_addon->add_field('listing_banner_image', 'Custom Image', 'image' ),
							),
			'banner_video' => array(
					'Video',
					$inventorwp_addon->add_field('listing_banner_video', 'Video', 'file' ),
					$inventorwp_addon->add_field(
						'listing_banner_video_loop',
						'Loop',
						'radio',
						array(
							'' => 'No',
							'on' => 'Yes'
						),
						'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
					)
			),
		);
		
		/*
		 *  If "Inventor Google Map" plugin is active, add the "Google Map",
		 *   "Google Street view" & "Google Inside View" banner types to
		 *   the list of availble banner types.
		 */
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-google-map/inventor-google-map.php')) {
				$google_maps_banner_fields = array(
					'banner_map' => array(
						'Google Map',
						$inventorwp_addon->add_field('listing_banner_map_zoom', 'Zoom', 'text', null, 'Minimum value of zoom is 0. Maximum value depends on location (12-25).'),
						$inventorwp_addon->add_field(
							'listing_banner_map_type',
							'Map Type',
							'radio',
							array(
								'ROADMAP' => 'Roadmap',
								'SATELLITE' => 'Satellite',
								'HYBRID' => 'Hybrid'
							),
							'If \'Set with XPath\' is chosen, the value must be \'ROADMAP\' for \'Roadmap\', \'SATELLITE\' for \'Satellite\' or \'HYBRID\' for \'Hybrid\'.'
						),
						$inventorwp_addon->add_field(
							'listing_banner_map_marker',
							'Marker',
							'radio',
							array(
								'' => 'No',
								'on' => 'Yes'
							),
							'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
						)
					),
					'banner_street_view' => 'Google Street View',
					'banner_inside_view' => 'Google Inside View',
				);
				
				$banner_fields = array_merge($banner_fields, $google_maps_banner_fields);
			}
		}

		$inventorwp_addon->add_title('Banner');
		$inventorwp_addon->add_field(
		        'listing_banner',
		        'Banner Type',
		        'radio', 
				$banner_fields
		);

		// Contact fields
		$inventorwp_addon->add_title('Contact');
		$inventorwp_addon->add_field('listing_email', 'Email', 'text');
		$inventorwp_addon->add_field('listing_phone', 'Phone', 'text');
		$inventorwp_addon->add_field('listing_website', 'Website', 'text', null, 'Use a full web address including http:// or https://');
		$inventorwp_addon->add_field('listing_person', 'Person', 'text');		
		$inventorwp_addon->add_field('listing_address', 'Address', 'text');

		// Social fields
		$inventorwp_addon->add_title('Social', 'Use full URL' );
		$inventorwp_addon->add_field('listing_facebook', 'Facebook', 'text');
		$inventorwp_addon->add_field('listing_twitter', 'Twitter', 'text');
		$inventorwp_addon->add_field('listing_google', 'Google+', 'text');
		$inventorwp_addon->add_field('listing_instagram', 'Instagram', 'text');
		$inventorwp_addon->add_field('listing_linkedin', 'LinkedIn', 'text');
		$inventorwp_addon->add_options(
		        null,
		        'Other Social Networks', 
		        array(
		                $inventorwp_addon->add_field('listing_vimeo', 'Vimeo', 'text'),
						$inventorwp_addon->add_field('listing_youtube', 'YouTube', 'text'),
						$inventorwp_addon->add_field('listing_dribbble', 'Dribbble', 'text'),
						$inventorwp_addon->add_field('listing_skype', 'Skype', 'text'),
						$inventorwp_addon->add_field('listing_foursquare', 'Foursquare', 'text'),
						$inventorwp_addon->add_field('listing_behance', 'Behance', 'text')
		        )
		);

		// Video fields
		$inventorwp_addon->add_title('Video');
		$inventorwp_addon->add_field('listing_video', 'Video URL', 'text', null, 'URL of externally hosted video for embedding');

		// Price fields
		$inventorwp_addon->add_title('Price');
		$inventorwp_addon->add_options(
				$inventorwp_addon->add_field('listing_price', 'Price', 'text', null, 'In USD'),
				'Advanced Options',
				array(
						$inventorwp_addon->add_field('listing_price_prefix', 'Price Prefix', 'text', null, 'Any text shown before price (for example "from")'),
						$inventorwp_addon->add_field('listing_price_suffix', 'Price Suffix', 'text', null, 'Any text shown after price (for example "per night")'),
						$inventorwp_addon->add_field('listing_price_custom', 'Custom Text', 'text', null, 'Any text instead of numeric price (for example "by agreement"). Prefix and Suffix will be ignored')
					)
		);

		// Flags check boxes
		$inventorwp_addon->add_title('Flags');
		$inventorwp_addon->add_field(
			'listing_featured',
			'Featured',
			'radio',
				array(
					'0' => 'No',
					'on' => 'Yes'
					),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or \'0\' for \'No\'.'
			);

		$inventorwp_addon->add_field(
			'listing_reduced',
			'Reduced',
			'radio',
				array(
					'' => 'No',
					'on' => 'Yes'
						 ),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
			);


		// Location fields
		$inventorwp_addon->add_title('Location');
		$inventorwp_addon->add_field('listing_map_location_address', 'Map Location', 'text', null, 'Enter the full address of the location');
		$inventorwp_addon->add_field('listing_map_location_latitude', 'Map Location Latitude', 'text');
		$inventorwp_addon->add_field('listing_map_location_longitude', 'Map Location Longitude', 'text');

		$inventorwp_addon->add_options(
		    null,
		    'Advanced Location Options', 
		    array(	
		    		$inventorwp_addon->add_field('listing_map_location_polygon', 'Map Location Polygon', 'text', null, 'Enter encoded path. It can be constructed using Google\'s Interactive Polyline Encoder Utility'),
		            $inventorwp_addon->add_field(
			    		'listing_street_view',
			    		'Street View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_street_view_location_address', 'Street View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_street_view_location_latitude', 'Street View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_street_view_location_longitude', 'Street View Longitude', 'text' )
		       						 ),
			   					 )
						),
		            $inventorwp_addon->add_field(
			    		'listing_inside_view',
			    		'Inside View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_inside_view_location_address', 'Inside View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_inside_view_location_latitude', 'Inside View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_inside_view_location_longitude', 'Inside View Longitude', 'text' )
		       						 ),
			   					 )
						),
		    )
		);

		// Location Categories
		$inventorwp_addon->add_title('Location Categories', 'Use only one value for each field. The Parent Location field is required. Your location will not import correctly if it\'s left blank.');
		$inventorwp_addon->add_text('<div class="location-tax-wrap"><div class="location-tax lt-parent">', true);
		$inventorwp_addon->add_field('tmp_locations_parent', 'Location Parent', 'text', null, 'The top level location. This is often the country or state/province.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-child">', true);
		$inventorwp_addon->add_field('tmp_locations_child', 'Location Child', 'text', null, 'The second level location. This is often the state/province or city.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-sub-child">', true);
		$inventorwp_addon->add_field('tmp_locations_sub_child', 'Location Sub Child', 'text', null, 'The third level location. This is often the city or district/neighborhood.');
		$inventorwp_addon->add_text('</div></div><div style="clear:both">', true);

		// Listing Categories
		$inventorwp_addon->add_title('Listing Categories');
		$inventorwp_addon->add_text('<table class="seperator">
										<tr>
											<td>
												Seperator Character
											</td>
											<td>', true);
		$inventorwp_addon->add_field('listing_categories_seperator_character', '', 'text', array("," => null));
		$inventorwp_addon->add_text('		</td>
											<td>', true);
		$inventorwp_addon->add_title(' ', 'Use this to choose the seperator used in your CSV file. For example, if your listing categories are formatted like this: \'category 1, category 2, category 3\' then enter the character \',\' into the field.');
		$inventorwp_addon->add_text('		</td>
										</tr>
									</table>', true);
		$inventorwp_addon->add_field('listing_listing_category', 'Listing Categories', 'text');
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-statistics/inventor-statistics.php')) {
		
				$inventorwp_addon->add_title('Post Views');
				$inventorwp_addon->add_field('inventor_statistics_post_total_views', 'Post Views', 'text');
		
			}
		}

		break;

	case 'food':

		/** 
		* Food Post Type Fields
		**/

		// Details
		$inventorwp_addon->add_title('Details');
		$inventorwp_addon->add_field('tmp_food_kind', 'Food Kind', 'text');

		// Meals and drinks Menu
		$inventorwp_addon->add_title('Meals and drinks menu','The title field is required. Your menu item will not import correctly if it\'s left blank.'); // Don't forget the help text
		$inventorwp_addon->add_text('<table class="seperator">
										<tr>
											<td>
												Seperator Character
											</td>
											<td>', true);
		$inventorwp_addon->add_field('menu_seperator_character', '', 'text',  array("," => null));
		$inventorwp_addon->add_text('		</td>
											<td>', true);
		$inventorwp_addon->add_title(' ', 'Use this to choose the seperator used in your CSV file. For example, if your menu items are formatted like this: \'food 1, food 2, food 3\' then enter the character \',\' into the field.');
		$inventorwp_addon->add_text('		</td>
										</tr>
									</table>', true);
		$inventorwp_addon->add_field('listing_food_menu_title', 'Title', 'text');
		$inventorwp_addon->add_field('listing_food_menu_description', 'Description', 'text');
		$inventorwp_addon->add_field('listing_food_menu_price', 'Price', 'text', null, 'In USD');
		$inventorwp_addon->add_field('listing_food_menu_serving', 'Serving Day', 'text', null, 'Leave blank if it is not a daily menu. Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.');
		$inventorwp_addon->add_field('listing_food_menu_speciality','Speciality','text', null, 'Use \'yes\' or \'no\'');
		$inventorwp_addon->add_field('listing_food_menu_photo', 'Photo', 'image', null, 'Seperate multiple image URLs or filenames with the seperator character chosen above.');

		// Banner fields
		
		$banner_fields = array(
			'banner_simple' => 'Simple',
			'banner_featured_image' => 'Featured Image',
			'banner_image' => array(
					'Custom Image',
					$inventorwp_addon->add_field('listing_banner_image', 'Custom Image', 'image' ),
							),
			'banner_video' => array(
					'Video',
					$inventorwp_addon->add_field('listing_banner_video', 'Video', 'file' ),
					$inventorwp_addon->add_field(
						'listing_banner_video_loop',
						'Loop',
						'radio',
						array(
							'' => 'No',
							'on' => 'Yes'
						),
						'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
					)
			),
		);
		
		/*
		 *  If "Inventor Google Map" plugin is active, add the "Google Map",
		 *   "Google Street view" & "Google Inside View" banner types to
		 *   the list of availble banner types.
		 */
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-google-map/inventor-google-map.php')) {
				$google_maps_banner_fields = array(
					'banner_map' => array(
						'Google Map',
						$inventorwp_addon->add_field('listing_banner_map_zoom', 'Zoom', 'text', null, 'Minimum value of zoom is 0. Maximum value depends on location (12-25).'),
						$inventorwp_addon->add_field(
							'listing_banner_map_type',
							'Map Type',
							'radio',
							array(
								'ROADMAP' => 'Roadmap',
								'SATELLITE' => 'Satellite',
								'HYBRID' => 'Hybrid'
							),
							'If \'Set with XPath\' is chosen, the value must be \'ROADMAP\' for \'Roadmap\', \'SATELLITE\' for \'Satellite\' or \'HYBRID\' for \'Hybrid\'.'
						),
						$inventorwp_addon->add_field(
							'listing_banner_map_marker',
							'Marker',
							'radio',
							array(
								'' => 'No',
								'on' => 'Yes'
							),
							'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
						)
					),
					'banner_street_view' => 'Google Street View',
					'banner_inside_view' => 'Google Inside View',
				);
				
				$banner_fields = array_merge($banner_fields, $google_maps_banner_fields);
			}
		}

		$inventorwp_addon->add_title('Banner');
		$inventorwp_addon->add_field(
		        'listing_banner',
		        'Banner Type',
		        'radio', 
				$banner_fields
		);

		// Video fields
		$inventorwp_addon->add_title('Video');
		$inventorwp_addon->add_field('listing_video', 'Video URL', 'text', null, 'URL of externally hosted video for embedding');

		// Price fields
		$inventorwp_addon->add_title('Price');
		$inventorwp_addon->add_options(
				$inventorwp_addon->add_field('listing_price', 'Price', 'text', null, 'In USD'),
				'Advanced Options',
				array(
						$inventorwp_addon->add_field('listing_price_prefix', 'Price Prefix', 'text', null, 'Any text shown before price (for example "from")'),
						$inventorwp_addon->add_field('listing_price_suffix', 'Price Suffix', 'text', null, 'Any text shown after price (for example "per night")'),
						$inventorwp_addon->add_field('listing_price_custom', 'Custom Text', 'text', null, 'Any text instead of numeric price (for example "by agreement"). Prefix and Suffix will be ignored')
					)
		);

		// Flags check boxes
		$inventorwp_addon->add_title('Flags');
		$inventorwp_addon->add_field(
			'listing_featured',
			'Featured',
			'radio',
				array(
					'0' => 'No',
					'on' => 'Yes'
					),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or \'0\' for \'No\'.'
			);

		$inventorwp_addon->add_field(
			'listing_reduced',
			'Reduced',
			'radio',
				array(
					'' => 'No',
					'on' => 'Yes'
						 ),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
			);


		// Location fields
		$inventorwp_addon->add_title('Location');
		$inventorwp_addon->add_field('listing_map_location_address', 'Map Location', 'text', null, 'Enter the full address of the location');
		$inventorwp_addon->add_field('listing_map_location_latitude', 'Map Location Latitude', 'text');
		$inventorwp_addon->add_field('listing_map_location_longitude', 'Map Location Longitude', 'text');

		$inventorwp_addon->add_options(
		    null,
		    'Advanced Location Options', 
		    array(	
		    		$inventorwp_addon->add_field('listing_map_location_polygon', 'Map Location Polygon', 'text', null, 'Enter encoded path. It can be constructed using Google\'s Interactive Polyline Encoder Utility'),
		            $inventorwp_addon->add_field(
			    		'listing_street_view',
			    		'Street View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_street_view_location_address', 'Street View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_street_view_location_latitude', 'Street View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_street_view_location_longitude', 'Street View Longitude', 'text' )
		       						 ),
			   					 )
						),
		            $inventorwp_addon->add_field(
			    		'listing_inside_view',
			    		'Inside View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_inside_view_location_address', 'Inside View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_inside_view_location_latitude', 'Inside View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_inside_view_location_longitude', 'Inside View Longitude', 'text' )
		       						 ),
			   					 )
						),
		    )
		);

		// Location Categories
		$inventorwp_addon->add_title('Location Categories', 'Use only one value for each field. The Parent Location field is required. Your location will not import correctly if it\'s left blank.');
		$inventorwp_addon->add_text('<div class="location-tax-wrap"><div class="location-tax lt-parent">', true);
		$inventorwp_addon->add_field('tmp_locations_parent', 'Location Parent', 'text', null, 'The top level location. This is often the country or state/province.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-child">', true);
		$inventorwp_addon->add_field('tmp_locations_child', 'Location Child', 'text', null, 'The second level location. This is often the state/province or city.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-sub-child">', true);
		$inventorwp_addon->add_field('tmp_locations_sub_child', 'Location Sub Child', 'text', null, 'The third level location. This is often the city or district/neighborhood.');
		$inventorwp_addon->add_text('</div></div><div style="clear:both">', true);

		// Opening hours
		$inventorwp_addon->add_title('Opening Hours', 'Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable time will work.');
		$inventorwp_addon->add_text('<table id="opening-hours" class="form-table">
									    <tbody>
									    	<tr>
									    		<td class="opening-hours-day">
									    			<div class="input">Monday</div>
									    		</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_mon', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_mon', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_mon', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
											<tr>
												<td class="opening-hours-day">
													<div class="input">Tuesday</div>
												</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_tues', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_tues', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_tues', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
											<tr>
												<td class="opening-hours-day">
													<div class="input">Wednesday</div>
												</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_weds', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_weds', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_weds', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
											<tr>
												<td class="opening-hours-day">
													<div class="input">Thursday</div>
												</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_thurs', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_thurs', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_thurs', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
											<tr>
												<td class="opening-hours-day">
													<div class="input">Friday</div>
												</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_fri', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_fri', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_fri', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
											<tr>
												<td class="opening-hours-day">
													<div class="input">Saturday</div>
												</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_sat', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_sat', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_sat', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
											<tr>
												<td class="opening-hours-day">
													<div class="input">Sunday</div>
												</td>
												<td class="opening-hours-from">', true);
		$inventorwp_addon->add_field('listing_opening_hours_from_sun', 'Time from', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-to">', true);
		$inventorwp_addon->add_field('listing_opening_hours_to_sun', 'Time to', 'text');
		$inventorwp_addon->add_text('			</td>
												<td class="opening-hours-custom-text">', true);
		$inventorwp_addon->add_field('listing_opening_hours_custom_text_sun', 'Custom Text', 'text');
		$inventorwp_addon->add_text('			</td>
											</tr>
										</tbody>
									</table>', true);

		// Contact fields
		$inventorwp_addon->add_title('Contact');
		$inventorwp_addon->add_field('listing_email', 'Email', 'text');
		$inventorwp_addon->add_field('listing_phone', 'Phone', 'text');
		$inventorwp_addon->add_field('listing_website', 'Website', 'text', null, 'Use a full web address including http:// or https://');
		$inventorwp_addon->add_field('listing_person', 'Person', 'text');		
		$inventorwp_addon->add_field('listing_address', 'Address', 'text');

		// Social fields
		$inventorwp_addon->add_title('Social', 'Use full URL' );
		$inventorwp_addon->add_field('listing_facebook', 'Facebook', 'text');
		$inventorwp_addon->add_field('listing_twitter', 'Twitter', 'text');
		$inventorwp_addon->add_field('listing_google', 'Google+', 'text');
		$inventorwp_addon->add_field('listing_instagram', 'Instagram', 'text');
		$inventorwp_addon->add_field('listing_linkedin', 'LinkedIn', 'text');
		$inventorwp_addon->add_options(
		        null,
		        'Other Social Networks', 
		        array(
		                $inventorwp_addon->add_field('listing_vimeo', 'Vimeo', 'text'),
						$inventorwp_addon->add_field('listing_youtube', 'YouTube', 'text'),
						$inventorwp_addon->add_field('listing_dribbble', 'Dribbble', 'text'),
						$inventorwp_addon->add_field('listing_skype', 'Skype', 'text'),
						$inventorwp_addon->add_field('listing_foursquare', 'Foursquare', 'text'),
						$inventorwp_addon->add_field('listing_behance', 'Behance', 'text')
		        )
		);

		// Listing Categories
		$inventorwp_addon->add_title('Listing Categories');
		$inventorwp_addon->add_text('<table class="seperator">
										<tr>
											<td>
												Seperator Character
											</td>
											<td>', true);
		$inventorwp_addon->add_field('listing_categories_seperator_character', '', 'text', array("," => null));
		$inventorwp_addon->add_text('		</td>
											<td>', true);
		$inventorwp_addon->add_title(' ', 'Use this to choose the seperator used in your CSV file. For example, if your listing categories are formatted like this: \'category 1, category 2, category 3\' then enter the character \',\' into the field.');
		$inventorwp_addon->add_text('		</td>
										</tr>
									</table>', true);
		$inventorwp_addon->add_field('listing_listing_category', 'Listing Categories', 'text');
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-statistics/inventor-statistics.php')) {
		
				$inventorwp_addon->add_title('Post Views');
				$inventorwp_addon->add_field('inventor_statistics_post_total_views', 'Post Views', 'text');
		
			}
		}

		break;

	case 'hotel':

		/** 
		* Hotel Post Type Fields
		**/

		// Details
		$inventorwp_addon->add_title('Details');
		// Taxonomy ONLY - NO post_meta associated
		$inventorwp_addon->add_field('tmp_hotel_class', 'Hotel Class', 'text');
		$inventorwp_addon->add_field('listing_hotel_rooms', 'Rooms', 'text');

		// Banner fields
		
		$banner_fields = array(
			'banner_simple' => 'Simple',
			'banner_featured_image' => 'Featured Image',
			'banner_image' => array(
					'Custom Image',
					$inventorwp_addon->add_field('listing_banner_image', 'Custom Image', 'image' ),
							),
			'banner_video' => array(
					'Video',
					$inventorwp_addon->add_field('listing_banner_video', 'Video', 'file' ),
					$inventorwp_addon->add_field(
						'listing_banner_video_loop',
						'Loop',
						'radio',
						array(
							'' => 'No',
							'on' => 'Yes'
						),
						'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
					)
			),
		);
		
		/*
		 *  If "Inventor Google Map" plugin is active, add the "Google Map",
		 *   "Google Street view" & "Google Inside View" banner types to
		 *   the list of availble banner types.
		 */
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-google-map/inventor-google-map.php')) {
				$google_maps_banner_fields = array(
					'banner_map' => array(
						'Google Map',
						$inventorwp_addon->add_field('listing_banner_map_zoom', 'Zoom', 'text', null, 'Minimum value of zoom is 0. Maximum value depends on location (12-25).'),
						$inventorwp_addon->add_field(
							'listing_banner_map_type',
							'Map Type',
							'radio',
							array(
								'ROADMAP' => 'Roadmap',
								'SATELLITE' => 'Satellite',
								'HYBRID' => 'Hybrid'
							),
							'If \'Set with XPath\' is chosen, the value must be \'ROADMAP\' for \'Roadmap\', \'SATELLITE\' for \'Satellite\' or \'HYBRID\' for \'Hybrid\'.'
						),
						$inventorwp_addon->add_field(
							'listing_banner_map_marker',
							'Marker',
							'radio',
							array(
								'' => 'No',
								'on' => 'Yes'
							),
							'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
						)
					),
					'banner_street_view' => 'Google Street View',
					'banner_inside_view' => 'Google Inside View',
				);
				
				$banner_fields = array_merge($banner_fields, $google_maps_banner_fields);
			}
		}

		$inventorwp_addon->add_title('Banner');
		$inventorwp_addon->add_field(
		        'listing_banner',
		        'Banner Type',
		        'radio', 
				$banner_fields
		);

		// Video fields
		$inventorwp_addon->add_title('Video');
		$inventorwp_addon->add_field('listing_video', 'Video URL', 'text', null, 'URL of externally hosted video for embedding');

		// Price fields
		$inventorwp_addon->add_title('Price');
		$inventorwp_addon->add_options(
				$inventorwp_addon->add_field('listing_price', 'Price', 'text', null, 'In USD'),
				'Advanced Options',
				array(
						$inventorwp_addon->add_field('listing_price_prefix', 'Price Prefix', 'text', null, 'Any text shown before price (for example "from")'),
						$inventorwp_addon->add_field('listing_price_suffix', 'Price Suffix', 'text', null, 'Any text shown after price (for example "per night")'),
						$inventorwp_addon->add_field('listing_price_custom', 'Custom Text', 'text', null, 'Any text instead of numeric price (for example "by agreement"). Prefix and Suffix will be ignored')
					)
		);

		// Flags check boxes
		$inventorwp_addon->add_title('Flags');
		$inventorwp_addon->add_field(
			'listing_featured',
			'Featured',
			'radio',
				array(
					'0' => 'No',
					'on' => 'Yes'
					),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or \'0\' for \'No\'.'
			);

		$inventorwp_addon->add_field(
			'listing_reduced',
			'Reduced',
			'radio',
				array(
					'' => 'No',
					'on' => 'Yes'
						 ),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
			);


		// Location fields
		$inventorwp_addon->add_title('Location');
		$inventorwp_addon->add_field('listing_map_location_address', 'Map Location', 'text', null, 'Enter the full address of the location');
		$inventorwp_addon->add_field('listing_map_location_latitude', 'Map Location Latitude', 'text');
		$inventorwp_addon->add_field('listing_map_location_longitude', 'Map Location Longitude', 'text');

		$inventorwp_addon->add_options(
		    null,
		    'Advanced Location Options', 
		    array(	
		    		$inventorwp_addon->add_field('listing_map_location_polygon', 'Map Location Polygon', 'text', null, 'Enter encoded path. It can be constructed using Google\'s Interactive Polyline Encoder Utility'),
		            $inventorwp_addon->add_field(
			    		'listing_street_view',
			    		'Street View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_street_view_location_address', 'Street View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_street_view_location_latitude', 'Street View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_street_view_location_longitude', 'Street View Longitude', 'text' )
		       						 ),
			   					 )
						),
		            $inventorwp_addon->add_field(
			    		'listing_inside_view',
			    		'Inside View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_inside_view_location_address', 'Inside View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_inside_view_location_latitude', 'Inside View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_inside_view_location_longitude', 'Inside View Longitude', 'text' )
		       						 ),
			   					 )
						),
		    )
		);

		// Location Categories
		$inventorwp_addon->add_title('Location Categories', 'Use only one value for each field. The Parent Location field is required. Your location will not import correctly if it\'s left blank.');
		$inventorwp_addon->add_text('<div class="location-tax-wrap"><div class="location-tax lt-parent">', true);
		$inventorwp_addon->add_field('tmp_locations_parent', 'Location Parent', 'text', null, 'The top level location. This is often the country or state/province.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-child">', true);
		$inventorwp_addon->add_field('tmp_locations_child', 'Location Child', 'text', null, 'The second level location. This is often the state/province or city.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-sub-child">', true);
		$inventorwp_addon->add_field('tmp_locations_sub_child', 'Location Sub Child', 'text', null, 'The third level location. This is often the city or district/neighborhood.');
		$inventorwp_addon->add_text('</div></div><div style="clear:both">', true);

		// Contact fields
		$inventorwp_addon->add_title('Contact');
		$inventorwp_addon->add_field('listing_email', 'Email', 'text');
		$inventorwp_addon->add_field('listing_phone', 'Phone', 'text');
		$inventorwp_addon->add_field('listing_website', 'Website', 'text', null, 'Use a full web address including http:// or https://');
		$inventorwp_addon->add_field('listing_person', 'Person', 'text');		
		$inventorwp_addon->add_field('listing_address', 'Address', 'text');

		// Social fields
		$inventorwp_addon->add_title('Social', 'Use full URL' );
		$inventorwp_addon->add_field('listing_facebook', 'Facebook', 'text');
		$inventorwp_addon->add_field('listing_twitter', 'Twitter', 'text');
		$inventorwp_addon->add_field('listing_google', 'Google+', 'text');
		$inventorwp_addon->add_field('listing_instagram', 'Instagram', 'text');
		$inventorwp_addon->add_field('listing_linkedin', 'LinkedIn', 'text');
		$inventorwp_addon->add_options(
		        null,
		        'Other Social Networks', 
		        array(
		                $inventorwp_addon->add_field('listing_vimeo', 'Vimeo', 'text'),
						$inventorwp_addon->add_field('listing_youtube', 'YouTube', 'text'),
						$inventorwp_addon->add_field('listing_dribbble', 'Dribbble', 'text'),
						$inventorwp_addon->add_field('listing_skype', 'Skype', 'text'),
						$inventorwp_addon->add_field('listing_foursquare', 'Foursquare', 'text'),
						$inventorwp_addon->add_field('listing_behance', 'Behance', 'text')
		        )
		);

		// Listing Categories
		$inventorwp_addon->add_title('Listing Categories');
		$inventorwp_addon->add_text('<table class="seperator">
										<tr>
											<td>
												Seperator Character
											</td>
											<td>', true);
		$inventorwp_addon->add_field('listing_categories_seperator_character', '', 'text', array("," => null));
		$inventorwp_addon->add_text('		</td>
											<td>', true);
		$inventorwp_addon->add_title(' ', 'Use this to choose the seperator used in your CSV file. For example, if your listing categories are formatted like this: \'category 1, category 2, category 3\' then enter the character \',\' into the field.');
		$inventorwp_addon->add_text('		</td>
										</tr>
									</table>', true);
		$inventorwp_addon->add_field('listing_listing_category', 'Listing Categories', 'text');
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-statistics/inventor-statistics.php')) {
		
				$inventorwp_addon->add_title('Post Views');
				$inventorwp_addon->add_field('inventor_statistics_post_total_views', 'Post Views', 'text');
		
			}
		}

		break;

	case 'pet':

		/** 
		* Pet Post Type Fields
		**/

		// Details
		$inventorwp_addon->add_title('Details');
		// Taxonomy ONLY - NO post_meta associated
		$inventorwp_addon->add_field('tmp_animal', 'Animal', 'text');

		// Color
		$inventorwp_addon->add_title('Color');
		$inventorwp_addon->add_field('tmp_color', 'Color', 'text', null, 'Seperate multiple values with a comma. For example: value 1, value 2, value 3');

		// Video fields
		$inventorwp_addon->add_title('Video');
		$inventorwp_addon->add_field('listing_video', 'Video URL', 'text', null, 'URL of externally hosted video for embedding');

		// Location fields
		$inventorwp_addon->add_title('Location');
		$inventorwp_addon->add_field('listing_map_location_address', 'Map Location', 'text', null, 'Enter the full address of the location');
		$inventorwp_addon->add_field('listing_map_location_latitude', 'Map Location Latitude', 'text');
		$inventorwp_addon->add_field('listing_map_location_longitude', 'Map Location Longitude', 'text');

		$inventorwp_addon->add_options(
		    null,
		    'Advanced Location Options', 
		    array(	
		    		$inventorwp_addon->add_field('listing_map_location_polygon', 'Map Location Polygon', 'text', null, 'Enter encoded path. It can be constructed using Google\'s Interactive Polyline Encoder Utility'),
		            $inventorwp_addon->add_field(
			    		'listing_street_view',
			    		'Street View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_street_view_location_address', 'Street View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_street_view_location_latitude', 'Street View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_street_view_location_longitude', 'Street View Longitude', 'text' )
		       						 ),
			   					 )
						),
		            $inventorwp_addon->add_field(
			    		'listing_inside_view',
			    		'Inside View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_inside_view_location_address', 'Inside View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_inside_view_location_latitude', 'Inside View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_inside_view_location_longitude', 'Inside View Longitude', 'text' )
		       						 ),
			   					 )
						),
		    )
		);

		// Location Categories
		$inventorwp_addon->add_title('Location Categories', 'Use only one value for each field. The Parent Location field is required. Your location will not import correctly if it\'s left blank.');
		$inventorwp_addon->add_text('<div class="location-tax-wrap"><div class="location-tax lt-parent">', true);
		$inventorwp_addon->add_field('tmp_locations_parent', 'Location Parent', 'text', null, 'The top level location. This is often the country or state/province.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-child">', true);
		$inventorwp_addon->add_field('tmp_locations_child', 'Location Child', 'text', null, 'The second level location. This is often the state/province or city.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-sub-child">', true);
		$inventorwp_addon->add_field('tmp_locations_sub_child', 'Location Sub Child', 'text', null, 'The third level location. This is often the city or district/neighborhood.');
		$inventorwp_addon->add_text('</div></div><div style="clear:both">', true);

		// Price fields
		$inventorwp_addon->add_title('Price');
		$inventorwp_addon->add_options(
				$inventorwp_addon->add_field('listing_price', 'Price', 'text', null, 'In USD'),
				'Advanced Options',
				array(
						$inventorwp_addon->add_field('listing_price_prefix', 'Price Prefix', 'text', null, 'Any text shown before price (for example "from")'),
						$inventorwp_addon->add_field('listing_price_suffix', 'Price Suffix', 'text', null, 'Any text shown after price (for example "per night")'),
						$inventorwp_addon->add_field('listing_price_custom', 'Custom Text', 'text', null, 'Any text instead of numeric price (for example "by agreement"). Prefix and Suffix will be ignored')
					)
		);

		// Contact fields
		$inventorwp_addon->add_title('Contact');
		$inventorwp_addon->add_field('listing_email', 'Email', 'text');
		$inventorwp_addon->add_field('listing_phone', 'Phone', 'text');
		$inventorwp_addon->add_field('listing_website', 'Website', 'text', null, 'Use a full web address including http:// or https://');
		$inventorwp_addon->add_field('listing_person', 'Person', 'text');		
		$inventorwp_addon->add_field('listing_address', 'Address', 'text');

		// Social fields
		$inventorwp_addon->add_title('Social', 'Use full URL' );
		$inventorwp_addon->add_field('listing_facebook', 'Facebook', 'text');
		$inventorwp_addon->add_field('listing_twitter', 'Twitter', 'text');
		$inventorwp_addon->add_field('listing_google', 'Google+', 'text');
		$inventorwp_addon->add_field('listing_instagram', 'Instagram', 'text');
		$inventorwp_addon->add_field('listing_linkedin', 'LinkedIn', 'text');
		$inventorwp_addon->add_options(
		        null,
		        'Other Social Networks', 
		        array(
		                $inventorwp_addon->add_field('listing_vimeo', 'Vimeo', 'text'),
						$inventorwp_addon->add_field('listing_youtube', 'YouTube', 'text'),
						$inventorwp_addon->add_field('listing_dribbble', 'Dribbble', 'text'),
						$inventorwp_addon->add_field('listing_skype', 'Skype', 'text'),
						$inventorwp_addon->add_field('listing_foursquare', 'Foursquare', 'text'),
						$inventorwp_addon->add_field('listing_behance', 'Behance', 'text')
		        )
		);

		// Flags check boxes
		$inventorwp_addon->add_title('Flags');
		$inventorwp_addon->add_field(
			'listing_featured',
			'Featured',
			'radio',
				array(
					'0' => 'No',
					'on' => 'Yes'
					),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or \'0\' for \'No\'.'
			);

		$inventorwp_addon->add_field(
			'listing_reduced',
			'Reduced',
			'radio',
				array(
					'' => 'No',
					'on' => 'Yes'
						 ),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
			);

		// Listing Categories
		$inventorwp_addon->add_title('Listing Categories');
		$inventorwp_addon->add_text('<table class="seperator">
										<tr>
											<td>
												Seperator Character
											</td>
											<td>', true);
		$inventorwp_addon->add_field('listing_categories_seperator_character', '', 'text', array("," => null));
		$inventorwp_addon->add_text('		</td>
											<td>', true);
		$inventorwp_addon->add_title(' ', 'Use this to choose the seperator used in your CSV file. For example, if your listing categories are formatted like this: \'category 1, category 2, category 3\' then enter the character \',\' into the field.');
		$inventorwp_addon->add_text('		</td>
										</tr>
									</table>', true);
		$inventorwp_addon->add_field('listing_listing_category', 'Listing Categories', 'text');
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-statistics/inventor-statistics.php')) {
		
				$inventorwp_addon->add_title('Post Views');
				$inventorwp_addon->add_field('inventor_statistics_post_total_views', 'Post Views', 'text');
		
			}
		}

		break;

	case 'shopping':

		/** 
		* Shopping Post Type Fields
		**/

		// Details
		$inventorwp_addon->add_title('Details');
		// Taxonomy ONLY - NO post_meta associated
		$inventorwp_addon->add_field('tmp_shopping_category', 'Shopping Category', 'text');
		$inventorwp_addon->add_field('tmp_color', 'Color', 'text', null, 'Seperate multiple values with a comma. For example: value 1, value 2, value 3');
		$inventorwp_addon->add_field('listing_size', 'Size', 'text', null, 'For example M, 10cm, 47 ...');
		$inventorwp_addon->add_field('listing_weight', 'Weight', 'text', null, 'Numerical values only.');

		// Video fields
		$inventorwp_addon->add_title('Video');
		$inventorwp_addon->add_field('listing_video', 'Video URL', 'text', null, 'URL of externally hosted video for embedding');

		// Price fields
		$inventorwp_addon->add_title('Price');
		$inventorwp_addon->add_options(
				$inventorwp_addon->add_field('listing_price', 'Price', 'text', null, 'In USD'),
				'Advanced Options',
				array(
						$inventorwp_addon->add_field('listing_price_prefix', 'Price Prefix', 'text', null, 'Any text shown before price (for example "from")'),
						$inventorwp_addon->add_field('listing_price_suffix', 'Price Suffix', 'text', null, 'Any text shown after price (for example "per night")'),
						$inventorwp_addon->add_field('listing_price_custom', 'Custom Text', 'text', null, 'Any text instead of numeric price (for example "by agreement"). Prefix and Suffix will be ignored')
					)
		);

		// Location fields
		$inventorwp_addon->add_title('Location');
		$inventorwp_addon->add_field('listing_map_location_address', 'Map Location', 'text', null, 'Enter the full address of the location');
		$inventorwp_addon->add_field('listing_map_location_latitude', 'Map Location Latitude', 'text');
		$inventorwp_addon->add_field('listing_map_location_longitude', 'Map Location Longitude', 'text');

		$inventorwp_addon->add_options(
		    null,
		    'Advanced Location Options', 
		    array(	
		    		$inventorwp_addon->add_field('listing_map_location_polygon', 'Map Location Polygon', 'text', null, 'Enter encoded path. It can be constructed using Google\'s Interactive Polyline Encoder Utility'),
		            $inventorwp_addon->add_field(
			    		'listing_street_view',
			    		'Street View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_street_view_location_address', 'Street View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_street_view_location_latitude', 'Street View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_street_view_location_longitude', 'Street View Longitude', 'text' )
		       						 ),
			   					 )
						),
		            $inventorwp_addon->add_field(
			    		'listing_inside_view',
			    		'Inside View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_inside_view_location_address', 'Inside View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_inside_view_location_latitude', 'Inside View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_inside_view_location_longitude', 'Inside View Longitude', 'text' )
		       						 ),
			   					 )
						),
		    )
		);

		// Location Categories
		$inventorwp_addon->add_title('Location Categories', 'Use only one value for each field. The Parent Location field is required. Your location will not import correctly if it\'s left blank.');
		$inventorwp_addon->add_text('<div class="location-tax-wrap"><div class="location-tax lt-parent">', true);
		$inventorwp_addon->add_field('tmp_locations_parent', 'Location Parent', 'text', null, 'The top level location. This is often the country or state/province.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-child">', true);
		$inventorwp_addon->add_field('tmp_locations_child', 'Location Child', 'text', null, 'The second level location. This is often the state/province or city.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-sub-child">', true);
		$inventorwp_addon->add_field('tmp_locations_sub_child', 'Location Sub Child', 'text', null, 'The third level location. This is often the city or district/neighborhood.');
		$inventorwp_addon->add_text('</div></div><div style="clear:both">', true);

		// Flags check boxes
		$inventorwp_addon->add_title('Flags');
		$inventorwp_addon->add_field(
			'listing_featured',
			'Featured',
			'radio',
				array(
					'0' => 'No',
					'on' => 'Yes'
					),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or \'0\' for \'No\'.'
			);

		$inventorwp_addon->add_field(
			'listing_reduced',
			'Reduced',
			'radio',
				array(
					'' => 'No',
					'on' => 'Yes'
						 ),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
			);

		// Contact fields
		$inventorwp_addon->add_title('Contact');
		$inventorwp_addon->add_field('listing_email', 'Email', 'text');
		$inventorwp_addon->add_field('listing_phone', 'Phone', 'text');
		$inventorwp_addon->add_field('listing_website', 'Website', 'text', null, 'Use a full web address including http:// or https://');
		$inventorwp_addon->add_field('listing_person', 'Person', 'text');		
		$inventorwp_addon->add_field('listing_address', 'Address', 'text');

		// Social fields
		$inventorwp_addon->add_title('Social', 'Use full URL' );
		$inventorwp_addon->add_field('listing_facebook', 'Facebook', 'text');
		$inventorwp_addon->add_field('listing_twitter', 'Twitter', 'text');
		$inventorwp_addon->add_field('listing_google', 'Google+', 'text');
		$inventorwp_addon->add_field('listing_instagram', 'Instagram', 'text');
		$inventorwp_addon->add_field('listing_linkedin', 'LinkedIn', 'text');
		$inventorwp_addon->add_options(
		        null,
		        'Other Social Networks', 
		        array(
		                $inventorwp_addon->add_field('listing_vimeo', 'Vimeo', 'text'),
						$inventorwp_addon->add_field('listing_youtube', 'YouTube', 'text'),
						$inventorwp_addon->add_field('listing_dribbble', 'Dribbble', 'text'),
						$inventorwp_addon->add_field('listing_skype', 'Skype', 'text'),
						$inventorwp_addon->add_field('listing_foursquare', 'Foursquare', 'text'),
						$inventorwp_addon->add_field('listing_behance', 'Behance', 'text')
		        )
		);

		// Listing Categories
		$inventorwp_addon->add_title('Listing Categories');
		$inventorwp_addon->add_text('<table class="seperator">
										<tr>
											<td>
												Seperator Character
											</td>
											<td>', true);
		$inventorwp_addon->add_field('listing_categories_seperator_character', '', 'text', array("," => null));
		$inventorwp_addon->add_text('		</td>
											<td>', true);
		$inventorwp_addon->add_title(' ', 'Use this to choose the seperator used in your CSV file. For example, if your listing categories are formatted like this: \'category 1, category 2, category 3\' then enter the character \',\' into the field.');
		$inventorwp_addon->add_text('		</td>
										</tr>
									</table>', true);
		$inventorwp_addon->add_field('listing_listing_category', 'Listing Categories', 'text');
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-statistics/inventor-statistics.php')) {
		
				$inventorwp_addon->add_title('Post Views');
				$inventorwp_addon->add_field('inventor_statistics_post_total_views', 'Post Views', 'text');
		
			}
		}

		break;

	case 'travel':

		/** 
		* Travel Post Type Fields
		**/

		// Details
		$inventorwp_addon->add_title('Details');
		$inventorwp_addon->add_field('tmp_travel_activity', 'Travel Activities', 'text', null, 'Seperate multiple values with a comma. For example: value 1, value 2, value 3');

		// Date Interval
		$inventorwp_addon->add_title('Date Interval', 'Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.');
		$inventorwp_addon->add_field('listing_date_from', 'Date from', 'text');
		$inventorwp_addon->add_field('listing_date_to', 'Date to', 'text');		

		// Banner fields
		
		$banner_fields = array(
			'banner_simple' => 'Simple',
			'banner_featured_image' => 'Featured Image',
			'banner_image' => array(
					'Custom Image',
					$inventorwp_addon->add_field('listing_banner_image', 'Custom Image', 'image' ),
							),
			'banner_video' => array(
					'Video',
					$inventorwp_addon->add_field('listing_banner_video', 'Video', 'file' ),
					$inventorwp_addon->add_field(
						'listing_banner_video_loop',
						'Loop',
						'radio',
						array(
							'' => 'No',
							'on' => 'Yes'
						),
						'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
					)
			),
		);
		
		/*
		 *  If "Inventor Google Map" plugin is active, add the "Google Map",
		 *   "Google Street view" & "Google Inside View" banner types to
		 *   the list of availble banner types.
		 */
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-google-map/inventor-google-map.php')) {
				$google_maps_banner_fields = array(
					'banner_map' => array(
						'Google Map',
						$inventorwp_addon->add_field('listing_banner_map_zoom', 'Zoom', 'text', null, 'Minimum value of zoom is 0. Maximum value depends on location (12-25).'),
						$inventorwp_addon->add_field(
							'listing_banner_map_type',
							'Map Type',
							'radio',
							array(
								'ROADMAP' => 'Roadmap',
								'SATELLITE' => 'Satellite',
								'HYBRID' => 'Hybrid'
							),
							'If \'Set with XPath\' is chosen, the value must be \'ROADMAP\' for \'Roadmap\', \'SATELLITE\' for \'Satellite\' or \'HYBRID\' for \'Hybrid\'.'
						),
						$inventorwp_addon->add_field(
							'listing_banner_map_marker',
							'Marker',
							'radio',
							array(
								'' => 'No',
								'on' => 'Yes'
							),
							'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
						)
					),
					'banner_street_view' => 'Google Street View',
					'banner_inside_view' => 'Google Inside View',
				);
				
				$banner_fields = array_merge($banner_fields, $google_maps_banner_fields);
			}
		}

		$inventorwp_addon->add_title('Banner');
		$inventorwp_addon->add_field(
		        'listing_banner',
		        'Banner Type',
		        'radio', 
				$banner_fields
		);

		// Video fields
		$inventorwp_addon->add_title('Video');
		$inventorwp_addon->add_field('listing_video', 'Video URL', 'text', null, 'URL of externally hosted video for embedding');

		// Location fields
		$inventorwp_addon->add_title('Location');
		$inventorwp_addon->add_field('listing_map_location_address', 'Map Location', 'text', null, 'Enter the full address of the location');
		$inventorwp_addon->add_field('listing_map_location_latitude', 'Map Location Latitude', 'text');
		$inventorwp_addon->add_field('listing_map_location_longitude', 'Map Location Longitude', 'text');

		$inventorwp_addon->add_options(
		    null,
		    'Advanced Location Options', 
		    array(	
		    		$inventorwp_addon->add_field('listing_map_location_polygon', 'Map Location Polygon', 'text', null, 'Enter encoded path. It can be constructed using Google\'s Interactive Polyline Encoder Utility'),
		            $inventorwp_addon->add_field(
			    		'listing_street_view',
			    		'Street View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_street_view_location_address', 'Street View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_street_view_location_latitude', 'Street View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_street_view_location_longitude', 'Street View Longitude', 'text' )
		       						 ),
			   					 )
						),
		            $inventorwp_addon->add_field(
			    		'listing_inside_view',
			    		'Inside View',
			    		'radio',
			    			array(
			        			'' => 'No',
			        			'on' => array(
		                			'Yes',
		                			$inventorwp_addon->add_field('listing_inside_view_location_address', 'Inside View Location', 'text', null, 'Enter the full address of the location'),
		                			$inventorwp_addon->add_field('listing_inside_view_location_latitude', 'Inside View Latitude', 'text' ),
		                			$inventorwp_addon->add_field('listing_inside_view_location_longitude', 'Inside View Longitude', 'text' )
		       						 ),
			   					 )
						),
		    )
		);

		// Location Categories
		$inventorwp_addon->add_title('Location Categories', 'Use only one value for each field. The Parent Location field is required. Your location will not import correctly if it\'s left blank.');
		$inventorwp_addon->add_text('<div class="location-tax-wrap"><div class="location-tax lt-parent">', true);
		$inventorwp_addon->add_field('tmp_locations_parent', 'Location Parent', 'text', null, 'The top level location. This is often the country or state/province.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-child">', true);
		$inventorwp_addon->add_field('tmp_locations_child', 'Location Child', 'text', null, 'The second level location. This is often the state/province or city.');
		$inventorwp_addon->add_text('</div><div class="child-indicator"> > </div><div class="location-tax lt-sub-child">', true);
		$inventorwp_addon->add_field('tmp_locations_sub_child', 'Location Sub Child', 'text', null, 'The third level location. This is often the city or district/neighborhood.');
		$inventorwp_addon->add_text('</div></div><div style="clear:both">', true);

		// Price fields
		$inventorwp_addon->add_title('Price');
		$inventorwp_addon->add_options(
				$inventorwp_addon->add_field('listing_price', 'Price', 'text', null, 'In USD'),
				'Advanced Options',
				array(
						$inventorwp_addon->add_field('listing_price_prefix', 'Price Prefix', 'text', null, 'Any text shown before price (for example "from")'),
						$inventorwp_addon->add_field('listing_price_suffix', 'Price Suffix', 'text', null, 'Any text shown after price (for example "per night")'),
						$inventorwp_addon->add_field('listing_price_custom', 'Custom Text', 'text', null, 'Any text instead of numeric price (for example "by agreement"). Prefix and Suffix will be ignored')
					)
		);

		// Contact fields
		$inventorwp_addon->add_title('Contact');
		$inventorwp_addon->add_field('listing_email', 'Email', 'text');
		$inventorwp_addon->add_field('listing_phone', 'Phone', 'text');
		$inventorwp_addon->add_field('listing_website', 'Website', 'text', null, 'Use a full web address including http:// or https://');
		$inventorwp_addon->add_field('listing_person', 'Person', 'text');		
		$inventorwp_addon->add_field('listing_address', 'Address', 'text');

		// Social fields
		$inventorwp_addon->add_title('Social', 'Use full URL' );
		$inventorwp_addon->add_field('listing_facebook', 'Facebook', 'text');
		$inventorwp_addon->add_field('listing_twitter', 'Twitter', 'text');
		$inventorwp_addon->add_field('listing_google', 'Google+', 'text');
		$inventorwp_addon->add_field('listing_instagram', 'Instagram', 'text');
		$inventorwp_addon->add_field('listing_linkedin', 'LinkedIn', 'text');
		$inventorwp_addon->add_options(
		        null,
		        'Other Social Networks', 
		        array(
		                $inventorwp_addon->add_field('listing_vimeo', 'Vimeo', 'text'),
						$inventorwp_addon->add_field('listing_youtube', 'YouTube', 'text'),
						$inventorwp_addon->add_field('listing_dribbble', 'Dribbble', 'text'),
						$inventorwp_addon->add_field('listing_skype', 'Skype', 'text'),
						$inventorwp_addon->add_field('listing_foursquare', 'Foursquare', 'text'),
						$inventorwp_addon->add_field('listing_behance', 'Behance', 'text')
		        )
		);

		// Flags check boxes
		$inventorwp_addon->add_title('Flags');
		$inventorwp_addon->add_field(
			'listing_featured',
			'Featured',
			'radio',
				array(
					'0' => 'No',
					'on' => 'Yes'
					),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or \'0\' for \'No\'.'
			);

		$inventorwp_addon->add_field(
			'listing_reduced',
			'Reduced',
			'radio',
				array(
					'' => 'No',
					'on' => 'Yes'
						 ),
			'If \'Set with XPath\' is chosen, the value must be either \'on\' for \'Yes\' or empty for \'No\'.'
			);

		// Listing Categories
		$inventorwp_addon->add_title('Listing Categories');
		$inventorwp_addon->add_text('<table class="seperator">
										<tr>
											<td>
												Seperator Character
											</td>
											<td>', true);
		$inventorwp_addon->add_field('listing_categories_seperator_character', '', 'text', array("," => null));
		$inventorwp_addon->add_text('		</td>
											<td>', true);
		$inventorwp_addon->add_title(' ', 'Use this to choose the seperator used in your CSV file. For example, if your listing categories are formatted like this: \'category 1, category 2, category 3\' then enter the character \',\' into the field.');
		$inventorwp_addon->add_text('		</td>
										</tr>
									</table>', true);
		$inventorwp_addon->add_field('listing_listing_category', 'Listing Categories', 'text');
		
		if (function_exists('is_plugin_active')) {
			if (is_plugin_active('inventor-statistics/inventor-statistics.php')) {
		
				$inventorwp_addon->add_title('Post Views');
				$inventorwp_addon->add_field('inventor_statistics_post_total_views', 'Post Views', 'text');
		
			}
		}

		break;

	default:

		// No match but some how it runs

		$inventorwp_addon->add_title('Error');
		$inventorwp_addon->add_text('Incorrect content type.');

		break;
}


/**
* Remove default image upload
**/

$inventorwp_addon->disable_default_images();


/**
* Import functions
**/

//
// Register import function
//

$inventorwp_addon->set_import_function('inventorwp_import');

//
// Main Import function
//

function inventorwp_import($post_id, $data, $import_options, $article, $logger) {


	// Don't forget to grab those sweet, sweet, variables
	global $inventorwp_addon;
	
	// Simple fields : All fields except images, opening hours, array fields, repeater fields, and strange taxonomy/post_meta hybrid fields
    $fields = array(

    	//
    	// Post Type Specific fields
    	//

    	// Business Branding fields - except 'listing_logo'
        'listing_slogan',
        'listing_brand_color',

        // Car Details
        'listing_car_year_manufactured',
        'listing_car_mileage',
        'listing_car_condition',
        'listing_car_leasing',

        // Dating Details
        'listing_dating_gender',
        'listing_dating_age',
        'listing_dating_weight',

        // Education Details
        'listing_education_lector',

        // Hotel Details
        'listing_hotel_rooms',

        // Shopping Details
        'listing_size',
        'listing_weight',

        // Travel Date interval
        'listing_date_from',
        'listing_date_to',

        //
    	// Shared fields
        //

		// Date and time interval
    	'listing_date',
    	'listing_time_from',
    	'listing_time_to',

    	// Banner fields - except 'listing_banner_image' & 'listing_banner_video'
        'listing_banner',
        'listing_banner_video_loop',
		'listing_banner_map_marker',
		'listing_banner_map_type',
		'listing_banner_map_zoom',

        // Video fields
        'listing_video',

        // Price fields 
        'listing_price',
        'listing_price_prefix',
        'listing_price_suffix',
        'listing_price_custom',

        // Contact fields
        'listing_email',
        'listing_phone',
        'listing_website',
        'listing_person',
        'listing_address',

        // Social fields
        'listing_facebook',
        'listing_twitter',
        'listing_google',
        'listing_instagram',
        'listing_linkedin',
        'listing_vimeo',
        'listing_youtube',
        'listing_dribbble',
        'listing_skype',
        'listing_foursquare',
        'listing_behance',

        // Non-Array Location fields
        'listing_map_location_address',
        'listing_map_location_latitude',
        'listing_map_location_longitude',
        'listing_map_location_polygon',
        'listing_street_view', // Street view
        'listing_street_view_location_latitude',
        'listing_street_view_location_longitude',
        'listing_inside_view', // Inside view
        'listing_inside_view_location_latitude',
        'listing_inside_view_location_longitude',

        // Flag check box ('listing_featured' must be updated separately)
        'listing_reduced',

        //
        // Temporary fields for setting taxonomy
        //

        'tmp_engine_type',
        'tmp_transmission',
        'tmp_status',
        'tmp_eye_color',
        'tmp_education_level',
        'tmp_education_subject',
        'tmp_event_type',
        'tmp_food_kind',
        'tmp_hotel_class',
        'tmp_animal',
        'tmp_shopping_category',
        'tmp_locations_parent',
        'tmp_locations_child',
        'tmp_locations_sub_child'
    );

    // Update everything in fields arrays
    foreach ( $fields as $field ) {
    	// Check if each field is allowed to be updated in import settings and whether it's empty or it's a new post
        if (
	    	// It's a new post
	    	(empty($article['ID']) 	
	    	// The field has a value
	    	&& isset($data[$field]) 
	    	&& !empty($data[$field])) 
	    	|| 
	    	// Updating the post_meta is allowed
	    	($inventorwp_addon->can_update_meta($field, $import_options) 
	    	// The field has a value
	    	&& isset($data[$field]) 
	    	&& !empty($data[$field]))
    	){

        	$time_tester = substr($field, 0, 12);

        	if($time_tester == 'listing_date') {

        		update_post_meta( $post_id, $field, strtotime($data[$field]));

        	} elseif($time_tester == 'listing_time'){

        		update_post_meta( $post_id, $field, date('h:i A', strtotime($data[$field])));

        	} else {

        		if ( $field == 'listing_price' ) {

        			update_post_meta( $post_id, $field, inventorwp_addon_fix_price( $data[$field] ) );

        		} else {

            		update_post_meta( $post_id, $field, $data[$field] );
            	}

        	}	

        	// Don't log the import of temporary fields
        	if(substr($field, 0, 3) == 'tmp') {

        	} else {
            // Log custom field import
            $inventorwp_addon->log( '- Importing custom field `' . $field . '` : \'' . $data[$field] . '\'');
        	}

        }
    }

    //
    // Import Logo Image, Banner Image and Banner Video
    //

    // Update logo

    $field = 'listing_logo';

    // Check if logo field is allowed to be updated in import settings and whether it's empty or it's a new post
    if (
    	// It's a new post
    	(empty($article['ID']) 	
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field])) 
    	|| 
    	// Updating the post_meta is allowed
    	($inventorwp_addon->can_update_meta($field, $import_options) 
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field]))
    ){

		$imported_file_url = wp_get_attachment_image_src($data[$field]['attachment_id'], 'full', false);

		// Update image file URL
        update_post_meta( $post_id, $field, $imported_file_url[0] );

         // Update image attachment ID
        update_post_meta( $post_id, 'listing_logo_id', $data[$field]['attachment_id']);

        // Log banner source URL, attachement ID and final URL
        $inventorwp_addon->log( '- Importing custom field `' . $field . '` From URL: `' . $data[$field]['image_url_or_path'] . '\', Attachment ID: \'' . $data[$field]['attachment_id'] . '`, Attachment URL: `' . $imported_file_url[0] . '`');

    }

    // Update banner image
    $field = 'listing_banner_image';

    // Check if banner image field is allowed to be updated in import settings and whether it's empty or it's a new post
    if (
    	// It's a new post
    	(empty($article['ID']) 	
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field])) 
    	|| 
    	// Updating the post_meta is allowed
    	($inventorwp_addon->can_update_meta($field, $import_options) 
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field]))
    ){

		$imported_file_url = wp_get_attachment_image_src($data[$field]['attachment_id'], 'full', false);

		// Update image file URL
        update_post_meta( $post_id, $field, $imported_file_url[0] );

         // Update image attachment ID
        update_post_meta( $post_id, 'listing_banner_image_id', $data[$field]['attachment_id']);

        // Log banner source URL, attachement ID and final URL
        $inventorwp_addon->log( '- Importing custom field `' . $field . '` From URL: `' . $data[$field]['image_url_or_path'] . '`, Attachment ID: `' . $data[$field]['attachment_id'] . '`, Attachment URL: `' . $imported_file_url[0] . '`');

    }

    // Update Banner Video

    $field = 'listing_banner_video';

    // Check if banner video field is allowed to be updated in import settings and whether it's empty or it's a new post
    if (
    	// It's a new post
    	(empty($article['ID']) 	
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field])) 
    	|| 
    	// Updating the post_meta is allowed
    	($inventorwp_addon->can_update_meta($field, $import_options) 
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field]))
    ){

		$attachment_id = $data[$field]['attachment_id'];
		$imported_file_url = wp_get_attachment_url($attachment_id);

		// Update video file URL
        update_post_meta( $post_id, $field, $imported_file_url);

        // Update video attachment ID
        update_post_meta( $post_id, 'listing_banner_video_id', $data[$field]['attachment_id']);


        // Log video source URL, attachement ID and final URL
        $inventorwp_addon->log( '- Importing custom field `' . $field . '` From URL: `' . $data[$field]['image_url_or_path'] . '`, Attachment ID: `' . $data[$field]['attachment_id'] . '`, Attachment File URL: `' . $imported_file_url . '`');
    }
	
	// Update featured status
	
	$field = 'listing_featured';
	
	if (
		// Is a new post
		empty($article['ID'])
		||
		// Updating the post meta is allowed
		$inventorwp_addon->can_update_meta($field, $import_options)
	) {
		if ($data[$field] == "0" || $data[$field] == "on") {
			update_post_meta( $post_id, "listing_featured", $data[$field] );
			$inventorwp_addon->log( '- Importing Featured Status');
		} elseif ( empty($article['ID']) ) {
			update_post_meta( $post_id, "listing_featured", "0" );
			$inventorwp_addon->log( '- Importing Featured Status');
		} else {
			// Do nothing, no value was provided
		}
	}
    //
    // Import Opening Hours
    //
    $field = 'listing_opening_hours';

	// Check if opening hours is allowed to be updated in import settings or it's a new post

	if (empty($article['ID']) || $inventorwp_addon->can_update_meta($field, $import_options)){

		$data = wpai_inventor_addon_fix_hours_in_data( $data, array(
			'listing_opening_hours_from_mon',
			'listing_opening_hours_to_mon',
			'listing_opening_hours_custom_text_mon',
			'listing_opening_hours_from_tues',
			'listing_opening_hours_to_tues',
			'listing_opening_hours_custom_text_tues',
			'listing_opening_hours_from_weds',
			'listing_opening_hours_to_weds',
			'listing_opening_hours_custom_text_weds',
			'listing_opening_hours_from_thurs',
			'listing_opening_hours_to_thurs',
			'listing_opening_hours_custom_text_thurs',
			'listing_opening_hours_from_fri',
			'listing_opening_hours_to_fri',
			'listing_opening_hours_custom_text_fri',
			'listing_opening_hours_from_sat',
			'listing_opening_hours_to_sat',
			'listing_opening_hours_custom_text_sat',
			'listing_opening_hours_from_sun',
			'listing_opening_hours_to_sun',
			'listing_opening_hours_custom_text_sun' ) );

       $hours = array( 
			0 => array( 
				'listing_day'		=> 'MONDAY',
				'listing_time_from'	=> date( 'g:i a', strtotime( $data['listing_opening_hours_from_mon'])),
				'listing_time_to'	=> date( 'g:i a', strtotime($data['listing_opening_hours_to_mon'])),
				'listing_custom'	=> $data['listing_opening_hours_custom_text_mon']
				),
			1 => array(
				'listing_day'		=> 'TUESDAY',
				'listing_time_from'	=> date( 'g:i a', strtotime($data['listing_opening_hours_from_tues'])),
				'listing_time_to'	=> date( 'g:i a', strtotime($data['listing_opening_hours_to_tues'])),
				'listing_custom'	=> $data['listing_opening_hours_custom_text_tues']
				),
			2 => array( 
				'listing_day'		=> 'WEDNESDAY',
				'listing_time_from'	=> date( 'g:i a', strtotime($data['listing_opening_hours_from_weds'])),
				'listing_time_to'	=> date( 'g:i a', strtotime($data['listing_opening_hours_to_weds'])),
				'listing_custom'	=> $data['listing_opening_hours_custom_text_weds']
				),
			3 => array( 
				'listing_day'		=> 'THURSDAY',
				'listing_time_from'	=> date( 'g:i a', strtotime($data['listing_opening_hours_from_thurs'])),
				'listing_time_to'	=> date( 'g:i a', strtotime($data['listing_opening_hours_to_thurs'])),
				'listing_custom'	=> $data['listing_opening_hours_custom_text_thurs']
				),
			4 => array( 
				'listing_day'		=> 'FRIDAY',
				'listing_time_from'	=> date( 'g:i a', strtotime($data['listing_opening_hours_from_fri'])),
				'listing_time_to'	=> date( 'g:i a', strtotime($data['listing_opening_hours_to_fri'])),
				'listing_custom'	=> $data['listing_opening_hours_custom_text_fri'] 
				),
			5 => array( 
				'listing_day'		=> 'SATURDAY',
				'listing_time_from'	=> date( 'g:i a', strtotime($data['listing_opening_hours_from_sat'])),
				'listing_time_to'	=> date( 'g:i a', strtotime($data['listing_opening_hours_to_sat'])),
				'listing_custom'	=> $data['listing_opening_hours_custom_text_sat']
				),
			6 => array(
				'listing_day'		=> 'SUNDAY',
				'listing_time_from'	=> date( 'g:i a', strtotime($data['listing_opening_hours_from_sun'])),
				'listing_time_to'	=> date( 'g:i a', strtotime($data['listing_opening_hours_to_sun'])),
				'listing_custom'	=> $data['listing_opening_hours_custom_text_sun']
				) 
			);

    	update_post_meta( $post_id, $field, $hours);

    	// Log opening hours update
    	$inventorwp_addon->log( '- Importing Opening Hours');
    }

    //
    // Import Location Fields Arrays
    //	

    // Update Main Location Data Array

    $field = 'listing_map_location';

    // Check if main location field is allowed to be updated in import settings and whether it's empty or it's a new post
  	if (
    	// It's a new post
    	empty($article['ID'])
    	|| 
    	// Updating the post_meta is allowed
    	$inventorwp_addon->can_update_meta($field, $import_options) 
    ){

    	$listing_location = array( 
						'address'	=> $data['listing_map_location_address'],
						'latitude'	=> $data['listing_map_location_latitude'],
						'longitude'	=> $data['listing_map_location_longitude']
					);

    	update_post_meta( $post_id, $field, $listing_location);

    	// log main location data update
    	$inventorwp_addon->log( '- Importing Main Location Data');
    }

    // Update Street View Location Data Array and hidden defaults
    $field = 'listing_street_view_location';

    // Check if street view location field is allowed to be updated in import settings and whether it's empty or it's a new post
  	if (
    	// It's a new post
    	empty($article['ID']) 	
    	|| 
    	// Updating the post_meta is allowed
    	$inventorwp_addon->can_update_meta($field, $import_options) 
    ){

    	$listing_location = array( 
						'latitude'	=> $data['listing_street_view_location_latitude'],
						'longitude'	=> $data['listing_street_view_location_longitude'],
						'zoom'		=> '1',
						'heading'	=> '-18',
						'pitch'		=> '25'
					);

    	// Update street view array
    	update_post_meta( $post_id, $field, $listing_location);

    	// Update street view hidden defaults
    	update_post_meta( $post_id, 'listing_street_view_location_zoom', '1' );
    	update_post_meta( $post_id, 'listing_street_view_location_heading', '-18' );
    	update_post_meta( $post_id, 'listing_street_view_location_pitch', '25' );

    	// log main location data update
    	$inventorwp_addon->log( '- Importing Street View Location data');
    }


    // Update Inside View Location Data Array and hidden defaults
    $field = 'listing_inside_view_location';

    // Check if inside view location field is allowed to be updated in import settings and whether it's empty or it's a new post
    if (
    	// It's a new post
    	empty($article['ID']) 	
    	|| 
    	// Updating the post_meta is allowed
    	$inventorwp_addon->can_update_meta($field, $import_options) 
    ){

	    $listing_location = array( 
							'latitude'	=> $data['listing_inside_view_location_latitude'],
							'longitude'	=> $data['listing_inside_view_location_longitude'],
							'zoom'		=> '1',
							'heading'	=> '-18',
							'pitch'		=> '25'
						);

    	// Update inside view array
    	update_post_meta( $post_id, $field, $listing_location );

    	// Update inside view hidden defaults
    	update_post_meta( $post_id, 'listing_inside_view_location_zoom', '1' );
    	update_post_meta( $post_id, 'listing_inside_view_location_heading', '-18' );
    	update_post_meta( $post_id, 'listing_inside_view_location_pitch', '25' );

    	// log main location data update
    	$inventorwp_addon->log( '- Importing Inside View Location data');
    }

    //
    // Import Location Categories (post_meta update)
    //

    // The post_meta for locations is set to the last term in the hierarchy

    if(
    	// It's a new post
    	(empty($article['ID'])
		// The parent field is not empty 
		&& isset($data['tmp_locations_parent']) 
		&& !empty($data['tmp_locations_parent']))
		|| 
		// Updating the post is allowed
		($inventorwp_addon->can_update_meta('listing_locations', $import_options)
		// The parent field is not empty 
		&& isset($data['tmp_locations_parent']) 
		&& !empty($data['tmp_locations_parent']))
	){
		// Add parent location to the array
		$locations_array = array($data['tmp_locations_parent']);
		
		// Check if child is empty
		if(isset($data['tmp_locations_child']) && !empty($data['tmp_locations_child'])){
			
			// Add child to locations array	
			$locations_array[] = $data['tmp_locations_child'];
			
			// Check if sub-child field is empty
			if(isset($data['tmp_locations_sub_child']) && !empty($data['tmp_locations_sub_child'])){	
			
				// Add sub-child to locations array				
				$locations_array[] = $data['tmp_locations_sub_child'];
			
			}
			
		}

    	update_post_meta( $post_id, 'listing_locations', $locations_array);

    	$inventorwp_addon->log( '- Importing Location');

	} else {

   		// Nothing here...
   	}

	if (function_exists('is_plugin_active')) {
		if (is_plugin_active('inventor-statistics/inventor-statistics.php')) {
	
			// Update post total views

			$field = 'inventor_statistics_post_total_views';

			// Check if post total views field is allowed to be updated in import settings or it's a new post
			if ( empty( $article['ID'] ) || $inventorwp_addon->can_update_meta( $field, $import_options ) ){
				if( empty( $data['inventor_statistics_post_total_views'] ) ) {
					update_post_meta( $post_id, 'inventor_statistics_post_total_views', "0" );
				} elseif( !is_numeric( $data['inventor_statistics_post_total_views'] ) ) {
					update_post_meta( $post_id, 'inventor_statistics_post_total_views', "0" );
				} else {
					update_post_meta( $post_id, 'inventor_statistics_post_total_views', $data['inventor_statistics_post_total_views'] );
				}

			}
	
		}
	}

   	//
    // Import FAQ
    //

	$field = 'listing_faq';

	if (
	    	// It's a new post
	    	(empty($article['ID']) 	
	    	// The question field has a value
	    	&& isset($data['listing_question']) 
	    	&& !empty($data['listing_question'])) 
	    	|| 
	    	// Updating the post_meta is allowed
	    	($inventorwp_addon->can_update_meta($field, $import_options) 
	    	// The question field has a value
	    	&& isset($data['listing_question']) 
	    	&& !empty($data['listing_question']))
	    ){

		    // Set variables for the seperator, questions and answers 
		    $seperator = $data['faq_seperator_character'];

			// Explode comma seperated questions and answers into arrays based on the seperator
			$questions = explode($seperator, $data['listing_question']);
			$answers = explode($seperator, $data['listing_answer']);

			// Count number of questions to determine how many Q/A arrays to create. This will be the basis of structuring the multi-dimensional array of menu items.
			$question_count = count($questions);

			// Create counter
			$counter = 0;

			// Create empty faq array
			$faq = array();

			// Create a QA array for every question
			foreach($questions as $question){

				// Run until the amount of QA arrays equals the amount of questions
				if($counter <= $question_count){

					// Create an array in faq array for each set of QA, set the question and set the answer with keys
					$faq[$counter] = array(
							'listing_question'	 => $question,
							'listing_answer'	 => ''
							);
					$counter++; // Add to counter
				}
			}

			// Reset counter
			$counter = 0;

			// Set the answers in the each array
			foreach($answers as $answer){

				// Run until the amount of QA arrays updated equals the amount of questions
				if($counter <= $question_count){

					// Set answer in each QA array, looping through the FAQ array based on numerical keys
					$faq[$counter]['listing_answer'] = $answer;
					$counter++; // Add to counter
				}
			}

			update_post_meta( $post_id, $field, $faq );
			
			$inventorwp_addon->log( '- Importing FAQs');

	} elseif(
			// It's a new post
	    	(empty($article['ID']) 	
	    	// The question field has a value
	    	&& !isset($data['listing_question']) 
	    	&& empty($data['listing_question'])
	    	&& isset($data['listing_answer']) 
	    	&& !empty($data['listing_answer'])) 
	    	|| 
	    	// Updating the post_meta is allowed
	    	($inventorwp_addon->can_update_meta($field, $import_options) 
	    	// The question field has a value
	    	&& !isset($data['listing_question']) 
	    	&& empty($data['listing_question'])
	    	&& isset($data['listing_answer']) 
	    	&& !empty($data['listing_answer']))
	    	){	
			
			$inventorwp_addon->log( '- FAQs has been skipped: The question field was left blank.');
	}

	//
	// Import Food Menu
	//

    if (
    	// It's a new post
    	(empty($article['ID']) 	
    	// The field has a value
    	&& isset($data['listing_food_menu_title']) 
    	&& !empty($data['listing_food_menu_title'])) 
    	|| 
    	// Updating the post_meta is allowed
    	($inventorwp_addon->can_update_meta('listing_food_menu_group', $import_options) 
    	// The field has a value
    	&& isset($data['listing_food_menu_title']) 
    	&& !empty($data['listing_food_menu_title']))
    ){

		// Set variables for the seperator, and menu values
	    $seperator = $data['menu_seperator_character'];

	    // Explode comma seperated values into arrays based on seperator
		$menu_titles = explode($seperator, $data['listing_food_menu_title']);
		$menu_descriptions = explode($seperator, $data['listing_food_menu_description']);
		$menu_prices = explode($seperator, $data['listing_food_menu_price']);
		$menu_serving_days = explode($seperator, $data['listing_food_menu_serving']);
		$menu_specialities = explode($seperator, $data['listing_food_menu_speciality']);
		$menu_photos = explode($seperator, $data['listing_food_menu_photo']);

		// Count the amount of titles in menu titles array to figure out how many menu items arrays will be needed. This will be the basis of structuring the multi-dimensional array of all menu items.
		$menu_title_count = count($menu_titles);

		// Set counter to zero
		$counter = 0;

		// Create empty menu items array
		$menu_items = array();

		// Create the structure of the menu items array, creating an array for each menu item based on the amount of titles and set the titles
		foreach($menu_titles as $title){

			// Add menu item array until the number of arrays matches the number of menu item titles
			if($counter <= $menu_title_count){

				// Create and set intial structure of the menu item arrays
				$menu_items[$counter] = array(
						'listing_food_menu_title'		 => $title,
						'listing_food_menu_description'	 => '',
						'listing_food_menu_price'		 => '',
						'listing_food_menu_serving'		 => '',
						'listing_food_menu_speciality'	 => '',
						'listing_food_menu_photo_id'	 => '',
						'listing_food_menu_photo'		 => ''
						);

				// Add to the counter
				$counter++;
			}
		}

		// Food Menu Description import

		// Reset the counter
		$counter = 0;

		// Loop through each description in the master description array and set each one as the description for each menu item
		foreach($menu_descriptions as $description){

			// Update description values based on menu items number key until the number of arrays matches the number of menu item titles
			if($counter <= $menu_title_count){

				// Set the description value
				$menu_items[$counter]['listing_food_menu_description'] = $description;

				// Add to the counter
				$counter++;
			}
		}

		// Menu Price import

		// Reset counter
		$counter = 0;

		// Loop through each price in the master price array and set each one as the price for each menu item
		foreach($menu_prices as $price){

			// Update price values based on menu items number key until the number of arrays matches the number of menu item titles
			if($counter <= $menu_title_count){

				// Set the price value
				$menu_items[$counter]['listing_food_menu_price'] = $price;

				// Add to the counter
				$counter++;
			}
		}

		// Menu Serving Days import

		// Reset counter
		$counter = 0;

		// Loop through each serving day in the master day array and set each one as the day for each menu item
		foreach($menu_serving_days as $day){

			// Update serving day values based on menu items number key until the number of arrays matches the number of menu item titles
			if($counter <= $menu_title_count){

				// Convert the day date to be unix time and set the day value
				$menu_items[$counter]['listing_food_menu_serving'] = date( 'U', strtotime($day));

				// Add to the counter
				$counter++;
			}
		}

		// Menu Speciality

		// Reset the counter
		$counter = 0;

		// Loop through each specialty in the master specialties array and set each one as the specialty value for each menu item
		foreach($menu_specialities as $speciality){

			// Update speciality values based on menu items number key until the number of arrays matches the number of menu item titles
			if($counter <= $menu_title_count){

				// Convert 'yes' to 'on' and 'no' to blank before setting the specialty value
				$menu_items[$counter]['listing_food_menu_speciality'] = wpai_util_map('yes,no','on,', $speciality); 
				$counter++;
			}
		}

		// Menu item photos - Multi-Image import for standard import field

		// Reset the counter
		$counter = 0;

		// Set the field to be used
		$field = 'listing_food_menu_photo';

		// Check if the attachment_id has been set previously
		if ( empty($data[$field]['attachment_id']) ){

			// Explode the comma seperated image urls into an array
			$urls = explode(",", $data[$field]['image_url_or_path']);

			// Check to make sure the array has values
			if ( !empty($urls) ){

				// Loop through each URL
				foreach ($urls as $url) {

					// Upload the image from each source URL, store ID in variable
					$uploaded_image = PMXI_API::upload_image($post_id, $url, $data[$field]['download'], $logger, true);

					// Get the uploaded image URL
					$uploaded_image_url = wp_get_attachment_image_src($uploaded_image, 'full', false);

					// Update photo ID and photo URL values based on menu items number key until the number of arrays matches the number of menu item titles
					if($counter <= $menu_title_count){

						// Set the photo ID value to the newly created attachment ID
						$menu_items[$counter]['listing_food_menu_photo_id'] = $uploaded_image;

						// Set the photo url value to the newly created attachment URL
						$menu_items[$counter]['listing_food_menu_photo'] = $uploaded_image_url[0];

						// Add to counter
						$counter++;
					}
				}
			}

		}

		// Update the post meta with the finished array 
		update_post_meta( $post_id, 'listing_food_menu_group', $menu_items );

		$inventorwp_addon->log( '- Importing Menu');

	} 


	//
	// Import Hybrid Taxonomy/Post Meta fields (updating the final post_meta and tmp fields)
	//

	# Need to check is you can update taxonomy and add logging

	// Single Value - Car Model
	$field = 'listing_car_model';

	// Check if field is allowed to be updated in import settings and whether it's empty or it's a new post
    if (
    	// It's a new post
    	(empty($article['ID']) 	
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field])) 
    	|| 
    	// Updating the post_meta is allowed
    	($inventorwp_addon->can_update_meta($field, $import_options) 
    	&& $inventorwp_addon->can_update_taxonomy('car_models', $import_options)
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field]))
    ){

        update_post_meta( $post_id, $field, $data[$field] );
        update_post_meta( $post_id, str_replace('listing','tmp', $field), $data[$field] );

        // Log custom field import
        $inventorwp_addon->log( '- Importing custom field `' . $field . '` : \'' . $data[$field] . '\'');

    } elseif (// Updating the post_meta is allowed
    	$inventorwp_addon->can_update_meta($field, $import_options) 
    	&& $inventorwp_addon->can_update_taxonomy('car_models', $import_options) == false 
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field])
    ){
    	$inventorwp_addon->log( '- Custom field `car_model` has been skipped. Updating `car_models` taxonomy is disallowed in the import settings.');
    }

    //
	// Update Listing Categories post_meta
	//
    
	$field = 'listing_listing_category';

	if (
    	// It's a new post
    	(empty($article['ID']) 	
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field])) 
    	|| 
    	// Updating the post_meta is allowed
    	($inventorwp_addon->can_update_meta($field, $import_options)
    	// Updating taxonomy is allowed
    	&& $inventorwp_addon->can_update_taxonomy('listing_categories', $import_options)
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field]))
    ){

		// Get values from field and create an array based on the seperator field
		$cat_list = $data[$field];
		$seperator = $data['listing_categories_seperator_character'];
		$listing_categories = explode($seperator, $cat_list);

		// Save taxonomy terms in temporary custom field
		update_post_meta( $post_id, 'tmp_listing_listing_category', $listing_categories );

	} elseif (
		// Updating the post_meta is allowed
    	($inventorwp_addon->can_update_meta($field, $import_options)
    	// Updating taxonomy is disallowed
    	&& $inventorwp_addon->can_update_taxonomy('listing_categories', $import_options) == false
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field]))) {
		
		$inventorwp_addon->log( '- Listing Categories has been skipped. Updating `listing_categories` taxonomy is disallowed in the import settings.');
	}

	//
	// Update hybrid taxonomy/post meta into temp fields
	//

	// Array keys are the corresponding taxonomies
	$fields = array (
			'car_body_styles' 		=> 'tmp_car_body_style',
			'dating_groups' 		=> 'tmp_dating_group',
			'dating_interests' 		=> 'tmp_dating_interest',
			'colors' 				=> 'tmp_color',
			'travel_activities' 	=> 'tmp_travel_activity'
			);

	foreach($fields as $key => $field){
		if (// It's a new post
    	(empty($article['ID']) 	
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field])) 
    	|| 
    	// Updating the post_meta is allowed
    	($inventorwp_addon->can_update_meta($field, $import_options)
    	// Updating taxonomy is allowed
    	&& $inventorwp_addon->can_update_taxonomy($key, $import_options)
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field]))
    	){

		$hybrid_terms = explode(",", $data[$field]);
		update_post_meta( $post_id, $field, $hybrid_terms );

		} elseif (
		// Updating the post_meta is allowed
    	($inventorwp_addon->can_update_meta($field, $import_options)
    	// Updating taxonomy is disallowed
    	&& $inventorwp_addon->can_update_taxonomy('listing_categories', $import_options) == false
    	// The field has a value
    	&& isset($data[$field]) 
    	&& !empty($data[$field]))) {
		
		$inventorwp_addon->log( '- Custom field ' . str_replace('tmp', 'listing', $field) . ' has been skipped. Updating `' . $key . '` taxonomy is disallowed in the import settings.');
		}
	}


	//
	// Import Car colors post_meta
	//

	// Interior and Exterior car colors share the same taxonomy but also store their values in post_meta arrays.

	$fields = array (
			'tmp_car_color_interior',
			'tmp_car_color_exterior'
			);

	foreach($fields as $field){
		if (
	    	// It's a new post
	    	(empty($article['ID']) 	
	    	// The field has a value
	    	&& isset($data[$field]) 
	    	&& !empty($data[$field])) 
	    	|| 
	    	// Updating the post_meta is allowed (listing_ is how the post_meta begins so it has to be replaced)
	    	($inventorwp_addon->can_update_meta(str_replace('tmp_', 'listing_', $field), $import_options) 
	    	// Updating the colors taxonomy is allowed
	    	&& $inventorwp_addon->can_update_taxonomy('colors', $import_options)
	    	// The field has a value
	    	&& isset($data[$field]) 
	    	&& !empty($data[$field]))
    	){

			// Create an array for each field
			$car_color_terms = explode(",", $data[$field]);

			// Sort the array alphabetically
			sort($car_color_terms);

			// Create an array for the post_meta that mimics the slugs of the soon to be created terms. We have to fake slugs because we can't pull the terms to update the post_meta in the inventorwp_post_saved() function because the terms from the two fields will be mashed together.
			$fake_slugs = array();
			foreach ($car_color_terms as $term) {

				// Make lowercase, replace spaces with dashes, etc to mimic slugs
				$fake_slugs[] = sanitize_title($term);
			}

			// Update the temporary fields in the post_meta for delivery into the terms function
			update_post_meta( $post_id, $field, $car_color_terms );

			// Update the actual fields in the post_meta
			update_post_meta( $post_id, str_replace('tmp_', 'listing_', $field), $fake_slugs);

		} elseif (
			// Updating post meta is allowed
			$inventorwp_addon->can_update_meta(str_replace('tmp_', 'listing_', $field), $import_options) 
	    	// Updating the colors taxonomy is disallowed
	    	&& $inventorwp_addon->can_update_taxonomy('colors', $import_options) == false 
	    	// The field has a value
	    	&& isset($data[$field]) 
	    	&& !empty($data[$field])
			) {
			
			$inventorwp_addon->log( '- Custom field ' . str_replace('tmp_', 'listing_', $field) . ' has been skipped. Updating `colors` taxonomy is disallowed in the import settings.');
		}
	}

}

/*
* Gallery Import Function
**/

// Add Gallery image import box
$inventorwp_addon->import_images( 'inventorwp_addon_listing_gallery', 'Gallery' );

function inventorwp_addon_listing_gallery( $post_id, $attachment_id, $image_filepath, $import_options ) {

    $current_images = get_post_meta( $post_id, 'listing_gallery', true );

	$images_array = array();
		if ( ! empty( $current_images ) ) {
			foreach ( $current_images as $id => $url ) {
			
				$images_array[$id] = $url;
			}
		}
	$image_url = wp_get_attachment_url( $attachment_id );
	$images_array[$attachment_id] = $image_url;

	update_post_meta( $post_id, 'listing_gallery', $images_array );
	
}

/* 
* Taxonomy Import/Update & update the title and description based post_meta
**/

// Link add-on to pmxi_saved_post action
$inventorwp_addon->set_post_saved_function('inventorwp_post_saved');

function inventorwp_post_saved( $post_id, $import, $logger ){

	//
	// Assigning Title and Description based post_meta
	//
	
	update_post_meta( $post_id, 'listing_title', get_post_field( 'post_title', $post_id));
	update_post_meta( $post_id, 'listing_description', get_post_field( 'post_content', $post_id, 'db'));


	//
	// Taxonomy with NO post_meta
	//

	// Create array of the temporary fields, their values and the taxonomy they belong to

	$temp_to_tax = array(
			'car_models'			=> array(
					'tmp_car_model',
					get_post_meta($post_id, 'tmp_car_model', true)
					),
			'car_engine_types'		=> array( 
					'tmp_engine_type',
					get_post_meta( $post_id, 'tmp_engine_type', true )
					),
		    'car_transmissions' 	=> array(
					'tmp_transmission',
					get_post_meta( $post_id, 'tmp_transmission', true )
					),
		    'dating_statuses'		=> array( 
					'tmp_status',
					get_post_meta( $post_id, 'tmp_status', true )
					),
		    'colors' 				=> array( 
					'tmp_eye_color',
					get_post_meta( $post_id, 'tmp_eye_color', true )
					),

		    'education_levels'		=> array( 
					'tmp_education_level',
					get_post_meta( $post_id, 'tmp_education_level', true )
					),
		    'education_subjects'	=> array( 
					'tmp_education_subject',
					get_post_meta( $post_id, 'tmp_education_subject', true )
					),
		    'event_types'			=> array( 
					'tmp_event_type',
					get_post_meta( $post_id, 'tmp_event_type', true )
					),
		    'food_kinds'			=> array( 
					'tmp_food_kind',
					get_post_meta( $post_id, 'tmp_food_kind', true )
					),
		    'hotel_classes'			=> array( 
					'tmp_hotel_class',
					get_post_meta( $post_id, 'tmp_hotel_class', true )
					),
		    'pet_animals'			=> array( 
					'tmp_animal',
					get_post_meta( $post_id, 'tmp_animal', true )
					),
		    'shopping_categories'	=> array( 
					'tmp_shopping_category',
					get_post_meta( $post_id, 'tmp_shopping_category', true )
					)
			);

	// Loop through array and set the taxonomy for the post
	foreach($temp_to_tax as $key => $tax){

		// Make sure there is a value before updating terms
		if(isset($tax[1]) && !empty($tax[1])){

			// Set the taxonomy
			wp_set_object_terms( $post_id, $tax[1], $key);	

		}

		// Delete temporary meta key
		delete_post_meta($post_id, $tax[0]);

	}

	//
	// Taxonomy WITH post meta
	//

	//
	// Listing categories
	//

	$listing_categories = get_post_meta( $post_id, 'tmp_listing_listing_category', true );

	if (isset($listing_categories) && !empty($listing_categories)){

	// Set listing category terms
	wp_set_object_terms( $post_id, $listing_categories, 'listing_categories');

	// Get the newly create listing category terms in an array as slugs in alphabetical order
	$args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'slugs');
	$term_meta = wp_get_object_terms( $post_id, 'listing_categories', $args );

	// Update the post meta with the retrieved slugs
	update_post_meta( $post_id, 'listing_listing_category', $term_meta );

	// Delete temporary meta key
	delete_post_meta($post_id, 'tmp_listing_listing_category');	

	}

	//
	// Location categories
	//

	// Get the tmp fields post_meta values
	$locations_cat_parent = get_post_meta( $post_id, 'tmp_locations_parent', true );
	$locations_cat_child = get_post_meta( $post_id, 'tmp_locations_child', true );
	$locations_cat_sub_child = get_post_meta( $post_id, 'tmp_locations_sub_child', true );

	// Set the location terms

	// Check if parent location is empty
	if(isset($locations_cat_parent) && !empty($locations_cat_parent)) {

		// Add parent location value to taxonomy
		wp_set_object_terms( $post_id, $locations_cat_parent, 'locations', true);

		// Get the newly created term by name from tmp field value
		$locations_cat_parent_ID = get_term_by('name', $locations_cat_parent, 'locations', ARRAY_A);

		// Check if child is empty
		if(isset($locations_cat_child) && !empty($locations_cat_child)){

			// Add child location value to taxonomy
			wp_set_object_terms( $post_id, $locations_cat_child, 'locations', true);

			// Get the newly created term by name from the tmp field value
			$locations_cat_child_ID = get_term_by('name', $locations_cat_child, 'locations', ARRAY_A);

			// Update the newly created term to set it's parent to the ID of the parent location term
			wp_update_term( $locations_cat_child_ID['term_id'], 'locations', array('parent' => $locations_cat_parent_ID['term_id']) );

			// Check if sub-child is empty
			if(isset($locations_cat_sub_child) && !empty($locations_cat_sub_child)) {

				// Add sub child location value to taxonomy
				wp_set_object_terms( $post_id, $locations_cat_sub_child, 'locations', true);

				// Get the newly created term by name from the tmp field value
				$locations_cat_sub_child_ID = get_term_by('name', $locations_cat_sub_child, 'locations', ARRAY_A);

				// Update the newly created term to set it's parent to the ID of the child location term
				wp_update_term( $locations_cat_sub_child_ID['term_id'], 'locations', array('parent' => $locations_cat_child_ID['term_id']));
			}
		}
		// Allow for term counts to be generated
		wp_defer_term_counting(false);
	}

	// Delete temporary fields
	delete_post_meta($post_id, 'tmp_locations_parent');
	delete_post_meta($post_id, 'tmp_locations_child');
	delete_post_meta($post_id, 'tmp_locations_sub_child');

	//
	// Car colors...
	//

	// Get the values of the temporary post_meta
	$car_color_int = get_post_meta( $post_id, 'tmp_car_color_interior', true );
	$car_color_ext = get_post_meta( $post_id, 'tmp_car_color_exterior', true );

	// Check if value exists
	if(isset($car_color_int) && !empty($car_color_int)){ 

	// Set terms
	wp_set_object_terms( $post_id, $car_color_int, 'colors');

	// Delete temporary meta key
	delete_post_meta($post_id, 'tmp_car_color_interior');

	}

	if(isset($car_color_ext) && !empty($car_color_ext)){ 

	// Set terms with append
	wp_set_object_terms( $post_id, $car_color_ext, 'colors', true);

	// Delete temporary meta key
	delete_post_meta($post_id, 'tmp_car_color_exterior');

	}

	//
	// Other taxonomy with post meta
	//

	// Create an array with taxonomy as key, the tmp post_meta key and value
	$hybrid_temp_to_tax = array(
			'car_body_styles'	=> array(
					'tmp_car_body_style',
					get_post_meta( $post_id, 'tmp_car_body_style', true )
					),
			'colors'	=> array(
					'tmp_color',
					get_post_meta( $post_id, 'tmp_color', true )
					),
			'dating_groups'	=> array(
					'tmp_dating_group',
					get_post_meta( $post_id, 'tmp_dating_group', true )
					),
			'dating_interests'	=> array(
					'tmp_dating_interest',
					get_post_meta( $post_id, 'tmp_dating_interest', true )
					),
			'travel_activities'	=> array(
					'tmp_travel_activity',
					get_post_meta( $post_id, 'tmp_travel_activity', true )
					),
			);

	// Loop through array and set the taxonomy for the post
	foreach($hybrid_temp_to_tax as $key => $tax){

		// Make sure there is a value before updating terms
		if(isset($tax[1]) && !empty($tax[1])){

			// Set the taxonomy
			wp_set_object_terms( $post_id, $tax[1], $key);

			// Update post meta with term slugs in alphabetical order
			$args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'slugs');
			$term_meta = wp_get_object_terms( $post_id, $key, $args );
			update_post_meta( $post_id, str_replace('tmp_','listing_', $tax[0]), $term_meta );

		}

		// Delete temporary meta key
		delete_post_meta($post_id, $tax[0]);

	} 


}


/*
* Hide Taxonomy Section
*/

add_filter('pmxi_visible_template_sections', 'wpai_pmxi_visible_template_sections', 10, 2);

function wpai_pmxi_visible_template_sections( $sections, $post_type ){
	if ( in_array($post_type, array( "business","car","dating","education","event","food","hotel", "pet","shopping","travel" )) ){
		foreach ($sections as $key => $section) {
			if ( $section == 'taxonomies'){
				unset($sections[$key]);
			}
		}
	}
	return $sections;
}


/**
* Run & admin notice conditions
*/

// Run with Inventor Plugin and these post types only
$inventorwp_addon->run(
	array(
		"plugins" => array( "inventor/inventor.php" ),
		"post_types" => array( "business","car","dating","education","event","food","hotel", "pet","shopping","travel" )
	)
);

// Admin notice if WP All Import or Inventor WP is not enabled
$inventorwp_addon->admin_notice(
    'The Inventor WP Add-On requires WP All Import <a href="http://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=inventorwp" target="_blank">Pro</a> or <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a>, and the <a href="http://inventorwp.com/en/">Inventor WP</a> plugin.',
    array( 
        "plugins" => array( "inventor/inventor.php" ),
) );

// Fix prices
function inventorwp_addon_fix_price( $price = null ) {
   		$check = substr( $price, -3, 3 );
   		if ( stristr( $check, "," ) ) {
   			$price = str_replace( array( ".", "," ), array( "", "." ), $price );
   		} else if ( stristr( $check, "." ) ) {
   			$price = str_replace( ",", "", $price );
   		} else {
   			$price = str_replace( array( ",", "." ), array( "", "" ), $price );
   		}

   		return $price;
}

/* 
* Load Stylesheet 
*/

function inventorwp_addon_style_load($hook) {
    wp_register_style( 'inventorwp-addon-stylesheet', plugins_url( 'static/css/admin.css', __FILE__ ), false, '1.0.0' );
    wp_enqueue_style( 'inventorwp-addon-stylesheet' );
}

add_action( 'admin_enqueue_scripts', 'inventorwp_addon_style_load' );

if ( ! function_exists( 'wpai_inventor_addon_fix_hours_in_data' ) ) {
	function wpai_inventor_addon_fix_hours_in_data( $data = array(), $fields = array() ) {
		if ( ! empty( $data ) && ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				if ( ! array_key_exists( $field, $data ) ) {
					$data[ $field ] = null;
				}
			}
		}
		return $data; 
	}
}
