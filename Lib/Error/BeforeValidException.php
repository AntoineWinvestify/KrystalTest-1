<?php

App::uses('ExceptionRenderer', 'Error');
class BeforeValidException extends ExceptionRenderer {
    function __construct($text) {
      echo __FILE__ . " " . __LINE__ . " This is the constructor for BeforeValidException<br>";  
      echo "text1222 = $text";
      $this->BeforeValid($text);
      echo __FILE__ . " " . __LINE__ . "<br>";

    }
    
    public function BeforeValid($error) {   
        echo __FILE__ . " " . __LINE__ . "<br>"; 

    }

    
}