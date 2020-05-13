<?php

class ControllerExtensionThemeGen extends Controller {

	private $error = array();

	public function index() {
		$this->load->language( 'extension/theme/gen' );

		$this->document->setTitle( $this->language->get( 'heading_title' ) );

		$this->load->model( 'setting/setting' );

		$this->load->model( 'catalog/information' );

		$this->load->model( 'tool/image' );

		if ( ( $this->request->server['REQUEST_METHOD'] == 'POST' ) && $this->validate() ) {
			$this->model_setting_setting->editSetting( 'theme_gen', $this->request->post, $this->request->get['store_id'] );
			// Postcode
			if ( is_array( $this->request->post['theme_gen_checkout_hidden_fields'] ) && in_array( 'payment_postcode', $this->request->post['theme_gen_checkout_hidden_fields'] ) ) {
				$this->editCountryPostcode( 0 );
			} else {
				$this->editCountryPostcode( 1 );
			}

			// Option variation

			if ( $this->request->post['theme_gen_option_variation'] ) {
				$this->load->model( 'catalog/option' );
				$this->model_catalog_option->createVariationTables();
			}

			$this->session->data['success'] = $this->language->get( 'text_success' );

			$this->response->redirect( $this->url->link( 'marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme', true ) );
		}

		// Error warning
		if ( isset( $this->error['warning'] ) ) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if ( isset( $this->error['product_limit'] ) ) {
			$data['error_product_limit'] = $this->error['product_limit'];
		} else {
			$data['error_product_limit'] = '';
		}

		if ( isset( $this->error['product_description_length'] ) ) {
			$data['error_product_description_length'] = $this->error['product_description_length'];
		} else {
			$data['error_product_description_length'] = '';
		}

		if ( isset( $this->error['image_category'] ) ) {
			$data['error_image_category'] = $this->error['image_category'];
		} else {
			$data['error_image_category'] = '';
		}

		if ( isset( $this->error['image_thumb'] ) ) {
			$data['error_image_thumb'] = $this->error['image_thumb'];
		} else {
			$data['error_image_thumb'] = '';
		}

		if ( isset( $this->error['image_popup'] ) ) {
			$data['error_image_popup'] = $this->error['image_popup'];
		} else {
			$data['error_image_popup'] = '';
		}

		if ( isset( $this->error['image_product'] ) ) {
			$data['error_image_product'] = $this->error['image_product'];
		} else {
			$data['error_image_product'] = '';
		}

		if ( isset( $this->error['image_additional'] ) ) {
			$data['error_image_additional'] = $this->error['image_additional'];
		} else {
			$data['error_image_additional'] = '';
		}

		if ( isset( $this->error['image_related'] ) ) {
			$data['error_image_related'] = $this->error['image_related'];
		} else {
			$data['error_image_related'] = '';
		}

		if ( isset( $this->error['image_compare'] ) ) {
			$data['error_image_compare'] = $this->error['image_compare'];
		} else {
			$data['error_image_compare'] = '';
		}

		if ( isset( $this->error['image_wishlist'] ) ) {
			$data['error_image_wishlist'] = $this->error['image_wishlist'];
		} else {
			$data['error_image_wishlist'] = '';
		}

		if ( isset( $this->error['image_cart'] ) ) {
			$data['error_image_cart'] = $this->error['image_cart'];
		} else {
			$data['error_image_cart'] = '';
		}

		if ( isset( $this->error['image_location'] ) ) {
			$data['error_image_location'] = $this->error['image_location'];
		} else {
			$data['error_image_location'] = '';
		}

		// Breadcrumb
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get( 'text_home' ),
			'href' => $this->url->link( 'common/dashboard', 'user_token=' . $this->session->data['user_token'], true )
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get( 'text_extension' ),
			'href' => $this->url->link( 'marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme', true )
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get( 'heading_title' ),
			'href' => $this->url->link( 'extension/theme/gen', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true )
		);

		// Actions
		$data['action'] = $this->url->link( 'extension/theme/gen', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true );

		$data['cancel'] = $this->url->link( 'marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme', true );

