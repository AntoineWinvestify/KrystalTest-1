<?php
/**
* +-----------------------------------------------------------------------+
* | Copyright (C) 2016, http://beyond-language-skills.com                 |
* +-----------------------------------------------------------------------+
* | This file is free software; you can redistribute it and/or modify     |
* | it under the terms of the GNU General Public License as published by  |
* | the Free Software Foundation; either version 2 of the License, or     |
* | (at your option) any later version.                                   |
* | This file is distributed in the hope that it will be useful           |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
* | GNU General Public License for more details.                          |
* +-----------------------------------------------------------------------+
* | Author: Antoine de Poorter                                            |
* +-----------------------------------------------------------------------+
*
*
* @author Antoine de Poorter
* @version 0.1
* @date 2016-09-30
* @package
*

Panel which shows all linked accounts and for adding a new one or deleting and
existing linked acount

2016-09-30		version 0.1
multi-language support added

Added global AJAX service	(later to be put in global main.js								[Not yet tested]




Pending:
Reuse!!








*/
?>


<script>
$(document).ready(function(){
 //   $('[data-toggle="tooltip"]').tooltip();  
});
</script>

<script>
	
//	methodWS: format controller/method
//	data:		serialized data
//	success:	function callback for success
//	error:		function callback for error
//	
function getServerData(methodWS, data, success, error) {
        console.log("**AJAX** " + methodWS + " **", data);
        $.ajax({
            type: 'POST',
 //           dataType: "json",
            data:	data,
            url: 	methodWS,
            success: function (data) {	
				data1 = data.substr(1, data.length - 1).trim();	
				data = data.trim();				
				if (data.startsWith("1")) {
                    if (!!success) {
			console.log("SUCCESS RETURNED FROM AJAX Operation");
                        success(data1);      
                    }					
				}
				else {
                    if (!!error) {
			console.log("ERROR from AJAX operation");
                    }					
//					$('.editDatosPersonales').replaceWith(errorData);
				}			
				
            },
            error: function (e) {
                app.utils.trace("error");
				//	   $('#modalKO').modal('show');		// Error Modal
                //if (methodWS == "recuperarPassword") {
                //    $("#bloqueAvisoOlvidar p").html(TEXTOS.T3);
                //    $("#bloqueAvisoOlvidar").fadeIn();
                //} else {
                //    app.utils.tratarError(TEXTOS.T34, app.ajax.logOut);
                //}
            }
		});
    }






function getChatListSuccess(data)
{
	console.log("successAdd function is called");
	$(".mostActiveChatsList").empty();
	$('.mostActiveChatsList').html(data).show();
	
	console.log("List of most active chats was successfully updated");	
}


// get the total thread of a chat
function chatThreadSuccess(data) {
	console.log("successAdd function is called");
	$(".linkedAccountsList").empty();
	$('.linkedAccountsList').html(data).show();
	$('.addLinkedAccount').addClass("hide");
	
	console.log("Complete thread was successfully downloaded");	
}




	
$(document).ready(function() {	

$("#addNewAccount").on("click", function(event) {
	console.log("addNewAccount, show new panel");
	$('.addLinkedAccount').removeClass('hide');
	console.log("addNewAccount,new panel");	
	event.stopPropagation();
	event.preventDefault();	
});






$(document).on("click", ".myChats",function(event) {
	console.log("refresh most active chats list");
	var link = $(this).attr( "href" );
	var index =  $(this).val();  
//	var companyName = $("#linkedaccountCompanyName-" + index).val();
//	console.log("index = " + link + " companyName = " + companyName);
//	var userName = $('#linkedaccountUsername-' + index).val();
//	console.log("index = " + index + " companyName = " + companyName + " username = " + userName);	
	console.log("WSMethod = " + link);
//	var params = { companyName:companyName, userName:userName };
//	var data = jQuery.param( params );

	event.stopPropagation();
	event.preventDefault();
// Call Error checking for this method	
//	getServerData(link, data, successDelete, errorDelete);
});






$(document).on("click", ".deleteLinkedAccount",function(event) {
	console.log("Delete existing account");
	var link = $(this).attr( "href" );
	var index =  $(this).val();  
	var companyName = $("#linkedaccountCompanyName-" + index).val();
	console.log("index = " + index + " companyName = " + companyName);
	var userName = $('#linkedaccountUsername-' + index).val();
	console.log("index = " + index + " companyName = " + companyName + " username = " + userName);	
	console.log("WSMethod = " + link);
	var params = { companyName:companyName, userName:userName };
	var data = jQuery.param( params );

	event.stopPropagation();
	event.preventDefault();
// Call Error checking for this method	
	getServerData(link, data, successDelete, errorDelete);
});



});
</script>






