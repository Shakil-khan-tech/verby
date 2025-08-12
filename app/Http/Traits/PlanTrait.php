<?php

namespace App\Http\Traits;
use Illuminate\Http\Request;
use Carbon\Carbon;

trait PlanTrait {

  public function prepare_plan_symbol(String $symbol_raw) {
    $result = preg_replace_callback(
      '/[0-9]+/',
      function($matches) {
        return sprintf('%02d', $matches[0]);
      },
      $symbol_raw
    );
    return strtoupper( $result );
  }

}
