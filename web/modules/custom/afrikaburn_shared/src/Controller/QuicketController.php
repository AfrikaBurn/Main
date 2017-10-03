<?php
if(!function_exists('kid_count')){ 
  function kid_count ($kids) {
    $count = 0;
    $teen = 0;
    foreach ($kids['und'] as $value) {
      if (isset($value['field_first_name_value']) ) {
        
        
        
        $time = strtotime( substr( $value['field_date_of_birth']['und'][0]['value'], 0, 10 ) );
        $newformat = date('Y-m-d',$time);
        
        if ( strtotime('24 April 2002') < $time ) {
          $count++;
        } elseif ( strtotime('24 April 1999') < $time ) {
          $teen++;
        }
        //drupal_set_message('DoB:<br><pre>' . $value['field_date_of_birth']['und'][0]['value'] . ' ' . $newformat .  ' ' . $teen . ' ' . $count .'</pre><br><br><br>'); 
        
        //drupal_set_message('Node Result:<br><pre>1</pre><br><br><br>'); 
      } else {
        //drupal_set_message('user_fields:<br><pre>'. print_r($value, true) .'</pre><br><br><br>');
      }
    }
    return array ($count, $teen);
  }
}

GLOBAL $user;

if (!empty($account->field_sa_id_or_passport_number['und'][0]['value'])) {
  
    
  GLOBAL $user;
  
  
  //drupal_set_message('user uid:'.$account->uid."<br>");
    
    $update = FALSE;
    $user_fields = user_load($account->uid);
    
    /*if ( !isset( $user_fields->field_quicket_id['und'][0]['value'] ) ) {
      $user_fields->field_quicket_id['und'][0]['value'] = "x" + date('c');
      unset($user->Login, $user->Role, $user->Unit, $user->expire, $user->pass, $user->field_bb_minors_details, $user->field_previous_envolvement);
      user_save($user_fields);
    } elseif ( substr($user_fields->field_quicket_id['und'][0]['value'], 0, 1) == 'x' ) {
      if (strtotime( substr($user_fields->field_quicket_id['und'][0]['value'], 1) ) > strtotime("-1 minutes")) {
        //Quicket response takes 30 seconds to time out
        //Our Request time out in 45 seconds
        //To prevent over lap I set a safety of 1 minute.
        //drupal_set_message('Quicket request occured in under a minute');
        watchdog('Quicket request occured in under a minute.');
        return;
      }
    }*/
    
      
      if ( isset( $user_fields->field_quicket_code['und'][0]['value'] ) ) {
      
        //count kids
        list ($no_kids, $teens) = kid_count( $account->field_bb_minors_details );
        
        //Check if kids have been updated
        if ( $user_fields->field_bb_no_kids_tickets != $no_kids ) {
          $update = TRUE;
          $user_fields->field_bb_no_kids_tickets['und'][0]['value']=$no_kids;
        }
        
        //Check if teens have been updated
        if ( $user_fields->field_bb_no_kids_tickets != $no_kids ) {
          $update = TRUE;
          $user_fields->field_bb_no_kids_tickets['und'][0]['value']=$no_kids;
        }
        
        //Check if ID or passport has been updated
        if (str_replace(' ', '', $account->field_sa_id_or_passport_number['und'][0]['value']) != str_replace(' ', '', $user_fields->field_sa_id_or_passport_number['und'][0]['value']) ) {
          $update = TRUE;
          $user_fields->field_sa_id_or_passport_number['und'][0]['value'] = $account->field_sa_id_or_passport_number['und'][0]['value'];
        }
        
        //drupal_set_message('id number:<br><pre>'. $update .'</pre><br>'); 
        if ( $update == TRUE ) {
          //update node
          
          
          $headers = array(
              'Content-Type' => 'application/json',
              'usertoken' => '9KySOwx7LTxATa4ODyTeYnJ57kDBVErT',
          );
          $user_pass='';
          $data = "{
                      'CodeId': " . $user_fields->field_quicket_id['und'][0]['value'] . ",
                      'EventId':21831,
                      'TicketTypes': [".
                                      "38682".
                                      ( $teens > 0 ? ',39356' : '').
                                      ( $no_kids > 0 ? ',38683' : '').
                                      "],
                      'CodeValue': '" . $user_fields->field_quicket_code['und'][0]['value'] . "',
                      'DiscountAmount': 0,
                      'IsPercentage': false,
                      'NumUses': " . ( $no_kids + $teens + 2 ) . ",
                      'ValidFrom': '2016-10-15T00:00:00Z',
                      'ValidTo': '2017-04-30T00:00:00Z',
                      'IsAccessCode': true,
                      'Email': '" .  str_replace(' ', '', $account->field_sa_id_or_passport_number['und'][0]['value']) . "'
                  }";
      
          $options = array(
            'method' => 'PUT',
            'data' => $data,
            'timeout' => 45,
            'headers' => $headers,
          );
          
          $result = drupal_http_request('https://api.quicket.co.za/api/codes/' . $user_fields->field_quicket_id['und'][0]['value'] . '?api_key=3171510c9eb84a7b70700652bcbd5cae', $options);
          //drupal_set_message('Result:<br><pre>'. print_r($result, true) .'</pre><br><br><br>'); 
          
          watchdog('Quicket call', 'New user:<br><pre>@print_r</pre>', array('@print_r' => print_r( $result, TRUE)));

          if (isset($result->code) and ($result->code == 200) ) {
            
            $obj = json_decode($result->data);
            $result = $obj->result;
            $codevalue = $result->CodeValue;
            $codeid = $result->CodeId;
            $link = "https://www.quicket.co.za/events/21831-afrikaburn-2017-play/#/?dc=" . $codevalue ;
            
            $account->field_quicket_code['und'][0]['value']=$codevalue;
            $account->field_quicket_id['und'][0]['value']=$codeid;
            $account->field_bb_no_kids_tickets['und'][0]['value']=$no_kids;
            $account->field_bb_no_teens_tickets['und'][0]['value']=$teens;
            
            /*$user_fields->field_bb_no_kids_tickets['und'][0]['value']=$no_kids;
            $user_fields->field_bb_no_teens_tickets['und'][0]['value']=$teens;
            unset($user->Login, $user->Role, $user->Unit, $user->expire, $user->pass, $user->field_bb_minors_details, $user->field_previous_envolvement);
            user_save($user_fields);*/
            
          }
        }
      //}
    //}
    
    
  } else {
    //Add new user to list
    
    list ($no_kids, $teens) = kid_count( $account->field_bb_minors_details );
    
    $headers = array(
        'Content-Type' => 'application/json',
        'usertoken' => '9KySOwx7LTxATa4ODyTeYnJ57kDBVErT',
    );
    $user_pass='';
    $data = "{'EventId':21831,
              'TicketTypes':[".
                            "38682".
                            ( $teens > 0 ? ',39356' : '').
                            ($no_kids > 0 ? ',38683' : '').
                            "],
              'IsPercentage':false,
              'DiscountAmount':0.0,
              'NumUses':" . ( $no_kids + $teens + 2 ) . ",
              'ValidFrom':'2016-10-15T00:00:00Z',
              'ValidTo':'2017-04-30T00:00:00Z',
              'IsAccessCode':true,
              'Email':'" .  str_replace(' ', '', $account->field_sa_id_or_passport_number['und'][0]['value']) . "'}";

    $options = array(
      'method' => 'POST',
      'data' => $data,
      'timeout' => 45,
      'headers' => $headers,
    );

    $result = drupal_http_request('https://api.quicket.co.za/api/codes?api_key=3171510c9eb84a7b70700652bcbd5cae', $options);
    //drupal_set_message('Result:<br><pre>'. print_r($result->data, true) .'</pre>'); 
    
    watchdog('Quicket call', 'New user:<br><pre>@print_r</pre>', array('@print_r' => print_r( $result, TRUE)));

    if (isset($result->code) and ($result->code == 200) ) {
      
      $obj = json_decode($result->data);
      $result = $obj->result;
      $codevalue = $result->CodeValue;
      $codeid = $result->CodeId;
      $link = "https://www.quicket.co.za/events/21831-afrikaburn-2017-play/#/?dc=" . $codevalue ;
      
      /*$user_fields->field_quicket_code['und'][0]['value']=$codevalue;
      $user_fields->field_quicket_id['und'][0]['value']=$codeid;
      $user_fields->field_bb_no_kids_tickets['und'][0]['value']=$no_kids;
      $user_fields->field_bb_no_teens_tickets['und'][0]['value']=$teens;*/
      $account->field_quicket_code['und'][0]['value']=$codevalue;
      $account->field_quicket_id['und'][0]['value']=$codeid;
      $account->field_bb_no_kids_tickets['und'][0]['value']=$no_kids;
      $account->field_bb_no_teens_tickets['und'][0]['value']=$teens;
      //unset($user->Login, $user->Role, $user->Unit, $user->expire, $user->pass, $user->field_bb_minors_details, $user->field_previous_envolvement);
      /*user_save($user_fields);*/
      //drupal_set_message('Last line:' . $codevalue); 
      
    }  
  }
}
