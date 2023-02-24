<?php

require_once(ATT_PATH . 'includes/vendor/RationalOptionPages.php');

/* Add Fleet Menu */
function atb__register_my_custom_submenu_page() {
	add_submenu_page(
		'plugin-settings-atb',
		'Bookings',
		'Bookings',
		'manage_options',
		'edit.php?post_type=payment&payment_status=paid&booking_status=processing'
	);
	add_submenu_page(
		'plugin-settings-atb',
		'Fleet (Vehicles)',
		'Fleet (Vehicles)',
		'manage_options',
		'edit.php?post_type=fleet'
	);
	add_submenu_page(
		'plugin-settings-atb',
		'Rates',
		'Rates',
		'manage_options',
		'edit.php?post_type=pricing'
	);
	add_submenu_page(
		'plugin-settings-atb',
		'Min. Booking Time',
		'Min. Booking Time',
		'manage_options',
		'edit.php?post_type=min-wait-time'
	);
	add_submenu_page(
		'plugin-settings-atb',
		'Suppliers Portal',
		'Suppliers Portal',
		'manage_options',
		'edit.php?post_type=suppliers_portal'
	);
	add_submenu_page(
		'plugin-settings-atb',
		'AutoCab Settings',
		'AutoCab Settings',
		'manage_options',
		'admin.php?page=8550113C-2584-48D3-B6FE-95F1FB247802'
	);
}
add_action('admin_menu', 'atb__register_my_custom_submenu_page');

