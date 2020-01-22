<?php

class ModelExtensionShippingNovaPoshta extends Model {

    public function install() {
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "zone` MODIFY `code` VARCHAR(36) NOT NULL");
    }

    public function updateRegions() {

        $rows = $this->request->post['data'];

        $this->db->query("UPDATE `" . DB_PREFIX . "country` SET name = 'Україна' WHERE country_id = '220'");

        $this->cache->delete('country');
        
        if ($rows) {

            $this->db->query("DELETE FROM `" . DB_PREFIX . "zone` WHERE country_id = '220'");


            $query = "INSERT  INTO `" . DB_PREFIX . "zone` ( country_id, name, code, status ) VALUES ";

            foreach ($rows as $row) {

                $query .= "( '220', '" . $row['Description'] . "', '" . $row['Ref'] . "', '1' ),";
            }
            $query = substr_replace($query, ";", -1);

            $this->db->query($query);

            $this->cache->delete('zone');

            return true;
        } else {
            return false;
        }
    }

}