		// General settings
		if ( isset( $this->request->get['store_id'] ) && ( $this->request->server['REQUEST_METHOD'] != 'POST' ) ) {
			$setting_info = $this->model_setting_setting->getSetting( 'theme_gen', $this->request->get['store_id'] );
		}

		if ( isset( $this->request->post['theme_gen_directory'] ) ) {
			$data['theme_gen_directory'] = $this->request->post['theme_gen_directory'];
		} elseif ( isset( $setting_info['theme_gen_directory'] ) ) {
			$data['theme_gen_directory'] = $setting_info['theme_gen_directory'];
		} else {
			$data['theme_gen_directory'] = 'gen';
		}

		$data['directories'] = array();

		$directories = glob( DIR_CATALOG . 'view/theme/*', GLOB_ONLYDIR );

		foreach ( $directories as $directory ) {
			$data['directories'][] = basename( $directory );
		}

		if ( isset( $this->request->post['theme_gen_product_limit'] ) ) {
			$data['theme_gen_product_limit'] = $this->request->post['theme_gen_product_limit'];
		} elseif ( isset( $setting_info['theme_gen_product_limit'] ) ) {
			$data['theme_gen_product_limit'] = $setting_info['theme_gen_product_limit'];
		} else {
			$data['theme_gen_product_limit'] = 15;
		}

		if ( isset( $this->request->post['theme_gen_status'] ) ) {
			$data['theme_gen_status'] = $this->request->post['theme_gen_status'];
		} elseif ( isset( $setting_info['theme_gen_status'] ) ) {
			$data['theme_gen_status'] = $setting_info['theme_gen_status'];
		} else {
			$data['theme_gen_status'] = '';
		}

		if ( isset( $this->request->post['theme_gen_product_description_length'] ) ) {
			$data['theme_gen_product_description_length'] = $this->request->post['theme_gen_product_description_length'];
		} elseif ( isset( $setting_info['theme_gen_product_description_length'] ) ) {
			$data['theme_gen_product_description_length'] = $setting_info['theme_gen_product_description_length'];
		} else {
			$data['theme_gen_product_description_length'] = 100;
		}

		if ( isset( $this->request->post['theme_gen_image_category_width'] ) ) {
			$data['theme_gen_image_category_width'] = $this->request->post['theme_gen_image_category_width'];
		} elseif ( isset( $setting_info['theme_gen_image_category_width'] ) ) {
			$data['theme_gen_image_category_width'] = $setting_info['theme_gen_image_category_width'];
		} else {
			$data['theme_gen_image_category_width'] = 80;
		}

		if ( isset( $this->request->post['theme_gen_image_category_height'] ) ) {
			$data['theme_gen_image_category_height'] = $this->request->post['theme_gen_image_category_height'];
		} elseif ( isset( $setting_info['theme_gen_image_category_height'] ) ) {
			$data['theme_gen_image_category_height'] = $setting_info['theme_gen_image_category_height'];
		} else {
			$data['theme_gen_image_category_height'] = 80;
		}

		if ( isset( $this->request->post['theme_gen_image_thumb_width'] ) ) {
			$data['theme_gen_image_thumb_width'] = $this->request->post['theme_gen_image_thumb_width'];
		} elseif ( isset( $setting_info['theme_gen_image_thumb_width'] ) ) {
			$data['theme_gen_image_thumb_width'] = $setting_info['theme_gen_image_thumb_width'];
		} else {
			$data['theme_gen_image_thumb_width'] = 228;
		}

		if ( isset( $this->request->post['theme_gen_image_thumb_height'] ) ) {
			$data['theme_gen_image_thumb_height'] = $this->request->post['theme_gen_image_thumb_height'];
		} elseif ( isset( $setting_info['theme_gen_image_thumb_height'] ) ) {
			$data['theme_gen_image_thumb_height'] = $setting_info['theme_gen_image_thumb_height'];
		} else {
			$data['theme_gen_image_thumb_height'] = 228;
		}

