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

<script type="text/javascript">
$(document).ready(function() {

$("#selectSchool").click(function() {
//	alert("KLKL");
$("#testing").val("China");
});

$("#schoolinput").blur(function() {
//alert("I am an alert box!");
$("#testing").val("China");
});

$(".toggle").click(function() {
	if ($(this).next().is(":hidden")) {
	    $(this).next().slideDown("fast");
	    }else{
	    $(this).next().hide("fast");
    }
   
});


// Add a new option to a select
$('.addsingle').on('click',function(){
	var text_to_add = $('.single_add_text').val();
   var temp = $(this).val();
   alert(temp);
  if($.trim(text_to_add) == "") return;
    $('#languages1').append(
      "<option value='"+text_to_add+"'>"+text_to_add+"</option>"
    );
  });


$("#list-select-school").click(function(){
	var list_target_id = 'list-select-school'; 			//first select list ID
	var list_select_id = 'selectCheckboxGroups'; 		//second check list ID
	var text_to_add = $('.single_add_text').val();
	var output1;
   var temp = $(this).val();
 //  alert(temp);
	url_string = "/schools/getListGroups/" +temp;
		$.ajax({
			type: "POST",
//			dataType: "json",
			url: url_string,
			data: {
				text_to_add,
				},
			success:function(output){
//			$('#'+list_select_id).html(output1);

				output1 = $.trim(output);
	
				alert(output1);

				var obj = jQuery.parseJSON(output1);
alert(obj);

alert("TETE");		
html: items.join( "" )

}).insertAfter('#addGroups');			
			 }
		}
);

});




$("#addGroups").click(function() {
var count;

var check_value = new Array()
check_value[0] = "value1";
check_value[1] = "value2";
check_value[2] = "value3";
check_value[3] = "value4";
var obj = jQuery.parseJSON('{"1":"Finana_INFANTIL_1","2":"Finana_B2","3":"Finana_ELEMENTARY","4":"Finana_STARTER","5":"Finana_B1","6":"Finana_INFANTIL_2"}');
	
alert(obj.toString());
	var id;
	var checkbox1;	
	var label;

	for(count in check_value)
	{
		box_id = '"data[SelectedGroup][group_' + id + ']"';
		lbl = 'data[SelectedGroup][group_' + id + ']"';	
					
		checkbox1 = "<input id = " + box_id + ' type="checkbox" class="css-checkbox" value= "' + id + '" name = ' + box_id + '>';	
		label = '<label class="css-label" name= ' + box_id   + ' for="' + lbl +  '>' + check_value[count] + " </label>";

		$(checkbox1 + label + "<br>").insertAfter('#addGroups');
		id = id + 1;
	}

});

});
</script>




<h1 >test template</h1>
    
<?php
	echo $this->Html->css('form_template');
	echo $this->Form->create('Test', array(
											'action' =>'mytest',				
											) );

	echo "<br>";
	echo "Select a school:&nbsp;&nbsp;";				
	$attributes = array('legend' => false, 'id' => 'list-select-school');
	echo $this->Form->select('status', $listing, $attributes);		
	echo $this->Form->button(__('Add'), array('type' => 'button','id' => "addGroups",));
?>

</div>
<br />


<br/><br/>

<?php 	?>

<p>
</p>

<?php
	echo $this->Form->button(__('Confirm'), array("name" => "confirm",
											"type" => "submit",
 											));

	echo $this->Form->end();

?>
