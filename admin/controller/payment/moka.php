<?php

namespace Opencart\Admin\Controller\Extension\Moka\Payment;

use Opencart\System\Engine\Controller;

class Moka extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/moka/payment/moka');
        $this->load->model('setting/setting');
        $this->load->model('extension/moka/payment/moka');
        $this->load->model('localisation/order_status');

        $this->document->setTitle($this->language->get('heading_title'));

        $data = array();

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/moka/payment/moka', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_moka', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');
        }

        $data['error_api_environment'] = '';
        $data['error_dealer_code'] = '';
        $data['error_username'] = '';
        $data['error_password'] = '';
        $data['error_order_status'] = '';
        $data['error_cancel_order_status'] = '';

        $data['payment_moka_api_environment'] = $this->config->get('payment_moka_api_environment');
        $data['payment_moka_dealer_code'] = $this->config->get('payment_moka_dealer_code');
        $data['payment_moka_username'] = $this->config->get('payment_moka_username');
        $data['payment_moka_password'] = $this->config->get('payment_moka_password');
        $data['payment_moka_order_status'] = $this->config->get('payment_moka_order_status');
        $data['payment_moka_cancel_order_status'] = $this->config->get('payment_moka_cancel_order_status');
        $data['payment_moka_status'] = $this->config->get('payment_moka_status');
        $data['payment_moka_sort_order'] = $this->config->get('payment_moka_sort_order');

        if (isset($this->error['error_api_environment'])) {
            $data['error_api_environment'] = $this->error['error_api_environment'];
        }

        if (isset($this->error['error_dealer_code'])) {
            $data['error_dealer_code'] = $this->error['error_dealer_code'];
        }

        if (isset($this->error['error_username'])) {
            $data['error_username'] = $this->error['error_username'];
        }

        if (isset($this->error['error_password'])) {
            $data['error_password'] = $this->error['error_password'];
        }

        if (isset($this->error['error_order_status'])) {
            $data['error_order_status'] = $this->error['error_order_status'];
        }

        if (isset($this->error['error_cancel_order_status'])) {
            $data['error_cancel_order_status'] = $this->error['error_cancel_order_status'];
        }

        if (isset($this->request->post['payment_moka_api_environment'])) {
            $data['payment_moka_api_environment'] = $this->request->post['payment_moka_api_environment'];
        }

        if (isset($this->request->post['payment_moka_dealer_code'])) {
            $data['payment_moka_dealer_code'] = $this->request->post['payment_moka_dealer_code'];
        }

        if (isset($this->request->post['payment_moka_username'])) {
            $data['payment_moka_username'] = $this->request->post['payment_moka_username'];
        }

        if (isset($this->request->post['payment_moka_password'])) {
            $data['payment_moka_password'] = $this->request->post['payment_moka_password'];
        }

        if (isset($this->request->post['payment_moka_order_status'])) {
            $data['payment_moka_order_status'] = $this->request->post['payment_moka_order_status'];
        }

        if (isset($this->request->post['payment_moka_cancel_order_status'])) {
            $data['payment_moka_cancel_order_status'] = $this->request->post['payment_moka_cancel_order_status'];
        }

        if (isset($this->request->post['payment_moka_status'])) {
            $data['payment_moka_status'] = $this->request->post['payment_moka_status'];
        }

        if (isset($this->request->post['payment_moka_sort_order'])) {
            $data['payment_moka_sort_order'] = $this->request->post['payment_moka_sort_order'];
        }

        if (isset($this->error['warning'])) {
            $this->pushAlert(array(
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'text' => $this->error['warning']
            ));
        }

        if (isset($this->session->data['success'])) {
            $this->pushAlert(array(
                'type' => 'success',
                'icon' => 'exclamation-circle',
                'text' => $this->session->data['success']
            ));

            unset($this->session->data['success']);
        }

        $data['alerts'] = $this->pullAlerts();

        $this->clearAlerts();

        $data['action'] = $this->url->link('extension/moka/payment/moka', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
        $data['user_token'] = $this->session->data['user_token'];

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/moka/payment/moka', $data));
    }

    public function install()
    {
        $this->load->model('extension/moka/payment/moka');

        $this->model_extension_moka_payment_moka->createTables();
    }

    public function uninstall()
    {
        $this->load->model('extension/moka/payment/moka');

        $this->model_extension_moka_payment_moka->dropTables();
    }

    protected function pushAlert($alert)
    {
        $this->session->data['payment_moka_alerts'][] = $alert;
    }

    protected function pullAlerts()
    {
        if (isset($this->session->data['payment_moka_alerts'])) {
            return $this->session->data['payment_moka_alerts'];
        }

        return array();
    }

    protected function clearAlerts()
    {
        unset($this->session->data['payment_moka_alerts']);
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/moka/payment/moka')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_moka_api_environment']) {
            $this->error['error_api_environment'] = $this->language->get('error_api_environment');
        }

        if (!$this->request->post['payment_moka_dealer_code']) {
            $this->error['error_dealer_code'] = $this->language->get('error_dealer_code');
        }

        if (!$this->request->post['payment_moka_username']) {
            $this->error['error_username'] = $this->language->get('error_username');
        }

        if (!$this->request->post['payment_moka_password']) {
            $this->error['error_password'] = $this->language->get('error_password');
        }

        if (!$this->request->post['payment_moka_order_status']) {
            $this->error['error_order_status'] = $this->language->get('error_order_status');
        }

        if (!$this->request->post['payment_moka_cancel_order_status']) {
            $this->error['error_cancel_order_status'] = $this->language->get('error_cancel_order_status');
        }

        return !$this->error;
    }
}
