<script type="text/javascript">
            function successLanguage(data) {
                location.reload(true);
                var id = $(".flagvalue").attr("id");
                $(".schemaImg").attr("src", "/img/landingpage/schema_" + id + ".png");
            }

            function sendLocationDataSuccess(data) {
            }

            function sendLocationDataError(data) {
            }
            
            $(document).ready(function () {
                $.getJSON('https://freegeoip.net/json/?callback=?', function (data) {		// 10.000 /hour, only IP
                    var link = "/marketplaces/location";
                    console.log(JSON.stringify(data, null, 2));
                    getServerData(link, data, sendLocationDataSuccess, sendLocationDataError);
                    console.log("Send location Data to server");
                });

                $(".flag-language").on("click", function () {
                    var id = $(this).attr("id");
                    var link = $(this).attr("href");
                    var params = {id: id};
                    var data = jQuery.param(params);
                    getServerData(link, data, successLanguage, successLanguage);
                    return false;
                });

                $("#loginBtn").bind("click", function (event) {
                    if (app.visual.checkFormLogin() === true) {				// continue with default action
                        return true;
                    }
                    //console.log("Error detected in input parameters of login function");
                    event.stopPropagation();
                    event.preventDefault();
                    return false;
                });
                //dismiss popup
                $(document).on("click", ".closePopUp", function () {
                    $("#popUp").css("display", "none");
                });
                //fadeout popup
                fadeOutElement("#popUp", 15000);

                //disable clicking on #loginDropdown
                $("#loginDropdown").on("click", function(event) {
                    event.preventDefault;
                    event.stopPropagation();
                });
                
                
                
                //navbar collapse on clicking outside navbar
                $(document).on("click", function(){
                    $('.navbar-collapse').collapse('hide');
                    //$('#loginDropdown').hide(); //hide loginDropdown
                });

                //Dropdown menu click
                $("ul.nav li.dropdown").on("click", function() {
                    if ($(window).width() > 1023) {
                        $(this).find('.dropdown-menu').stop(true, true).fadeToggle(400);
                    }
                });
                
                $("#liLogin").click(function() {
                    if ($(window).width() < 1025) {
                        //Dropdown menu click
                        if ($('#principal_navbar').is(":visible")) {
                            $('#principal_navbar').collapse('hide');
                        }
                    }
                });
                
                //Initial schemaImg
                var id = $(".flagvalue").attr("id");
                $(".schemaImg").attr("src", "/img/landingpage/schema_" + id + ".png");
            });

        </script>
