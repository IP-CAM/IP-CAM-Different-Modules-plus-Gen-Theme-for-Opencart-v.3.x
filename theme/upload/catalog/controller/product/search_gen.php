<?php

class ControllerProductSearchGen extends Controller {
    public function index() {

        $json = array();

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        $data['products'] = array();

        if (isset($this->request->get['search'])) {

            $search = $this->request->get['search'];

            $filter_data = array(
                'filter_name' => $search,
                'sort'        => 'p.sort_order',
                'order'       => 'ASC',
                'start'       => 0,
                'limit'       => 25
            );

            $product_total = $this->model_catalog_product->getTotalProducts($filter_data);

            $results = $this->model_catalog_product->getProducts($filter_data);

            $json["success"] = !empty($results) ? true : false;

            foreach ($results as $result) {
                if ($result['image']) {
                    $image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
                } else {
                    $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
                }

                $category_id = $category_id = $this->model_catalog_product->getCategories($result['product_id'])[0]["category_id"];

                $json["data"][] = array(
                    'product_id' => $result["product_id"],
                    'thumb'      => $image,
                    'name'       => $result['name'],
                    'href'       => $this->url->link('product/product', 'path=' . $category_id . '&product_id=' . $result['product_id'])
                );
            }

            // Save search to history
            if (isset($this->request->get['search']) && $this->config->get('config_customer_search')) {
                $this->load->model('account/search');

                if ($this->customer->isLogged()) {
                    $customer_id = $this->customer->getId();
                } else {
                    $customer_id = 0;
                }

                if (isset($this->request->server['REMOTE_ADDR'])) {
                    $ip = $this->request->server['REMOTE_ADDR'];
                } else {
                    $ip = '';
                }

                $search_data = array(
                    'keyword'      => $search,
                    'category_id'  => "",
                    'sub_category' => "",
                    'description'  => "",
                    'products'     => $product_total,
                    'customer_id'  => $customer_id,
                    'ip'           => $ip
                );

                $this->model_account_search->addSearch($search_data);
            }
        } else {
            $json["success"] = false;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