		if ( isset( $this->request->post['theme_gen_image_popup_width'] ) ) {
			$data['theme_gen_image_popup_width'] = $this->request->post['theme_gen_image_popup_width'];
		} elseif ( isset( $setting_info['theme_gen_image_popup_width'] ) ) {
			$data['theme_gen_image_popup_width'] = $setting_info['theme_gen_image_popup_width'];
		} else {
			$data['theme_gen_image_popup_width'] = 500;
		}

		if ( isset( $this->request->post['theme_gen_image_popup_height'] ) ) {
			$data['theme_gen_image_popup_height'] = $this->request->post['theme_gen_image_popup_height'];
		} elseif ( isset( $setting_info['theme_gen_image_popup_height'] ) ) {
			$data['theme_gen_image_popup_height'] = $setting_info['theme_gen_image_popup_height'];
		} else {
			$data['theme_gen_image_popup_height'] = 500;
		}

		if ( isset( $this->request->post['theme_gen_image_product_width'] ) ) {
			$data['theme_gen_image_product_width'] = $this->request->post['theme_gen_image_product_width'];
		} elseif ( isset( $setting_info['theme_gen_image_product_width'] ) ) {
			$data['theme_gen_image_product_width'] = $setting_info['theme_gen_image_product_width'];
		} else {
			$data['theme_gen_image_product_width'] = 228;
		}

		if ( isset( $this->request->post['theme_gen_image_product_height'] ) ) {
			$data['theme_gen_image_product_height'] = $this->request->post['theme_gen_image_product_height'];
		} elseif ( isset( $setting_info['theme_gen_image_product_height'] ) ) {
			$data['theme_gen_image_product_height'] = $setting_info['theme_gen_image_product_height'];
		} else {
			$data['theme_gen_image_product_height'] = 228;
		}

		if ( isset( $this->request->post['theme_gen_image_additional_width'] ) ) {
			$data['theme_gen_image_additional_width'] = $this->request->post['theme_gen_image_additional_width'];
		} elseif ( isset( $setting_info['theme_gen_image_additional_width'] ) ) {
			$data['theme_gen_image_additional_width'] = $setting_info['theme_gen_image_additional_width'];
		} else {
			$data['theme_gen_image_additional_width'] = 74;
		}

		if ( isset( $this->request->post['theme_gen_image_additional_height'] ) ) {
			$data['theme_gen_image_additional_height'] = $this->request->post['theme_gen_image_additional_height'];
		} elseif ( isset( $setting_info['theme_gen_image_additional_height'] ) ) {
			$data['theme_gen_image_additional_height'] = $setting_info['theme_gen_image_additional_height'];
		} else {
			$data['theme_gen_image_additional_height'] = 74;
		}

		if ( isset( $this->request->post['theme_gen_image_related_width'] ) ) {
			$data['theme_gen_image_related_width'] = $this->request->post['theme_gen_image_related_width'];
		} elseif ( isset( $setting_info['theme_gen_image_related_width'] ) ) {
			$data['theme_gen_image_related_width'] = $setting_info['theme_gen_image_related_width'];
		} else {
			$data['theme_gen_image_related_width'] = 80;
		}

		if ( isset( $this->request->post['theme_gen_image_related_height'] ) ) {
			$data['theme_gen_image_related_height'] = $this->request->post['theme_gen_image_related_height'];
		} elseif ( isset( $setting_info['theme_gen_image_related_height'] ) ) {
			$data['theme_gen_image_related_height'] = $setting_info['theme_gen_image_related_height'];
		} else {
			$data['theme_gen_image_related_height'] = 80;
		}

		if ( isset( $this->request->post['theme_gen_image_compare_width'] ) ) {
			$data['theme_gen_image_compare_width'] = $this->request->post['theme_gen_image_compare_width'];
		} elseif ( isset( $setting_info['theme_gen_image_compare_width'] ) ) {
			$data['theme_gen_image_compare_width'] = $setting_info['theme_gen_image_compare_width'];
		} else {
			$data['theme_gen_image_compare_width'] = 90;
		}

		if ( isset( $this->request->post['theme_gen_image_compare_height'] ) ) {
			$data['theme_gen_image_compare_height'] = $this->request->post['theme_gen_image_compare_height'];
		} elseif ( isset( $setting_info['theme_gen_image_compare_height'] ) ) {
			$data['theme_gen_image_compare_height'] = $setting_info['theme_gen_image_compare_height'];
		} else {
			$data['theme_gen_image_compare_height'] = 90;
		}

