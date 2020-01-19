<?php

  class ControllerExtensionShippingNovaPoshta extends Controller {

      private $error = array();

      public function index() {
          $this->load->language( 'extension/shipping/nova_poshta' );

          $this->document->setTitle( $this->language->get( 'heading_title' ) );

          $this->document->addStyle( 'view/stylesheet/spinner.min.css' );

          $this->load->model( 'setting/setting' );

          if ( ($this->request->server[ 'REQUEST_METHOD' ] == 'POST') && $this->validate() ) {
              $this->model_setting_setting->editSetting( 'shipping_nova_poshta', $this->request->post );

              $this->session->data[ 'success' ] = $this->language->get( 'text_success_nova_poshta' );

              $this->response->redirect( $this->url->link( 'marketplace/extension', 'user_token=' . $this->session->data[ 'user_token' ] . '&type=shipping', true ) );
          }

          if ( isset( $this->error[ 'warning' ] ) ) {
              $data[ 'error_warning' ] = $this->error[ 'warning' ];
          } else {
              $data[ 'error_warning' ] = '';
          }

          $data[ 'breadcrumbs' ] = array();

          $data[ 'breadcrumbs' ][] = array(
              'text' => $this->language->get( 'text_home' ),
              'href' => $this->url->link( 'common/dashboard', 'user_token=' . $this->session->data[ 'user_token' ], true )
          );

          $data[ 'breadcrumbs' ][] = array(
              'text' => $this->language->get( 'text_extension' ),
              'href' => $this->url->link( 'marketplace/extension', 'user_token=' . $this->session->data[ 'user_token' ] . '&type=shipping', true )
          );

          $data[ 'breadcrumbs' ][] = array(
              'text' => $this->language->get( 'heading_title' ),
              'href' => $this->url->link( 'extension/shipping/nova_poshta', 'user_token=' . $this->session->data[ 'user_token' ], true )
          );

          $data[ 'action' ] = $this->url->link( 'extension/shipping/nova_poshta', 'user_token=' . $this->session->data[ 'user_token' ], true );

          $data[ 'cancel' ] = $this->url->link( 'marketplace/extension', 'user_token=' . $this->session->data[ 'user_token' ] . '&type=shipping', true );

          if ( isset( $this->request->post[ 'shipping_nova_poshta_cost' ] ) ) {
              $data[ 'shipping_nova_poshta_cost' ] = $this->request->post[ 'shipping_nova_poshta_cost' ];
          } else {
              $data[ 'shipping_nova_poshta_cost' ] = $this->config->get( 'shipping_nova_poshta_cost' );
          }

          if ( isset( $this->request->post[ 'shipping_nova_poshta_tax_class_id' ] ) ) {
              $data[ 'shipping_nova_poshta_tax_class_id' ] = $this->request->post[ 'shipping_nova_poshta_tax_class_id' ];
          } else {
              $data[ 'shipping_nova_poshta_tax_class_id' ] = $this->config->get( 'shipping_nova_poshta_tax_class_id' );
          }

          $this->load->model( 'localisation/tax_class' );

          $data[ 'tax_classes' ] = $this->model_localisation_tax_class->getTaxClasses();

          if ( isset( $this->request->post[ 'shipping_nova_poshta_geo_zone_id' ] ) ) {
              $data[ 'shipping_nova_poshta_geo_zone_id' ] = $this->request->post[ 'shipping_nova_poshta_geo_zone_id' ];
          } else {
              $data[ 'shipping_nova_poshta_geo_zone_id' ] = $this->config->get( 'shipping_nova_poshta_geo_zone_id' );
          }

          $this->load->model( 'localisation/geo_zone' );

          $data[ 'geo_zones' ] = $this->model_localisation_geo_zone->getGeoZones();

          if ( isset( $this->request->post[ 'shipping_nova_poshta_status' ] ) ) {
              $data[ 'shipping_nova_poshta_status' ] = $this->request->post[ 'shipping_nova_poshta_status' ];
          } else {
              $data[ 'shipping_nova_poshta_status' ] = $this->config->get( 'shipping_nova_poshta_status' );
          }

          if ( isset( $this->request->post[ 'shipping_nova_poshta_sort_order' ] ) ) {
              $data[ 'shipping_nova_poshta_sort_order' ] = $this->request->post[ 'shipping_nova_poshta_sort_order' ];
          } else {
              $data[ 'shipping_nova_poshta_sort_order' ] = $this->config->get( 'shipping_nova_poshta_sort_order' );
          }
          if ( isset( $this->request->post[ 'shipping_nova_poshta_api_key' ] ) ) {
              $data[ 'shipping_nova_poshta_api_key' ] = $this->request->post[ 'shipping_nova_poshta_api_key' ];
          } else {
              $data[ 'shipping_nova_poshta_api_key' ] = $this->config->get( 'shipping_nova_poshta_api_key' );
          }

          if ( isset( $this->request->post[ 'shipping_nova_poshta_api_key_validity' ] ) ) {
              $data[ 'shipping_nova_poshta_api_key_validity' ] = $this->request->post[ 'shipping_nova_poshta_api_key_validity' ];
          } else {
              $data[ 'shipping_nova_poshta_api_key_validity' ] = $this->config->get( 'shipping_nova_poshta_api_key_validity' );
          }

          $data[ 'user_token' ] = $this->session->data[ 'user_token' ];

          $data[ 'header' ]      = $this->load->controller( 'common/header' );
          $data[ 'column_left' ] = $this->load->controller( 'common/column_left' );
          $data[ 'footer' ]      = $this->load->controller( 'common/footer' );

          $this->response->setOutput( $this->load->view( 'extension/shipping/nova_poshta', $data ) );
      }

      protected function validate() {
          if ( ! $this->user->hasPermission( 'modify', 'extension/shipping/nova_poshta' ) ) {
              $this->error[ 'warning' ] = $this->language->get( 'error_permission' );
          }

          return ! $this->error;
      }

      public function install() {
          $this->load->model( 'setting/setting' );

          $this->model_setting_setting->editSetting( 'shipping_nova_poshta', array(
              'shipping_nova_poshta_api_key_validity' => 0,
              'shipping_nova_poshta_geo_zone_id'      => 0,
              'shipping_nova_poshta_tax_class_id'     => 0,
              'shipping_nova_poshta_cost'             => 0,
              'shipping_nova_poshta_status'           => 0,
              'shipping_nova_poshta_sort_order'       => 0,) );

          $this->load->model( 'extension/shipping/nova_poshta' );

          $this->model_extension_shipping_nova_poshta->install();
      }

      public function uninstall() {
          $this->load->model( 'setting/setting' );
          $this->load->model( 'setting/store' );

          $stores = $this->model_setting_store->getStores();

          if ( ! empty( $stores ) ) {
              foreach ( $stores as $store ) {

                  $this->model_setting_setting->deleteSetting( 'shipping_nova_poshta', $store[ 'store_id' ] );
              }
          } else {
              $this->model_setting_setting->deleteSetting( 'shipping_nova_poshta', 0 );
          }
      }

      public function update_regions() {

          $json = array();

          $this->load->model( 'extension/shipping/nova_poshta' );

          $json[ 'success' ] = $this->model_extension_shipping_nova_poshta->updateRegions( $this->request->post );

          $this->response->addHeader( 'Content-Type: application/json' );

          $this->response->setOutput( json_encode( $json ) );
      }

  }
