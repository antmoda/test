<?php
/**
 * Plugin Name: Universal Financial Plugin
 * Description: Плагін для ведення фінансового обліку, завантаження транзакцій через CSV та додавання рахунків.
 * Version: 1.0
 * Author: Elly Puli
 */

// Безпосередньо виконується підключення файлів
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'UFP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Підключаємо необхідні файли
require_once( UFP_PLUGIN_DIR . 'includes/ufp-db.php' );
require_once( UFP_PLUGIN_DIR . 'includes/ufp-functions.php' );

// Підключаємо стилі та скрипти
function ufp_enqueue_assets() {
    wp_enqueue_style( 'ufp-style', plugins_url( 'assets/css/ufp-style.css', __FILE__ ) );
    wp_enqueue_script( 'ufp-scripts', plugins_url( 'assets/js/ufp-scripts.js', __FILE__ ), array('jquery'), '', true );
}
add_action( 'wp_enqueue_scripts', 'ufp_enqueue_assets' );

// Створення шорткоду для реєстрації
function ufp_registration_form() {
    ob_start();
    ?>
    <form action="" method="POST">
        <label for="first_name">Ім'я</label>
        <input type="text" name="first_name" required>
        
        <label for="last_name">Призвище</label>
        <input type="text" name="last_name" required>
        
        <label for="email">Email</label>
        <input type="email" name="email" required>
        
        <input type="submit" name="ufp_register_user" value="Зареєструватися">
    </form>
    <?php
    if (isset($_POST['ufp_register_user'])) {
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $email = sanitize_email($_POST['email']);

        if (email_exists($email)) {
            echo 'Цей email вже зареєстровано.';
        } else {
            global $wpdb;
            $wpdb->insert(
                $wpdb->prefix . 'financial_users',
                array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email
                )
            );
            echo 'Реєстрація успішна!';
        }
    }

    return ob_get_clean();
}
add_shortcode('ufp_registration_form', 'ufp_registration_form');

// Створення шорткоду для додавання рахунку
function ufp_add_account_form() {
    ob_start();
    ?>
    <form action="" method="POST">
        <label for="account_name">Назва рахунку</label>
        <input type="text" name="account_name" required>
        
        <label for="balance">Початковий баланс</label>
        <input type="number" name="balance" required>
        
        <label for="currency">Валюта</label>
        <select name="currency">
            <option value="UAH">UAH</option>
            <option value="USD">USD</option>
            <option value="EUR">EUR</option>
        </select>
        
        <input type="submit" name="ufp_add_account" value="Додати рахунок">
    </form>
    <?php
    if (isset($_POST['ufp_add_account'])) {
        $account_name = sanitize_text_field($_POST['account_name']);
        $balance = floatval($_POST['balance']);
        $currency = sanitize_text_field($_POST['currency']);
        $user_email = wp_get_current_user()->user_email;

        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'finance_accounts',
            array(
                'user_email' => $user_email,
                'account_name' => $account_name,
                'balance' => $balance,
                'currency' => $currency
            )
        );
        echo 'Рахунок додано!';
    }
    
    return ob_get_clean();
}
add_shortcode('ufp_add_account_form', 'ufp_add_account_form');

// Створення шорткоду для завантаження CSV
function ufp_upload_csv_form() {
    ob_start();
    ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="csv_file">Виберіть CSV файл</label>
        <input type="file" name="csv_file" required>
        
        <input type="submit" name="ufp_upload_csv" value="Завантажити CSV">
    </form>
    <?php
    if (isset($_POST['ufp_upload_csv'])) {
        $csv_file = $_FILES['csv_file'];
        if ($csv_file['type'] == 'text/csv') {
            // Тобі потрібно додати код для обробки та імпорту даних з CSV
            echo 'Файл успішно завантажено!';
        } else {
            echo 'Будь ласка, завантажте CSV файл.';
        }
    }
    
    return ob_get_clean();
}
add_shortcode('ufp_upload_csv_form', 'ufp_upload_csv_form');