<style>
/* AJAX spinner are at  http://cssload.net/	*/
	
.investmenttable td span{
	text-align: left;	
	color:blue;
	font-size:17px;

}



.cssload-loader-walk {
	width: 42px;
	height: 17px;
	position: absolute;
	left: 50%;
	transform: translate(-50%, -50%);
		-o-transform: translate(-50%, -50%);
		-ms-transform: translate(-50%, -50%);
		-webkit-transform: translate(-50%, -50%);
		-moz-transform: translate(-50%, -50%);
}
.cssload-loader-walk > div {
	content: "";
	width: 8px;
	height: 8px;
	background: rgb(33,150,243);
	border-radius: 100%;
	position: absolute;
	animation: cssload-animate 2.3s linear infinite;
		-o-animation: cssload-animate 2.3s linear infinite;
		-ms-animation: cssload-animate 2.3s linear infinite;
		-webkit-animation: cssload-animate 2.3s linear infinite;
		-moz-animation: cssload-animate 2.3s linear infinite;
}
.cssload-loader-walk > div:nth-of-type(1) {
	animation-delay: -0.46s;
		-o-animation-delay: -0.46s;
		-ms-animation-delay: -0.46s;
		-webkit-animation-delay: -0.46s;
		-moz-animation-delay: -0.46s;
}
.cssload-loader-walk > div:nth-of-type(2) {
	animation-delay: -0.92s;
		-o-animation-delay: -0.92s;
		-ms-animation-delay: -0.92s;
		-webkit-animation-delay: -0.92s;
		-moz-animation-delay: -0.92s;
}
.cssload-loader-walk > div:nth-of-type(3) {
	animation-delay: -1.38s;
		-o-animation-delay: -1.38s;
		-ms-animation-delay: -1.38s;
		-webkit-animation-delay: -1.38s;
		-moz-animation-delay: -1.38s;
}
.cssload-loader-walk > div:nth-of-type(4) {
	animation-delay: -1.84s;
		-o-animation-delay: -1.84s;
		-ms-animation-delay: -1.84s;
		-webkit-animation-delay: -1.84s;
		-moz-animation-delay: -1.84s;
}



@keyframes cssload-animate {
	0% {
		left: 42px;
		top: 0;
	}
	80% {
		left: 0;
		top: 0;
	}
	85% {
		left: 0;
		top: -8px;
		width: 8px;
		height: 8px;
	}
	90% {
		width: 17px;
		height: 6px;
	}
	95% {
		left: 42px;
		top: -8px;
		width: 8px;
		height: 8px;
	}
	100% {
		left: 42px;
		top: 0;
	}
}

@-o-keyframes cssload-animate {
	0% {
		left: 42px;
		top: 0;
	}
	80% {
		left: 0;
		top: 0;
	}
	85% {
		left: 0;
		top: -8px;
		width: 8px;
		height: 8px;
	}
	90% {
		width: 17px;
		height: 6px;
	}
	95% {
		left: 42px;
		top: -8px;
		width: 8px;
		height: 8px;
	}
	100% {
		left: 42px;
		top: 0;
	}
}

@-ms-keyframes cssload-animate {
	0% {
		left: 42px;
		top: 0;
	}
	80% {
		left: 0;
		top: 0;
	}
	85% {
		left: 0;
		top: -8px;
		width: 8px;
		height: 8px;
	}
	90% {
		width: 17px;
		height: 6px;
	}
	95% {
		left: 42px;
		top: -8px;
		width: 8px;
		height: 8px;
	}
	100% {
		left: 42px;
		top: 0;
	}
}

