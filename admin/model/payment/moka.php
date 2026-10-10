<?php

namespace Opencart\Admin\Model\Extension\Moka\Payment;

use Opencart\System\Engine\Model;

class Moka extends Model
{
    public function createTables()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "moka_transaction` (
          `moka_transaction_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `payment_id` INT(11) NOT NULL,
          `order_id` INT(11) NOT NULL,
          `amount` DECIMAL(10, 2) NOT NULL,
          `currency_code` char(3) NOT NULL,
          `installment_number` int(11) NOT NULL,
          `commission_amount` int(11) NOT NULL,
          `payment_status` VARCHAR(20) NOT NULL,
          `transaction_status` VARCHAR(20) NOT NULL,
          `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`moka_transaction_id`),
          KEY `order_id` (`order_id`),
          KEY `payment_id` (`payment_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "moka_pending` (
          `trx_code` varchar(64) NOT NULL,
          `order_id` int(11) NOT NULL,
          `code_for_hash` varchar(64) NOT NULL,
          `amount` decimal(10,2) NOT NULL,
          `currency` varchar(8) NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`trx_code`),
          KEY `order_id` (`order_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    public function dropTables()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "moka_transaction`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "moka_pending`");
    }
}
