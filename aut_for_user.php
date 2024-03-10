<?php
/**
 * @package Aut_for_user
 */
/*
Plugin Name: Авторизація користувачів
Description: Плагін для авторизації користувачів та створенням для них логіну та паролю, а також для редагування даних адміністратором
Version: 1.0.1
Requires at least: 5.0
Requires PHP: 7.4
Tested up to: 6.0
WC tested up to: 6.9
Author: Serhii Krainik
Author URI: https://example.com/
License: GPLv2 or later
*/

/**
 * Пути
 * **/
$plugin_path = plugin_dir_path( __FILE__ );
require_once( $plugin_path . 'fun.php' );

/**
 * Подключение js and css
 * */


function add_login_ajax_script() {
    wp_enqueue_script('js_aut', plugin_dir_url( __FILE__ ) . '/js/js_aut.js', array('jquery'), '1.0', true);
    wp_localize_script( 'js_aut', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))); 
}

add_action('wp_enqueue_scripts', 'add_login_ajax_script');



function my_styles_aut() {
    wp_enqueue_style( 'my-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), '1.0', 'all' );
}
add_action( 'wp_enqueue_scripts', 'my_styles_aut' );


/**
 * Активация плагина
 * **/
function aut_for_user_activate() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'user_aut';

  if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    // таблица не существует, создаем ее
    $sql = "CREATE TABLE $table_name (
      id INT(11) NOT NULL AUTO_INCREMENT,
      date DATETIME NOT NULL,
      email VARCHAR(255) NOT NULL,
      login VARCHAR(50) NOT NULL,
      password VARCHAR(255) NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}

register_activation_hook(__FILE__, 'aut_for_user_activate');


/**
 * Из настрояк пользователя
 * */

// Добавление дополнительного пункта меню в админ-панели
function add_autodelivery_menu_item(){
    add_menu_page(
        "Авторизація користувачів", // Заголовок страницы в меню
        "Авторизація користувачів", // Текст ссылки на страницу
        "manage_options", // Роль пользователя, который может просматривать эту страницу
        "autodelivery", // Уникальный идентификатор страницы
        "autodelivery_page_content", // Функция, которая будет вызвана при выводе страницы
        "dashicons-admin-network", // Иконка, которая будет использоваться в меню
        30 // Позиция пункта меню в списке
    );
}
add_action("admin_menu", "add_autodelivery_menu_item");

