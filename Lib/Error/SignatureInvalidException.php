<?php

App::uses('ExceptionRenderer', 'Error');
class SignatureInvalidException extends ExceptionRenderer {
    function __construct($text) {
      echo __FILE__ . " " . __LINE__ . " This is the constructor for BeforeValidException<br>";  
      echo "text147777777777877777774 = $text";
      $this->Expired($text);
      echo __FILE__ . " " . __LINE__ . "<br>";

    }
    
    public function SignatureInvalid($error) {   
        echo __FILE__ . " " . __LINE__ . "<br>"; 

    }

    
}