$pages = array(
	'plugin-settings-atb'	=> array(
		'page_title'	=> __( 'ATB Settings', 'chauffeur' ),
		'menu_slug'	=> 'plugin-settings-atb',
		'icon_url'	=> 'dashicons-admin-tools',
		'position'	=> 2,
		'subpages'		=> array(
			'email-templates-settings-att'	=> array(
				'page_title'	=> __( 'Email Templates Settings', 'chauffeur' ),
				'menu_title'	=> __( 'Email Templates', 'chauffeur' ),
				'menu_slug'	=> 'email-templates-settings-att',
				'sections'		=> array(
					'section-one'	=> array(
						'title'			=> 'Booking Successful Email (to USER)',
						'fields'		=> array(
							'booking_email_success_subject'		=> array(
								'title'			=> __( 'Email Subject', 'chauffeur' ),
								'id'			=> 'booking_email_success_subject',
							),
							'booking_email_success_user'	=> array(
								'title'			=> __( 'Booking Successful Email (to USER):', 'chauffeur' ),
								'type'			=> 'wp_editor',
								'id'			=> 'booking_email_success_user',
								'media_buttons' => false,
								'autop'			=> false,
								'text'			=> 'This email will trigger when a new order is placed by an user.',
								'value'			=> '',
							),
						)
					),
					'section-two'	=> array(
						'title'			=> 'Booking Cancelled Email (to USER)',
						'fields'		=> array(
							'booking_email_cancelled_subject'		=> array(
								'title'			=> __( 'Email Subject', 'chauffeur' ),
								'id'			=> 'booking_email_cancelled_subject',
							),
							'booking_email_cancelled_user'	=> array(
								'title'			=> __( 'Booking Cancelled Email (to USER):', 'chauffeur' ),
								'type'			=> 'wp_editor',
								'id'			=> 'booking_email_cancelled_user',
								'media_buttons' => false,
								'autop'			=> false,
								'text'			=> 'This email will trigger when an order is Cancelled by Admin.',
								'value'			=> '',
							),
						)
					),
					'section-three'	=> array(
						'title'			=> 'Booking Cancelled and Money Refunded Email (to USER):',
						'fields'		=> array(
							'booking_email_cancelled_refunded_subject'		=> array(
								'title'			=> __( 'Email Subject', 'chauffeur' ),
								'id'			=> 'booking_email_cancelled_refunded_subject',
							),
							'booking_email_cancelled_refunded_user'	=> array(
								'title'			=> __( 'Booking Cancelled and Money Refunded Email (to USER):', 'chauffeur' ),
								'type'			=> 'wp_editor',
								'id'			=> 'booking_email_cancelled_refunded_user',
								'media_buttons' => false,
								'autop'			=> false,
								'text'			=> 'This email will trigger when an order is Cancelled and Money Refunded by Admin.',
								'value'			=> '',
							)
						)
					),
					'section-four'	=> array(
						'title'			=> 'Booking Completed Email (to USER):',
						'fields'		=> array(
							'booking_email_completed_subject'		=> array(
								'title'			=> __( 'Email Subject', 'chauffeur' ),
								'id'			=> 'booking_email_completed_subject',
							),
							'booking_email_completed_user'	=> array(
								'title'			=> __( 'Booking Completed Email (to USER):', 'chauffeur' ),
								'type'			=> 'wp_editor',
								'id'			=> 'booking_email_completed_user',
								'media_buttons' => false,
								'autop'			=> false,
								'text'			=> 'This email will trigger when an order is Marked as Completed by Admin.',
								'value'			=> '',
							)
						)
					),
					'section-six'	=> array(
						'title'			=> 'Suppliers Registration Email (document verification pending):',
						'fields'		=> array(
							'suppliers_submission_verifiation_pending_subject'		=> array(
								'title'			=> __( 'Email Subject', 'chauffeur' ),
								'id'			=> 'suppliers_submission_verifiation_pending_subject',
							),
							'suppliers_submission_verifiation_pending_email_body'	=> array(
								'title'			=> __( 'Suppliers Registration Email:', 'chauffeur' ),
								'type'			=> 'wp_editor',
								'id'			=> 'suppliers_submission_verifiation_pending_email_body',
								'media_buttons' => false,
								'autop'			=> false,
								'text'			=> 'This email will trigger when a supplier is just registered.',
								'value'			=> '',
							)
						)
					),
					'section-five'	=> array(
						'title'			=> 'Suppliers Submission Email (on new submission):',
						'fields'		=> array(
							'suppliers_submission_email_subject'		=> array(
								'title'			=> __( 'Email Subject', 'chauffeur' ),
								'id'			=> 'suppliers_submission_email_subject',
							),
							'suppliers_submission_email_body'	=> array(
								'title'			=> __( 'Suppliers Submission Email:', 'chauffeur' ),
								'type'			=> 'wp_editor',
								'id'			=> 'suppliers_submission_email_body',
								'media_buttons' => false,
								'autop'			=> false,
								'text'			=> 'This email will trigger when a supplier submit price against a booking.',
								'value'			=> '',
							)
						)
					),
					'section-seven'	=> array(
						'title'			=> 'Suppliers Registration Email (document verification completed):',
						'fields'		=> array(
							'suppliers_submission_verifiation_completed_subject'		=> array(
								'title'			=> __( 'Email Subject', 'chauffeur' ),
								'id'			=> 'suppliers_submission_verifiation_completed_subject',
							),
							'suppliers_submission_verifiation_completed_email_body'	=> array(
								'title'			=> __( 'Suppliers Verification Completed Email:', 'chauffeur' ),
								'type'			=> 'wp_editor',
								'id'			=> 'suppliers_submission_verifiation_completed_email_body',
								'media_buttons' => false,
								'autop'			=> false,
								'text'			=> 'This email will trigger when a supplier\'s document is verified by Admin.',
								'value'			=> '',
							)
						)
					),
					'section-eight'	=> array(
						'title'			=> 'Suppliers Registration Email (document verification rejected):',
						'fields'		=> array(
							'suppliers_submission_verifiation_rejected_subject'		=> array(
								'title'			=> __( 'Email Subject', 'chauffeur' ),
								'id'			=> 'suppliers_submission_verifiation_rejected_subject',
							),
							'suppliers_submission_verifiation_rejected_email_body'	=> array(
								'title'			=> __( 'Suppliers Verification Rejected Email:', 'chauffeur' ),
								'type'			=> 'wp_editor',
								'id'			=> 'suppliers_submission_verifiation_rejected_email_body',
								'media_buttons' => false,
								'autop'			=> false,
								'text'			=> 'This email will trigger when a supplier\'s document is rejected by Admin.',
								'value'			=> '',
							)
						)
					),
					'section-nine'	=> array(
						'title'			=> 'Suppliers Notification Email (on new order):',
						'fields'		=> array(
							'suppliers_submission_order_notification_subject'		=> array(
								'title'			=> __( 'Email Subject', 'chauffeur' ),
								'id'			=> 'suppliers_submission_order_notification_subject',
							),
							'suppliers_submission_order_notification_body'	=> array(
								'title'			=> __( 'Suppliers Notification Email:', 'chauffeur' ),
								'type'			=> 'wp_editor',
								'id'			=> 'suppliers_submission_order_notification_body',
								'media_buttons' => false,
								'autop'			=> false,
								'text'			=> 'This email will trigger when a new order is recevied. Emails will be sent to the Verified Suppliers only.',
								'value'			=> '',
							)
						)
					),
					'section-ten'	=> array(
						'title'			=> 'Suppliers Bid Approved by Admin:',
						'fields'		=> array(
							'suppliers_submission_order_approved_subject'		=> array(
								'title'			=> __( 'Email Subject', 'chauffeur' ),
								'id'			=> 'suppliers_submission_order_approved_subject',
							),
							'suppliers_submission_order_approved_body'	=> array(
								'title'			=> __( 'Suppliers Bid Approved Email:', 'chauffeur' ),
								'type'			=> 'wp_editor',
								'id'			=> 'suppliers_submission_order_approved_body',
								'media_buttons' => false,
								'autop'			=> false,
								'text'			=> 'This email will trigger when a supplier bid approved by admin.',
								'value'			=> '',
							)
						)
					),
					'section-eleven'	=> array(
						'title'			=> 'Suppliers Bid Cancelled by Admin:',
						'fields'		=> array(
							'suppliers_submission_order_cancelled_subject'		=> array(
								'title'			=> __( 'Email Subject', 'chauffeur' ),
								'id'			=> 'suppliers_submission_order_cancelled_subject',
							),
							'suppliers_submission_order_cancelled_body'	=> array(
								'title'			=> __( 'Suppliers Bid Cancelled Email:', 'chauffeur' ),
								'type'			=> 'wp_editor',
								'id'			=> 'suppliers_submission_order_cancelled_body',
								'media_buttons' => false,
								'autop'			=> false,
								'text'			=> 'This email will trigger when a supplier bid cancelled by admin.',
								'value'			=> '',
							)
						)
					),
					'section-twelve'	=> array(
						'title'			=> 'Supplier Confirmation Email:',
						'fields'		=> array(
							'suppliers_confirmation_subject'		=> array(
								'title'			=> __( 'Email Subject', 'chauffeur' ),
								'id'			=> 'suppliers_confirmation_subject',
							),
							'suppliers_confirmation_body'	=> array(
								'title'			=> __( 'Suppliers Bid Cancelled Email:', 'chauffeur' ),
								'type'			=> 'wp_editor',
								'id'			=> 'suppliers_confirmation_body',
								'media_buttons' => false,
								'autop'			=> false,
								'text'			=> 'This email will send to Customer when a supplier bid is confirmed by admin.',
								'value'			=> '',
							)
						)
					),
				)
			),
			'settings-page-atb'	=> array(
				'page_title'	=> __( 'General Settings', 'chauffeur' ),
				'menu_title'	=> __( 'General Settings', 'chauffeur' ),
				'menu_slug'	=> 'settings-page-atb',
				'sections'		=> array(
					'section-general-options'	=> array(
						'title'			=> __( ' ', 'chauffeur' ),
						'fields'		=> array(
							'maps-api-key'		=> array(
								'title'			=> __( 'Google Maps API Key (Client Side)', 'chauffeur' ),
								'placeholder'	=> __( 'e.g.: AIzaSyBxXDkCSBPquzn_3-Ddzkm8KeVc11P2xxx' ),
								'value'			=> 'AIzaSyBxXDkCSBPquzn_3-Ddzkm8KeVc11P2YqQ',
								'id'			=> 'google_maps_api_key',
							),
							'maps-api-key-2'		=> array(
								'title'			=> __( 'Google Maps API Key (Server Side)', 'chauffeur' ),
								'placeholder'	=> __( 'e.g.: AIzaSyBxXDkCSBPquzn_3-Ddzkm8KeVc11P2xxx' ),
								'value'			=> 'AIzaSyDgA9GlIKTNDlAOOkICgstDbJTIyJJ7N4o',
								'id'			=> 'google_maps_api_key_2',
							),
							'stripe-secret-key'		=> array(
								'title'			=> __( 'Stripe Secret Key', 'chauffeur' ),
								'placeholder'	=> __( 'e.g.: sk_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' ),
								'value'			=> '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',
							),
							'stripe-pub-key'		=> array(
								'title'			=> __( 'Stripe Publishable Key', 'chauffeur' ),
								'placeholder'	=> __( 'e.g.: pk_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' ),
								'value'			=> 'pk_live_nGU5sOxhqGlnq9Zy8e3S10yT',
							),
							'stripe-currency'		=> array(
								'title'			=> __( 'Stripe Currency', 'chauffeur' ),
								'type'			=> 'select',
								'value'			=> 'GBP',
								'choices'		=> array(
									'GBP'	=> __( 'GBP', 'chauffeur' ),
									'USD'	=> __( 'USD', 'chauffeur' ),
								),
							),
							'booking-email-address'		=> array(
								'title'			=> __( 'Bookings Email Address', 'chauffeur' ),
								'type'			=> 'email',
								'value'			=> 'booking@airporttaxibooking.co.uk',
								'text'			=> __( 'Emails will be sent to this address when a booking is placed.' )
							),
							'booking-threshold-time'		=> array(
								'title'			=> __( 'Minimum time for bookings', 'chauffeur' ),
								'type'			=> 'select',
								'value'			=> '180 ',
								'id'			=> 'booking_threshold_time',
								'choices'		=> array(
									'60 '	=> __( '1 Hour', 'chauffeur' ),
									'120 '	=> __( '2 Hours', 'chauffeur' ),
									'180 '	=> __( '3 Hours', 'chauffeur' ),
									'240 '	=> __( '4 Hours', 'chauffeur' ),
									'300 '	=> __( '5 Hours', 'chauffeur' ),
									'360 '	=> __( '6 Hours', 'chauffeur' ),
									'420 '	=> __( '7 Hours', 'chauffeur' ),
									'480 '	=> __( '8 Hours', 'chauffeur' ),
									'540 '	=> __( '9 Hours', 'chauffeur' ),
									'600 '	=> __( '10 Hours', 'chauffeur' ),
									'660 '	=> __( '11 Hours', 'chauffeur' ),
									'720 '	=> __( '12 Hours', 'chauffeur' ),
									'1440 '	=> __( '24 Hours', 'chauffeur' ),
									'2880 '	=> __( '48 Hours', 'chauffeur' ),
								)
							),
							'sp-booking-threshold-time'		=> array(
								'title'			=> __( 'Suppliers Portal Threshold Time', 'chauffeur' ),
								'type'			=> 'select',
								'value'			=> '4320 ',
								'id'			=> 'sp_booking_threshold_time',
								'choices'		=> array(
									'60 '	=> __( '1 Hour', 'chauffeur' ),
									'120 '	=> __( '2 Hours', 'chauffeur' ),
									'180 '	=> __( '3 Hours', 'chauffeur' ),
									'240 '	=> __( '4 Hours', 'chauffeur' ),
									'300 '	=> __( '5 Hours', 'chauffeur' ),
									'360 '	=> __( '6 Hours', 'chauffeur' ),
									'420 '	=> __( '7 Hours', 'chauffeur' ),
									'480 '	=> __( '8 Hours', 'chauffeur' ),
									'540 '	=> __( '9 Hours', 'chauffeur' ),
									'600 '	=> __( '10 Hours', 'chauffeur' ),
									'660 '	=> __( '11 Hours', 'chauffeur' ),
									'720 '	=> __( '12 Hours', 'chauffeur' ),
									'1440 '	=> __( '24 Hours', 'chauffeur' ),
									'2880 '	=> __( '48 Hours', 'chauffeur' ),
									'4320 '	=> __( '72 Hours', 'chauffeur' ),
								)
							)
						)
					)
				),
			),
		),
	),
);
$option_page = new RationalOptionPages( $pages );
