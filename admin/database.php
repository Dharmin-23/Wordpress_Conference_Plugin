<?php
function DBP_tb_create(){
    global $wpdb;
    
    $table_conference_registrations = $wpdb->prefix . 'conference_registrations';
    $table_conferences = $wpdb->prefix . 'conferences';
    $table_registration_fees = $wpdb->prefix . 'registration_fees';
    $table_payments = $wpdb->prefix . 'payments';
    $table_custom_user_fields = $wpdb->prefix . 'custom_user_fields';
    $table_user_custom_data = $wpdb->prefix . 'user_custom_data';

    $sql = "
        CREATE TABLE $table_conference_registrations (
            registration_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            conference_id INT,
            registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            registration_status VARCHAR(20) DEFAULT 'pending',
            total_fee DECIMAL(10,2),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID),
            FOREIGN KEY (conference_id) REFERENCES $table_conferences(conference_id)
        );

        CREATE TABLE $table_conferences (
            conference_id INT AUTO_INCREMENT PRIMARY KEY,
            conference_name VARCHAR(255),
            start_date DATE,
            end_date DATE,
            location VARCHAR(255),
            description TEXT,
            registration_fee DECIMAL(10,2)
        );

        CREATE TABLE $table_registration_fees (
            fee_id INT AUTO_INCREMENT PRIMARY KEY,
            conference_id INT,
            fee_type VARCHAR(50),
            fee_amount DECIMAL(10,2),
            FOREIGN KEY (conference_id) REFERENCES $table_conferences(conference_id)
        );

        CREATE TABLE $table_payments (
            payment_id INT AUTO_INCREMENT PRIMARY KEY,
            registration_id INT,
            payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            amount_paid DECIMAL(10,2),
            payment_status VARCHAR(20) DEFAULT 'pending',
            payment_method VARCHAR(50),
            transaction_id VARCHAR(255),
            FOREIGN KEY (registration_id) REFERENCES $table_conference_registrations(registration_id)
        );

        CREATE TABLE $table_custom_user_fields (
            field_id INT AUTO_INCREMENT PRIMARY KEY,
            field_name VARCHAR(100),
            field_type VARCHAR(50),
            required BOOLEAN,
            display_order INT
        );

        CREATE TABLE $table_user_custom_data (
            user_id INT,
            field_id INT,
            field_value TEXT,
            PRIMARY KEY (user_id, field_id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID),
            FOREIGN KEY (field_id) REFERENCES $table_custom_user_fields(field_id)
        );
    ";



    require_once(ABSPATH."wp-admin/incldues/upgrade.php");
    dbDelta($sql);

    function save_event_details($event_name, $start_date, $end_date) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'conference_events';
   
        $event_name = sanitize_text_field($event_name);
        $start_date = sanitize_text_field($start_date);
        $end_date = sanitize_text_field($end_date);
    
  
        $wpdb->insert(
            $table_name,
            array(
                'event_name' => $event_name,
                'start_date' => $start_date,
                'end_date'   => $end_date,
            ),
            array('%s', '%s', '%s')
        );
    }

    function save_fee_structure($fee_types, $fee_amounts) {
        global $wpdb;
    
        $table_name = $wpdb->prefix . 'conference_fee_structure';
    

        $fee_types = array_map('sanitize_text_field', $fee_types);
        $fee_amounts = array_map('floatval', $fee_amounts);
    
        for ($i = 0; $i < count($fee_types); $i++) {
            $wpdb->insert(
                $table_name,
                array(
                    'fee_type'   => $fee_types[$i],
                    'fee_amount' => $fee_amounts[$i],
                ),
                array('%s', '%f')
            );
        }
    }

    function save_conference_registration($user_id, $conference_id, $total_fee) {
        global $wpdb;
        $table_conference_registrations = $wpdb->prefix . 'conference_registrations';
        $user_id = intval($user_id);
        $conference_id = intval($conference_id);
        $total_fee = floatval($total_fee);
        $wpdb->insert(
            $table_conference_registrations,
            array(
                'user_id' => $user_id,
                'conference_id' => $conference_id,
                'total_fee' => $total_fee,
            ),
            array('%d', '%d', '%f')
        );
    }
    
    function save_payment($registration_id, $amount_paid, $payment_method, $transaction_id) {
        global $wpdb;
    
        $table_payments = $wpdb->prefix . 'payments';
    
        $registration_id = intval($registration_id);
        $amount_paid = floatval($amount_paid);
        $wpdb->insert(
            $table_payments,
            array(
                'registration_id' => $registration_id,
                'amount_paid' => $amount_paid,
                'payment_method' => sanitize_text_field($payment_method),
                'transaction_id' => sanitize_text_field($transaction_id),
            ),
            array('%d', '%f', '%s', '%s')
        );
    }
    
    function save_user_custom_data($user_id, $field_id, $field_value) {
        global $wpdb;
    
        $table_user_custom_data = $wpdb->prefix . 'user_custom_data';
        $user_id = intval($user_id);
        $field_id = intval($field_id);
    
        $wpdb->insert(
            $table_user_custom_data,
            array(
                'user_id' => $user_id,
                'field_id' => $field_id,
                'field_value' => sanitize_text_field($field_value),
            ),
            array('%d', '%d', '%s')
        );
    }

    function get_available_events() {
        global $wpdb;
    
        $table_conferences = $wpdb->prefix . 'conferences';

        $sql = "SELECT conference_id, conference_name, start_date, end_date, location, description, registration_fee
                FROM $table_conferences
                WHERE start_date >= CURDATE()";
    
        $events = $wpdb->get_results($sql);
    
        return $events;
    }

    function deactivate_conference_plugin() {
        global $wpdb;
    
      
        $table_conferences = $wpdb->prefix . 'conference_events';
        $table_fee_structure = $wpdb->prefix . 'conference_fee_structure';

        $sql = "DROP TABLE IF EXISTS $table_conferences, $table_fee_structure;";
    
        $wpdb->query($sql);
    }


}
