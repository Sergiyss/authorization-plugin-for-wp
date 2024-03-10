const popup = document.querySelector('.popup');

function showPopup(message) {
  popup.classList.add('show');
  document.getElementsByClassName("msg_popup")[0].innerText = arguments[0];
}

// Предположим, что вы отправляете сообщение через форму и получаете ответ от сервера
function onSubmit() {
  // Ваш код для отправки сообщения
  // Если сообщение успешно отправлено, вызывайте функцию showPopup()
  showPopup();
}

function hidePopap(){
    jQuery('input[name="auth_email"]').val("");
    jQuery('input[name="auth_login"]').val("");
    jQuery('input[name="auth_pass"]').val("");
    jQuery('.msg_create_new_user').text('');
    popup.classList.remove('show');
}

// Функция создания HTML-элемента списка
function createListItem(item) {
  const li = document.createElement("li");
  //li.textContent = `email: ${item.email}, Login: ${item.login}, Password: ${item.password}`;
  li.textContent = `email: ${item.email}`;
  return li;
}



/**
 * Список
 * **/
jQuery(document).ready(function($) {
$( function() {
    function log( message ) {
      $( "<div>" ).text( message ).prependTo( "#log" );
      $( "#log" ).scrollTop( 0 );
    }
 
    $( "#input-field" ).autocomplete({
      source: function( request, response ) {
        jQuery.ajax( {
          url: myAjax.ajaxurl,
          type: "post",
          dataType: "json",
          data: {
             action: "set_find_user_data",
            user_email: request.term
          },
          success: function( data ) {

              var data = JSON.parse(data.message); // преобразование JSON-строки в объект
            
              var emails = [];
              for (var i = 0; i < data.length; i++) {
                emails.push(data[i].email); // создание массива с e-mail
              }
      
                response( emails );
              }
        } );
      },
      minLength: 3,
      select: function( event, ui ) {
        log( "Selected: " + ui.item.value + " aka " + ui.item.id );
      }
    } );
  } );

})

///////


/**
 * 
 * Получение данных по пользователю
 * **/

jQuery(document).ready(function($) {

    $('.find_user_email_btn').on('click', function(event) {
        event.preventDefault();

        var email = $('input[name="search_user"]').val();
    

        $.ajax({
            url : myAjax.ajaxurl,
            type: 'post',
            dataType: "json",
            data: {
                action: 'set_find_user_email',
                user_email: email,
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('.red_user_data').show(500);
                    var data = JSON.parse(response.message); // преобразование JSON-строки в объект

                    if(data.length > 0){
            
                        var login = [];
                        for (var i = 0; i < data.length; i++) {
                            login.push(data[i].login); // создание массива с e-mail
                        }

                        $('input[name="new_login_user"]').val(login[0]);
                   }else{
                    $('.red_user_data').hide(500);
                    showPopup('Користувач не знайдений' );
                   }
                } else {
                    showPopup(response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Произошла ошибка 2: ' + textStatus + ' ' + errorThrown);
            }
        });
    });
});

/**
 * 
 * Обновление данных пользователя 
 * 
 * **/


jQuery(document).ready(function($) {

    $('.update_user_date_btn').on('click', function(event) {
        event.preventDefault();
        
        var email = $('input[name="search_user"]').val();
        var login = $('input[name="new_login_user"]').val();
        var password = $('input[name="new_password_user"]').val();

        $.ajax({
            url : myAjax.ajaxurl,
            type: 'post',
            dataType: "json",
            data: {
                action: 'update_user_info',
                auth_email : email,
                auth_login: login,
                auth_pass: password,
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('input[name="search_user"]').val('');
                    $('.red_user_data').hide(500);

                    send_email(email, login, password);
                    
                } else {
                    showPopup(response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Произошла ошибка 2: ' + textStatus + ' ' + errorThrown);
            }
        });
    });

    //Удалить пользователя

    $('.detele_user_date_btn').on('click', function(event) {
        event.preventDefault();
        
        var email = $('input[name="search_user"]').val();


        $.ajax({
            url : myAjax.ajaxurl,
            type: 'post',
            dataType: "json",
            data: {
                action: 'delete_user_info',
                auth_email : email,
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('input[name="search_user"]').val('');
                    $('.red_user_data').hide(500);

                    send_email(email, login, password);
                    
                } else {
                    showPopup(response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Произошла ошибка 2: ' + textStatus + ' ' + errorThrown);
            }
        });
    });
});




jQuery(document).ready(function($) {

    $('.form_auth_button').on('click', function(event) {
        event.preventDefault();
    
    $('.error_input').hide();


        var email = $('input[name="auth_login"]').val();
        var password = $('input[name="auth_pass"]').val();

        $.ajax({
            url : myAjax.ajaxurl,
            type: 'post',
            data: {
                action: 'check_login_credentials',
                auth_email: email,
                auth_pass: password,
            },
            success: function(response) {
                if (response.status === 'success') {
                     document.location.href = "https://uie.kiev.ua/osobovyj-kabinet-testovyj-korystuvach/";
                } else {
                    $('.error_input').show(200);
                    $('.error_input').text(response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Произошла ошибка 2: ' + textStatus + ' ' + errorThrown);
            }
        });
    });
});


/**
 * 
 * Сохраняю пользователя в базе данных
 * 
 * **/

 jQuery(document).ready(function($) {

    $('.reg_auth_user').on('click', function(event) {
        event.preventDefault();

        $('.msg_create_new_user').text("Зачекайте");

        var email = $('input[name="auth_email"]').val();
        var login = $('input[name="auth_login"]').val();
        var password = $('input[name="auth_pass"]').val();

        $.ajax({
            url : myAjax.ajaxurl,
            type: 'post',
            data: {
                action: 'add_user_credentials_to_table',
                auth_email: email,
                auth_login: login,
                auth_pass: password,
            },
            success: function(response) {
                if (response.status === 'success') {
                    send_email(email, login, password);
                } else {
                    showPopup(response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Произошла ошибка 2: ' + textStatus + ' ' + errorThrown);
            }
        });
    });
});


/**
 * 
 * Отправляю письмо пользователю
 * **/

function send_email(email, login, password){
    var email = email;
    var login = login;
    var password = password;

    jQuery.ajax({
        url : myAjax.ajaxurl,
        type: 'post',
        data: {
            action: 'sendEmail',
            auth_email: email,
            auth_login: login,
            auth_pass: password,
        },
        success: function(response) {
            if (response.status === 'success') {
                showPopup(response.message);
            } else {
                showPopup('Произошла ошибка');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Произошла ошибка 2: ' + textStatus + ' ' + errorThrown);
        }
    });
}


/**
 * Генерация пароля
 * */

 function generatePassword() {
  var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
  var password = "";
  for (var i = 0; i < 12; i++) {
    password += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  
  var passwordInput = document.getElementById("passwordInput");
  passwordInput.value = password;

  return password;
}


/**
 * Генерация пароля
 * */

 function generatePasswordOld() {
  var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
  var password = "";
  for (var i = 0; i < 12; i++) {
    password += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  
  var passwordInput = document.getElementById("passwordInputOld");
  passwordInput.value = password;

  return password;
}