@-webkit-keyframes cssload-animate {
	0% {
		left: 42px;
		top: 0;
	}
	80% {
		left: 0;
		top: 0;
	}
	85% {
		left: 0;
		top: -8px;
		width: 8px;
		height: 8px;
	}
	90% {
		width: 17px;
		height: 6px;
	}
	95% {
		left: 42px;
		top: -8px;
		width: 8px;
		height: 8px;
	}
	100% {
		left: 42px;
		top: 0;
	}
}

@-moz-keyframes cssload-animate {
	0% {
		left: 42px;
		top: 0;
	}
	80% {
		left: 0;
		top: 0;
	}
	85% {
		left: 0;
		top: -8px;
		width: 8px;
		height: 8px;
	}
	90% {
		width: 17px;
		height: 6px;
	}
	95% {
		left: 42px;
		top: -8px;
		width: 8px;
		height: 8px;
	}
	100% {
		left: 42px;
		top: 0;
	}
}
.cssload-squeeze{
	position:relative;
	display:block;
	margin: 97px auto;
	width:107px;
}
	
.cssload-squeeze, .cssload-squeeze * {
	box-sizing: border-box;
		-o-box-sizing: border-box;
		-ms-box-sizing: border-box;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
}

.cssload-squeeze span {
	display: inline-block;
	height: 15px;
	width: 15px;
	background: rgb(47,172,155);
	border-radius: 0px;
}

.cssload-squeeze span:nth-child(1) {
	animation: cssload-rotateX 2.3s 0.12s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-o-animation: cssload-rotateX 2.3s 0.12s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-ms-animation: cssload-rotateX 2.3s 0.12s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-webkit-animation: cssload-rotateX 2.3s 0.12s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-moz-animation: cssload-rotateX 2.3s 0.12s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
}
.cssload-squeeze span:nth-child(2) {
	animation: cssload-rotateX 2.3s 0.23s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-o-animation: cssload-rotateX 2.3s 0.23s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-ms-animation: cssload-rotateX 2.3s 0.23s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-webkit-animation: cssload-rotateX 2.3s 0.23s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-moz-animation: cssload-rotateX 2.3s 0.23s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
}
.cssload-squeeze span:nth-child(3) {
	animation: cssload-rotateX 2.3s 0.35s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-o-animation: cssload-rotateX 2.3s 0.35s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-ms-animation: cssload-rotateX 2.3s 0.35s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-webkit-animation: cssload-rotateX 2.3s 0.35s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-moz-animation: cssload-rotateX 2.3s 0.35s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
}
.cssload-squeeze span:nth-child(4) {
	animation: cssload-rotateX 2.3s 0.46s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-o-animation: cssload-rotateX 2.3s 0.46s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-ms-animation: cssload-rotateX 2.3s 0.46s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-webkit-animation: cssload-rotateX 2.3s 0.46s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-moz-animation: cssload-rotateX 2.3s 0.46s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
}
.cssload-squeeze span:nth-child(5) {
	animation: cssload-rotateX 2.3s 0.58s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-o-animation: cssload-rotateX 2.3s 0.58s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-ms-animation: cssload-rotateX 2.3s 0.58s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-webkit-animation: cssload-rotateX 2.3s 0.58s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
		-moz-animation: cssload-rotateX 2.3s 0.58s infinite cubic-bezier(0.65, 0.03, 0.735, 0.045);
}



@keyframes cssload-rotateX {
	0% {
		transform: rotateX(0deg);
	}
	50% {
		transform: rotateX(90deg) scale(0.5, 0.5);
		background: rgb(147,225,215);
	}
	100% {
		transform: rotateX(0deg);
						transform: rotateX(0deg);
	}
}

@-o-keyframes cssload-rotateX {
	0% {
		-o-transform: rotateX(0deg);
	}
	50% {
		-o-transform: rotateX(90deg) scale(0.5, 0.5);
		background: rgb(147,225,215);
	}
	100% {
		-o-transform: rotateX(0deg);
						transform: rotateX(0deg);
	}
}