		if ( isset( $this->request->post['theme_gen_image_wishlist_width'] ) ) {
			$data['theme_gen_image_wishlist_width'] = $this->request->post['theme_gen_image_wishlist_width'];
		} elseif ( isset( $setting_info['theme_gen_image_wishlist_width'] ) ) {
			$data['theme_gen_image_wishlist_width'] = $setting_info['theme_gen_image_wishlist_width'];
		} else {
			$data['theme_gen_image_wishlist_width'] = 47;
		}

		if ( isset( $this->request->post['theme_gen_image_wishlist_height'] ) ) {
			$data['theme_gen_image_wishlist_height'] = $this->request->post['theme_gen_image_wishlist_height'];
		} elseif ( isset( $setting_info['theme_gen_image_wishlist_height'] ) ) {
			$data['theme_gen_image_wishlist_height'] = $setting_info['theme_gen_image_wishlist_height'];
		} else {
			$data['theme_gen_image_wishlist_height'] = 47;
		}

		if ( isset( $this->request->post['theme_gen_image_cart_width'] ) ) {
			$data['theme_gen_image_cart_width'] = $this->request->post['theme_gen_image_cart_width'];
		} elseif ( isset( $setting_info['theme_gen_image_cart_width'] ) ) {
			$data['theme_gen_image_cart_width'] = $setting_info['theme_gen_image_cart_width'];
		} else {
			$data['theme_gen_image_cart_width'] = 47;
		}

		if ( isset( $this->request->post['theme_gen_image_cart_height'] ) ) {
			$data['theme_gen_image_cart_height'] = $this->request->post['theme_gen_image_cart_height'];
		} elseif ( isset( $setting_info['theme_gen_image_cart_height'] ) ) {
			$data['theme_gen_image_cart_height'] = $setting_info['theme_gen_image_cart_height'];
		} else {
			$data['theme_gen_image_cart_height'] = 47;
		}

		if ( isset( $this->request->post['theme_gen_image_location_width'] ) ) {
			$data['theme_gen_image_location_width'] = $this->request->post['theme_gen_image_location_width'];
		} elseif ( isset( $setting_info['theme_gen_image_location_width'] ) ) {
			$data['theme_gen_image_location_width'] = $setting_info['theme_gen_image_location_width'];
		} else {
			$data['theme_gen_image_location_width'] = 268;
		}

		if ( isset( $this->request->post['theme_gen_image_location_height'] ) ) {
			$data['theme_gen_image_location_height'] = $this->request->post['theme_gen_image_location_height'];
		} elseif ( isset( $setting_info['theme_gen_image_location_height'] ) ) {
			$data['theme_gen_image_location_height'] = $setting_info['theme_gen_image_location_height'];
		} else {
			$data['theme_gen_image_location_height'] = 50;
		}

		//  - Custom settings -

		// Checkout
		if ( isset( $this->request->post['theme_gen_checkout_hidden_fields'] ) ) {
			$data['shipping_comment'] = in_array( 'shipping_comment', $this->request->post['theme_gen_checkout_hidden_fields'] );
		} elseif ( isset( $setting_info['theme_gen_checkout_hidden_fields'] ) ) {
			$data['shipping_comment'] = in_array( 'shipping_comment', $setting_info['theme_gen_checkout_hidden_fields'] );
		} else {
			$data['shipping_comment'] = 0;
		}

