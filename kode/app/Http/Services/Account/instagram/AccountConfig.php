<?php

namespace App\Http\Services\Account\instagram;


use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\MultipartStream;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\RSA;

use App\Models\SocialAccount;

class AccountConfig
{
	const DEVICE_STRING    = '26/8.0.0; 640dpi; 1440x2560; samsung; SM-G935F; hero2lte; samsungexynos8890';

	const RESUMABLE_UPLOAD = 1;

	const SEGMENTED_UPLOAD = 2;
	
	const MIN_ASPECT_RATIO = 0.8;
	
   	const MAX_ASPECT_RATIO = 1.91;

	private $username;
	private $password;
	private $deviceParams      = [];
	private $settings          = [];
	private $need_to_save_data = FALSE;
	private $cache_data_id;
	private $identification;
	private $tmpFiles 		   = [];
	/**
	 * @var Client
	 */
	private $client;
	/**
	 * @var bool|CookieJar
	 */
	private $cookies;
	/**
	 * @var bool
	 */
	private $mid = NULL;

	public function __construct ( string $username, string $password, ? string  $identification = null, string $proxy = '' )
	{
		$this->identification   = $identification;
		$this->username         = $username;
		$this->password         = $password;
 
		$cookies                = true;

 
		$this->initDefaultSettings();

		if ( empty( $this->getSettings( 'advertising_id' ) ) )
		{
			$this->setSettings( 'advertising_id', $this->generateUUID() );
		}

		if ( empty( $this->getSettings( 'session_id' ) ) )
		{
			$this->setSettings( 'session_id', $this->generateUUID() );
		}

		$this->cookies = $cookies;
		$this->client  = new Client( [
			'proxy'       => empty( $proxy ) ? NULL : $proxy,
			'verify'      => FALSE,
			'http_errors' => FALSE,
			'headers'     => [
				'User-Agent'                  => $this->buildUserAgent(),
				'Connection'                  => 'Keep-Alive',
				'Accept'                      => '*/*',
				'Accept-Encoding'             => 'gzip,deflate',
				'Accept-Language'             => 'en-US',
				'X-IG-Capabilities'           => '3brTvx0=',
				'X-IG-WWW-Claim'              => 0,
				'X-Bloks-Is-Layout-RTL'       => FALSE,
				'X-Bloks-Is-Panorama-Enabled' => true,
			],
			'cookies'     => $cookies
		] );
	}

	


	/**
	 * ig login
	 *
	 * @return void
	 */
	public function login () :array
	{
		$this->_sendPreLoginFlow();

		$sendData = [
			'country_codes'       => '[{"country_code":"1","source":["default"]}]',
			'phone_id'            => $this->getSettings( 'phone_id' ),
			'_csrftoken'          => $this->getCsrfToken(),
			'username'            => $this->username,
			'adid'                => $this->getSettings( 'advertising_id' ),
			'guid'                => $this->getSettings( 'uuid' ),
			'device_id'           => $this->getSettings( 'device_id' ),
			'enc_password'        => $this->encPass( $this->password ),
			'google_tokens'       => '[]',
			'login_attempt_count' => 0
		];

		try
		{
			$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/accounts/login/', [
				'form_params' => $this->signData( $sendData )
			] )->getBody();


