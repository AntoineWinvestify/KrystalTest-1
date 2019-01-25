<?php

App::uses('ExceptionRenderer', 'Error');
class ExpiredException extends ExceptionRenderer {
    function __construct($text) {
      echo __FILE__ . " " . __LINE__ . " This is the constructor for BeforeValidException<br>";  
      echo "text133 = $text";
      $this->Expired($text);
      echo __FILE__ . " " . __LINE__ . "<br>";

    }
    
    public function Expired($error) {   
        echo __FILE__ . " " . __LINE__ . "<br>"; 

    }

    
}