// Функция, которая будет вызвана при выводе содержимого страницы
function autodelivery_page_content(){


    add_login_ajax_script();
    my_styles_aut();


    echo '<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';


    echo '<div class="popup">
          <h2 class="msg_popup">Повідомлення успішно відправлено!</h2>
          <div class="btn" onclick="hidePopap();">Добре</div>
        </div>';

    // Ваш код здесь
    echo '<h1 style="text-aling: center;">Створення логіну та пароля для користувачів</h1>';
    echo '<div class="wpb_wrapper console">
            <p>Щоб створити новий пароль та логін для користувача, введіть його email, придумайте йому логін та пароль. Після натискання кнопки "Створити" клієнту на email буде надіслано листа з логіном та паролем.</p>
    <div class="form_auth_block form_kr">
        <div class="form_auth_block_content">
        <h2 class="form_auth_block_head_text">Реєстрація нового користувача</h2>
        <form class="form_auth_style" action="#" method="post">
        <label>E-mail</label><br>
        <input name="auth_email" required="" type="email" placeholder="Введіть його E-mail"><br>

        <form class="form_auth_style" action="#" method="post">
        <label>Login</label><br>
        <input name="auth_login" required="" type="text" placeholder="Введіть йому Login"><br>

        <label>Пароль</label><br>
        <div class="generate"> 
        <input id="passwordInput" name="auth_pass" required="" type="text" placeholder="Введіть йому пароль"> 
            <div class="create_pass" onclick="generatePassword();" >  
                <img src="'.plugin_dir_url( __FILE__ )."image/create_pass.png".'" alt="Сгенерировать пароль">
            </div> 
        </div><br>
         <div class="msg_create_new_user" style="padding-bottom: 10px; color: orange;"></div> 
        <button class="reg_auth_user" name="form_auth_submit" type="submit">Створити</button></form>
        </div>
    </div>
</div>';


/**
 * Вторая сраница
 * */

echo '<h1 style="text-aling: center;">Зміна пароля користувача</h1>';
echo '<div class="wpb_wrapper console">
            <p>Щоб змінити пароль користувача необхідно знайти його по email. Після цього ввести новий пароль. Повідомлення про зміну пароля буде надіслано клієнту на пошту.</p>
            </div>';

echo '<div class="form_auth_block">
        <div class="form_auth_block_content">
        <h2 class="form_auth_block_head_text">Знайти користувача</h2>
        <form class="form_auth_style" action="#" method="post">
        <label>E-mail</label><br>
        <input name="search_user" id="input-field"  required=""   type="email" placeholder="Введіть E-mail користувача">
            <button class="find_user_email_btn" name="form_auth_submit" type="submit">Знайти</button>
        </form>
';


/**
 * 
 * Редактирование данных
 * 
 * */
echo'
<div class="red_user_data" style="display: none;">
    <div class="wpb_wrapper console">
            <p>Оновлення даних</p>
        </div>
    
    <label>Новий логін</label><br>
    <input type="text" name="new_login_user">
    <label>Новий пароль</label><br>
    <div class="generate"> 
    <input id="passwordInputOld" type="text" name="new_password_user">
        <div class="create_pass" onclick="generatePasswordOld();" >  
            <img src="'.plugin_dir_url( __FILE__ )."image/create_pass.png".'" alt="Сгенерировать пароль">
        </div> 
    </div>
    <div class="btns_style"> 
        <button class="update_user_date_btn" name="form_auth_submit_" type="submit">Оновити дані</button>
        <button class="detele_user_date_btn" name="form_auth_submit" type="submit">Видалити дані</button>
    </div>
</div>
';    


echo '<a href="https://drive.google.com/u/0/uc?id=1UY0rR7k47f2FnM88OjyJgR2esYI_UzHM&export=download" download>Скачать файл</a>';

}


// function add_submenu_items(){
//     add_submenu_page(
//         "autodelivery", // Уникальный идентификатор родительской страницы
//         "Управление товарами", // Заголовок страницы в меню
//         "Управление товарами", // Текст ссылки на страницу
//         "manage_options", // Роль пользователя, который может просматривать эту страницу
//         "product_management", // Уникальный идентификатор страницы
//         "product_management_page_content" // Функция, которая будет вызвана при выводе страницы
//     );
    
//     add_submenu_page(
//         "autodelivery", // Уникальный идентификатор родительской страницы
//         "Управление скидками", // Заголовок страницы в меню
//         "Управление скидками", // Текст ссылки на страницу
//         "manage_options", // Роль пользователя, который может просматривать эту страницу
//         "discount_management", // Уникальный идентификатор страницы
//         "discount_management_page_content" // Функция, которая будет вызвана при выводе страницы
//     );
// }
// add_action("admin_menu", "add_submenu_items");

// Функции, которые будут вызваны при выводе содержимого страниц
function product_management_page_content(){
    // Ваш код здесь
    echo "<h1>Управление товарами</h1>";
}

function discount_management_page_content(){
    // Ваш код здесь
    echo "<h1>Управление скидками</h1>";
}




function custom_shortcode($atts, $content = null ) {
    


   $output = '<div class="wpb_wrapper">'
   .html_entity_decode($content).'
    <div class="form_auth_block">
    <div class="form_auth_block_content">
        <h2 class="form_auth_block_head_text">Авторизація</h2>
        <form class="form_auth_style" action="#" method="post"><label>Ваш Login</label><br>
        <input name="auth_login" required="" type="text" placeholder="Введіть Ваш Login"><br>
        <label>Ваш пароль</label><br>
        <input name="auth_pass" required="" type="password" placeholder="Введіть Ваш пароль"><br>
        <div class="error_input" style="display: none;"></div>
        <button class="form_auth_button" name="form_auth_submit" type="submit">Увійти</button></form>
    </div>
    </div>

        </div>';

   return $output;
}
add_shortcode( 'shortcode_for_aut_user', 'custom_shortcode' );


?>
