<?php

class ControllerExtensionModuleCategoryTopImage extends Controller {

    public function index() {
        $this->load->language('extension/module/category_top_image');

        $this->document->addStyle('catalog/view/javascript/igdev/css/category-top-image.min.css');

        $data['category_id'] = 0;

        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {

            $filter_data = array(
                'filter_category_id'  => $category['category_id'],
                'filter_sub_category' => false
            );

            $data['categories'][] = array(
                'category_id' => $category['category_id'],
                'name'        => $category['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                'image'       => $this->model_tool_image->resize($category['image'], $this->config->get('module_category_top_image_width'), $this->config->get('module_category_top_image_height')),
                'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
            );
        }

        return $this->load->view('extension/module/category_top_image', $data);
    }

}