		if ( isset( $this->request->post['theme_gen_checkout_hidden_fields'] ) ) {
			$data['payment_comment'] = in_array( 'payment_comment', $this->request->post['theme_gen_checkout_hidden_fields'] );
		} elseif ( isset( $setting_info['theme_gen_checkout_hidden_fields'] ) ) {
			$data['payment_comment'] = in_array( 'payment_comment', $setting_info['theme_gen_checkout_hidden_fields'] );
		} else {
			$data['payment_comment'] = 0;
		}
		if ( isset( $this->request->post['theme_gen_checkout_hidden_fields'] ) ) {
			$data['payment_company'] = in_array( 'payment_company', $this->request->post['theme_gen_checkout_hidden_fields'] );
		} elseif ( isset( $setting_info['theme_gen_checkout_hidden_fields'] ) ) {
			$data['payment_company'] = in_array( 'payment_company', $setting_info['theme_gen_checkout_hidden_fields'] );
		} else {
			$data['payment_company'] = 0;
		}
		if ( isset( $this->request->post['theme_gen_checkout_hidden_fields'] ) ) {
			$data['payment_address_2'] = in_array( 'payment_address_2', $this->request->post['theme_gen_checkout_hidden_fields'] );
		} elseif ( isset( $setting_info['theme_gen_checkout_hidden_fields'] ) ) {
			$data['payment_address_2'] = in_array( 'payment_address_2', $setting_info['theme_gen_checkout_hidden_fields'] );
		} else {
			$data['payment_address_2'] = 0;

		}
		if ( isset( $this->request->post['theme_gen_checkout_hidden_fields'] ) ) {
			$data['payment_postcode'] = in_array( 'payment_postcode', $this->request->post['theme_gen_checkout_hidden_fields'] );
		} elseif ( isset( $setting_info['theme_gen_checkout_hidden_fields'] ) ) {
			$data['payment_postcode'] = in_array( 'payment_postcode', $setting_info['theme_gen_checkout_hidden_fields'] );
		} else {
			$data['payment_postcode'] = 0;
		}
		if ( isset( $this->request->post['theme_gen_checkout_hidden_fields'] ) ) {
			$data['payment_country'] = in_array( 'payment_country', $this->request->post['theme_gen_checkout_hidden_fields'] );
		} elseif ( isset( $setting_info['theme_gen_checkout_hidden_fields'] ) ) {
			$data['payment_country'] = in_array( 'payment_country', $setting_info['theme_gen_checkout_hidden_fields'] );
		} else {
			$data['payment_country'] = 0;
		}
		if ( isset( $this->request->post['theme_gen_checkout_hidden_fields'] ) ) {
			$data['payment_email'] = in_array( 'payment_email', $this->request->post['theme_gen_checkout_hidden_fields'] );
		} elseif ( isset( $setting_info['theme_gen_checkout_hidden_fields'] ) ) {
			$data['payment_email'] = in_array( 'payment_email', $setting_info['theme_gen_checkout_hidden_fields'] );
		} else {
			$data['payment_email'] = 0;
		}

		//  - Functionality -

		// Sticky card
		if ( isset( $this->request->post['theme_gen_sticky_cart'] ) ) {
			$data['theme_gen_sticky_cart'] = $this->request->post['theme_gen_sticky_cart'];
		} elseif ( isset( $setting_info['theme_gen_sticky_cart'] ) ) {
			$data['theme_gen_sticky_cart'] = $setting_info['theme_gen_sticky_cart'];
		} else {
			$data['theme_gen_sticky_cart'] = 0;
		}

		// Scroll to top
		if ( isset( $this->request->post['theme_gen_scroll_to_top'] ) ) {
			$data['theme_gen_scroll_to_top'] = $this->request->post['theme_gen_scroll_to_top'];
		} elseif ( isset( $setting_info['theme_gen_scroll_to_top'] ) ) {
			$data['theme_gen_scroll_to_top'] = $setting_info['theme_gen_scroll_to_top'];
		} else {
			$data['theme_gen_scroll_to_top'] = 0;
		}

