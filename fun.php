<?php
// if(!defined(ABSPATH)){
//     $pagePath = explode('/wp-content/', dirname(__FILE__));
//     include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
// }

/**
 * Проверка на существующие данные в таблице
 * **/
function check_email_bd($email){
    // проверяем, существует ли email в таблице
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_aut';
    $result = $wpdb->get_var( $wpdb->prepare( 
        "
            SELECT COUNT(*)
            FROM $table_name
            WHERE email = %s
        ", 
        $email
    ) );

    if ( $result > 0 ) {
        return false;
    } else {
        // запись с таким email не найдена
        return true;
    }

}


/**
 * Проверка на существующие данные в таблице
 * **/
function check_login_bd($login){
    // проверяем, существует ли email в таблице
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_aut';
    $result = $wpdb->get_var( $wpdb->prepare( 
        "
            SELECT COUNT(*)
            FROM $table_name
            WHERE login = %s
        ", 
        $login
    ) );

    if ( $result > 0 ) {
        return false;
    } else {
        // запись с таким email не найдена
        return true;
    }
}


/**
 * 
 * Получить данные из таблицы
 * **/
function set_find_user_data(){
    $email = $_POST['user_email'];
    
    global $wpdb;
    $results = $wpdb->get_results( "SELECT * FROM `wp_user_aut` WHERE `email` LIKE '%$email%'");

    $response = array(
        'status' => 'success', // замените 'success' или 'error' на соответствующий статус в зависимости от результата проверки
        'message' => json_encode($results) // замените это сообщение на соответствующее сообщение в зависимости от результата проверки
    );

    wp_send_json($response);
    wp_die();
    

}
add_action('wp_ajax_set_find_user_data', 'set_find_user_data');
add_action('wp_ajax_nopriv_set_find_user_data', 'set_find_user_data');


/**
 * 
 * Получить конкретного пользователя
 * **/
function set_find_user_email(){
    $email = $_POST['user_email'];
    
    global $wpdb;
    $results = $wpdb->get_results( "SELECT * FROM `wp_user_aut` WHERE `email`LIKE '$email'");
    //$res_int = $wpdb->get_var($results);

    if ($results > 0 ) {
        $response = array(
            'status' => 'success', // замените 'success' или 'error' на соответствующий статус в зависимости от результата проверки
            'message' => json_encode($results) // замените это сообщение на соответствующее сообщение в зависимости от результата проверки
        );
    } else {
        // запись с таким email не найдена
       $response = array(
            'status' => 'error', // замените 'success' или 'error' на соответствующий статус в зависимости от результата проверки
            'message' => 'Користувач не знайдений' // замените это сообщение на соответствующее сообщение в зависимости от результата проверки
        );
    }

    wp_send_json($response);
    wp_die();
    

}
add_action('wp_ajax_set_find_user_email', 'set_find_user_email');
add_action('wp_ajax_nopriv_set_find_user_email', 'set_find_user_email');


/**
 * Обновление данных
 * 
 * */

function update_user_info(){
    global $wpdb;

    $status = 'success';
    $message =  'Дані успішно записані';
    $email = $_POST['auth_email'];
    $login = $_POST['auth_login'];
    $password = $_POST['auth_pass'];
    
    $password_ = wp_hash_password( $password );

    
    $results = $wpdb->get_results( "UPDATE `wp_user_aut` SET `login` = '$login', `password` = '$password_' WHERE `wp_user_aut`.`email` = '$email';");

    if ( $results > 0 ) {
        $response = array(
            'status' => 'success', // замените 'success' или 'error' на соответствующий статус в зависимости от результата проверки
            'message' => json_encode($results) // замените это сообщение на соответствующее сообщение в зависимости от результата проверки
        );
    } else {
        // запись с таким email не найдена
       $response = array(
            'status' => 'error', // замените 'success' или 'error' на соответствующий статус в зависимости от результата проверки
            'message' => 'Користувач не знайдений' // замените это сообщение на соответствующее сообщение в зависимости от результата проверки
        );
    }

    wp_send_json($response);
    wp_die();

}
add_action('wp_ajax_update_user_info', 'update_user_info');
add_action('wp_ajax_nopriv_update_user_info', 'update_user_info');

/**
 * 
 * Удалить пользователя
 * **/

