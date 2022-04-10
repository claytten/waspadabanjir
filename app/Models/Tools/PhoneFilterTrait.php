<?php

namespace App\Models\Tools;

trait PhoneFilterTrait
{

  protected $phoneNumber = null;

  /**
   * Filtering Indonesian Phone Number
   * 
   * @param string $phone
   * 
   * @return string
   */
  public function filterPhone($phone)
  {
    if(substr($phone,0,3) == '+62') {
      $phoneNumber = preg_replace("/^0/", "+62", $phone);
    } else if(substr($phone,0,1) == '0') {
      $phoneNumber = preg_replace("/^0/", "+62", $phone);
    } else {
      $phoneNumber = "+62".$phone;
    }
    return $phoneNumber;
  }
}