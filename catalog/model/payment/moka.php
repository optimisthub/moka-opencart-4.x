<?php

namespace Opencart\Catalog\Model\Extension\Moka\Payment;

use Opencart\System\Engine\Model;

class Moka extends Model
{
    public function getMethods(array $address = []): array
    {
        $this->load->language('extension/moka/payment/moka');

        if ($this->cart->hasSubscription()) {
            $status = false;
        } elseif (!$this->cart->hasShipping()) {
            $status = false;
        } elseif (!$this->config->get('config_checkout_payment_address')) {
            $status = true;
        } elseif (!$this->config->get('payment_moka_geo_zone_id')) {
            $status = true;
        } else {
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('payment_moka_geo_zone_id') . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");

            if ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        }

        $method_data = [];

        if ($status) {
            $method_data = array(
                'code'       => 'moka',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('payment_moka_sort_order')
            );
        }

        if ($status) {
            $option_data['moka'] = [
                'code' => 'moka.moka',
                'name' => $this->language->get('text_title')
            ];

            $method_data = [
                'code'       => 'moka',
                'name'       => $this->language->get('text_title'),
                'option'     => $option_data,
                'sort_order' => $this->config->get('payment_moka_sort_order')
            ];
        }

        return $method_data;
    }

    public function addTransaciton($transaciton, $order_id)
    {
        $addTransaciton = $this->db->query("INSERT INTO `" . DB_PREFIX . "moka_transaction` SET 
			`payment_id` = '" . $this->db->escape($transaciton->DealerPaymentId) . "',
			`order_id` = '" . $this->db->escape($order_id) . "', 
			`amount` = '" . $this->db->escape($transaciton->Amount) . "',
			`currency_code` = '" . $this->db->escape($transaciton->CurrencyCode) . "',
			`installment_number` = '" . $this->db->escape($transaciton->InstallmentNumber) . "',
			`commission_amount` = '" . $this->db->escape($transaciton->DealerCommissionAmount) . "',
			`payment_status` = '" . $this->db->escape($transaciton->PaymentStatus) . "',
			`transaction_status` = '" . $this->db->escape($transaciton->TrxStatus) . "'");

        return $addTransaciton;
    }
}