@-ms-keyframes cssload-rotateX {
	0% {
		-ms-transform: rotateX(0deg);
	}
	50% {
		-ms-transform: rotateX(90deg) scale(0.5, 0.5);
		background: rgb(147,225,215);
	}
	100% {
		-ms-transform: rotateX(0deg);
						transform: rotateX(0deg);
	}
}

@-webkit-keyframes cssload-rotateX {
	0% {
		-webkit-transform: rotateX(0deg);
	}
	50% {
		-webkit-transform: rotateX(90deg) scale(0.5, 0.5);
		background: rgb(147,225,215);
	}
	100% {
		-webkit-transform: rotateX(0deg);
						transform: rotateX(0deg);
	}
}

@-moz-keyframes cssload-rotateX {
	0% {
		-moz-transform: rotateX(0deg);
	}
	50% {
		-moz-transform: rotateX(90deg) scale(0.5, 0.5);
		background: rgb(147,225,215);
	}
	100% {
		-moz-transform: rotateX(0deg);
						transform: rotateX(0deg);
	}
}
</style>






<?php
/*


AJAX Calls:


createNewChat (companyId, investorId, )
	newChatCreated














*/
echo "<h3>" . __("Chats") . "</h3>";

?>

<hr>



	<div class="container">
		<div class="row"> <!-- top buttons -->
			<div class="panel">
				<div class="topButtonLayout">
					<input class="btn btn-default searchChat" type="button" value="<?php echo __('Search for Chat') ?>">
					<input class="btn btn-default newChat" type="button" value="<?php echo __('Create New Chat') ?>">
					<input class="btn btn-default myChats" href="getActiveChatsList" type="button" value="<?php echo __('my Chats') ?>">
				</div>
			</div>
		</div>
		
		
		<div class="row"> <!-- most active chats -->
			<div class="panel">
				<?php echo "<h4>" . __('Most Active Chats') . "</h4>"  ?>
				<div class="mostActiveChatsList">
				<!-- contents will be provided via AJAX call during loading of page -->	
					<div class="row">
						<div class="col-xs-3 col-lg-1 col-md-1 text-center">	
							<?php echo "Zank"?>
						</div>
						<div class="col-xs-3 col-lg-2 col-md-3">	
							<?php echo "I need help please"?>
						</div>					
						<div class="col-xs-3 col-lg-4 col-md-6">	
							<?php echo "Can somebody tell me where I can get the ....." ?>
						</div>
						<div class="col-xs-3 col-lg-1 col-md-1 text-center ">							
							<button type="button" id="linkNewAccount" href="/investors/linkAccount" class="form submitButton">Read</button>
						</div>
					</div>
					
					<div class="row">
						<div class="col-xs-3 col-lg-1 col-md-1 text-center">	
							<?php echo "Zank"?>
						</div>
						<div class="col-xs-3 col-lg-2 col-md-3">	
							<?php echo "I need help please 7"?>
						</div>					
						<div class="col-xs-3 col-lg-4 col-md-6">	
							<?php echo "This is a rude question but can somebody tell me where I can get the ....." ?>
						</div>
						<div class="col-xs-3 col-lg-1 col-md-1 text-center ">							
							<button type="button" id="linkNewAccount" href="/investors/linkAccount" class="form submitButton">Read</button>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-3 col-lg-1 col-md-1 text-center">	
							<?php echo "Zank"?>
						</div>
						<div class="col-xs-3 col-lg-2 col-md-3">	
							<?php echo "Is there somene overthere?"?>
						</div>					
						<div class="col-xs-3 col-lg-4 col-md-6">	
							<?php echo "It seems I am alone overhere Can somebody tell me where I can get the ....." ?>
						</div>
						<div class="col-xs-3 col-lg-1 col-md-1 text-center">							
							<button type="button" id="linkNewAccount" href="/investors/linkAccount" class="form submitButton">Read</button>
						</div>
					</div>
					
					