		// Copy admin category text
		if ( isset( $this->request->post['theme_gen_admin_category_copy_status'] ) ) {
			$data['theme_gen_admin_category_copy_status'] = $this->request->post['theme_gen_admin_category_copy_status'];
		} elseif ( isset( $setting_info['theme_gen_admin_category_copy_status'] ) ) {
			$data['theme_gen_admin_category_copy_status'] = $setting_info['theme_gen_admin_category_copy_status'];
		} else {
			$data['theme_gen_admin_category_copy_status'] = 0;
		}
		// Copy admin page text
		if ( isset( $this->request->post['theme_gen_admin_product_copy_status'] ) ) {
			$data['theme_gen_admin_product_copy_status'] = $this->request->post['theme_gen_admin_product_copy_status'];
		} elseif ( isset( $setting_info['theme_gen_admin_product_copy_status'] ) ) {
			$data['theme_gen_admin_product_copy_status'] = $setting_info['theme_gen_admin_product_copy_status'];
		} else {
			$data['theme_gen_admin_product_copy_status'] = 0;
		}
		// Dynamically counting price with options
		if ( isset( $this->request->post['theme_gen_counting_price_with_options'] ) ) {
			$data['theme_gen_counting_price_with_options'] = $this->request->post['theme_gen_counting_price_with_options'];
		} elseif ( isset( $setting_info['theme_gen_counting_price_with_options'] ) ) {
			$data['theme_gen_counting_price_with_options'] = $setting_info['theme_gen_counting_price_with_options'];
		} else {
			$data['theme_gen_counting_price_with_options'] = 0;
		}
		// Option variations
		if ( isset( $this->request->post['theme_gen_option_variation'] ) ) {
			$data['theme_gen_option_variation'] = $this->request->post['theme_gen_option_variation'];
		} elseif ( isset( $setting_info['theme_gen_option_variation'] ) ) {
			$data['theme_gen_option_variation'] = $setting_info['theme_gen_option_variation'];
		} else {
			$data['theme_gen_option_variation'] = 0;
		}
		// Opengraph
		$data['placeholder'] = $this->model_tool_image->resize( 'no_image.png', 100, 100 );

		if ( isset( $this->request->post['theme_gen_opengraph_logo'] ) ) {
			$data['theme_gen_opengraph_logo'] = $this->request->post['theme_gen_opengraph_logo'];
		} elseif ( isset( $setting_info['theme_gen_opengraph_logo'] ) ) {
			$data['theme_gen_opengraph_logo'] = $this->config->get( 'theme_gen_opengraph_logo' );
		}

		if ( isset( $this->request->post['theme_gen_opengraph_logo'] ) && is_file( DIR_IMAGE . $this->request->post['theme_gen_opengraph_logo'] ) ) {
			$data['opengraph_logo_preview'] = $this->model_tool_image->resize( $this->request->post['theme_gen_opengraph_logo'], 100, 100 );
		} elseif ( $this->config->get( 'theme_gen_opengraph_logo' ) && is_file( DIR_IMAGE . $this->config->get( 'theme_gen_opengraph_logo' ) ) ) {
			$data['opengraph_logo_preview'] = $this->model_tool_image->resize( $this->config->get( 'theme_gen_opengraph_logo' ), 100, 100 );
		} else {
			$data['opengraph_logo_preview'] = $this->model_tool_image->resize( 'no_image.png', 100, 100 );
		}

		if ( isset( $this->request->post['theme_gen_opengraph_api_id'] ) ) {
			$data['theme_gen_opengraph_api_id'] = $this->request->post['theme_gen_opengraph_api_id'];
		} elseif ( isset( $setting_info['theme_gen_opengraph_api_id'] ) ) {
			$data['theme_gen_opengraph_api_id'] = $this->config->get( 'theme_gen_opengraph_api_id' );
		} else {
			$data['theme_gen_opengraph_api_id'] = "";
		}
		// visa mastercard
		if ( isset( $this->request->post['theme_gen_visa_mastercard_logo'] ) ) {
			$data['theme_gen_visa_mastercard_logo'] = $this->request->post['theme_gen_visa_mastercard_logo'];
		} elseif ( isset( $setting_info['theme_gen_visa_mastercard_logo'] ) ) {
			$data['theme_gen_visa_mastercard_logo'] = $this->config->get( 'theme_gen_visa_mastercard_logo' );
		}

