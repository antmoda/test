<?php
// Створення таблиць під час активації плагіна
function ufp_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Таблиця для користувачів
    $users_table = $wpdb->prefix . 'financial_users';
    $sql = "CREATE TABLE $users_table (
        id INT NOT NULL AUTO_INCREMENT,
        first_name VARCHAR(255) NOT NULL,
        last_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        PRIMARY KEY(id)
    ) $charset_collate;";

    // Таблиця для рахунків
    $accounts_table = $wpdb->prefix . 'finance_accounts';
    $sql .= "CREATE TABLE $accounts_table (
        id INT NOT NULL AUTO_INCREMENT,
        user_email VARCHAR(255) NOT NULL,
        account_name VARCHAR(255) NOT NULL,
        balance FLOAT NOT NULL,
        currency VARCHAR(10) NOT NULL,
        PRIMARY KEY(id),
        FOREIGN KEY (user_email) REFERENCES {$wpdb->prefix}users(user_email) ON DELETE CASCADE
    ) $charset_collate;";

    // Таблиця для транзакцій
    $transactions_table = $wpdb->prefix . 'finance_transactions';
    $sql .= "CREATE TABLE $transactions_table (
        id INT NOT NULL AUTO_INCREMENT,
        user_email VARCHAR(255) NOT NULL,
        transaction_date DATE NOT NULL,
        transaction_time TIME NOT NULL,
        amount FLOAT NOT NULL,
        currency VARCHAR(10) NOT NULL,
        category VARCHAR(255),
        PRIMARY KEY(id),
        FOREIGN KEY (user_email) REFERENCES {$wpdb->prefix}users(user_email) ON DELETE CASCADE
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}
register_activation_hook( __FILE__, 'ufp_create_tables' );
