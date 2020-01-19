<?php

  class ControllerExtensionShippingUkrPoshta extends Controller {

      private $error = array();

      public function index() {
          $this->load->language( 'extension/shipping/ukr_poshta' );

          $this->document->setTitle( $this->language->get( 'heading_title' ) );

          $this->load->model( 'setting/setting' );

          if ( ($this->request->server[ 'REQUEST_METHOD' ] == 'POST') && $this->validate() ) {
              $this->model_setting_setting->editSetting( 'shipping_ukr_poshta', $this->request->post );

              $this->session->data[ 'success' ] = $this->language->get( 'text_success_ukr_poshta' );

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
              'href' => $this->url->link( 'extension/shipping/ukr_poshta', 'user_token=' . $this->session->data[ 'user_token' ], true )
          );

          $data[ 'action' ] = $this->url->link( 'extension/shipping/ukr_poshta', 'user_token=' . $this->session->data[ 'user_token' ], true );

          $data[ 'cancel' ] = $this->url->link( 'marketplace/extension', 'user_token=' . $this->session->data[ 'user_token' ] . '&type=shipping', true );

          if ( isset( $this->request->post[ 'shipping_ukr_poshta_cost' ] ) ) {
              $data[ 'shipping_ukr_poshta_cost' ] = $this->request->post[ 'shipping_ukr_poshta_cost' ];
          } else {
              $data[ 'shipping_ukr_poshta_cost' ] = $this->config->get( 'shipping_ukr_poshta_cost' );
          }

          if ( isset( $this->request->post[ 'shipping_ukr_poshta_tax_class_id' ] ) ) {
              $data[ 'shipping_ukr_poshta_tax_class_id' ] = $this->request->post[ 'shipping_ukr_poshta_tax_class_id' ];
          } else {
              $data[ 'shipping_ukr_poshta_tax_class_id' ] = $this->config->get( 'shipping_ukr_poshta_tax_class_id' );
          }

          $this->load->model( 'localisation/tax_class' );

          $data[ 'tax_classes' ] = $this->model_localisation_tax_class->getTaxClasses();

          if ( isset( $this->request->post[ 'shipping_ukr_poshta_geo_zone_id' ] ) ) {
              $data[ 'shipping_ukr_poshta_geo_zone_id' ] = $this->request->post[ 'shipping_ukr_poshta_geo_zone_id' ];
          } else {
              $data[ 'shipping_ukr_poshta_geo_zone_id' ] = $this->config->get( 'shipping_ukr_poshta_geo_zone_id' );
          }

          $this->load->model( 'localisation/geo_zone' );

          $data[ 'geo_zones' ] = $this->model_localisation_geo_zone->getGeoZones();

          if ( isset( $this->request->post[ 'shipping_ukr_poshta_status' ] ) ) {
              $data[ 'shipping_ukr_poshta_status' ] = $this->request->post[ 'shipping_ukr_poshta_status' ];
          } else {
              $data[ 'shipping_ukr_poshta_status' ] = $this->config->get( 'shipping_ukr_poshta_status' );
          }

          if ( isset( $this->request->post[ 'shipping_ukr_poshta_sort_order' ] ) ) {
              $data[ 'shipping_ukr_poshta_sort_order' ] = $this->request->post[ 'shipping_ukr_poshta_sort_order' ];
          } else {
              $data[ 'shipping_ukr_poshta_sort_order' ] = $this->config->get( 'shipping_ukr_poshta_sort_order' );
          }

          $data[ 'header' ]      = $this->load->controller( 'common/header' );
          $data[ 'column_left' ] = $this->load->controller( 'common/column_left' );
          $data[ 'footer' ]      = $this->load->controller( 'common/footer' );

          $this->response->setOutput( $this->load->view( 'extension/shipping/ukr_poshta', $data ) );
      }

      protected function validate() {
          if ( ! $this->user->hasPermission( 'modify', 'extension/shipping/ukr_poshta' ) ) {
              $this->error[ 'warning' ] = $this->language->get( 'error_permission' );
          }

          return ! $this->error;
      }

      public function install() {
          $this->load->model( 'setting/setting' );

          $this->model_setting_setting->editSetting( 'shipping_ukr_poshta', array(
              'shipping_ukr_poshta_geo_zone_id'  => 0,
              'shipping_ukr_poshta_tax_class_id' => 0,
              'shipping_ukr_poshta_cost'         => 0,
              'shipping_ukr_poshta_status'       => 0,
              'shipping_ukr_poshta_sort_order'   => 0,) );
      }

      public function uninstall() {
          $this->load->model( 'setting/setting' );
          $this->load->model( 'setting/store' );

          $stores = $this->model_setting_store->getStores();

          if ( ! empty( $stores ) ) {
              foreach ( $stores as $store ) {

                  $this->model_setting_setting->deleteSetting( 'shipping_ukr_poshta', $store[ 'store_id' ] );
              }
          } else {
              $this->model_setting_setting->deleteSetting( 'shipping_ukr_poshta', 0 );
          }
      }

  }