		if ( isset( $this->request->post['theme_gen_visa_mastercard_logo'] ) && is_file( DIR_IMAGE . $this->request->post['theme_gen_visa_mastercard_logo'] ) ) {
			$data['visa_mastercard_logo_preview'] = $this->model_tool_image->resize( $this->request->post['theme_gen_visa_mastercard_logo'], 100, 100 );
		} elseif ( $this->config->get( 'theme_gen_visa_mastercard_logo' ) && is_file( DIR_IMAGE . $this->config->get( 'theme_gen_visa_mastercard_logo' ) ) ) {
			$data['visa_mastercard_logo_preview'] = $this->model_tool_image->resize( $this->config->get( 'theme_gen_visa_mastercard_logo' ), 100, 100 );
		} else {
			$data['visa_mastercard_logo_preview'] = $this->model_tool_image->resize( 'no_image.png', 100, 100 );
		}

		// instagram

		if ( isset( $this->request->post['theme_gen_instagram_logo'] ) ) {
			$data['theme_gen_instagram_logo'] = $this->request->post['theme_gen_instagram_logo'];
		} elseif ( isset( $setting_info['theme_gen_instagram_logo'] ) ) {
			$data['theme_gen_instagram_logo'] = $this->config->get( 'theme_gen_instagram_logo' );
		}

		if ( isset( $this->request->post['theme_gen_instagram_logo'] ) && is_file( DIR_IMAGE . $this->request->post['theme_gen_instagram_logo'] ) ) {
			$data['instagram_logo_preview'] = $this->model_tool_image->resize( $this->request->post['theme_gen_instagram_logo'], 100, 100 );
		} elseif ( $this->config->get( 'theme_gen_instagram_logo' ) && is_file( DIR_IMAGE . $this->config->get( 'theme_gen_instagram_logo' ) ) ) {
			$data['instagram_logo_preview'] = $this->model_tool_image->resize( $this->config->get( 'theme_gen_instagram_logo' ), 100, 100 );
		} else {
			$data['instagram_logo_preview'] = $this->model_tool_image->resize( 'no_image.png', 100, 100 );
		}

		if ( isset( $this->request->post['theme_gen_instagram_account'] ) ) {
			$data['theme_gen_instagram_account'] = $this->request->post['theme_gen_instagram_account'];
		} elseif ( isset( $setting_info['theme_gen_instagram_account'] ) ) {
			$data['theme_gen_instagram_account'] = $this->config->get( 'theme_gen_instagram_account' );
		} else {
			$data['theme_gen_instagram_account'] = "";
		}
		// Menu Info

		$data['informations'] = array();

		$data['blog_informations'] = array();

		foreach ( $this->model_catalog_information->getInformations() as $result ) {

			$data['informations'][]      = array(
				'title' => $result['title'],
				'id'    => $result['information_id']
			);
			$data['blog_informations'][] = array(
				'title' => $result['title'],
				'id'    => $result['information_id']
			);

		}
		if ( isset( $this->request->post['theme_gen_menu_info_links'] ) ) {
			$data['info_id'] = $this->request->post['theme_gen_menu_info_links'];
		} elseif ( isset( $setting_info['theme_gen_menu_info_links'] ) ) {
			$data['info_id'] = $setting_info['theme_gen_menu_info_links'];
		} else {
			$data['info_id'] = array();
		}

		$data['informations'][] = array(
			'title' => $this->language->get( 'text_contact' ),
			'id'    => 'contact'
		);
		$data['informations'][] = array(
			'title' => $this->language->get( 'text_voucher' ),
			'id'    => 'voucher'
		);
		$data['informations'][] = array(
			'title' => $this->language->get( 'text_affiliate' ),
			'id'    => 'affiliate'
		);
		$data['informations'][] = array(
			'title' => $this->language->get( 'text_special' ),
			'id'    => 'special'
		);
		$data['informations'][] = array(
			'title' => $this->language->get( 'text_blog_link' ),
			'id'    => 'blog'
		);

		if ( isset( $this->request->post['theme_gen_blog_info_links'] ) ) {
			$data['blog_info_id'] = $this->request->post['theme_gen_blog_info_links'];
		} elseif ( isset( $setting_info['theme_gen_blog_info_links'] ) ) {
			$data['blog_info_id'] = $setting_info['theme_gen_blog_info_links'];
		} else {
			$data['blog_info_id'] = array();
		}

		$data['currency_symbol_left']  = $this->currency->getSymbolLeft( $this->config->get( 'config_currency' ) );
		$data['currency_symbol_right'] = $this->currency->getSymbolRight( $this->config->get( 'config_currency' ) );


