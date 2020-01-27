<?php

class ControllerCommonMenuInfo extends Controller {
    public function index() {

        $this->load->language('common/menu_info');

        $this->load->model('catalog/information');

        $data['informations'] = array();

        $info_config = $this->config->get('theme_gen_menu_info_links') ? $this->config->get('theme_gen_menu_info_links') : array();

        foreach ($this->model_catalog_information->getInformations() as $result) {
            if (in_array($result['information_id'], $info_config)) {
                $data['informations'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }

        if (in_array('contact', $info_config)) {
            $data['informations'][] = array(
                'title' => $this->language->get('text_contact'),
                'href'  => $this->url->link('information/contact')
            );
        }
        if (in_array('special', $info_config)) {
            $data['informations'][] = array(
                'title' => $this->language->get('text_special'),
                'href'  => $this->url->link('product/special')
            );
        }
        if (in_array('blog', $info_config)) {
            $data['informations'][] = array(
                'title' => $this->language->get('text_blog'),
                'href'  => $this->url->link('information/blog')
            );
        }
        if (in_array('voucher', $info_config)) {
            $data['informations'][] = array(
                'title' => $this->language->get('text_voucher'),
                'href'  => $this->url->link('account/voucher')
            );
        }
        if (in_array('affiliate', $info_config)) {
            $data['informations'][] = array(
                'title' => $this->language->get('text_affiliate'),
                'href'  => $this->url->link('affiliate/login')
            );
        }
        return $this->load->view('common/menu_info', $data);
    }
}
