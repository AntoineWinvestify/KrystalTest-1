<?php
/*
// +-----------------------------------------------------------------------+
// | Copyright (C) 2014, http://beyond-language-skills.com                 |
// +-----------------------------------------------------------------------+
// | This file is free software; you can redistribute it and/or modify     |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation; either version 2 of the License, or     |
// | (at your option) any later version.                                   |
// | This file is distributed in the hope that it will be useful           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
// | GNU General Public License for more details.                          |
// +-----------------------------------------------------------------------+
// | Author: Antoine de Poorter                                            |
// +-----------------------------------------------------------------------+
//

/*
/**
 *
 *
 * Public form for retrieving data from the students database
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2014-09-25
 * @package



2014-09-25	  version 2014_0.1
very basic version without any fancy formatting

2014-09-29	  version 2014_0.2
Added a number of missing fields and improved layout 



Pending:

Avoid double submit:

<input type="submit" value = "Submit" id ="mybutton" />
form action ="xxx.php" method = POST
onsubmit="document.getElementById('myButton')disabled=true;
getElementById('mybutton').value="Submitting, please wait...";"

*/
?>
  <script>
  $(function() {
    function log( message ) {
      $( "<div>" ).text( message ).prependTo( "#log" );
      $( "#log" ).scrollTop( 0 );
    }
 
    $( "#birds" ).autocomplete({
      source: "/tests/searchbird",
      minLength: 1,
      select: function( event, ui ) {
        log( ui.item ?
          "Selected: " + ui.item.value + " aka " + ui.item.id:
          "Nothing selected, input was " + this.value );
      }
    });
  });
  </script>
</head>
<body>
 
<div class="ui-widget">
  <label for="birds">Birds: </label>
  <input id="birds">
</div>
 
<div class="ui-widget" style="margin-top:2em; font-family:Arial">
  Result:
  <div id="log" style="height: 200px; width: 300px; overflow: auto;" class="ui-widget-content"></div>
</div>