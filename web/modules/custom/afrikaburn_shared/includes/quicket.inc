<?php

/**
 * @file
 * Contains quicket utility functions
 */

use \Drupal\Core\Site\Settings;
use Drupal\Component\Serialization\Json;

/**
 * Fetches a new code and id from quicket
 * @param [object] $user User to fetch new code & id for
 */
function _quicket_new($user){
  return _quicket($user, 'POST');
}

/**
 * Fetches an updated code and id from quicket
 * @param [object] $user User to fetch updated code & id for
 */
function _quicket_update($user){

  $field_quicket_id = $user->get('field_quicket_id');
  $quicket_id = $field_quicket_id ? $field_quicket_id->getValue()[0]['value'] : FALSE;

  return _quicket($user, 'PUT', $quicket_id);
}

/**
 * Makes a request from quicket
 */
function _quicket($user, $method, $quicket_id = FALSE){

  $settings = Settings::get('afrikaburn.quicket');
  $teens = 0;
  $kids = 0;

  $id = str_replace(
    ' ', '', 
    array_column(
      $user
        ->get('field_id_number')
        ->getValue(), 
      'value'
    )[0]
  );

  $ticket_types = implode(',',
    array_filter(
      [
        65400, 
        ($kids ? 0 : 0),
        ($teens ? 0 : 0)
      ]
    )
  );

  $data = json_encode(
    [
      'EventId' => $settings['event_id'],
      'TicketTypes' =>  $ticket_types,
      'IsPercentage' => FALSE,
      'DiscountAmount' => 0.0,
      'NumUses' => 2 + $kids + $teens,
      'ValidFrom' => $settings['valid_from'],
      'ValidTo' => $settings['valid_to'],
      'IsAccessCode' => TRUE,
      'Email' => $id,
    ]
  );

  $options = [
    'headers' => [
      'Content-Type' => 'application/json',
      'usertoken' => $settings['user_token'],
    ],
    'timeout' => 45,
    'form_params' => [
      'data' => $data,
    ]
  ];

  dpm($data);

  try{
    $client = \Drupal::httpClient();
    $response = $client->request(
      $method,
      'https://api.quicket.co.za/api/codes' . ($quicket_id ? '/' . $quicket_id : '') . '?api_key=' . $settings['api_key'],
      $options
    );
  } catch (Exception $e){
    dpm($e);
  }

  dpm($response);
  // if ($response->getStatusCode() == 200){
    
  //   $result = $response->getBody()->getContents();
  //   // $user->field_quicket_code = $result->CodeValue;
  //   // $user->field_quicket_id = $result->CodeId;
  //   \Drupal::logger('afrikaburn_shared')->debug('<pre>' . var_export($result, TRUE) . '</pre>');

    // return $user->save();
  // }

  return FALSE;  
}
