<?php

class ControllerCommonMenuLeft extends Controller {
    public function index() {

        $this->load->language('common/header');

        // Menu
        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            if ($category['top']) {
                // Level 2
                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);

                foreach ($children as $child) {

                    // Level 3
                    $sub_children_data = array();
                    $sub_children      = $this->model_catalog_category->getCategories($child['category_id']);

                    foreach ($sub_children as $sub_child) {

                        $sub_children_filter_data = array(
                            'filter_category_id'  => $sub_child['category_id'],
                            'filter_sub_category' => true);

                        $sub_children_data[] = array(
                            'name' => $sub_child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($sub_children_filter_data) . ')' : ''),
                            'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $sub_child['category_id']));
                    }

                    // Level 2
                    $filter_data = array(
                        'filter_category_id'  => $child['category_id'],
                        'filter_sub_category' => true
                    );

                    $children_data[] = array(
                        'name'         => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                        'sub_children' => $sub_children_data,
                        'href'         => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                    );
                }

                // Level 1
                $data['categories'][] = array(
                    'name'     => $category['name'],
                    'children' => $children_data,
                    'column'   => 1,
                    'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
                );
            }
        }

        return $this->load->view('common/menu_left', $data);
    }
}