			$response = json_decode( $response, true );
		}
		catch (\Exception $e )
		{
			$response = [];
		}

		$response = $this->checkChallenge( $response );
		return $response;
	}

	public function startChallenge ( $api_path )
	{
		sleep(1);
		$sendData = [
			'choice'        => '1',
			'_csrftoken'    => $this->getCsrfToken(),
			'username'      => $this->username,
			'device_id'     => $this->getSettings( 'device_id' ),
			'google_tokens' => '[]',
			'password'      => $this->password
		];

		try
		{
			$response = (string) $this->client->post( 'https://i.instagram.com/api/v1' . $api_path, [
				'form_params' => $this->signData( $sendData )
			] )->getBody();

			$response = json_decode( $response, true );
		}
		catch (\Exception $e )
		{
			$response = [];
		}

		if ( $response[ 'status' ] == 'fail' && strpos( $response[ 'message' ], 'valid choice' ) )
		{
			$sendData[ 'choice' ] = '0';

			try
			{
				$response = (string) $this->client->post( 'https://i.instagram.com/api/v1' . $api_path, [
					'form_params' => $this->signData( $sendData )
				] )->getBody();

				$response = json_decode( $response, true );
			}
			catch (\Exception $e )
			{
				$response = [];
			}
		}

		return $response;
	}

	public function finishChallenge ( $api_path, $code )
	{
		$code = preg_replace( '/\s+/', '', $code );

		$sendData = [
			'security_code' => $code,
			'_csrftoken'    => $this->getCsrfToken(),
			'username'      => $this->username,
			'device_id'     => $this->getSettings( 'device_id' ),
			'password'      => $this->password
		];

		try
		{
			$response = (string) $this->client->post( 'https://i.instagram.com/api/v1' . $api_path , [
				'form_params' => $this->signData( $sendData )
			] )->getBody();

			$response = json_decode( $response, true );
		}
		catch (\Exception $e )
		{
			$response = [];
		}

		$response = $this->checkChallenge( $response );

		$this->need_to_save_data = true;

		return $response;
	}

	public function finishTwoFactorLogin ( $twoFactorIdentifier, $verificationCode, $verificationMethod = '1' )
	{
		$verificationCode = preg_replace( '/\s+/', '', $verificationCode );

		$sendData = [
			'verification_method'   => $verificationMethod,
			'verification_code'     => $verificationCode,
			'trust_this_device'     => 1,
			'two_factor_identifier' => $twoFactorIdentifier,
			'_csrftoken'            => $this->getCsrfToken(),
			'username'              => $this->username,
			'device_id'             => $this->getSettings( 'device_id' ),
			'guid'                  => $this->getSettings( 'uuid' )
		];

		try
		{
			$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/accounts/two_factor_login/', [
				'form_params' => $this->signData( $sendData )
			] )->getBody();

			$response = json_decode( $response, true );
		}
		catch (\Exception $e )
		{
			$response = [];
		}

		$response = $this->checkChallenge( $response );

		$this->need_to_save_data = true;

		return $response;
	}

	public function getCurrentUser(){
		try
		{
			$response = (string) $this->client->get( 'https://i.instagram.com/api/v1/accounts/current_user/', [
				'edit' => true
			] )->getBody();
			$response = json_decode( $response, true );
		}
		catch (\Exception $e )
		{
			$response = [];
		}

		$this->need_to_save_data = true;
		
		return $response;
	}

	public function uploadPhoto ( $account_id, $photo, $caption, $link = '', $target = 'timeline', $instagramPinThePost = 0 )
	{

		$uploadId = $this->createUploadId();

		$uploadIgPhoto = $this->uploadIgPhoto( $uploadId, $photo );


		$result = $this->configurePhoto( $photo, $caption, $uploadId,$photo, $target );

		if ( isset( $result[ 'status' ] ) && $result[ 'status' ] == 'fail' )
		{
			throw new \Exception( ! empty( $result[ 'message' ] ) && is_string( $result[ 'message' ] ) ? strip_tags( $result[ 'message' ] ) : 'Error!' );
		}

		if ( $result[ 'media' ][ 'pk' ] && $instagramPinThePost === 1 )
		{
			$this->pinPost( $result[ 'media' ][ 'pk' ] );
		}

		$response =  [
			'status' => 'ok',
			'code'   => isset( $result[ 'media' ][ 'code' ] ) ? strip_tags( $result[ 'media' ][ 'code' ] ) : '?',
			'id'     => isset( $result[ 'media' ][ 'id' ] ) ? strip_tags( $result[ 'media' ][ 'id' ] ) : '?'
		];

		return $response;

	}

	public function uploadCarouselItem ( $photo )
	{
		$photo_info = $this->validatePhoto($photo);

		$uploadId = $this->createUploadId();

		$params = [
			'media_type'          => '1',
			'upload_media_height' => (string) $photo_info[ 'height' ],
			'upload_media_width'  => (string) $photo_info[ 'width' ],
			'upload_id'           => $uploadId,
		];


		$imageContent = @file_get_contents($photo);

		if ($imageContent !== false) {
			$fileSize = strlen($imageContent);
		}


		  $headers =  [
			'X-Requested-With'           => 'XMLHttpRequest',
			'X-CSRFToken'                => csrf_token(),
			'X-Instagram-Rupload-Params' => json_encode( $params ),
			'X-Entity-Name'              => 'feed_' . $uploadId,
			'X-Entity-Length'            => $fileSize,
			'Offset'                     => '0',
			'Content-Length'             => $fileSize, 
		  ];




		try
		{
			$response = (string) $this->client->post( 'https://www.instagram.com/rupload_igphoto/fb_uploader_' . $uploadId, [
				'headers' =>  $headers,
				'body'    => fopen( $photo, 'r' )
			] )->getBody();
			$result   = json_decode( $response, true );

	
			if ( isset($result[ 'status' ]) && $result[ 'status' ]== 'fail' )
			{
				return [
					'status'    => 'error',
					'message' => isset( $result[ 'message' ] ) ? $result[ 'message' ] : __( 'Error' )
				];
			}

			return $result;
		}
		catch (\Exception $e )
		{
			return [
				'status'    => 'error',
				'message' => __("Unable to process the image")
			];
		}
	}

	public function generateAlbum ( $accountID, $photos, $caption, $instagramPinThePost )
	{
		$body = [
			"caption"                       => $caption,
			"children_metadata"             => [],
			"client_sidecar_id"             => $this->createUploadId(),
			"disable_comments"              => "0",
			"like_and_view_counts_disabled" => FALSE,
			"source_type"                   => "library"
		];

		foreach ( $photos as $photo )
		{
			$response = $this->uploadCarouselItem( $photo );
			if ( @$response[ 'status' ] === "ok" )
			{
				$body[ "children_metadata" ][] = [
					"upload_id" => $response[ 'upload_id' ]
				];
			}
			else{
				return [
					'message' => 'Invalid Images'
				];

			}
		}

		if ( count( $body[ 'children_metadata' ] ) == 0 )
		{
			return [
				'status'    => 'error',
				'message' => __( 'Error' )
			];
		}

		try
		{
			$response = (string) $this->client->post( "https://i.instagram.com/api/v1/media/configure_sidecar/", [
				'headers' => [
					'X-Requested-With' => 'XMLHttpRequest',
					'X-CSRFToken'      => $this->getCsrfToken(),
					'Offset'           => '0',
					"x-ig-app-id"      => "936619743392459",
					"x-csrf-token"     => $this->getCsrfToken()
				],
				"json"    => $body
			] )->getBody();

			$result = json_decode( $response, true );

			if ( isset( $result[ 'status' ] ) && $result[ 'status' ] == 'fail' )
			{
				return [
					'status'    => 'error',
					'message' => ! empty( $result[ 'message' ] ) && is_string( $result[ 'message' ] ) ? $result[ 'message' ] : __('Error')
				];
			}

			if ( $result[ 'media' ][ 'pk' ] && $instagramPinThePost === 1 )
			{
				$this->pinPost( $result[ 'media' ][ 'pk' ] );
			}

			return [
				'status' => 'ok',
				'code'   => isset( $result[ 'media' ][ 'code' ] ) ? $result[ 'media' ][ 'code' ] : '?',
				'id'     => isset( $result[ 'media' ][ 'id' ] ) ? $result[ 'media' ][ 'id' ] : '?'
			];
		}
		catch (\Exception $e )
		{
			return [
				'status'  => 'error',
				'message' => __( $e->getMessage() )
			];
		}
	}



	public function pinPost ( $postID )
	{
		$data = [
			'post_id'    => $postID,
			'_uuid'      => $this->getSettings( 'uuid' ),
			'device_id'  => $this->getSettings( 'device_id' ),
			'radio_type' => 'wifi_none'
		];

		try
		{
			$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/users/pin_timeline_media/', [
				'form_params' => $this->signData( $data )
			] )->getBody();
		}
		catch (\Exception $e )
		{
		}
	}


	
	private function checkChallenge ( $response )
	{
		if ( $response[ 'status' ] == 'fail' && isset( $response[ 'challenge' ] ) && is_array( $response[ 'challenge' ] ) )
		{
			return $this->startChallenge( $response[ 'challenge' ][ 'api_path' ] );
		}

		return $response;
	}

	private function getSettings ( $key )
	{
		return key_exists( $key, $this->settings ) ? $this->settings[ $key ] : NULL;
	}

	private function setSettings ( $key, $value )
	{
		$this->settings[ $key ] = $value;
	}

	private function setSettingsIfEmpty ( $key, $value )
	{
		if ( ! isset( $this->settings[ $key ] ) || empty( $this->settings[ $key ] ) )
		{
			$this->settings[ $key ] = $value;
		}
	}

	private function initDefaultSettings ()
	{
		$this->setSettingsIfEmpty( 'devicestring', static::DEVICE_STRING );
		$this->setSettingsIfEmpty( 'device_id', $this->generateDeviceId() );
		$this->setSettingsIfEmpty( 'phone_id', $this->generateUUID() );
		$this->setSettingsIfEmpty( 'uuid', $this->generateUUID() );
		$this->setSettingsIfEmpty( 'account_id', '' );
	}

	private function getCookies ()
	{
		$cookies_purified = [];
		$cookies          = $this->client->getConfig( 'cookies' )->toArray();

		foreach ( $cookies as $cookie )
		{
			if ( $cookie[ 'Name' ] == 'sessionid' && empty( trim( $cookie[ 'Value' ], '\"' ) ) )
			{
				continue;
			}

			$cookies_purified[] = $cookie;
		}

		return $cookies_purified;
	}

	private function getCookie ( $name, $default = '' )
	{
		$cookies = $this->getCookies();
		$value   = $default;

		foreach ( $cookies as $cookieInf )
		{
			if ( $cookieInf[ 'Name' ] == $name )
			{
				$value = $cookieInf[ 'Value' ];
			}
		}

		return $value;
	}

	private function getCsrfToken ()
	{
		return csrf_token();
	}

	private function _sendPreLoginFlow ()
	{
		$this->fetchZeroRatingToken();
		$this->bootstrapMsisdnHeader();
		$this->readMsisdnHeader( 'default' );
		$this->syncDeviceFeatures( true );
		$this->sendLauncherSync( true );
		$this->bootstrapMsisdnHeader();
		$this->logAttribution();
		$this->getPrefillCandidates();
		$this->readMsisdnHeader( 'default', true );
		$this->setContactPointPrefill( 'prefill' );
		$this->sendLauncherSync( true, true, true );
		$this->syncDeviceFeatures( true, true );
	}

	private function fetchZeroRatingToken ( $reason = 'token_expired' )
	{
		$url = 'https://b.i.instagram.com/api/v1/zr/token/result/?';
		$url .= 'custom_device_id=' . $this->getSettings( 'uuid' );
		$url .= '&device_id=' . $this->getSettings( 'device_id' );
		$url .= '&fetch_reason=' . $reason;
		$url .= '&token_hash=' . $this->getSettings( 'zr_token' );

		$request = $this->client->get( $url )->getBody();

	}

	private function bootstrapMsisdnHeader ( $usage = 'ig_select_app' )
	{
		$sendData = [
			'mobile_subno_usage' => $usage,
			'device_id'          => $this->getSettings( 'uuid' )
		];

		try
		{
			$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/accounts/msisdn_header_bootstrap/', [
				'form_params' => $this->signData( $sendData )
			] )->getBody();
		}
		catch ( \Exception $e )
		{
			$response = [];
		}

		return $response;
	}

	private function readMsisdnHeader ( $usage, $useCsrfToken = FALSE )
	{
		$sendData = [
			'device_id'          => $this->getSettings( 'uuid' ),
			'mobile_subno_usage' => $usage
		];

		if ( $useCsrfToken )
		{
			$sendData[ '_csrftoken' ] = $this->getCsrfToken();
		}

		try
		{
			$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/accounts/read_msisdn_header/', [
				'form_params' => $this->signData( $sendData ),
				'headers'     => [
					'X-DEVICE-ID' => $this->getSettings( 'uuid' )
				]
			] )->getBody();
		}
		catch ( \Exception $e )
		{
			$response = [];
		}

		return $response;
	}

	private function syncDeviceFeatures ( $prelogin = FALSE, $useCsrfToken = FALSE )
	{
		$sendData = [
			'id'          => $this->getSettings( 'uuid' ),
			'experiments' => 'ig_android_fci_onboarding_friend_search,ig_android_device_detection_info_upload,ig_android_account_linking_upsell_universe,ig_android_direct_main_tab_universe_v2,ig_android_sms_retriever_backtest_universe,ig_android_direct_add_direct_to_android_native_photo_share_sheet,ig_growth_android_profile_pic_prefill_with_fb_pic_2,ig_account_identity_logged_out_signals_global_holdout_universe,ig_android_login_identifier_fuzzy_match,ig_android_video_render_codec_low_memory_gc,ig_android_custom_transitions_universe,ig_android_push_fcm,ig_android_show_login_info_reminder_universe,ig_android_email_fuzzy_matching_universe,ig_android_one_tap_aymh_redesign_universe,ig_android_direct_send_like_from_notification,ig_android_suma_landing_page,ig_android_session_scoped_logger,ig_android_user_session_scoped_class_opt_universe,ig_android_accoun_switch_badge_fix_universe,ig_android_smartlock_hints_universe,ig_android_black_out,ig_activation_global_discretionary_sms_holdout,ig_android_account_switch_infra_universe,ig_android_video_ffmpegutil_pts_fix,ig_android_multi_tap_login_new,ig_android_caption_typeahead_fix_on_o_universe,ig_android_save_pwd_checkbox_reg_universe,ig_android_nux_add_email_device,ig_android_direct_remove_view_mode_stickiness_universe,ig_username_suggestions_on_username_taken,ig_android_ingestion_video_support_hevc_decoding,ig_android_secondary_account_creation_universe,ig_android_account_recovery_auto_login,ig_android_sim_info_upload,ig_android_mobile_http_flow_device_universe,ig_android_hide_fb_button_when_not_installed_universe,ig_android_targeted_one_tap_upsell_universe,ig_android_gmail_oauth_in_reg,ig_android_account_linking_flow_shorten_universe,ig_android_hide_typeahead_for_logged_users,ig_android_vc_interop_use_test_igid_universe,ig_android_log_suggested_users_cache_on_error,ig_android_reg_modularization_universe,ig_android_phone_edit_distance_universe,ig_android_device_verification_separate_endpoint,ig_android_universe_noticiation_channels,ig_smartlock_login,ig_android_igexecutor_sync_optimization_universe,ig_android_account_linking_skip_value_props_universe,ig_android_account_linking_universe,ig_android_hsite_prefill_new_carrier,ig_android_retry_create_account_universe,ig_android_family_apps_user_values_provider_universe,ig_android_reg_nux_headers_cleanup_universe,ig_android_device_info_foreground_reporting,ig_android_shortcuts_2019,ig_android_device_verification_fb_signup,ig_android_onetaplogin_optimization,ig_video_debug_overlay,ig_android_ask_for_permissions_on_reg,ig_assisted_login_universe,ig_android_display_full_country_name_in_reg_universe,ig_android_security_intent_switchoff,ig_android_device_info_job_based_reporting,ig_android_passwordless_auth,ig_android_direct_main_tab_account_switch,ig_android_modularized_dynamic_nux_universe,ig_android_fb_account_linking_sampling_freq_universe,ig_android_fix_sms_read_lollipop,ig_android_access_flow_prefill'
		];

		if ( $useCsrfToken )
		{
			$sendData[ '_csrftoken' ] = $this->getCsrfToken();
		}

		else
		{
			$sendData[ '_uuid' ] = $this->getSettings( 'uuid' );
			$sendData[ '_uid' ]  = $this->getSettings( 'account_id' );
		}

		try
		{
			$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/qe/sync/', [
				'form_params' => $this->signData( $sendData ),
				'headers'     => [
					'X-DEVICE-ID' => $this->getSettings( 'uuid' )
				]
			] )->getBody();
		}
		catch ( \Exception $e )
		{
			$response = [];
		}

		return $response;
	}

	private function sendLauncherSync ( $prelogin, $idIsUuid = true, $useCsrfToken = FALSE, $loginConfigs = FALSE )
	{
		$sendData = [
			'configs' => $loginConfigs ? 'ig_camera_ard_use_ig_downloader,ig_android_dogfooding,ig_android_bloks_data_release,ig_donation_sticker_public_thanks,ig_business_profile_donate_cta_android,ig_launcher_ig_android_network_dispatcher_priority_decider_qe2,ig_multi_decode_config,ig_android_improve_segmentation_hint,ig_android_memory_manager_holdout,ig_android_interactions_direct_sharing_comment_launcher,ig_launcher_ig_android_analytics_request_cap_qe,ig_direct_e2e_send_waterfall_sample_rate_config,ig_android_cdn_image_sizes_config,ig_android_critical_path_manager,ig_android_mobileboost_camera,ig_android_pdp_default_sections,ig_android_video_playback,ig_launcher_explore_sfplt_secondary_response_android,ig_android_upload_heap_on_oom,ig_synchronous_account_switch,ig_android_direct_presence_digest_improvements,ig_android_request_compression_launcher,ig_android_feed_attach_report_logs,ig_android_insights_welcome_dialog_tooltip,ig_android_qp_surveys_v1,ig_direct_requests_approval_config,ig_android_react_native_ota_kill_switch,ig_android_video_profiler_loom_traces,video_call_gk,ig_launcher_ig_android_network_stack_cap_video_request_qe,ig_shopping_android_business_new_tagging_flow,ig_android_igtv_bitrate,ig_android_geo_gating,ig_android_explore_startup_prefetch,ig_android_camera_asset_blocker_config,post_user_cache_user_based,ig_android_branded_content_story_partner_promote_rollout,ig_android_quic,ig_android_videolite_uploader,ig_direct_message_type_reporting_config,ig_camera_android_whitelist_all_effects_in_pre,ig_android_shopping_influencer_creator_nux,ig_android_mobileboost_blacklist,ig_android_direct_gifs_killswitch,ig_android_global_scheduler_direct,ig_android_image_display_logging,ig_android_global_scheduler_infra,ig_igtv_branded_content_killswitch,ig_cg_donor_duplicate_sticker,ig_launcher_explore_verified_badge_on_ads,ig_android_cold_start_class_preloading,ig_camera_android_attributed_effects_endpoint_api_query_config,ig_android_highlighted_products_business_option,ig_direct_join_chat_sticker,ig_android_direct_admin_tools_requests,ig_android_rage_shake_whitelist,ig_android_shopping_ads_cta_rollout,ig_android_igtv_segmentation,ig_launcher_force_switch_on_dialog,ig_android_iab_fullscreen_experience_config,ig_android_instacrash,ig_android_specific_story_url_handling_killswitch,ig_mobile_consent_settings_killswitch,ig_android_influencer_monetization_hub_launcher,ig_and roid_scroll_perf_mobile_boost_launcher,ig_android_cx_stories_about_you,ig_android_replay_safe,ig_android_stories_scroll_perf_misc_fixes_h2_2019,ig_android_shopping_django_product_search,ig_direct_giphy_gifs_rating,ig_android_ppr_url_logging_config,ig_canvas_ad_pixel,ig_strongly_referenced_mediacache,ig_android_direct_show_threads_status_in_direct,ig_camera_ard_brotli_model_compression,ig_image_pipeline_skip_disk_config,ig_android_explore_grid_viewpoint,ig_android_iab_persistent_process,ig_android_in_process_iab,ig_android_launcher_value_consistency_checker,ig_launcher_ig_explore_peek_and_sfplt_android,ig_android_skip_photo_finish,ig_biz_android_use_professional_account_term,ig_android_settings_search,ig_android_direct_presence_media_viewer,ig_launcher_explore_navigation_redesign_android,ig_launcher_ig_android_network_stack_cap_api_request_qe,ig_qe_value_consistency_checker,ig_stories_fundraiser_view_payment_address,ig_business_create_donation_android,ig_android_qp_waterfall_logging,ig_android_bloks_demos,ig_redex_dynamic_analysis,ig_android_bug_report_screen_record,ig_shopping_android_carousel_product_ids_fix_killswitch,ig_shopping_android_creators_new_tagging_flow,ig_android_direct_threads_app_dogfooding_flags,ig_shopping_camera_android,ig_android_qp_keep_promotion_during_cooldown,ig_android_qp_slot_cooldown_enabled_universe,ig_android_request_cap_tuning_with_bandwidth,ig_android_client_config_realtime_subscription,ig_launcher_ig_android_network_request_cap_tuning_qe,ig_android_concurrent_coldstart,ig_android_gps_improvements_launcher,ig_android_notification_setting_sync,ig_android_stories_canvas_mode_colour_wheel,ig_android_iab_session_logging_config,ig_android_network_trace_migration,ig_android_extra_native_debugging_info,ig_android_insights_top_account_dialog_tooltip,ig_launcher_ig_android_dispatcher_viewpoint_onscreen_updater_qe,ig_android_disable_browser_multiple_windows,ig_contact_invites_netego_killswitch,ig_android_update_items_header_height_launcher,ig_android_bulk_tag_untag_killswitch,ig_android_employee_options,ig_launcher_ig_android_video_pending_request_store_qe,ig_story_insights_entry,ig_android_creator_multi_select,ig_android_direct_new_media_viewer,ig_android_gps_profile_launcher,ig_android_direct_real_names_launcher,ig_fev_info_launcher,ig_android_remove_request_params_in_network_trace,ig_android_rageshake_redesign,ig_launcher_ig_android_network_stack_queue_undefined_request_qe,ig_cx_promotion_tooltip,ig_text_response_bottom_sheet,ig_android_carrier_signal_timestamp_max_age,ig_android_qp_xshare_to_fb,ig_android_rollout_gating_payment_settings,ig_android_mobile_boost_kill_switch,ig_android_betamap_cold_start,ig_android_media_store,ig_android_async_view_model_launcher,ig_android_newsfeed_recyclerview,ig_android_feed_optimistic_upload,ig_android_fix_render_backtrack_reporting,ig_delink_lasso_accounts,ig_android_feed_report_ranking_issue,ig_android_shopping_insights_events_validator,ig_biz_android_new_logging_architecture,ig_launcher_ig_android_reactnative_realtime_ota,ig_android_boomerang_crash_android_go,ig_android_shopping_influencer_product_sticker_editing,ig_camera_android_max_vertex_texture_launcher,bloks_suggested_hashtag' : 'ig_android_media_codec_info_collection,stories_gif_sticker,ig_android_felix_release_players,bloks_binding,ig_android_camera_network_activity_logger,ig_android_os_version_blocking_config,ig_android_carrier_signals_killswitch,live_special_codec_size_list,fbns,ig_android_aed,ig_client_config_server_side_retrieval,ig_android_bloks_perf_logging,ig_user_session_operation,ig_user_mismatch_soft_error,ig_android_prerelease_event_counter,fizz_ig_android,ig_android_vc_clear_task_flag_killswitch,ig_android_killswitch_perm_direct_ssim,ig_android_codec_high_profile,ig_android_smart_prefill_killswitch,sonar_prober,action_bar_layout_width,ig_auth_headers_device,always_use_server_recents',
			'id'      => ( $idIsUuid ? $this->getSettings( 'uuid' ) : $this->getSettings( 'account_id' ) )
		];

		if ( $useCsrfToken )
		{
			$sendData[ '_csrftoken' ] = $this->getCsrfToken();
		}

	
		else
		{
			$sendData[ '_uuid' ] = $this->getSettings( 'uuid' );
			$sendData[ '_uid' ]  = $this->getSettings( 'account_id' );
		}

		try
		{
			$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/launcher/sync/', [
				'form_params' => $this->signData( $sendData )
			] )->getBody();
		}
		catch ( \Exception $e )
		{
			$response = [];
		}

		return $response;
	}

	private function logAttribution ()
	{
		$sendData = [
			'adid' => $this->getSettings( 'advertising_id' )
		];

		try
		{
			$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/attribution/log_attribution/', [
				'form_params' => $this->signData( $sendData )
			] )->getBody();
		}
		catch ( \Exception $e )
		{
			$response = [];
		}

		return $response;
	}

	private function getPrefillCandidates ()
	{
		$sendData = [
			'android_device_id' => $this->getSettings( 'device_id' ),
			'device_id'         => $this->getSettings( 'uuid' ),
			'usages'            => '["account_recovery_omnibox"]'
		];

		try
		{
			$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/accounts/get_prefill_candidates/', [
				'form_params' => $this->signData( $sendData )
			] )->getBody();
		}
		catch ( \Exception $e )
		{
			$response = [];
		}

		return $response;
	}

	private function setContactPointPrefill ( $usage )
	{
		$sendData = [
			'phone_id'   => $this->getSettings( 'phone_id' ),
			'_csrftoken' => $this->getCsrfToken(),
			'usages'     => $usage
		];

		try
		{
			$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/accounts/contact_point_prefill/', [
				'form_params' => $this->signData( $sendData )
			] )->getBody();
		}
		catch ( \Exception $e )
		{
			$response = [];
		}

		return $response;
	}

	private function generateDeviceId ()
	{
		return 'android-' . substr( md5( number_format( microtime( true ), 7, '', '' ) ), 16 );
	}

	private function generateUUID ( $keepDashes = true )
	{
		$uuid = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );

		return $keepDashes ? $uuid : str_replace( '-', '', $uuid );
	}

	private function getDeviceParam ( $param )
	{
		if ( ! isset( $this->deviceParams[ $param ] ) )
		{
			$this->buildUserAgent();
		}

		return isset( $this->deviceParams[ $param ] ) ? $this->deviceParams[ $param ] : '';
	}

	private function buildUserAgent ( $app_version = '107.0.0.27.121', $version_code = '168361634' )
	{
		$this->deviceParams[ 'appVersion' ]  = $app_version; 
		$this->deviceParams[ 'versionCode' ] = $version_code;
		$this->deviceParams[ 'userLocale' ]  = 'en_US';

		$deviceString = $this->getSettings( 'devicestring' );

		$parts = explode( '; ', $deviceString );

		$androidOS = explode( '/', $parts[ 0 ], 2 );

		$resolution                         = explode( 'x', $parts[ 2 ], 2 );
		$this->deviceParams[ 'pixelCount' ] = (int) $resolution[ 0 ] * (int) $resolution[ 1 ];

		$manufacturerAndBrand = explode( '/', $parts[ 3 ], 2 );

		$this->deviceParams[ 'androidVersion' ] = $androidOS[ 0 ];
		$this->deviceParams[ 'androidRelease' ] = $androidOS[ 1 ];
		$this->deviceParams[ 'dpi' ]            = $parts[ 1 ];
		$this->deviceParams[ 'resolution' ]     = $parts[ 2 ];
		$this->deviceParams[ 'manufacturer' ]   = $manufacturerAndBrand[ 0 ];
		$this->deviceParams[ 'brand' ]          = ( isset( $manufacturerAndBrand[ 1 ] ) ? $manufacturerAndBrand[ 1 ] : NULL );
		$this->deviceParams[ 'model' ]          = $parts[ 4 ];
		$this->deviceParams[ 'device' ]         = $parts[ 5 ];
		$this->deviceParams[ 'cpu' ]            = $parts[ 6 ];

		$this->deviceParams[ 'manufacturerWithBrand' ] = $this->deviceParams[ 'brand' ] !== NULL ? $this->deviceParams[ 'manufacturer' ] . '/' . $this->deviceParams[ 'brand' ] : $this->deviceParams[ 'manufacturer' ];

		return sprintf( 'Instagram %s Android (%s/%s; %s; %s; %s; %s; %s; %s; %s; %s)', $this->deviceParams[ 'appVersion' ], $this->deviceParams[ 'androidVersion' ], $this->deviceParams[ 'androidRelease' ], $this->deviceParams[ 'dpi' ], $this->deviceParams[ 'resolution' ], $this->deviceParams[ 'manufacturerWithBrand' ], $this->deviceParams[ 'model' ], $this->deviceParams[ 'device' ], $this->deviceParams[ 'cpu' ], $this->deviceParams[ 'userLocale' ], $this->deviceParams[ 'versionCode' ] );
	}

	private function signData ( $data, $exclude = [] )
	{
		$result = [];

		foreach ( $exclude as $key )
		{
			if ( isset( $data[ $key ] ) )
			{
				$result[ $key ] = $data[ $key ];
				unset( $data[ $key ] );
			}
		}

		foreach ( $data as &$value )
		{
			if ( is_scalar( $value ) )
			{
				$value = (string) $value;
			}
		}

		$data = json_encode( (object) $this->reorderByHashCode( $data ) );

		$result[ 'ig_sig_key_version' ] = 4;
		$result[ 'signed_body' ]        = $this->generateSignature( $data ) . '.' . $data;

		return $this->reorderByHashCode( $result );
	}

	private function reorderByHashCode ( $data )
	{
		$hashCodes = [];
		foreach ( $data as $key => $value )
		{
			$hashCodes[ $key ] = $this->hashCode( $key );
		}

		uksort( $data, function ( $a, $b ) use ( $hashCodes ) {
			$a = $hashCodes[ $a ];
			$b = $hashCodes[ $b ];
			if ( $a < $b )
			{
				return -1;
			}
			else if ( $a > $b )
			{
				return 1;
			}
			else
			{
				return 0;
			}
		} );

		return $data;
	}

	private function hashCode ( $string )
	{
		$result = 0;
		for ( $i = 0, $len = strlen( $string ); $i < $len; ++$i )
		{
			$result = ( -$result + ( $result << 5 ) + ord( $string[ $i ] ) ) & 0xFFFFFFFF;
		}

		if ( PHP_INT_SIZE > 4 )
		{
			if ( $result > 0x7FFFFFFF )
			{
				$result -= 0x100000000;
			}
			else if ( $result < -0x80000000 )
			{
				$result += 0x100000000;
			}
		}

		return $result;
	}

	private function generateSignature ( $data )
	{
		return hash_hmac( 'sha256', $data, 'c36436a942ea1dbb40d7f2d7d45280a620d991ce8c62fb4ce600f0a048c32c11' );
	}

	private function validatePhoto ( $photo )
	{
        $result = @getimagesize($photo);
        if ($result === false) {
            throw new \InvalidArgumentException(sprintf('The photo file "%s" is not a valid image.', $photo));
        }
		list($width, $height, $type) =  $result;

        $response =  [
        	'width' => $width,
        	'height' => $height,
        	'type' => $type
        ];

	    return $response;
	}

	private function uploadIgPhoto ( $uploadId, $photo )
	{
		$photo_info = $this->validatePhoto($photo);

		$params = [
			'media_type'          => '1',
			'upload_media_height' => (string) $photo_info[ 'height' ],
			'upload_media_width'  => (string) $photo_info[ 'width' ],
			'upload_id'           => $uploadId,
			'image_compression'   => '{"lib_name":"moz","lib_version":"3.1.m","quality":"87"}',
			'xsharing_user_ids'   => '[]',
			'retry_context'       => json_encode( [
				'num_step_auto_retry'   => 0,
				'num_reupload'          => 0,
				'num_step_manual_retry' => 0
			] )
		];

		$entity_name = sprintf( '%s_%d_%d', $uploadId, 0, $this->hashCode( basename( $photo ) ) );
		$endpoint    = 'https://i.instagram.com/rupload_igphoto/' . $entity_name;

		try
		{
			$imageContent = @file_get_contents($photo);

			if ($imageContent !== false) {
				$fileSize = strlen($imageContent);
			}
			$response = (string) $this->client->post( $endpoint, [
				'headers' => [
					'X_FB_PHOTO_WATERFALL_ID'    => $this->generateUUID(),
					'X-Requested-With'           => 'XMLHttpRequest',
					'X-CSRFToken'                => $this->getCsrfToken(),
					'X-Instagram-Rupload-Params' => json_encode( $this->reorderByHashCode( $params ) ),
					'X-Entity-Type'              => 'image/jpeg',
					'X-Entity-Name'              => $entity_name,
					'X-Entity-Length'            => $fileSize ,
					'Content-Length'             => $fileSize, 
					'Offset'                     => '0'
				],
				'body'    => fopen( $photo, 'r' )
			] )->getBody();

			$response = json_decode( $response, true );
		}
		catch (\Exception $e )
		{
			$response = [];
		}

		return $response;
	}

	private function configurePhoto ( $photo, $caption, $uploadId, $link = '', $target = 'timeline' )
	{
		$photo_info = $this->validatePhoto($photo);
		$date = date( 'Y:m:d H:i:s' );

		if ( $target == 'story' )
		{
			$tap_models = '}';

			$endpoint = 'media/configure_to_story/';
			if ( ! empty( $link ) )
			{
				$link_y = 1000;

				$link_y = $link_y / $photo_info[ 'height' ];

				$this->client->post( 'https://i.instagram.com/api/v1/media/validate_reel_url/', [
					'cookies'     => $this->cookies,
					'headers'     => [
						'User-Agent'                  => $this->buildUserAgent( '203.0.0.29.118', '314665256' ),
						'Accept-Encoding'             => 'gzip, deflate',
						'Accept'                      => '*/*',
						'Connection'                  => 'keep-alive',
						'X-IG-App-Locale'             => 'en_US',
						'X-IG-Device-Locale'          => 'en_US',
						'X-IG-Mapped-Locale'          => 'en_US',
						'X-IG-App-Startup-Country'    => 'US',
						'X-IG-WWW-Claim'              => '0',
						'X-Bloks-Is-Layout-RTL'       => 'false',
						'X-Bloks-Is-Panorama-Enabled' => 'true',
						'X-IG-Device-ID'              => $this->getSettings( 'uuid' ),
						'X-IG-Family-Device-ID'       => $this->generateUUID(),
						'X-IG-Android-ID'             => $this->getSettings( 'device_id' ),
						'X-IG-Timezone-Offset'        => '-14400',
						'X-IG-Connection-Type'        => 'WIFI',
						'X-IG-Capabilities'           => '3brTvx0=',
						'X-IG-App-ID'                 => '567067343352427',
						'Priority'                    => 'u=3',
						'Accept-Language'             => 'en-US',
						'X-MID'                       => $this->mid,
						'X-FB-HTTP-Engine'            => 'Liger',
						'X-FB-Client-IP'              => 'True',
						'X-FB-Server-Cluster'         => 'True',
						'IG-INTENDED-USER-ID'         => $this->getSettings( 'account_id' ),
						'X-IG-Nav-Chain'              => '9MV:self_profile:2,ProfileMediaTabFragment:self_profile:3,9Xf:self_following:4',
						'Content-Type'                => 'application/x-www-form-urlencoded; charset=UTF-8',
						'IG-U-DS-USER-ID'             => $this->getSettings( 'account_id' ),
						'X-Ads-Opt-Out'               => '0',
						'X-DEVICE-ID'                 => $this->getSettings( 'uuid' ),
						'X-CM-Bandwidth-KBPS'         => '-1.000',
						'X-CM-Latency'                => '1',
					],
					'form_params' => [
						'signed_body' => 'SIGNATURE.{"url":"' . $link . '","_uid":"' . $this->getSettings( 'account_id' ) . '","_uuid":"' . $this->getSettings( 'uuid' ) . '"}'
					]
				] )->getBody();
				$link_model = '{\"x\":0.5126011,\"y\":' . $link_y . ',\"z\":0,\"width\":0.80998676,\"height\":0.12075,\"rotation\":0.0,\"type\":\"story_link\",\"is_sticker\":true,\"selected_index\":0,\"tap_state\":0,\"link_type\":\"web\",\"url\":\"' . $link . '\",\"tap_state_str_id\":\"link_sticker_default\"}';
			}

			$hashtag = "";
			//instagram_story_hashtag_top
			if ( ! empty( $hashtag ) )
			{
				$hashtag_y = (float) 1000;
				$hashtag_y     = $hashtag_y / $photo_info[ 'height' ];
				$hashtag_y     = number_format( $hashtag_y, 2 );
				$hashtag_model = '{\"x\":0.51,\"y\":' . $hashtag_y . ',\"z\":0,\"width\":0.8,\"height\":0.12,\"rotation\":0.0,\"type\":\"hashtag\",\"tag_name\":\"' . $hashtag . '\",\"is_sticker\":true,\"tap_state\":0,\"tap_state_str_id\":\"hashtag_sticker_gradient\"}';
			}

			if ( ! empty( $hashtag_model ) || ! empty( $link_model ) )
			{
				$tap_models = ! empty( $hashtag_model ) && ! empty( $link_model ) ? ( $hashtag_model . ',' . $link_model ) : ( empty( $link_model ) ? $hashtag_model : $link_model );
				$tap_models = ',"tap_models":"[' . $tap_models . ']"}';
			}

			try
			{
				$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/' . $endpoint, [
					'cookies'     => $this->cookies,
					'headers'     => [
						'User-Agent'                  => $this->buildUserAgent( '203.0.0.29.118', '314665256' ),
						'Accept-Encoding'             => 'gzip, deflate',
						'Accept'                      => '*/*',
						'Connection'                  => 'keep-alive',
						'X-IG-App-Locale'             => 'en_US',
						'X-IG-Device-Locale'          => 'en_US',
						'X-IG-Mapped-Locale'          => 'en_US',
						'X-IG-App-Startup-Country'    => 'US',
						'X-IG-WWW-Claim'              => '0',
						'X-Bloks-Is-Layout-RTL'       => 'false',
						'X-Bloks-Is-Panorama-Enabled' => 'true',
						'X-IG-Device-ID'              => $this->getSettings( 'uuid' ),
						'X-IG-Family-Device-ID'       => $this->generateUUID(),
						'X-IG-Android-ID'             => $this->getSettings( 'device_id' ),
						'X-IG-Timezone-Offset'        => '-14400',
						'X-IG-Connection-Type'        => 'WIFI',
						'X-IG-Capabilities'           => '3brTvx0=',
						'X-IG-App-ID'                 => '567067343352427',
						'Priority'                    => 'u=3',
						'Accept-Language'             => 'en-US',
						'X-MID'                       => $this->mid,
						'X-FB-HTTP-Engine'            => 'Liger',
						'X-FB-Client-IP'              => 'True',
						'X-FB-Server-Cluster'         => 'True',
						'IG-INTENDED-USER-ID'         => $this->getSettings( 'account_id' ),
						'X-IG-Nav-Chain'              => '9MV:self_profile:2,ProfileMediaTabFragment:self_profile:3,9Xf:self_following:4',
						'Content-Type'                => 'application/x-www-form-urlencoded; charset=UTF-8',
						'IG-U-DS-USER-ID'             => $this->getSettings( 'account_id' ),
						'X-Ads-Opt-Out'               => '0',
						'X-DEVICE-ID'                 => $this->getSettings( 'uuid' ),
						'X-CM-Bandwidth-KBPS'         => '-1.000',
						'X-CM-Latency'                => '1',
					],
					'form_params' => [
						'signed_body' => 'SIGNATURE.{"_uuid":"' . $this->getSettings( 'uuid' ) . '","device_id":"' . $this->getSettings( 'uuid' ) . '","text_metadata":"[{\"font_size\":40.0,\"scale\":1.0,\"width\":611.0,\"height\":169.0,\"x\":0.51414347,\"y\":0.8487708,\"rotation\":0.0}]","supported_capabilities_new":"[{\"name\":+\"SUPPORTED_SDK_VERSIONS\",+\"value\":+\"108.0,109.0,110.0,111.0,112.0,113.0,114.0,115.0,116.0,117.0,118.0,119.0,120.0,121.0,122.0,123.0,124.0,125.0,126.0,127.0\"},+{\"name\":+\"FACE_TRACKER_VERSION\",+\"value\":+\"14\"},+{\"name\":+\"segmentation\",+\"value\":+\"segmentation_enabled\"},+{\"name\":+\"COMPRESSION\",+\"value\":+\"ETC2_COMPRESSION\"},+{\"name\":+\"world_tracker\",+\"value\":+\"world_tracker_enabled\"},+{\"name\":+\"gyroscope\",+\"value\":+\"gyroscope_enabled\"}]","has_original_sound":"1","camera_session_id":"45e0c374-d84f-4289-9f81-a7419752f684","scene_capture_type":"","timezone_offset":"-14400","client_shared_at":"' . ( time() - 5 ) . '","story_sticker_ids":"link_sticker_default","media_folder":"Camera","configure_mode":"1","source_type":"4","creation_surface":"camera","imported_taken_at":1643659109,"capture_type":"normal","rich_text_format_types":"[\"default\"]","upload_id":"' . $uploadId . '","client_timestamp":"' . time() . '","device":{"android_version":26,"android_release":"8.0.0","manufacturer":"Xiaomi","model":"MI+5s"},"_uid":49154269846,"composition_id":"8e56be0b-ba75-44c6-bd61-9fd77680f84a","app_attribution_android_namespace":"","media_transformation_info":"{\"width\":\"720\",\"height\":\"720\",\"x_transform\":\"0\",\"y_transform\":\"0\",\"zoom\":\"1.0\",\"rotation\":\"0.0\",\"background_coverage\":\"0.0\"}","original_media_type":"photo","camera_entry_point":"121","edits":{"crop_original_size":[720.0,720.0],"filter_type":0,"filter_strength":1.0},"extra":{"source_width":720,"source_height":720}' . $tap_models
					]
				] )->getBody();

				$response = json_decode( $response, true );
			}
			catch (\Exception $e )
			{
				$response = [];
			}
		} elseif ( $target == 'reel' )
		{
			$tap_models = '}';

			$endpoint = 'media/configure_to_clips/';
			if ( ! empty( $link ) )
			{
				$link_y = 0;

				$link_y = $link_y / $photo_info[ 'height' ];

				$this->client->post( 'https://i.instagram.com/api/v1/media/validate_reel_url/', [
					'cookies'     => $this->cookies,
					'headers'     => [
						'User-Agent'                  => $this->buildUserAgent( '203.0.0.29.118', '314665256' ),
						'Accept-Encoding'             => 'gzip, deflate',
						'Accept'                      => '*/*',
						'Connection'                  => 'keep-alive',
						'X-IG-App-Locale'             => 'en_US',
						'X-IG-Device-Locale'          => 'en_US',
						'X-IG-Mapped-Locale'          => 'en_US',
						'X-IG-App-Startup-Country'    => 'US',
						'X-IG-WWW-Claim'              => '0',
						'X-Bloks-Is-Layout-RTL'       => 'false',
						'X-Bloks-Is-Panorama-Enabled' => 'true',
						'X-IG-Device-ID'              => $this->getSettings( 'uuid' ),
						'X-IG-Family-Device-ID'       => $this->generateUUID(),
						'X-IG-Android-ID'             => $this->getSettings( 'device_id' ),
						'X-IG-Timezone-Offset'        => '-14400',
						'X-IG-Connection-Type'        => 'WIFI',
						'X-IG-Capabilities'           => '3brTvx0=',
						'X-IG-App-ID'                 => '567067343352427',
						'Priority'                    => 'u=3',
						'Accept-Language'             => 'en-US',
						'X-MID'                       => $this->mid,
						'X-FB-HTTP-Engine'            => 'Liger',
						'X-FB-Client-IP'              => 'True',
						'X-FB-Server-Cluster'         => 'True',
						'IG-INTENDED-USER-ID'         => $this->getSettings( 'account_id' ),
						'X-IG-Nav-Chain'              => '9MV:self_profile:2,ProfileMediaTabFragment:self_profile:3,9Xf:self_following:4',
						'Content-Type'                => 'application/x-www-form-urlencoded; charset=UTF-8',
						'IG-U-DS-USER-ID'             => $this->getSettings( 'account_id' ),
						'X-Ads-Opt-Out'               => '0',
						'X-DEVICE-ID'                 => $this->getSettings( 'uuid' ),
						'X-CM-Bandwidth-KBPS'         => '-1.000',
						'X-CM-Latency'                => '1',
					],
					'form_params' => [
						'signed_body' => 'SIGNATURE.{"url":"' . $link . '","_uid":"' . $this->getSettings( 'account_id' ) . '","_uuid":"' . $this->getSettings( 'uuid' ) . '"}'
					]
				] )->getBody();
				$link_model = '{\"x\":0.5126011,\"y\":' . $link_y . ',\"z\":0,\"width\":0.80998676,\"height\":0.12075,\"rotation\":0.0,\"type\":\"story_link\",\"is_sticker\":true,\"selected_index\":0,\"tap_state\":0,\"link_type\":\"web\",\"url\":\"' . $link . '\",\"tap_state_str_id\":\"link_sticker_default\"}';
			}

			$hashtag = "";
			if ( ! empty( $hashtag ) )
			{
				$hashtag_y = (float) 1000;
				$hashtag_y     = $hashtag_y / $photo_info[ 'height' ];
				$hashtag_y     = number_format( $hashtag_y, 2 );
				$hashtag_model = '{\"x\":0.51,\"y\":' . $hashtag_y . ',\"z\":0,\"width\":0.8,\"height\":0.12,\"rotation\":0.0,\"type\":\"hashtag\",\"tag_name\":\"' . $hashtag . '\",\"is_sticker\":true,\"tap_state\":0,\"tap_state_str_id\":\"hashtag_sticker_gradient\"}';
			}

			if ( ! empty( $hashtag_model ) || ! empty( $link_model ) )
			{
				$tap_models = ! empty( $hashtag_model ) && ! empty( $link_model ) ? ( $hashtag_model . ',' . $link_model ) : ( empty( $link_model ) ? $hashtag_model : $link_model );
				$tap_models = ',"tap_models":"[' . $tap_models . ']"}';
			}

			try
			{
				$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/' . $endpoint, [
					'cookies'     => $this->cookies,
					'headers'     => [
						'User-Agent'                  => $this->buildUserAgent( '203.0.0.29.118', '314665256' ),
						'Accept-Encoding'             => 'gzip, deflate',
						'Accept'                      => '*/*',
						'Connection'                  => 'keep-alive',
						'X-IG-App-Locale'             => 'en_US',
						'X-IG-Device-Locale'          => 'en_US',
						'X-IG-Mapped-Locale'          => 'en_US',
						'X-IG-App-Startup-Country'    => 'US',
						'X-IG-WWW-Claim'              => '0',
						'X-Bloks-Is-Layout-RTL'       => 'false',
						'X-Bloks-Is-Panorama-Enabled' => 'true',
						'X-IG-Device-ID'              => $this->getSettings( 'uuid' ),
						'X-IG-Family-Device-ID'       => $this->generateUUID(),
						'X-IG-Android-ID'             => $this->getSettings( 'device_id' ),
						'X-IG-Timezone-Offset'        => '-14400',
						'X-IG-Connection-Type'        => 'WIFI',
						'X-IG-Capabilities'           => '3brTvx0=',
						'X-IG-App-ID'                 => '567067343352427',
						'Priority'                    => 'u=3',
						'Accept-Language'             => 'en-US',
						'X-MID'                       => $this->mid,
						'X-FB-HTTP-Engine'            => 'Liger',
						'X-FB-Client-IP'              => 'True',
						'X-FB-Server-Cluster'         => 'True',
						'IG-INTENDED-USER-ID'         => $this->getSettings( 'account_id' ),
						'X-IG-Nav-Chain'              => '9MV:self_profile:2,ProfileMediaTabFragment:self_profile:3,9Xf:self_following:4',
						'Content-Type'                => 'application/x-www-form-urlencoded; charset=UTF-8',
						'IG-U-DS-USER-ID'             => $this->getSettings( 'account_id' ),
						'X-Ads-Opt-Out'               => '0',
						'X-DEVICE-ID'                 => $this->getSettings( 'uuid' ),
						'X-CM-Bandwidth-KBPS'         => '-1.000',
						'X-CM-Latency'                => '1',
					],
					'form_params' => [
						'signed_body' => 'SIGNATURE.{"_uuid":"' . $this->getSettings( 'uuid' ) . '","device_id":"' . $this->getSettings( 'uuid' ) . '","text_metadata":"[{\"font_size\":40.0,\"scale\":1.0,\"width\":611.0,\"height\":169.0,\"x\":0.51414347,\"y\":0.8487708,\"rotation\":0.0}]","supported_capabilities_new":"[{\"name\":+\"SUPPORTED_SDK_VERSIONS\",+\"value\":+\"108.0,109.0,110.0,111.0,112.0,113.0,114.0,115.0,116.0,117.0,118.0,119.0,120.0,121.0,122.0,123.0,124.0,125.0,126.0,127.0\"},+{\"name\":+\"FACE_TRACKER_VERSION\",+\"value\":+\"14\"},+{\"name\":+\"segmentation\",+\"value\":+\"segmentation_enabled\"},+{\"name\":+\"COMPRESSION\",+\"value\":+\"ETC2_COMPRESSION\"},+{\"name\":+\"world_tracker\",+\"value\":+\"world_tracker_enabled\"},+{\"name\":+\"gyroscope\",+\"value\":+\"gyroscope_enabled\"}]","has_original_sound":"1","camera_session_id":"45e0c374-d84f-4289-9f81-a7419752f684","scene_capture_type":"","timezone_offset":"-14400","client_shared_at":"' . ( time() - 5 ) . '","story_sticker_ids":"link_sticker_default","media_folder":"Camera","configure_mode":"1","source_type":"4","creation_surface":"camera","imported_taken_at":1643659109,"capture_type":"normal","rich_text_format_types":"[\"default\"]","upload_id":"' . $uploadId . '","client_timestamp":"' . time() . '","device":{"android_version":26,"android_release":"8.0.0","manufacturer":"Xiaomi","model":"MI+5s"},"_uid":49154269846,"composition_id":"8e56be0b-ba75-44c6-bd61-9fd77680f84a","app_attribution_android_namespace":"","media_transformation_info":"{\"width\":\"720\",\"height\":\"720\",\"x_transform\":\"0\",\"y_transform\":\"0\",\"zoom\":\"1.0\",\"rotation\":\"0.0\",\"background_coverage\":\"0.0\"}","original_media_type":"photo","camera_entry_point":"121","edits":{"crop_original_size":[720.0,720.0],"filter_type":0,"filter_strength":1.0},"extra":{"source_width":720,"source_height":720}' . $tap_models
					]
				] )->getBody();

				$response = json_decode( $response, true );
			}
			catch (\Exception $e )
			{
				$response = [];
			}
		}
		else
		{
			$endpoint = 'media/configure/?timezone_offset=' . date( 'Z' );

			$sendData = [
				'_csrftoken'            => $this->getCsrfToken(),
				'_uid'                  => $this->getSettings( 'account_id' ),
				'_uuid'                 => $this->getSettings( 'uuid' ),
				'edits'                 => [
					'crop_original_size' => [ (float) $photo_info[ 'width' ], (float) $photo_info[ 'height' ] ],
					'crop_zoom'          => 1.0,
					'crop_center'        => [ 0.0, -0.0 ]
				],
				'device'                => [
					'manufacturer'    => $this->getDeviceParam( 'manufacturer' ),
					'model'           => $this->getDeviceParam( 'model' ),
					'android_version' => $this->getDeviceParam( 'androidVersion' ),
					'android_release' => $this->getDeviceParam( 'androidRelease' ),
				],
				'extra'                 => [
					'source_width'  => $photo_info[ 'width' ],
					'source_height' => $photo_info[ 'height' ],
				],
				'upload_id'             => $uploadId,
				'date_time_original'    => $date,
				'date_time_digitalized' => $date,
				'caption'               => $caption,
				'source_type'           => 4,
				'media_folder'          => 'Camera'
			];

			try
			{
				$response = (string) $this->client->post( 'https://i.instagram.com/api/v1/' . $endpoint, [
					'form_params' => $this->signData( $sendData )
				] )->getBody();

				$response = json_decode( $response, true );
			}
			catch (\Exception $e )
			{
				$response = [];
			}
		}

		return $response;
	}


	private function uploadIgVideoResumableMethod ( $uploadId, $video, $target )
	{
		$params = [
			'upload_id'                => $uploadId,
			'retry_context'            => json_encode( [
				'num_step_auto_retry'   => 0,
				'num_reupload'          => 0,
				'num_step_manual_retry' => 0
			] ),
			'xsharing_user_ids'        => '[]',
			'upload_media_height'      => (string) $video[ 'height' ],
			'upload_media_width'       => (string) $video[ 'width' ],
			'upload_media_duration_ms' => (string) $video[ 'duration' ] * 1000,
			'media_type'               => '2',
			'potential_share_types'    => json_encode( [ 'not supported type' ] ),
		];

		if ( $target == 'story' )
		{
			$params[ 'for_album' ] = '1';
		}

		if( $target == 'reel' )
		{
			$params[ 'is_clips_video' ] = '1';
		}

		$entity_name = sprintf( '%s_%d_%d', $uploadId, 0, $this->hashCode( basename( $video[ 'path' ] ) ) );
		$endpoint    = 'https://i.instagram.com/rupload_igvideo/' . $entity_name . '?target=' . $this->getCookie( 'rur', 'PRN' );

		try
		{
			$response = (string) $this->client->post( $endpoint, [
				'headers' => [
					'X_FB_VIDEO_WATERFALL_ID'    => $this->generateUUID(),
					'X-Instagram-Rupload-Params' => json_encode( $this->reorderByHashCode( $params ) ),
					'X-Entity-Type'              => 'video/mp4',
					'X-Entity-Name'              => $entity_name,
					'X-Entity-Length'            => filesize( $video[ 'path' ] ),
					'Offset'                     => '0'
				],
				'body'    => fopen( $video[ 'path' ], 'r' )
			] )->getBody();

			$response = json_decode( $response, true );
		}
		catch (\Exception $e )
		{
			$response = [];
		}

		return $response;
	}





	private function createUploadId ()
	{
		return number_format( round( microtime( true ) * 1000 ), 0, '', '' );
	}


	private function getPublicKeys ()
	{
		$client = new Client();

		$response = '';

		try
		{
			$response = $client->get( "https://i.instagram.com/api/v1/qe/sync/" );
		}
		catch (\Exception $e )
		{
			if ( method_exists( $e, 'getResponse' ) )
			{
				$response = @$e->getResponse();

				if ( is_null( $response ) )
				{
					return false;
				}
			}
		}

		if ( ! method_exists( $response, 'getHeader' ) )
		{
			return false;
		}

		if ( empty( $response->getHeader( "ig-set-password-encryption-key-id" )[ 0 ] ) || empty( $response->getHeader( "ig-set-password-encryption-pub-key" )[ 0 ] ) )
		{
			return false;
		}

		$pubKeyID  = $response->getHeader( "ig-set-password-encryption-key-id" )[ 0 ];
		$pubKeyStr = $response->getHeader( "ig-set-password-encryption-pub-key" )[ 0 ];

		return [
			$pubKeyID,
			$pubKeyStr,
		];
	}

	private function encPass ( $password )
	{
		list( $publicKeyId, $publicKey ) = $this->getPublicKeys();
		$key  = substr( md5( uniqid( mt_rand() ) ), 0, 32 );
		$iv   = substr( md5( uniqid( mt_rand() ) ), 0, 12 );
		$time = time();

		$rsa          = PublicKeyLoader::loadPublicKey( base64_decode( $publicKey ) );
		$rsa          = @$rsa->withPadding( RSA::ENCRYPTION_PKCS1 );
		$encryptedRSA = $rsa->encrypt( $key );

		$aes = new AES( 'gcm' );
		$aes->setNonce( $iv );
		$aes->setKey( $key );
		$aes->setAAD( strval( $time ) );
		$encrypted = $aes->encrypt( $password );

		$payload = base64_encode( "\x01" | pack( 'n', intval( $publicKeyId ) ) . $iv . pack( 's', strlen( $encryptedRSA ) ) . $encryptedRSA . $aes->getTag() . $encrypted );

		return sprintf( '#PWD_INSTAGRAM:4:%s:%s', $time, $payload );
	}
}

