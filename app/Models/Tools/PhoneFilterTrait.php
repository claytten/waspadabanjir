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
    if (substr($phone, 0, 3) == '+62') {
      $phoneNumber = preg_replace("/^0/", "+62", $phone);
    } else if (substr($phone, 0, 1) == '0') {
      $phoneNumber = preg_replace("/^0/", "+62", $phone);
    } else {
      $phoneNumber = "+62" . $phone;
    }
    return $phoneNumber;
  }

  public function checkingPhone($phone)
  {
    //regex php phone number
    $regex = '/^(\+62|62|08)(\d{3,4}-?){2}\d{3,4}$/';
    if (preg_match($regex, $phone)) {
      return true;
    } else {
      return false;
    }
  }
}
