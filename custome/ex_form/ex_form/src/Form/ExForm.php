<?php

namespace Drupal\ex_form\Form;

use Drupal\Core\Form\FormBase;																			// Базовый класс Form API
use Drupal\Core\Form\FormStateInterface;														// Класс отвечает за обработку данных

/**
 * Наследуемся от базового класса Form API
 * @see \Drupal\Core\Form\FormBase
 */
class ExForm extends FormBase
{

  // метод, который отвечает за саму форму - кнопки, поля
  public function buildForm(array $form, FormStateInterface $form_state)
  {

    $form['first_name'] = array(
      '#title' => t('First Name'),
      '#type' => 'textfield',
      '#description' => $this->t('Не должно содержать цифр'),
      '#required' => TRUE,
    );


    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#required' => TRUE,
      '#description' => $this->t('Не должно содержать цифр'),
    ];

    $form['subject'] = array(
      '#title' => t('Subject '),
      '#type' => 'textarea',

    );
    $form['message'] = array(
      '#title' => t('Message '),
      '#type' => 'text_format',

    );
    $form['email'] = array(
      '#title' => t('Email '),
      '#type' => 'email',
      '#required' => TRUE,

    );


    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Отправить форму'),


    ];


    return $form;
  }

  // метод, который будет возвращать название формы
  public function getFormId()
  {
    return 'ex_form_exform_form';
  }

  // ф-я валидации
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $email  = $_POST['email'];
    $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

    if (filter_var($emailB, FILTER_VALIDATE_EMAIL) === false ||
      $emailB != $email
    ) {
      //exit(0);
      drupal_set_message(t('Не верный Email'), 'error');
    }
    else
    {
      //drupal_set_message(t('This email adress is valid!'));
      drupal_set_message(t('Отправлено'));

      $log =  $_POST['email'];
      file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
    }

  }




  // действия по сабмиту
  public function submitForm(array &$form, FormStateInterface $form_state)
  {



    $arr = array(
      'properties' => array(
        array(
          'property' => 'email',
          'value' => $form_state->getValue('email')
        ),
        array(
          'property' => 'firstname',
          'value' => $form_state->getValue('first_name')
        ),
        array(
          'property' => 'lastname',
          'value' => $form_state->getValue('last_name')
        ),

      )
    );
    $post_json = json_encode($arr);
    $hapikey = readline("75b8fa05-e3cd-465c-9a69-346e53ac7254");
    $endpoint = 'https://api.hubapi.com/contacts/v1/contact?hapikey=75b8fa05-e3cd-465c-9a69-346e53ac7254' . $hapikey;
    $ch = @curl_init();
    @curl_setopt($ch, CURLOPT_POST, true);
    @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
    @curl_setopt($ch, CURLOPT_URL, $endpoint);
    @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = @curl_exec($ch);
    $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_errors = curl_error($ch);
    @curl_close($ch);
    echo "curl Errors: " . $curl_errors;
    echo "\nStatus code: " . $status_code;
    echo "\nResponse: " . $response;


  }


}
