<?php

class ControllerCheckoutSimpleShippingMethod extends Controller {
    public function index() {

        $this->load->language('checkout/checkout');

        if ($this->cart->hasShipping()) {

            unset($this->session->data['shipping_methods']);

            // Helper
            $this->load->model('localisation/country');

            $this->load->model('localisation/zone');

            if (isset($this->request->post['country_id'])) {
                $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
            } elseif (isset($this->session->data['payment_address']['country_id'])) {
                $country_info = $this->model_localisation_country->getCountry($this->session->data['payment_address']['country_id']);
            } else {
                $country_info = $this->model_localisation_country->getCountry($this->config->get('config_country_id'));
            }

            if (isset($this->request->post['zone_id'])) {
                $zone_id = $this->request->post['zone_id'];
            } elseif (isset($this->session->data['payment_address']['zone_id'])) {
                $zone_id = $this->session->data['payment_address']['zone_id'];
            } elseif ($this->config->get('config_zone_id')) {
                $zone_id = $this->config->get('config_zone_id');
            } else {
                $zones   = $this->model_localisation_zone->getZonesByCountryId($country_info['country_id']);
                $zone_id = $zones[0]['zone_id'];
            }

            $country_zone_id_def = array('country_id'     => $country_info['country_id'],
                                         'name'           => $country_info['name'],
                                         'iso_code_2'     => $country_info['iso_code_2'],
                                         'iso_code_3'     => $country_info['iso_code_3'],
                                         'address_format' => $country_info['address_format'],
                                         'zone_id'        => $zone_id,
                                         'status'         => $country_info['status'],
                                         'postcode'       => isset($this->request->post['postcode']) ? $this->request->post['postcode'] : '11111',
                                         'firstname'      => isset($this->request->post['firstname']) ? $this->request->post['firstname'] : 'default',
                                         'lastname'       => isset($this->request->post['lastname']) ? $this->request->post['lastname'] : 'default',
                                         'company'        => isset($this->request->post['company']) ? $this->request->post['company'] : 'default',
                                         'city'           => isset($this->request->post['city']) ? $this->request->post['city'] : 'default',
                                         'address_1'      => isset($this->request->post['address_1']) ? $this->request->post['address_1'] : 'default'

            );

            // Shipping Methods
            $method_data = array();

            $this->load->model('setting/extension');

            $results = $this->model_setting_extension->getExtensions('shipping');

            foreach ($results as $result) {
                if ($this->config->get('shipping_' . $result['code'] . '_status')) {
                    $this->load->model('extension/shipping/' . $result['code']);

                    $quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($country_zone_id_def);

                    if ($quote) {
                        $method_data[$result['code']] = array(
                            'title'      => $quote['title'],
                            'quote'      => $quote['quote'],
                            'sort_order' => $quote['sort_order'],
                            'error'      => $quote['error']
                        );
                    }
                }
            }

            $sort_order = array();

            foreach ($method_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $method_data);

            $this->session->data['shipping_methods'] = $method_data;
        }

        if (empty($this->session->data['shipping_methods'])) {
            $data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['shipping_methods'])) {
            $data['shipping_methods'] = $this->session->data['shipping_methods'];
        } else {
            $data['shipping_methods'] = array();
        }

        if (isset($this->session->data['shipping_method']['code'])) {
            $data['code'] = $this->session->data['shipping_method']['code'];
        } else {
            $data['code'] = '';
        }

        if (isset($this->session->data['comment'])) {
            $data['comment'] = $this->session->data['comment'];
        } else {
            $data['comment'] = '';
        }

        $data['show_shipping_coast'] = ($this->config->get('total_shipping_status') && $this->config->get('total_shipping_estimator')) ? 1 : 0;

        $data['show_shipping_comment'] = in_array('shipping_comment', $this->config->get('theme_gen_checkout_hidden_fields'));

        $this->response->setOutput($this->load->view('checkout/shipping_method', $data));
    }

    public function save() {

        $this->load->language('checkout/checkout');

        $json = array();

        // Validate if shipping is required. If not the customer should not have reached this page.
        if (!$this->cart->hasShipping()) {
            $json['redirect'] = $this->url->link('checkout/checkout', '', true);
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart');
        }

        unset($this->session->data['shipping_method']);

        // Validate minimum quantity requirements.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart');

                break;
            }
        }

        if (!isset($this->request->post['shipping_method'])) {
            $json['error']['warning'] = $this->language->get('error_shipping');
        } else {
            $shipping = explode('.', $this->request->post['shipping_method']);

            if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
                $json['error']['warning'] = $this->language->get('error_shipping');
            }
        }

        if (!$json) {

            $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

            $this->session->data['comment'] = strip_tags($this->request->post['comment']);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}