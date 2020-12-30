<?php

class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model( 'setting/extension' );

		$this->load->model( 'tool/image' );

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions( 'analytics' );

		foreach ( $analytics as $analytic ) {
			if ( $this->config->get( 'analytics_' . $analytic['code'] . '_status' ) ) {
				$data['analytics'][] = $this->load->controller( 'extension/analytics/' . $analytic['code'], $this->config->get( 'analytics_' . $analytic['code'] . '_status' ) );
			}
		}

		if ( $this->request->server['HTTPS'] ) {
			$server = $this->config->get( 'config_ssl' );
		} else {
			$server = $this->config->get( 'config_url' );
		}

		if ( is_file( DIR_IMAGE . $this->config->get( 'config_icon' ) ) ) {
			$this->document->addLink( $server . 'image/' . $this->config->get( 'config_icon' ), 'icon' );
		}

		if ( $this->config->get( 'theme_gen_sticky_cart' ) ) {
			$this->document->addScript( 'catalog/view/javascript/igdev/js/sticky-cart.min.js' );
		}
		if ( $this->config->get( 'theme_gen_scroll_to_top' ) ) {
			$data['scroll_to_top'] = 1;
		} else {
			$data['scroll_to_top'] = 0;
		}

		$data['title'] = $this->document->getTitle();

		$data['base']        = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords']    = $this->document->getKeywords();
		$data['links']       = $this->document->getLinks();
		$data['styles']      = $this->document->getStyles();
		$data['scripts']     = $this->document->getScripts( 'header' );
		$data['lang']        = $this->language->get( 'code' );
		$data['direction']   = $this->language->get( 'direction' );

		$data['name'] = $this->config->get( 'config_name' );

		if ( is_file( DIR_IMAGE . $this->config->get( 'config_logo' ) ) ) {
			$data['logo'] = $server . 'image/' . $this->config->get( 'config_logo' );
		} else {
			$data['logo'] = '';
		}

		$this->load->language( 'common/header' );

		// Wishlist
		if ( $this->customer->isLogged() ) {
			$this->load->model( 'account/wishlist' );

			$data['text_wishlist'] = sprintf( $this->language->get( 'text_wishlist' ), $this->model_account_wishlist->getTotalWishlist() );
		} else {
			$data['text_wishlist'] = sprintf( $this->language->get( 'text_wishlist' ), ( isset( $this->session->data['wishlist'] ) ? count( $this->session->data['wishlist'] ) : 0 ) );
		}

		$data['text_logged'] = sprintf( $this->language->get( 'text_logged' ), $this->url->link( 'account/account', '', true ), $this->customer->getFirstName(), $this->url->link( 'account/logout', '', true ) );

		$data['home']          = $this->url->link( 'common/home' );
		$data['wishlist']      = $this->url->link( 'account/wishlist', '', true );
		$data['logged']        = $this->customer->isLogged();
		$data['account']       = $this->url->link( 'account/account', '', true );
		$data['register']      = $this->url->link( 'account/register', '', true );
		$data['login']         = $this->url->link( 'account/login', '', true );
		$data['order']         = $this->url->link( 'account/order', '', true );
		$data['transaction']   = $this->url->link( 'account/transaction', '', true );
		$data['download']      = $this->url->link( 'account/download', '', true );
		$data['logout']        = $this->url->link( 'account/logout', '', true );
		$data['shopping_cart'] = $this->url->link( 'checkout/cart' );
		$data['checkout']      = $this->url->link( 'checkout/checkout', '', true );
		$data['contact']       = $this->url->link( 'information/contact' );
		$data['telephone']     = $this->config->get( 'config_telephone' );
		$data['telephone_2']   = $this->config->get( 'config_fax' );

		$data['language'] = $this->load->controller( 'common/language' );
		$data['currency'] = $this->load->controller( 'common/currency' );
		$data['search']   = $this->load->controller( 'common/search' );
		$data['cart']     = $this->load->controller( 'common/cart' );

		// Opengraph
		$data['locale'] = str_replace( '-', '_', $this->session->data['language'] );

		if ( $this->config->get( 'config_theme' ) != 'gen' ) {
			$data['menu'] = $this->load->controller( 'common/menu' );
		} else {
			$data['menu'] = $this->load->controller( 'common/menu_info' );
		}

		foreach ( $data['links'] as $url ) {
			if ( $url['rel'] == 'canonical' ) {
				$data['og_url'] = $url['href'];
			}
		}
		$data['og_url'] = empty( $data['og_url'] ) ? $data['base'] : $data['og_url'];

		if ( $this->config->get( 'theme_gen_opengraph_logo' ) ) {
			$data['og_image'] = $this->model_tool_image->resize( $this->config->get( 'theme_gen_opengraph_logo' ), 600, 314 );
		} else {
			$data['og_image'] = 0;
		}
		if ( $this->config->get( 'theme_gen_viber_logo' ) ) {
			$data['viber_logo'] = $this->model_tool_image->resize( $this->config->get( 'theme_gen_viber_logo' ), 25, 25 );
		} else {
			$data['viber_logo'] = 0;
		}
		if ( $this->config->get( 'theme_gen_telegram_logo' ) ) {
			$data['telegram_logo'] = $this->model_tool_image->resize( $this->config->get( 'theme_gen_telegram_logo' ), 25, 25 );
		} else {
			$data['telegram_logo'] = 0;
		}
		if ( $this->config->get( 'theme_gen_telegram_name' ) ) {
			$data['telegram_name'] = $this->config->get( 'theme_gen_telegram_name' );
		} else {
			$data['telegram_name'] = 0;
		}
		if ( $this->config->get( 'theme_gen_instagram_logo' ) ) {
			$data['instagram_logo'] = $this->model_tool_image->resize( $this->config->get( 'theme_gen_instagram_logo' ), 30, 30 );
		} else {
			$data['instagram_logo'] = 0;
		}
		if ( $this->config->get( 'theme_gen_instagram_account' ) ) {
			$data['instagram_account'] = $this->config->get( 'theme_gen_instagram_account' );
		} else {
			$data['instagram_account'] = 0;
		}


		$data['fb_api_id'] = $this->config->get( 'theme_gen_opengraph_api_id' ) ? $this->config->get( 'theme_gen_opengraph_api_id' ) : false;

		if ( $this->config->get( 'config_theme' ) == 'gen' ) {
			$data['main_menu'] = $this->load->controller( 'common/menu_left' );
		}

		return $this->load->view( 'common/header', $data );
	}
}
