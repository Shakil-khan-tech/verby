<?php

namespace App\Http\Traits;
use DB;
use Illuminate\Http\Request;
// use Carbon\Carbon;
use WebReinvent\CPanel\CPanel;

trait EmailTrait {

    //private functions
    private function prepare_errors($uapi) {
      $_return = null;
      if ( !is_array($uapi) ) {
        $_return[][] = $uapi;
      } else {
        if ( !is_array($uapi[0]) ) {
          $_return[] = $uapi;
        } else {
          $_return = $uapi;
        }
      }
      return $_return;
    }

    //public functions
    public function list_emails($email = '') {

      $cpanel = new CPanel();  
      $Module = 'Email';
      // $function = 'list_pops';
      $function = 'list_pops_with_disk';
      $parameters_array = [
        'regex'          => '@aaab.ch',
        'domain'           => 'aaab.ch',
        'maxaccounts'      => '500',
        'no_validate'      => '1',
        'get_restrictions' => '1',
        'email'            => ($email != '') ? explode('@',$email)[0] : '',
        'infinitylang'     => '0'
      ];
      $response = $cpanel->callUAPI($Module, $function, $parameters_array);

      if ( $response['status'] == "success" ) {
        if ( $response['data'] == null || empty($response['data']->data) ) {
          // token not ok or other problem ....
          return (object) ['status' => 'failed', 'errors' => ["Technical Error"]];
        } else {
          // success
          return (object) ['status' => 'success', 'data' => $response['data']->data];
        }
      } elseif( $response['status'] == "failed" ) {
        // failed
        return (object) ['status' => 'failed', 'errors' => $response['errors']];
      } else {
        // some error
        return (object) ['status' => 'failed', 'errors' => $response];
      }
      
    }

    public function change_password($email, $password) {

      $cpanel = new CPanel();  
      $Module = 'Email';
      $function = 'passwd_pop';
      $parameters_array = [
        'email'           => explode('@',$email)[0],
        'password'        => $password,
        'domain'          => 'aaab.ch',
      ];
      $response = $cpanel->callUAPI($Module, $function, $parameters_array);

      // return $response;

      if ( $response['status'] == "success" ) {
        if ( !empty($response['data']->errors) ) {
          // token not ok or other problem ....
          return (object) ['status' => 'failed', 'errors' => ["Technical Error"]];
        } else {
          // success
          return (object) ['status' => 'success', 'data' => $response['data']->data];
        }
      } elseif( $response['status'] == "failed" ) {
        // failed
        return (object) ['status' => 'failed', 'errors' => $response['errors']];
      } else {
        // some error
        return (object) ['status' => 'failed', 'errors' => $response];
      }
      
    }

    public function change_quota($email, $quota) {

      $cpanel = new CPanel();  
      $Module = 'Email';
      $function = 'edit_pop_quota';
      $parameters_array = [
        'email'           => explode('@',$email)[0],
        'quota'           => $quota,
        'domain'          => 'aaab.ch',
      ];
      $response = $cpanel->callUAPI($Module, $function, $parameters_array);

      // return $response;

      if ( $response['status'] == "success" ) {
        if ( !empty($response['data']->errors) ) {
          // token not ok or other problem ....
          return (object) ['status' => 'failed', 'errors' => ["Technical Error"]];
        } else {
          // success
          return (object) ['status' => 'success', 'data' => $response['data']->data];
        }
      } elseif( $response['status'] == "failed" ) {
        // failed
        return (object) ['status' => 'failed', 'errors' => $response['errors']];
      } else {
        // some error
        return (object) ['status' => 'failed', 'errors' => $response];
      }
      
    }

    public function create_email(String $_email, String $pass, String $quota) {
      $email = explode('@',$_email)[0];
      $domain = explode('@',$_email)[1];

      $cpanel = new CPanel();  
      $Module = 'Email';
      $function = 'add_pop';
      $parameters_array = [
        'email'           => $email,
        'password'        => $pass,
        'quota'           => $quota,
        'domain'          => 'aaab.ch',
        'skip_update_db'  => '1',
      ];
      $response = $cpanel->callUAPI($Module, $function, $parameters_array);

      if ( $response['status'] == "success" ) {
        if ( !empty($response['data']->errors) ) {
          // token not ok or other problem ....
          return (object) ['status' => 'failed', 'errors' => ["Technical Error"]];
        } else {
          // success
          return (object) ['status' => 'success', 'data' => $response['data']->data];
        }
      } elseif( $response['status'] == "failed" ) {
        // failed
        return (object) ['status' => 'failed', 'errors' => $response['errors']];
      } else {
        // some error
        return (object) ['status' => 'failed', 'errors' => $response];
      }

    }

    public function delete_email($email) {

      $cpanel = new CPanel();  
      $Module = 'Email';
      $function = 'delete_pop';
      $parameters_array = [
        'email'           => $email,
      ];
      $response = $cpanel->callUAPI($Module, $function, $parameters_array);

      if ( $response['status'] == "success" ) {
        if ( !empty($response['data']->errors) ) {
          // token not ok or other problem ....
          return (object) ['status' => 'failed', 'errors' => ["Technical Error"]];
        } else {
          // success
          return (object) ['status' => 'success', 'data' => $response['data']->data];
        }
      } elseif( $response['status'] == "failed" ) {
        // failed
        return (object) ['status' => 'failed', 'errors' => $response['errors']];
      } else {
        // some error
        return (object) ['status' => 'failed', 'errors' => $response];
      }
      
    }

}