<div class="row">
	<div class="col-lg-7 col-md-6 ">
		
	<div class="bs-example">
	    <ul class="media-list">
	        <li class="media">
	            <div class="media-left">
	                <a href="#">
	                    <img src="../img/avatarDefectoNorm.jpg" class="media-object" alt="Sample Image">
	                </a>
	            </div>
	            <div class="media-body">
	                <h4 class="media-heading">I need help please 7</h4>
	                <p>This is a rude question but can somebody tell me where I can get the latest information regarding the status
			of this loan? It turns out that Zank is withholding some money and they don't provide a good explanation.</p>
	                <!-- Nested media object -->
	                <div class="media">
	                    <div class="media-left">
	                        <a href="#">
	                           <img src="../img/noImages.jpg" class="media-object img-rounded" alt="Sample Image1">
	                        </a>
	                    </div>
	                    <div class="media-body">
	                        <h4 class="media-heading">Nested Media Heading</h4>
	                        <p>Vestibulum quam ut magdunt. Ut tempus dictum risus. Pellentesque viverra sagittis quam at mattis.</p>
	                        <!-- Nested media object -->
	                        <div class="media">
	                            <div class="media-left">
	                                <a href="#">
	                                    <img src="../img/noImages.jpg" class="media-object" alt="Sample Image2">
	                                </a>
	                            </div>
	                            <div class="media-body">
	                                <h4 class="media-heading">Nested Media Heading</h4>
	                                <p>Amet nibh  Nulla vel metus scelerisque ante sollicitudin commodo. Nulla vel metus scelerisque ante sollicitudin commodo. , in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis.</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	                <!-- Nested media object -->
	                <div class="media">
	                    <div class="media-left">
	                        <a href="#">
	                            <img src="../img/noImages.jpg" class="media-object" alt="Sample Image3">
	                        </a>
	                    </div>
	                    <div class="media-body">
	                        <h4 class="media-heading">I need help please 7</h4>
	                        <p>Read the bloody manual</p>
	                    </div>
	                </div>
	            </div>
	        </li>
	        <li class="media">
	            <div class="media-left">
	                <a href="#">
	                    <img src="../img/noImages.jpg" class="media-object" alt="Sample Image4">
	                </a>
	            </div>
	            <div class="media-body">
	                <h4 class="media-heading">Media Heading</h4>
	                <p>Quisque pharetra velit id velit iaculis pretium. Nullam a justo sed ligula porta semper eu quis enim. Pellentesque pellentesque, metus at facilisis hendrerit, lectus velit.</p>
	            </div>
	        </li>
	    </ul>
	</div>
	</div>
</div>
					
				</div>
			</div>
		</div>

<hr>

		<div class="row">	<!-- search for existing chat -->
			<div class="panel">
				<?php echo "<h4>" . __('Search for existing Chats') . "</h4>"  ?>
					<div class="col-xs-3 col-lg-3 col-md-4 text-center">
						<div class="text-center ">
						<label>
							<strong> <?php echo __("Company") ?> </strong>
						</label>
<?php      
							echo $this->Form->input('', array(
										    'options' 	=> $companyList,
										    'empty' 	=> '(choose one)',
										    'id'   	=> 'linkedaccount_companyId'
										));
?>
					</div>
						</div>
					<div class="col-xs-3 col-lg-3 col-md-4 text-center">
						<label>
							<strong> <?php echo __("Loan Identifier") ?> </strong>
						</label>
						
<?php
							$class = " " ;
	echo $this->Form->input('Linkedaccount.linkedaccount_username', array(
											'id' 	=> 'ContentPlaceHolder_userName',
											'label' => false,
											'class' => $class,
											));				
?>						
					</div>				
			</div>
		</div>
		

<hr>		
		
		<div class="row">	<!-- new chat -->
			<div class="panel">
				
					<div data-toggle="tooltip" data-placement="right" title="A new chat must include loan reference">
					<?php echo "<h4>" . __('New Chat') . "</h4>"  ?>
					<div class="col-xs-12 col-lg-2 col-md-3 text-center">
						<label>
							<strong> <?php echo __("Company") ?> </strong>
						</label>
