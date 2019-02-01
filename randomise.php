
add_action( 'init', 'fk_randomize_user_data' );
function fk_randomize_user_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'users';
    $selector_col = 'ID';
    $col_to_randomize = 'user_email';
    $data_type = '';

    $exclude = array(
        'field'     => 'user_login',
        'values'    => array( 'email@gmail.com', 'foad@provider.no' )
    );
    fk_randomize_it( $table_name, $selector_col, $col_to_randomize, $data_type, $exclude );
    
}

function fk_randomize_it( $table_name, $selector_col, $col_to_randomize, $data_type, $exclude = array() ) {
    global $wpdb;

    $select_query = "SELECT * FROM $table_name";

    if ( isset( $exclude ) && !empty( $exclude ) ) {
        $exclude_values = implode( '\', \'', $exclude['values'] );
        $select_query .= " WHERE {$exclude['field']} NOT IN ('{$exclude_values}')";
    } else {
        $select_query .= " WHERE 1 = 1";
    }
    $records = $wpdb->get_results( $select_query );


    foreach ($records as $record) {
        switch ( $data_type ) {
            case 'email':
                $random_record = fk_generate_randon_email();
                break;
                
            case 'name':
            default:
                $random_record = fk_generate_randon_name();
                break;
        }

        $wpdb->query("UPDATE $table_name SET 
        $col_to_randomize = '{$random_record}'
        WHERE {$selector_col} = '{$record->$selector_col}'");
    }
    
}

function fk_generate_randon_name( $max_len = 6 ){
    $alphabetic = 'abcdefghijklmnopqrstuvwxyz';
    for ($i = 0; $i < $max_len; $i++) {
        $random_name .= $alphabetic[rand(0, strlen($alphabetic) - 1)];
    }
}

function fk_generate_randon_email( $max_len_local = 6, $max_len_domain = 5 ){
    $numeric        =  '0123456789';
    $alphabetic     = 'abcdefghijklmnopqrstuvwxyz';
    $tld_list       = array( 'co', 'no', 'com', 'org', 'io', 'net' );
    $all            = $numeric . $alphabetic;
    $random_email   = '';
    // GENERATE 1ST 4 CHARACTERS OF THE LOCAL-PART
    for ($i = 0; $i < 4; $i++) {
        $random_email .= $alphabetic[rand(0, strlen($alphabetic) - 1)];
    }
    // GENERATE A NUMBER BETWEEN 3 & $max_len_local
    $rndNum = rand(3, $max_len_local);
    for ($i = 0; $i < $rndNum; $i++) {
        $random_email .= $all[rand(0, strlen($all) - 1)];
    }
    // ADD AN @ SYMBOL...
    $random_email .= "@";
    // GENERATE DOMAIN NAME - INITIAL 3 CHARS:
    for ($i = 0; $i < 3; $i++) {
        $random_email .= $alphabetic[rand(0, strlen($alphabetic) - 1)];
    }
    // GENERATE A NUMBER BETWEEN 3 & $max_len_domain
    $rndNum2        = rand(3, $max_len_domain);
    for ($i = 0; $i < $rndNum2; $i++) {
        $random_email .= $all[rand(0, strlen($all) - 1)];
    }
    // ADD AN DOT . SYMBOL...
    $random_email .= ".";

    // GENERATE TLD: 4
    $random_email .= $tld_list[ array_rand( $tld_list, 1 ) ];

    return $random_email;
}