		$data['header']      = $this->load->controller( 'common/header' );
		$data['column_left'] = $this->load->controller( 'common/column_left' );
		$data['footer']      = $this->load->controller( 'common/footer' );

		$this->response->setOutput( $this->load->view( 'extension/theme/gen', $data ) );
	}

	protected function validate() {
		if ( ! $this->user->hasPermission( 'modify', 'extension/theme/gen' ) ) {
			$this->error['warning'] = $this->language->get( 'error_permission' );
		}

		if ( ! $this->request->post['theme_gen_product_limit'] ) {
			$this->error['product_limit'] = $this->language->get( 'error_limit' );
		}

		if ( ! $this->request->post['theme_gen_product_description_length'] ) {
			$this->error['product_description_length'] = $this->language->get( 'error_limit' );
		}

		if ( ! $this->request->post['theme_gen_image_category_width'] || ! $this->request->post['theme_gen_image_category_height'] ) {
			$this->error['image_category'] = $this->language->get( 'error_image_category' );
		}

		if ( ! $this->request->post['theme_gen_image_thumb_width'] || ! $this->request->post['theme_gen_image_thumb_height'] ) {
			$this->error['image_thumb'] = $this->language->get( 'error_image_thumb' );
		}

		if ( ! $this->request->post['theme_gen_image_popup_width'] || ! $this->request->post['theme_gen_image_popup_height'] ) {
			$this->error['image_popup'] = $this->language->get( 'error_image_popup' );
		}

		if ( ! $this->request->post['theme_gen_image_product_width'] || ! $this->request->post['theme_gen_image_product_height'] ) {
			$this->error['image_product'] = $this->language->get( 'error_image_product' );
		}

		if ( ! $this->request->post['theme_gen_image_additional_width'] || ! $this->request->post['theme_gen_image_additional_height'] ) {
			$this->error['image_additional'] = $this->language->get( 'error_image_additional' );
		}

		if ( ! $this->request->post['theme_gen_image_related_width'] || ! $this->request->post['theme_gen_image_related_height'] ) {
			$this->error['image_related'] = $this->language->get( 'error_image_related' );
		}

		if ( ! $this->request->post['theme_gen_image_compare_width'] || ! $this->request->post['theme_gen_image_compare_height'] ) {
			$this->error['image_compare'] = $this->language->get( 'error_image_compare' );
		}

		if ( ! $this->request->post['theme_gen_image_wishlist_width'] || ! $this->request->post['theme_gen_image_wishlist_height'] ) {
			$this->error['image_wishlist'] = $this->language->get( 'error_image_wishlist' );
		}

		if ( ! $this->request->post['theme_gen_image_cart_width'] || ! $this->request->post['theme_gen_image_cart_height'] ) {
			$this->error['image_cart'] = $this->language->get( 'error_image_cart' );
		}

		if ( ! $this->request->post['theme_gen_image_location_width'] || ! $this->request->post['theme_gen_image_location_height'] ) {
			$this->error['image_location'] = $this->language->get( 'error_image_location' );
		}

		return ! $this->error;
	}

	public function editCountryPostcode( $value ) {

		$this->load->model( 'localisation/country' );

		$country_info = $this->model_localisation_country->getCountry( $this->config->get( 'config_country_id' ) );

		if ( $country_info ) {

			$new_settings = array(
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $value,
				'status'            => $country_info['status']
			);

			$this->model_localisation_country->editCountry( $this->config->get( 'config_country_id' ), $new_settings );
		}

	}

	public function install() {

		$this->load->model( 'design/layout' );

		$layout_name = '';

		foreach ( $this->model_design_layout->getLayouts() as $layout ) {
			if ( $layout['name'] == 'Blog' ) {
				$layout_name = $layout['name'];
			}
		}
		if ( $layout_name == '' ) {
			$this->model_design_layout->addLayout( array(
				'name'         => 'Blog',
				'layout_route' => array(
					0 => array(
						'store_id' => 0,
						'route'    => 'information/blog%'
					)

				)
			) );
		}
	}
}
