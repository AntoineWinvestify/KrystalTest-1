<?php
/* 
 * Copyright (C) 2019 Winvestify S.L.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

App::uses('ExceptionRenderer', 'Error');
class AppExceptionRenderer extends ExceptionRenderer {
   function __construct($text) {
      echo __FILE__ . " " . __LINE__ . " This is the constructorA<br>";  
      echo "text = $text";
}    
    public function missingWidget($error) {   
        echo __FILE__ . " " . __LINE__ . "<br>"; 
    //    exit;
    }
/*
}
class MissingWidgetException extends CakeException { 
        function __construct() {
      echo __FILE__ . " " . __LINE__ . " This is the constructorB<br>";   

    echo __FILE__ . " " . __LINE__ . "<br>"; 
   } 
};

class MissingWidgetException1 extends ExceptionRenderer {
    function __construct() {
      echo __FILE__ . " " . __LINE__ . " This is the constructorC<br>";   
}*/
 /*   
    public function MissingWidgetException($error) {
echo __FILE__ . " " . __LINE__ . "<br>"; 
        echo 'Oops that widget is missing!';
exit;
    

   

    echo __FILE__ . " " . __LINE__ . "<br>"; 
        echo 'Oops that widget is missing!';
        
exit;
 }
}*/


}