<?php      
						echo $this->Form->input('', array(
										    'options' 	=> $companyList,
										    'empty' 	=> '(choose one)',
										    'id'   	=> 'linkedaccount_companyId'
										));
?>
					</div>
				</div>	
				<div class="col-xs-3 col-lg-2 col-md-3 text-center">
						<label>
							<strong> <?php echo __("Loan Identifier") ?> </strong>
						</label>
<?php						
							$class = " " ;
						echo $this->Form->input('Linkedaccount.linkedaccount_username', array(
											'id' 	=> 'ContentPlaceHolder_userName',
											'label' => false,
											'class' => $class,
											));				
?>
				</div>	
				<div class="col-xs-12 col-lg-6 col-md-6 text-center">
						<label>
							<strong> <?php echo __("Subject") ?> </strong>
						</label>
<?php						
							$class = " " ;
						echo $this->Form->input('Linkedaccount.linkedaccount_username', array(
											'id' 	=> 'ContentPlaceHolder_userName',
											'label' => false,
											'class' => $class,
											));				
?>				
					<button type="button" id="linkNewAccount" href="/investors/linkAccount" class="form submitButton">Create</button>		
				</div>
			</div>
		</div>		
	</div>
<div class="cssload-squeeze">
	<span></span><span></span><span></span><span></span><span></span>
</div>
<hr>
<?php
/*

extra snippets usefull for future design
http://www.tutorialrepublic.com/codelab.php?topic=bootstrap&file=media-list
    <div class="media">
        <div class="media-left">
            <a href="#">
              <img src="../images/avatar-tiny.jpg" class="media-object" alt="Sample Image">
            </a>
        </div>
        <div class="media-body">
            <h4 class="media-heading">Jhon Carter <small><i>Posted on January 10, 2014</i></small></h4>
            <p>Excellent feature! I love it. One day I'm definitely going to put this Bootstrap component into use and I'll let you know once I do.</p>
        </div>
    </div>
    <hr />



<div class="bs-example">
    <ul class="media-list">
        <li class="media">
            <div class="media-left">
                <a href="#">
                    <img src="../images/avatar-tiny.jpg" class="media-object" alt="Sample Image">
                </a>
            </div>
            <div class="media-body">
                <h4 class="media-heading">Media Heading</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam eu sem tempor, varius quam at, luctus dui. Mauris magna metus, dapibus nec turpis vel, semper malesuada ante.</p>
                <!-- Nested media object -->
                <div class="media">
                    <div class="media-left">
                        <a href="#">
                            <img src="../images/avatar-tiny.jpg" class="media-object" alt="Sample Image">
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">Nested Media Heading</h4>
                        <p>Vestibulum quis quam ut magna consequat faucibus. Pellentesque eget nisi a mi suscipit tincidunt. Ut tempus dictum risus. Pellentesque viverra sagittis quam at mattis.</p>
                        <!-- Nested media object -->
                        <div class="media">
                            <div class="media-left">
                                <a href="#">
                                    <img src="../images/avatar-tiny.jpg" class="media-object" alt="Sample Image">
                                </a>
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">Nested Media Heading</h4>
                                <p>Amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Nested media object -->
                <div class="media">
                    <div class="media-left">
                        <a href="#">
                            <img src="../images/avatar-tiny.jpg" class="media-object" alt="Sample Image">
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">Nested Media Heading</h4>
                        <p>Phasellus vitae suscipit justo. Mauris pharetra feugiat ante id lacinia. Etiam faucibus mauris id tempor egestas. Duis luctus turpis at accumsan tincidunt.</p>
                    </div>
                </div>
            </div>
        </li>
        <li class="media">
            <div class="media-left">
                <a href="#">
                    <img src="../images/avatar-tiny.jpg" class="media-object" alt="Sample Image">
                </a>
            </div>
            <div class="media-body">
                <h4 class="media-heading">Media Heading</h4>
                <p>Quisque pharetra velit id velit iaculis pretium. Nullam a justo sed ligula porta semper eu quis enim. Pellentesque pellentesque, metus at facilisis hendrerit, lectus velit.</p>
            </div>
        </li>
    </ul>
</div>













*/
?>