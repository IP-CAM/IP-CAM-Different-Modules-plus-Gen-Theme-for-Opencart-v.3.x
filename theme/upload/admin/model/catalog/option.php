<?php

class ModelCatalogOption extends Model {
	public function addOption( $data ) {
		$this->db->query( "INSERT INTO `" . DB_PREFIX . "option` SET type = '" . $this->db->escape( $data['type'] ) . "', sort_order = '" . (int) $data['sort_order'] . "'" );

		$option_id = $this->db->getLastId();

		foreach ( $data['option_description'] as $language_id => $value ) {
			$this->db->query( "INSERT INTO " . DB_PREFIX . "option_description SET option_id = '" . (int) $option_id . "', language_id = '" . (int) $language_id . "', name = '" . $this->db->escape( $value['name'] ) . "'" );
		}

		if ( isset( $data['option_value'] ) ) {
			foreach ( $data['option_value'] as $option_value ) {
				$this->db->query( "INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int) $option_id . "', image = '" . $this->db->escape( html_entity_decode( $option_value['image'], ENT_QUOTES, 'UTF-8' ) ) . "', sort_order = '" . (int) $option_value['sort_order'] . "'" );

				$option_value_id = $this->db->getLastId();

				foreach ( $option_value['option_value_description'] as $language_id => $option_value_description ) {
					$this->db->query( "INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int) $option_value_id . "', language_id = '" . (int) $language_id . "', option_id = '" . (int) $option_id . "', name = '" . $this->db->escape( $option_value_description['name'] ) . "'" );
				}
			}
		}

		// Option var
		$this->db->query( "INSERT INTO `" . DB_PREFIX . "option_var` SET option_id = '" . (int) $option_id . "', option_var_status = '" . (int) $data['option_var_status'] . "'" );


		return $option_id;
	}

	public function editOption( $option_id, $data ) {
		$this->db->query( "UPDATE `" . DB_PREFIX . "option` SET type = '" . $this->db->escape( $data['type'] ) . "', sort_order = '" . (int) $data['sort_order'] . "' WHERE option_id = '" . (int) $option_id . "'" );

		$this->db->query( "DELETE FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int) $option_id . "'" );

		foreach ( $data['option_description'] as $language_id => $value ) {
			$this->db->query( "INSERT INTO " . DB_PREFIX . "option_description SET option_id = '" . (int) $option_id . "', language_id = '" . (int) $language_id . "', name = '" . $this->db->escape( $value['name'] ) . "'" );
		}

		$this->db->query( "DELETE FROM " . DB_PREFIX . "option_value WHERE option_id = '" . (int) $option_id . "'" );
		$this->db->query( "DELETE FROM " . DB_PREFIX . "option_value_description WHERE option_id = '" . (int) $option_id . "'" );

		if ( isset( $data['option_value'] ) ) {
			foreach ( $data['option_value'] as $option_value ) {
				if ( $option_value['option_value_id'] ) {
					$this->db->query( "INSERT INTO " . DB_PREFIX . "option_value SET option_value_id = '" . (int) $option_value['option_value_id'] . "', option_id = '" . (int) $option_id . "', image = '" . $this->db->escape( html_entity_decode( $option_value['image'], ENT_QUOTES, 'UTF-8' ) ) . "', sort_order = '" . (int) $option_value['sort_order'] . "'" );
				} else {
					$this->db->query( "INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int) $option_id . "', image = '" . $this->db->escape( html_entity_decode( $option_value['image'], ENT_QUOTES, 'UTF-8' ) ) . "', sort_order = '" . (int) $option_value['sort_order'] . "'" );
				}

				$option_value_id = $this->db->getLastId();

				foreach ( $option_value['option_value_description'] as $language_id => $option_value_description ) {
					$this->db->query( "INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int) $option_value_id . "', language_id = '" . (int) $language_id . "', option_id = '" . (int) $option_id . "', name = '" . $this->db->escape( $option_value_description['name'] ) . "'" );
				}
			}

		}

		$id_exist = $this->db->query( "SELECT * FROM `" . DB_PREFIX . "option_var` WHERE option_id = '" . (int) $option_id . "'" );

		if ( $id_exist->num_rows == 1 ) {
			$this->db->query( "UPDATE `" . DB_PREFIX . "option_var` SET option_var_status = '" . (int) $data['option_var_status'] . "' WHERE option_id = '" . (int) $option_id . "'" );
		} else {
			$this->db->query( "INSERT INTO `" . DB_PREFIX . "option_var` SET option_id = '" . (int) $option_id . "', option_var_status = '" . (int) $data['option_var_status'] . "'" );
		}
	}

	public function deleteOption( $option_id ) {
		$this->db->query( "DELETE FROM `" . DB_PREFIX . "option` WHERE option_id = '" . (int) $option_id . "'" );
		$this->db->query( "DELETE FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int) $option_id . "'" );
		$this->db->query( "DELETE FROM " . DB_PREFIX . "option_value WHERE option_id = '" . (int) $option_id . "'" );
		$this->db->query( "DELETE FROM " . DB_PREFIX . "option_value_description WHERE option_id = '" . (int) $option_id . "'" );
		$this->db->query( "DELETE FROM " . DB_PREFIX . "option_var WHERE option_id = '" . (int) $option_id . "'" );
	}

	public function getOption( $option_id ) {
		$query = $this->db->query( "SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE o.option_id = '" . (int) $option_id . "' AND od.language_id = '" . (int) $this->config->get( 'config_language_id' ) . "'" );

		return $query->row;
	}

	public function getOptions( $data = array() ) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE od.language_id = '" . (int) $this->config->get( 'config_language_id' ) . "'";

		if ( ! empty( $data['filter_name'] ) ) {
			$sql .= " AND od.name LIKE '" . $this->db->escape( $data['filter_name'] ) . "%'";
		}

		$sort_data = array(
			'od.name',
			'o.type',
			'o.sort_order'
		);

		if ( isset( $data['sort'] ) && in_array( $data['sort'], $sort_data ) ) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY od.name";
		}

		if ( isset( $data['order'] ) && ( $data['order'] == 'DESC' ) ) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if ( isset( $data['start'] ) || isset( $data['limit'] ) ) {
			if ( $data['start'] < 0 ) {
				$data['start'] = 0;
			}

			if ( $data['limit'] < 1 ) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
		}

		$query = $this->db->query( $sql );

		return $query->rows;
	}

	public function getOptionDescriptions( $option_id ) {
		$option_data = array();

		$query = $this->db->query( "SELECT * FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int) $option_id . "'" );

		foreach ( $query->rows as $result ) {
			$option_data[ $result['language_id'] ] = array( 'name' => $result['name'] );
		}

		return $option_data;
	}

	public function getOptionValue( $option_value_id ) {
		$query = $this->db->query( "SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_value_id = '" . (int) $option_value_id . "' AND ovd.language_id = '" . (int) $this->config->get( 'config_language_id' ) . "'" );

		return $query->row;
	}

	public function getOptionValues( $option_id ) {
		$option_value_data = array();

		$option_value_query = $this->db->query( "SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_id = '" . (int) $option_id . "' AND ovd.language_id = '" . (int) $this->config->get( 'config_language_id' ) . "' ORDER BY ov.sort_order, ovd.name" );

		foreach ( $option_value_query->rows as $option_value ) {
			$option_value_data[] = array(
				'option_value_id' => $option_value['option_value_id'],
				'name'            => $option_value['name'],
				'image'           => $option_value['image'],
				'sort_order'      => $option_value['sort_order']
			);
		}

		return $option_value_data;
	}

	public function getOptionValueDescriptions( $option_id ) {
		$option_value_data = array();

		$option_value_query = $this->db->query( "SELECT * FROM " . DB_PREFIX . "option_value WHERE option_id = '" . (int) $option_id . "' ORDER BY sort_order" );

		foreach ( $option_value_query->rows as $option_value ) {
			$option_value_description_data = array();

			$option_value_description_query = $this->db->query( "SELECT * FROM " . DB_PREFIX . "option_value_description WHERE option_value_id = '" . (int) $option_value['option_value_id'] . "'" );

			foreach ( $option_value_description_query->rows as $option_value_description ) {
				$option_value_description_data[ $option_value_description['language_id'] ] = array( 'name' => $option_value_description['name'] );
			}

			$option_value_data[] = array(
				'option_value_id'          => $option_value['option_value_id'],
				'option_value_description' => $option_value_description_data,
				'image'                    => $option_value['image'],
				'sort_order'               => $option_value['sort_order']
			);
		}

		return $option_value_data;
	}

	public function getTotalOptions() {
		$query = $this->db->query( "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "option`" );

		return $query->row['total'];
	}

	public function getOptionVar( $option_id ) {

		$query = $this->db->query( "SELECT option_var_status FROM `" . DB_PREFIX . "option_var` WHERE option_id = '" . (int) $option_id . "'" );

		return $query->row;
	}

	public function getOptionsVar() {

		$option_value_data = array();

		$option_value_query = $this->db->query( "SELECT * FROM " . DB_PREFIX . "option_var ov LEFT JOIN " . DB_PREFIX . "option_description od ON (ov.option_id = od.option_id) WHERE od.language_id = '" . (int) $this->config->get( 'config_language_id' ) . "' ORDER BY od.name" );

		foreach ( $option_value_query->rows as $option_value ) {
			$option_value_data[] = array(
				'option_id' => $option_value['option_id'],
				'name'      => $option_value['name'],
			);
		}

		return $option_value_data;
	}

	public function createVariationTables() {

		$table_option_var_exist = $this->db->query( "SHOW TABLES LIKE '" . DB_PREFIX . "option_var' " );

		if ( $table_option_var_exist->num_rows != 1 ) {
			$this->db->query(
				"CREATE TABLE `" . DB_PREFIX . "option_var` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `option_id` INT(11) NOT NULL,
  `option_var_status` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `option_id` (`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;" );
		}

		$table_product_option_var_exist = $this->db->query( "SHOW TABLES LIKE '" . DB_PREFIX . "product_option_var' " );

		if ( $table_product_option_var_exist->num_rows != 1 ) {
			$this->db->query(
				"CREATE TABLE `" . DB_PREFIX . "product_option_var` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `variation_id` INT(11) NOT NULL,
  `option_id` INT(11) NOT NULL,
  `option_value_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `variation_id` (`variation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;" );
		}

		$table_product_option_var_images_exist = $this->db->query( "SHOW TABLES LIKE '" . DB_PREFIX . "product_option_var_images' " );

		if ( $table_product_option_var_images_exist->num_rows != 1 ) {
			$this->db->query(
				"CREATE TABLE `" . DB_PREFIX . "product_option_var_images` (
  `variation_id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `image_variation` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`variation_id`),
  KEY `variation_id` (`variation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;" );
		}
	}
}