function delete_user_info(){
    global $wpdb;

    $status = 'success';
    $message =  'Дані успішно видалені';
    $email = $_POST['auth_email'];
    
    $results = $wpdb->get_results( "DELETE FROM `wp_user_aut` WHERE `wp_user_aut`.`email` = '$email';");

    $response = array(
        'status' => 'success', // замените 'success' или 'error' на соответствующий статус в зависимости от результата проверки
        'message' => 'Дані успішно видалені' // замените это сообщение на соответствующее сообщение в зависимости от результата проверки
    );
  

    wp_send_json($response);
    wp_die();

}
add_action('wp_ajax_delete_user_info', 'delete_user_info');
add_action('wp_ajax_nopriv_delete_user_info', 'delete_user_info');


/**
 * Проверка на вход
 * */

function check_login_credentials() {
    $login = $_POST['auth_email'];
    $password = $_POST['auth_pass'];
    $login_ = '';
    $pass_ = '';


    global $wpdb;

    $response = $wpdb->get_results( "SELECT * FROM `wp_user_aut` WHERE `login` LIKE '$login'" );
    

    foreach ($response as $page) {
        $login_ =  $page->login;
        $pass_ = $page->password;
    }

    if($login_ != '' && $pass_ != ''){

        if($login_ === $login && wp_check_password( $password, $pass_ )){
            $response = array(
                'status' => 'success', // замените 'success' или 'error' на соответствующий статус в зависимости от результата проверки
                'message' => 'Вход выполнен успешно' // замените это сообщение на соответствующее сообщение в зависимости от результата проверки
            );
        }else{
            $response = array(
                'status' => 'error', // замените 'success' или 'error' на соответствующий статус в зависимости от результата проверки
                'message' => 'Логін або пароль невірні' // замените это сообщение на соответствующее сообщение в зависимости от результата проверки
            );
        }
    }else{
       $response = array(
            'status' => 'error', // замените 'success' или 'error' на соответствующий статус в зависимости от результата проверки
            'message' => 'Логін або пароль невірні' // замените это сообщение на соответствующее сообщение в зависимости от результата проверки
        ); 
    }

     wp_send_json($response);
     wp_die();
}

add_action('wp_ajax_check_login_credentials', 'check_login_credentials');
add_action('wp_ajax_nopriv_check_login_credentials', 'check_login_credentials');


//Создание нового пользователя в базе данных
function add_user_credentials_to_table() {
    global $wpdb;
    $status = 'success';
    $message =  'Дані успішно записані';
    
    $email = $_POST['auth_email'];
    $login = $_POST['auth_login'];
    $password = $_POST['auth_pass'];

    if(check_email_bd($email)){
        if(check_login_bd($login)){
            $table_name = $wpdb->prefix . 'user_aut';

            $wpdb->insert($table_name, array(
                'date' => current_time('mysql'),
                'email' => $email,
                'login' => $login,
                'password' => wp_hash_password( $password ),
            ));

            $status = 'success';
            $message = 'Дані успішно записані';
        }else{
            $status = 'error';
            $message = 'Помилка, такий login існує';
        }
    }else{
        $status = 'error';
        $message = 'Помилка, такий користувач існує';
    }


    $response = array(
        'status' => $status, // замените 'success' или 'error' на соответствующий статус в зависимости от результата проверки
        'message' => $message // замените это сообщение на соответствующее сообщение в зависимости от результата проверки
    );

    wp_send_json($response);

    wp_die();
}


add_action('wp_ajax_add_user_credentials_to_table', 'add_user_credentials_to_table');
add_action('wp_ajax_nopriv_add_user_credentials_to_table', 'add_user_credentials_to_table');


function sendEmail(){

    $email = $_POST['auth_email'];
    $login = $_POST['auth_login'];
    $password = $_POST['auth_pass'];

    $to = $email;
    $subject = 'Укрінтеренерго';
    $message = 'Вам отправлено письмо для входа в особовий кабінет споживача. <br> 
    Ваш логін: '.$login.' Ваш пароль: '.$password.' <br> 
    Для входу, перейдіть за посиланням https://uie.kiev.ua/ok-3/';

    $headers = array('Content-Type: text/html; charset=utf-8');

    wp_mail( $to, $subject, $message, $headers );


    $response = array(
        'status' => 'success', // замените 'success' или 'error' на соответствующий статус в зависимости от результата проверки
        'message' => 'Дані збережені. Повідомлення з дотупом надіслано на email' // замените это сообщение на соответствующее сообщение в зависимости от результата проверки
    );

    wp_send_json($response);
    wp_die();
}


add_action('wp_ajax_sendEmail', 'sendEmail');
add_action('wp_ajax_nopriv_sendEmail', 'sendEmail');



