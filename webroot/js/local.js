
var app = app || {};
//definition of various namespaces
app.main = {};
app.navegation = {};
app.utils = {};
app.ajax = {};
app.visual = {};
//---//	 	



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
        data: data,
        url: methodWS,
        complete: ajaxComplete,
        beforeSend: ajaxSend,
        success: function (data) {
            data = data.trim();
            data1 = data.substr(1, data.length - 1);
            if (data.startsWith("1")) {
                if (!!success) {
                    console.log("APPLICATION SUCCESS RETURNED FROM AJAX Operation");
                    success(data1);
                }
            } else {
                if (!!error) {
                    console.log("APPLICATION ERROR from AJAX operation");
                    error(data1);
                }
            }
        },
        error: function (e) {
            //            app.utils.trace("error");	DEAL WITH IT LATER
        }
    });
}





/**Elements to Fade Out
*
* @param {String} element - element to fadeout (class/id)
* @param {Number} time - time to fadeout (milliseconds)
*/
function fadeOutElement($element, $time) {
    setTimeout(function () {
        $($element).each(function (index) {
            $(this).delay(200 * index).fadeTo(1500, 0).slideUp(500, function () {
                $(this).remove();
            });
        });
    }, $time);
}
;





/** Elements to Fade In
 *
 * @param {String} element - Element to fadein (class/id)
 * @param {Number} time - Time to fadein (milliseconds)
 */
function fadeInElement($element, $time) {
    setTimeout(function () {
        $($element).fadeIn(1500);
    }, $time);
}
;






function ajaxComplete(event, request, settings) {
    console.log("STOP the ajax spinner");
    $('.overlay').hide();
}


function ajaxSend(event, request, settings) {
    console.log("START the ajax spinner");
    $('.overlay').show();
}

function contactForm() {

    var params = {
        name: $("#ContactFormName").val(),
        email: $("#ContactFormEmail").val(),
        subjectval: $("#ContactFormSubject option:selected").val(),
        subjecttext: $("#ContactFormSubject option:selected").text(),
        text: $("#ContactFormText").val(),
        result: $("#ContactFormResult").val(),
        captcha: $("#ContactFormCaptcha").val()
    };
    link = $("#send").attr('href');
    $("#send").prop('disabled', true);
    $("#overlay").addClass("overlay");
    $("#spinner").addClass("fa fa-spin fa-spinner");
    //event.stopPropagation();
    //event.preventDefault();



    var validation = contactFormValidation(params);
    if (validation.constructor === Array) {
        if (validation[0]) {
            $(".errorName").css('display', 'block');
            $("#ContactFormName").addClass('redBorder');
        } else {
            $("#ContactFormName").removeClass('redBorder');
            $(".errorName").css('display', 'none');
        }
        if (validation[1]) {
            $(".errorEmail").css('display', 'block');
            $("#ContactFormEmail").addClass('redBorder');
        } else {
            $("#ContactFormEmail").removeClass('redBorder');
            $(".errorEmail").css('display', 'none');
        }
        if (validation[2]) {
            $(".errorSubject").css('display', 'block');
            $("#ContactFormSubject").addClass('redBorder');
        } else {
            $("#ContactFormSubject").removeClass('redBorder');
            $(".errorSubject").css('display', 'none');
        }
        if (validation[3]) {
            $(".errorMessage").css('display', 'block');
            $("#ContactFormText").addClass('redBorder');
        } else {
            $("#ContactFormText").removeClass('redBorder');
            $(".errorMessage").css('display', 'none');
        }
        if (validation[4]) {
            $(".errorCaptcha").css('display', 'block');
            $("#ContactFormCaptcha").addClass('redBorder');
        } else {
            $(".errorCaptcha").css('display', 'none');
            $("#ContactFormCaptcha").removeClass('redBorder');
        }
        $("#send").prop('disabled', false);
        $("#errorReporting").html("Form error:<br>");
    } else {

        $("#ContactFormName").removeClass('redBorder');
        $(".errorName").css('display', 'none');
        $("#ContactFormEmail").removeClass('redBorder');
        $(".errorEmail").css('display', 'none');
        $("#ContactFormSubject").removeClass('redBorder');
        $(".errorSubject").css('display', 'none');
        $("#ContactFormText").removeClass('redBorder');
        $(".errorMessage").css('display', 'none');
        $(".errorCaptcha").css('display', 'none');
        $("#ContactFormCaptcha").removeClass('redBorder');
        var data = jQuery.param(params);
        getServerData(link, data, contactFormSuccess,contactFormError);
    }


}

function contactFormSuccess(data) {
     $("#overlay").removeClass("overlay");
     $("#spinner").removeClass("fa fa-spin fa-refresh");
    if (data.includes('error envio') || data.includes('rror envio')) {
        alert('error al enviar email');
        $("#send").prop('disabled', false);
        
    } else {
        data = JSON.parse(data);
        //Captcha Error
        if (data[0] == 2) {
            $(".errorCaptcha").css('display', 'block');
            $("#ContactFormCaptcha").addClass('redBorder');
            $("#send").prop('disabled', false);
        } else {
            $(".errorCaptcha").css('display', 'none');
            $("#ContactFormCaptcha").removeClass('redBorder');
        }
        //Correct Sending
        if (data[0] == 1) {
            $("#ContactFormFormForm").trigger("reset");
            $("#send").prop('disabled', false);
            console.log("antoine" + data[1]);
            $("#reporting").html(data[1]);
            console.log("OK");
        } 
        //Fields errors
        else if (data[0] == 0) {
            $("#send").prop('disabled', false);
            if (data[1][0]['name']) {
                $(".errorName").css('display', 'block');
                $("#ContactFormName").addClass('redBorder');
            } else {
                $("#ContactFormName").removeClass('redBorder');
                $(".errorName").css('display', 'none');
            }
            if (data[1][0]['email']) {
                $(".errorEmail").css('display', 'block');
                $("#ContactFormEmail").addClass('redBorder');
            } else {
                $("#ContactFormEmail").removeClass('redBorder');
                $(".errorEmail").css('display', 'none');
            }
            if (data[1][0]['subject']) {
                $(".errorSubject").css('display', 'block');
                $("#ContactFormSubject").addClass('redBorder');
            } else {
                $("#ContactFormSubject").removeClass('redBorder');
                $(".errorSubject").css('display', 'none');
            }
            if (data[1][0]['text']) {
                $(".errorMessage").css('display', 'block');
                ;
                $("#ContactFormText").addClass('redBorder');
            } else {
                $("#ContactFormText").removeClass('redBorder');
                $(".errorMessage").css('display', 'none');
            }
        }
    }
}

function contactFormError(data) {
    $("#reporting").html("<h2 align='center'><?php echo __('Error. Message not sent) ?></h2>");
}

function contactFormValidation(data) {

    var errors = [true, true, true, true, true];
    var email = data['email'];

    if (data['name'].length > 2) {
        errors[0] = false;
    }
    if (app.utils.validEmail(email)) {
        errors[1] = false;
    }
    if (data['subjectval'] != 0) {
        errors[2] = false;
    }
    if (data['text'].length > 9) {
        errors[3] = false;
    }
    if (data['result'] == data['captcha']) {
        errors[4] = false;
    }
    if (errors[0] || errors[1] || errors[2] || errors[3] || errors[4]) {
        $("#overlay").removeClass("overlay");
        $("#spinner").removeClass("fa fa-spin fa-refresh");
        return errors;
    } else {
        return true
    }
}


// checks the input parameters 	
app.visual = {

// Checks if a crowdlending company has been selected, and if username and password are defined
    checkFormAddLinkedAccount: function (username, password, companyId) {
        var correctForm = true;
        $(".errorInputMessage").hide(); // remove all error texts
        $(".addLinkedAccount input, select").removeClass("redBorder"); // remove ALL red borders

        if (username === "") {
            console.log("empty name");
            $(".userName").addClass("redBorder");
            $(".ErrorUserName").find(".errorMessage").html(TEXTOS.T1);
            $(".ErrorUserName").fadeIn();
            correctForm = false;
        }
        if (password === "") {
            console.log("empty password");
            $(".userPassword").addClass("redBorder");
            $(".ErrorPassword").find(".errorMessage").html(TEXTOS.T1);
            $(".ErrorPassword").fadeIn();
            correctForm = false;
        }
        if (companyId === "") {
            console.log("empty company");
            $("#linkedaccount_companyId").addClass("redBorder");
            $(".ErrorPlatform").find(".errorMessage").html(TEXTOS.T19);
            $(".ErrorPlatform").fadeIn();
            correctForm = false;
        }
        return correctForm;
    },
    checkFormLogin: function () {
        var correctForm = true;
        var username = $(".userNameLogin").val();
        var password = $(".passwordLogin").val();
        console.log("checkFormLogin: Entered, user = " + username + " password = " + password);
        $(".errorInputMessage").hide(); // hide all error texts
        $("input").removeClass("redBorder"); // remove ALL redborders

        if (username === "") {
            console.log("empty username");
            $(".userNameLogin").addClass("redBorder");
            $(".ErrorUsername").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorUsername").fadeIn();
            correctForm = false;
        } else {
            if (!app.utils.validEmail(username)) {
                console.log("invalid email detected");
                $(".userNameLogin").addClass("redBorder");
                $(".ErrorUsername").find(".errorMessage").html(TEXTOS.T02); // "email not valid" warning
                $(".ErrorUsername").fadeIn();
                correctForm = false;
            }
        }
        if (password === "") {
            console.log("empty password");
            $(".passwordLogin").addClass("redBorder");
            $(".ErrorPassword").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorPassword").fadeIn();
            correctForm = false;
        }
        return correctForm;
    },
    checkFormRegistrationA: function () {
        var correctForm = true;
        var email = $(".userName").val();
        var password1 = $(".password1").val();
        var password2 = $(".password2").val();
        var telephone = $(".telephoneNumber").val();
        var isCheckedPrivacyPolicy = $("#ContentPlaceHolder_registerPrivacyPolicy").is(':checked');
        $(".errorInputMessage").hide(); // remove all error texts
        $(".editRegistrationData input").removeClass("redBorder"); // remove ALL redborders

        if (email === "") {
            $(".userName").addClass("redBorder");
            $(".ErrorUsername").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorUsername").fadeIn();
            correctForm = false;
        } else {
            if (!app.utils.validEmail(email)) {
                $(".userName").addClass("redBorder");
                $(".ErrorUsername").find(".errorMessage").html(TEXTOS.T02); // "email not valid" warning
                $(".ErrorUsername").fadeIn();
                correctForm = false;
            }
        }

        if (password1 === "" && password2 === "") {
            $(".password1").addClass("redBorder");
            $(".password2").addClass("redBorder");
            $(".ErrorPassword").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorPassword").fadeIn();
            $(".ErrorPasswordConfirm").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorPasswordConfirm").fadeIn();
            $(".ErrorPassword").fadeIn();
            correctForm = false;
        } else if (password1 !== "" && password2 === "") {
            $(".password2").addClass("redBorder");
            $(".ErrorPasswordConfirm").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorPasswordConfirm").fadeIn();
            correctForm = false;
        } else if (password1 === "" && password2 !== "") {
            $(".password1").addClass("redBorder");
            $(".ErrorPassword").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorPassword").fadeIn();
            correctForm = false;
        } else if (password1 !== password2) {
            $(".password1").addClass("redBorder");
            $(".password2").addClass("redBorder");
            $(".ErrorPassword").find(".errorMessage").html(TEXTOS.T03); // Incorrect format of password warning
            $(".ErrorPassword").fadeIn();
            $(".ErrorPasswordConfirm").find(".errorMessage").html(TEXTOS.T03); // Incorrect format of password warning
            $(".ErrorPassword").fadeIn();
            correctForm = false;
        } else if (!app.utils.checkPassword(password2)) {
            $(".password1").addClass("redBorder");
            $(".password2").addClass("redBorder");
            $(".ErrorPassword").find(".errorMessage").html(TEXTOS.T08); // Incorrect format of password warning
            $(".ErrorPassword").fadeIn();
            $(".ErrorPasswordConfirm").find(".errorMessage").html(TEXTOS.T08); // Incorrect format of password warning
            $(".ErrorPassword").fadeIn();
            correctForm = false;
        }
        if (telephone === "") {
            $(".telephoneNumber").addClass("redBorder");
            $(".ErrorPhoneNumber").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorPhoneNumber").fadeIn();
            correctForm = false;
        } else {
            if (!app.utils.checkPhoneNumber(telephone)) {
                $(".investorTelephone").addClass("redBorder");
                $(".ErrorPhoneNumber").find(".errorMessage").html(TEXTOS.T10); // "The telephone number is not valid" warning
                $(".ErrorPhoneNumber").fadeIn();
                correctForm = false;
            }
        }

        if (isCheckedPrivacyPolicy === false) {
            $(".ErrorPrivacyPolicy").find(".errorMessage").html(TEXTOS.T06); // "You should accept our privacy policy" warning
            $(".ErrorPrivacyPolicy").fadeIn();
            correctForm = false;
        }

        if (correctForm === false) {
            $("#ContentPlaceHolder1_btGuardarDatosPersonales").show(); //?
            $(".guardarDatosPersonales").hide(); //?
        }
        return correctForm;
    },
    checkFormRegistrationB: function () {
        var correctForm = true;
        var code = $(".confirmationCode").val();
        $(".errorInputMessage").hide(); // remove all error texts
        $(".editRegistrationData input").removeClass("redBorder"); // remove ALL redborders

        if (code === "") {
            $(".confirmationCode").addClass("redBorder");
            $(".ErrorConfirmationCode").find(".errorMessage").html(TEXTOS.T26);
            $(".ErrorConfirmationCode").fadeIn();
            correctForm = false;
        }
        return correctForm;
    },
// NOT YET USED    
    checkFormRegistrationC: function () {
        var correctForm = true;
        return true;
    },
    checkFormRegistrationD: function () {
        var correctForm = true;
        $(".errorInputMessage").hide(); // remove all error texts
        $(".editRegistrationData input").removeClass("redBorder"); // remove ALL redborders

        var investor,
                p2p,
                p2b,
                invoiceTrading,
                crowdHouser;
        investor = $('input[name="accreditedInvestor"]:checked').val();
        if (typeof investor == 'undefined') {
            console.log("Define type of user");
            $(".ErrorInvestor").find(".errorMessage").html(TEXTOS.T15); // "select 1 option" warning
            $(".ErrorInvestor").fadeIn();
            correctForm = false;
        }



        if ($('#ContentPlaceHolder_P2PInvestment').is(":checked")) {
            p2p = $('#ContentPlaceHolder_P2PInvestment:checkbox:checked').val();
        } else {
            p2p = 0;
        }

        if ($('#ContentPlaceHolder_P2BInvestment').is(":checked")) {
            p2b = $('#ContentPlaceHolder_P2BInvestment:checkbox:checked').val();
        } else {
            p2b = 0;
        }

        if ($('#ContentPlaceHolder_InvoiceTrading').is(":checked")) {
            invoiceTrading = $('#ContentPlaceHolder_InvoiceTrading:checkbox:checked').val();
        } else {
            invoiceTrading = 0;
        }

        if ($('#ContentPlaceHolder_CrowdHouser').is(":checked")) {
            crowdHouser = $('#ContentPlaceHolder_CrowdHouser:checkbox:checked').val();
        } else {
            crowdHouser = 0;
        }

        if ((p2b + p2p + invoiceTrading + crowdHouser) === 0) {
            console.log("Show error text");
            $(".ErrorPlatformSelection").find(".errorMessage").html(TEXTOS.T12); // "Select at least one warning
            $(".ErrorPlatformSelection").fadeIn();
            correctForm = false;
        }
        return correctForm;
    },
    checkFormRegistrationE: function () {
        return correctForm;
    },
    checkFormUserDataModification: function () {
        var correctForm = true;
        console.log("entering checkFormRegistration function");
        $("#ContentPlaceHolder1_btGuardarDatosPersonales").hide(); // CHECK UP
        $(".guardarDatosPersonales").show(); // CHECKUP

        return correctForm; // for testing only
        var password1 = $("#ContentPlaceHolder_password1").val();
        var password2 = $("#ContentPlaceHolder_password_confirm").val();
        var name = $("#ContentPlaceHolder_name").val();
        var surname = $("#ContentPlaceHolder_surname").val();
        var address = $("#ContentPlaceHolder_address1").val();
        var postCode = $("#ContentPlaceHolder_postCode").val();
        var city = $("#ContentPlaceHolder_city").val();
        var country = $("#ContentPlaceHolder_country").val();
        var identificationId = $("#ContentPlaceHolder_dni").val();
        var telephone = $("#ContentPlaceHolder_telephone").val();
        var dateOfBirth = $("#ContentPlaceHolder_dateOfBirth").val();
        if (typeof name != 'undefined') {			// name should be mandatory
            console.log("checking 'name'");
        }
        if (typeof surname != 'undefined') {			// surname should be mandatory
            console.log("checking 'surname'");
        }
        if (typeof address != 'undefined') {		// name contains something
            console.log("checking 'address1'");
        }
        if (typeof postCode != 'undefined') {			// name contains something
            console.log("checking 'postCode'");
        }
        if (typeof city != 'undefined') {			// name contains something
            console.log("checking 'city'");
        }
        if (typeof country == 'undefined') {			// country is mandatory
            console.log("checking 'country'");
        }
        if (typeof identificationId != 'undefined') {		// identificationId *should* contain something
            console.log("checking 'identificationId'");
        }
        if (typeof telephone != 'undefined') {			// telephone is mandatory
            console.log("checking 'telephone'");
        }
        if (typeof dateOfBirth != 'undefined') {		// dateofBirth is mandatory, just
            // to know if investor is > 18 years old
            console.log("checking 'dateOfBirth'");
        }
        return correctForm;
    },
    checkForm1CRInvestorData: function (){
        var correctForm = true;
        $(".errorInputMessage").hide(); // remove all error texts
        $("#1CR_investor_2_investorDataPanel input").removeClass("redBorder"); // remove ALL redborders
        var name = $("#ContentPlaceHolder_name").val();
        var surname = $("#ContentPlaceHolder_surname").val();
        var identificationId = $("#dni").val();
        var dateOfBirth = $("#ContentPlaceHolder_dateOfBirth").val();
        var email = $("#ContentPlaceHolder_email").val();
        var telephone = $("#ContentPlaceHolder_telephone").val();
        var postCode = $("#ContentPlaceHolder_postCode").val();
        var address = $("#ContentPlaceHolder_address1").val();
        var city = $("#ContentPlaceHolder_city").val();
        var country = $("#ContentPlaceHolder_country").val();
        var iban = $("#ContentPlaceHolder_iban").val();
        var cif = $("#ContentPlaceHolder_cif").val();
        var businessName = $("#ContentPlaceHolder_businessName").val();
        if (name === "") {
            console.log("empty name");
            $(".investorName").addClass("redBorder");
            $(".ErrorName").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorName").fadeIn();
            correctForm = false;
        }
        if (surname === "") {
            console.log("empty surname");
            $(".investorSurname").addClass("redBorder");
            $(".ErrorId").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorId").fadeIn();
            correctForm = false;
        }
        if (identificationId === "") {
            console.log("empty dni");
            $(".investorDni").addClass("redBorder");
            $(".ErrorSurname").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorSurname").fadeIn();
            correctForm = false;
        }
        if (dateOfBirth === "") {
            console.log("empty date of birth");
            $(".investorDateOfBirth").addClass("redBorder");
            $(".ErrorDateOfBirth").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorDateOfBirth").fadeIn();
            correctForm = false;
        }
        if (email === "") {
            $(".investorEmail").addClass("redBorder");
            $(".ErrorEmail").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorEmail").fadeIn();
            correctForm = false;
        } else {
            if (!app.utils.validEmail(email)) {
                $(".investorEmail").addClass("redBorder");
                $(".ErrorEmail").find(".errorMessage").html(TEXTOS.T02); // "email not valid" warning
                $(".ErrorEmail").fadeIn();
                correctForm = false;
            }
        }
        if (postCode === "") {
            console.log("empty post code");
            $(".investorPostCode").addClass("redBorder");
            $(".ErrorPostCode").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorPostCode").fadeIn();
            correctForm = false;
        }
        if (address === "") {
            console.log("empty address");
            $(".investorAddress").addClass("redBorder");
            $(".ErrorAddress").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorAddress").fadeIn();
            correctForm = false;
        }
        if (city === "") {
            console.log("empty city");
            $(".investorCity").addClass("redBorder");
            $(".ErrorCity").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorCity").fadeIn();
            correctForm = false;
        }
        if (country === "") {
            console.log("empty country");
            $(".investorCountry").addClass("redBorder");
            $(".ErrorCountry").find(".errorMessage").html(TEXTOS.T15); // "you have to select an option" warning
            $(".ErrorCountry").fadeIn();
            correctForm = false;
        }
        if (telephone === "") {
            $(".telephoneNumber").addClass("redBorder");
            $(".ErrorPhoneNumber").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorPhoneNumber").fadeIn();
            correctForm = false;
        } else {
            if (!app.utils.checkPhoneNumber(telephone)) {
                $(".investorTelephone").addClass("redBorder");
                $(".ErrorPhoneNumber").find(".errorMessage").html(TEXTOS.T10); // "The telephone number is not valid" warning
                $(".ErrorPhoneNumber").fadeIn();
                correctForm = false;
            }
        }
        if (iban === "") {
            console.log("empty IBAN");
            $(".investorIban").addClass("redBorder");
            $(".ErrorIban").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorIban").fadeIn();
            correctForm = false;
        }
        else {
            //Needed testing algorithm. (IBAN Format)
            //var IBAN = required('iban');
            if (!window.IBAN.isValid(iban)) {
                $(".investorIban").addClass("redBorder");
                $(".ErrorIban").find(".errorMessage").html(TEXTOS.T95); // "The IBAN is not valid" warning
                $(".ErrorIban").fadeIn();
                correctForm = false;
            }
        }
        //If is selected 'I use my company as investment vehicle', validate CIF & Business Name
            if ((cif === "") && ($("#investmentVehicle").prop("checked"))) { 
                console.log("empty CIF");
                $(".investorCif").addClass("redBorder");
                $(".ErrorCif").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
                $(".ErrorCif").fadeIn();
                correctForm = false;
            }
            /*else {
                //Needed testing algorithm. (CIF Format)
                if (!app.utils.checkCif(cif)) {
                    $(".investorCif").addClass("redBorder");
                    $(".ErrorCif").find(".errorMessage").html(TEXTOS.T24); // "The cif is not valid" warning
                    $(".ErrorCif").fadeIn();
                    correctForm = false;
                }
            }*/
            if ((businessName === "") && ($("#investmentVehicle").prop("checked"))) {
                console.log("empty business name");
                $(".investorBusinessName").addClass("redBorder");
                $(".ErrorBusinessName").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
                $(".ErrorBusinessName").fadeIn();
                correctForm = false;
            }
            $(".uploaded").each(function(){
                if ($(this).val() == 0) {
                    console.log("required files");
                    $(".ErrorFiles").find(".errorMessage").html(TEXTOS.T96); // "update all required files" warning
                    $(".ErrorFiles").fadeIn();
                    correctForm = false;
                }
            });
        return correctForm;   
    },
    checkFormWinadminBilling: function () {
        var correctForm = true;
        $(".errorInputMessage").hide(); // remove all error texts
        $("#uploadBill input").removeClass("redBorder"); // remove ALL redborders
        $("#uploadBill select").removeClass("redBorder"); // remove ALL redborders
        var pfp = $("#ContentPlaceHolder_pfp").val();
        var number = $("#ContentPlaceHolder_number").val();
        var concept = $("#ContentPlaceHolder_concept").val();
        var amount = $("#ContentPlaceHolder_amount").val();
        var currency = $("#ContentPlaceHolder_currency").val();
        if (pfp == 0) {
            console.log("pfp not selected");
            $(".billPFP").addClass("redBorder");
            $(".ErrorPFP").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorPFP").fadeIn();
            correctForm = false;
        }
        if (number === "") {
            console.log("empty bill number");
            $(".billNumber").addClass("redBorder");
            $(".ErrorNumber").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorNumber").fadeIn();
            correctForm = false;
        }
        if (concept === "") {
            console.log("empty bill concept");
            $(".billConcept").addClass("redBorder");
            $(".ErrorConcept").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorConcept").fadeIn();
            correctForm = false;
        }
        var regexp =  /^(?=.)(\d{1,3})?(\,\d+)?(\d{1,2})$/g;
        result = regexp.test(amount);
        if (amount === "") {
            console.log("empty bill amount");
            $(".billAmount").addClass("redBorder");
            $(".ErrorAmount").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorAmount").fadeIn();
            correctForm = false;
        }
        else if (!result) {
            console.log("incorrect bill amount");
            $(".billAmount").addClass("redBorder");
            $(".ErrorAmount").find(".errorMessage").html(TEXTOS.T72); // "introduce quantity > 0" warning
            $(".ErrorAmount").fadeIn();
            correctForm = false;
        }
        if (currency == 0) {
            console.log("not selected currency");
            $(".billCurrency").addClass("redBorder");
            $(".ErrorCurrency").find(".errorMessage").html(TEXTOS.T97); // "select currency" warning
            $(".ErrorCurrency").fadeIn();
            correctForm = false;
        }
        return correctForm;
    },
    checkFormPFPAdminTallyman: function () {
        var correctForm = true;
        var correctNIF = true;
        var correctEmail = true;
        var correctTelephone = true;
        var correctFormatEmail = true;
        var correctFormatTelephone = true;
        $(".errorInputMessage").hide(); // remove all error texts
        $("#investorFilters input").removeClass("redBorder"); // remove ALL redborders
        var nif = $("#tallyman_nif").val();
        var email = $("#tallyman_email").val();
        var telephone = $("#tallyman_telephone").val();
        
        //NIF validation
        if (nif === "") { 
            correctNIF = false; 
        }
        
        //Email validation
        if (email === "") {
            correctEmail = false;
        } else {
            if (!app.utils.validEmail(email)) {
                correctFormatEmail = false;
            }
        }
        
        //Telephone validation
        if (telephone === "") {
            correctTelephone = false;
        } else {
            if (!app.utils.checkPhoneNumber(telephone)) {
                correctFormatTelephone = false;
            }
        }

        //ERROR SHOWING
        if (!correctNIF && !correctEmail && !correctTelephone) {
            console.log("all fields are empty");
            $(".tallymanGeneral").addClass("redBorder");
            $(".ErrorTallyman").find(".errorMessage").html(TEXTOS.T98); // "at least 2 fields" warning
            $(".ErrorTallyman").fadeIn();
            correctForm = false;
        }
        if (correctNIF && !correctEmail && !correctTelephone) {
            console.log("email & telephone empty");
            $(".tallymanTelephone").addClass("redBorder");
            $(".tallymanEmail").addClass("redBorder");
            $(".ErrorTallyman").find(".errorMessage").html(TEXTOS.T99); // "at least 1 field more" warning
            $(".ErrorTallyman").fadeIn();
            correctForm = false;
        }
        if (!correctNIF && correctEmail && !correctTelephone) {
            console.log("nif & telephone empty");
            $(".tallymanTelephone").addClass("redBorder");
            $(".tallymanNIF").addClass("redBorder");
            $(".ErrorTallyman").find(".errorMessage").html(TEXTOS.T99); // "at least 1 field more" warning
            $(".ErrorTallyman").fadeIn();
            correctForm = false;
        }
        if (!correctNIF && !correctEmail && correctTelephone) {
            console.log("nif & email empty");
            $(".tallymanEmail").addClass("redBorder");
            $(".tallymanNIF").addClass("redBorder");
            $(".ErrorTallyman").find(".errorMessage").html(TEXTOS.T99); // "at least 1 field more" warning
            $(".ErrorTallyman").fadeIn();
            correctForm = false;
        }
        if (correctNIF && !correctFormatEmail && !correctTelephone) {
            console.log("name correct, email incorrect format, telephone empty");
            $(".tallymanEmail").addClass("redBorder");
            $(".ErrorEmail").find(".errorMessage").html(TEXTOS.T02); // "email incorrect" warning
            $(".ErrorEmail").fadeIn();
            correctForm = false;
        }
        if (correctNIF && !correctEmail && !correctFormatTelephone) {
            console.log("name correct, email empty, telephone incorrect format");
            $(".tallymanTelephone").addClass("redBorder");
            $(".ErrorTelephone").find(".errorMessage").html(TEXTOS.T10); // "telephone incorrect" warning
            $(".ErrorTelephone").fadeIn();
            correctForm = false;
        }
        if (!correctNIF && !correctEmail && !correctFormatTelephone) {
            console.log("name empty, email empty, telephone empty");
            $(".tallymanEmail").addClass("redBorder");
            $(".ErrorEmail").find(".errorMessage").html(TEXTOS.T02); // "email incorrect" warning
            $(".ErrorEmail").fadeIn();
            correctForm = false;
        }
        if (!correctNIF && !correctFormatEmail && !correctTelephone) {
            console.log("name correct, email empty, telephone incorrect format");
            $(".tallymanTelephone").addClass("redBorder");
            $(".ErrorTelephone").find(".errorMessage").html(TEXTOS.T10); // "telephone incorrect" warning
            $(".ErrorTelephone").fadeIn();
            correctForm = false;
        }
        if (!correctNIF && !correctFormatEmail && !correctFormatTelephone) {
            console.log("name empty, email incorrect format, telephone incorrect format");
            $(".tallymanTelephone").addClass("redBorder");
            $(".ErrorTelephone").find(".errorMessage").html(TEXTOS.T10); // "telephone incorrect" warning
            $(".ErrorTelephone").fadeIn();
            $(".tallymanEmail").addClass("redBorder");
            $(".ErrorEmail").find(".errorMessage").html(TEXTOS.T02); // "email incorrect" warning
            $(".ErrorEmail").fadeIn();
            correctForm = false;
        }
        if (!correctNIF && correctFormatEmail && !correctFormatTelephone) {
            console.log("name empty, email correct format, telephone incorrect format");
            $(".tallymanTelephone").addClass("redBorder");
            $(".ErrorTelephone").find(".errorMessage").html(TEXTOS.T10); // "telephone incorrect" warning
            $(".ErrorTelephone").fadeIn();
            correctForm = false;
        }
        if (!correctNIF && !correctFormatEmail && correctFormatTelephone) {
            console.log("name empty, email incorrect format, telephone correct format");    
            $(".tallymanEmail").addClass("redBorder");
            $(".ErrorEmail").find(".errorMessage").html(TEXTOS.T02); // "email incorrect" warning
            $(".ErrorEmail").fadeIn();
            correctForm = false;
        }
        return correctForm;
    },
    checkFormWinadminUpdatePFP: function () {
        var correctForm = true;
        var selectedPFP = $("#ContentPlaceHolder_pfp").val();
        var termsOfService = $("#ContentPlaceHolder_terms").val();
        var privacyPolicy = $("#ContentPlaceHolder_privacyPolicy").val();
        var modality = $("#ContentPlaceHolder_modality").val();
        var status = $("#ContentPlaceHolder_status").val();
        
        $(".errorInputMessage").hide(); // remove all error texts
        $("#modifyPFPData input").removeClass("redBorder"); // remove ALL redborders on input
        $("#modifyPFPData select").removeClass("redBorder"); // remove ALL redborders on select
        
        //Error showing
        if (selectedPFP == 0) {
            console.log("pfp not selected");
            $(".selectedPFP").addClass("redBorder");
            $(".ErrorPFP").find(".errorMessage").html(TEXTOS.T100); // "select PFP" warning
            $(".ErrorPFP").fadeIn();
            correctForm = false;
        }
        if (termsOfService === "") {
            console.log("empty terms of service");
            $(".pfpTermsOfService").addClass("redBorder");
            $(".ErrorTermsOfService").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorTermsOfService").fadeIn();
            correctForm = false;
        }
        if (privacyPolicy === "") {
            console.log("empty privacy policy");
            $(".pfpPrivacyPolicy").addClass("redBorder");
            $(".ErrorPrivacyPolicy").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorPrivacyPolicy").fadeIn();
            correctForm = false;
        }        
        if (modality == 0) {
            console.log("modality not selected");
            $(".pfpModality").addClass("redBorder");
            $(".ErrorModality").find(".errorMessage").html(TEXTOS.T101); // "select modality" warning
            $(".ErrorModality").fadeIn();
            correctForm = false;
        }
        if (status == 0) {
            console.log("status not selected");
            $(".pfpStatus").addClass("redBorder");
            $(".ErrorStatus").find(".errorMessage").html(TEXTOS.T101); // "select modality" warning
            $(".ErrorStatus").fadeIn();
            correctForm = false;
        }
        return correctForm;
    }
};
app.utils = {

    validEmail: function (email) {
        console.log("entering validEmail function ");
        //check if the email is semantically correct
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        //var re = /^[a-zA-Z0-9.!#$%&amp;'*+-/=?\^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
        return re.test(email);
    },
    checkPassword: function (password) {

        console.log("jkjkj" + password);
        var strength = false;
        return true;
        //if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/) && password.length >= 10 || password.match(/([a-zA-Z])/) && password.match(/([0-9])/) && password.match(/([!,%,&,@,#,$,^,*,?,_,~])/) && password.length >= 8) {
        console.log("checkPassword function ");
//	if (password.match(/([A-Z])/) && password.match(/([a-z])/) && password.match(/([0-9])/) && password.match(/([!,%,&,@,#,$,^,*,?,_,~,.,+,-,€])/) && password.length >= 8) {
        if (password.match(/([A-Z])/) && password.match(/([a-z])/) && password.match(/([0-9])/) && password.length >= 8) {
            return true;
        }
        return true;
        return strength;
        ////if password contains both lower and uppercase characters, increase strength value
        //if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))
        ////if it has numbers and characters, increase strength value
        //if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))
        ////if it has one special character, increase strength value
        //if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))
        ////if it has two special characters, increase strength value
        //if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/))

    },
    checkPhoneNumber: function (phonenumber) {		// temporary function Shall be done by specialized library
        phonenumber = phonenumber.replace(/\s/g, '');
        var re = /^[+123456789]\d{6}/; // at least 7 chars, may contain +
        return re.test(phonenumber);
    },
    sacarMensajeError: function (mostrar, inputCampo, mensaje) {
        var $bloque = $(inputCampo).parent();
        if (mostrar) {
            if (!!mensaje) {
                console.log("SHOW the error message on the screen");
                $bloque.find(".errorMessage").html(mensaje);
                $bloque.find(".errorInputMessage").fadeIn();
            }
        } else {
            $bloque.find(".errorInputMessage").fadeIn();
        }
    }


};
var TEXTOS = {
    T0: "ERROR",
    T01: 'This field cannot be empty',
    T02: 'The email address is not valid',
    T03: 'The provided passwords are not identical',
    //   T04: "Debe introducir el sexo.",
    T05: 'This is an invalid date',
    T06: 'You have to accept our privacy policy and terms of service',
    T07: 'You have to accept the terms of service',
    T08: 'Incorrect format. Your password should be at least 8 characters long and contain uppercase and lowercase characters, a number and another symbol',
    T09: 'You should be over 18 years old',
    T10: 'The telephone number is not valid',
    T11: 'The ID number is not valid',
    T12: 'Select at least one option.',
    T13: 'Add your current and new password',
    T14: 'Telephone is a mandatory field',
    T15: "You have to select one option",
    //   T16: "Las contraseñas no coinciden",
    //   T17: "Por favor, repite su contraseña.",
    T18: 'Select your gender',
    T19: 'Please select a Crowdlending platform',
    T20: 'Please add your surname',
    T21: 'Please enter your alias',
    T22: 'Please introduce your date of birth',
    T23: 'Please select a crowdlending platform.',
    T24: "The CIF is not valid.",
    //   T25: "Por favor, introduzca el nombre de su empresa.",
    T26: 'Please introduce the code which was sent to your mobile phone',
//    T27: "Por favor, introduzca la marca de su empresa.",
//    T28: "Por favor, introduzca el CIF de su empresa.",
    //   T29: "Por favor, introduzca la description corta sobre ti.",
    //   T30: "Por favor, introduzca la description larga sobre ti.",
    //   T31: "Por favor, introduzca su direción.",
    //   T32: "Por favor, introduzca su código postal.",
    //   T33: "Por favor, introduzca su ciudad.",
    //   T34: "Por favor, introduzca su nacionalidad.",
    //   T35: "Por favor, introduzca su teléfono.",
    //   T36: "Por favor, introduzca el nombre de titular de la cuenta.",
    //   T37: "Por favor, introduzca el apellido de titular de la cuenta.",
    //   T38: "Por favor, introduzca su DNI(NIE)",
    //   T39: "Por favor, introduzca el nombre del banco.",
    //   T40: "Por favor, introduzca el numero de la cuenta.",
    //   T41: "Por favor, introduzca la cantidad .",
    //   T42: "Por favor, introduzca su cargo.",
    //   T43: "Por favor, introduzca su sector laboral.",
    //   T44: "Por favor, introduzca el nombre de su empresa.",
    //   T45: "Por favor, rellena los campos de ingresos",
    //   T46: "Por favor, rellena los campos de gastos",
//    T47: "Por favor, introduzca una fecha válida.",
    //   T48: "Por favor, introduzca la web de la empresa.",
    //   T49: "Por favor, introduzca el horario de atención.",
    //   T50: "Por favor, suba un documento con su dni.",
    //   T51: "Por favor, suba un documento con su titularidad bancaria.",
    //   T52: "Por favor, suba un documento con sos tres últimas nominas.",
    //   T53: "Por favor, describa para que necesita el dinero.",
    //   T54: "Por favor, introduzca la fecha de validez del dni",
    //   T55: "Por favor, introduzca su estado civil",
    //   T56: "Por favor, introduzca su numero de hijos",
    //   T57: "Por favor, introduzca el número de personas que tiene a su cargo",
    //   T58: "Por favor, introduzca el tipo de vivienda donde reside",
    //   T59: "Por favor, introduzca su nivel academico",
    //   T60: "Por favor, introduzca su situación laboral",
    //   T61: "Por favor, introduzca su tipo de contrato",
    //   T62: "Por favor, introduzca la antiguedad de su contrato",
    //   T63: "Por favor, introduzca su cargo dentro de la empresa",
    //   T64: "Por favor, introduzca el sector laboral de la empresa",
    //   T65: "Por favor, introduzca el nombre de la empresa",
    //   T66: "Por favor, introduzca el importe",
    //   T67: "Por favor, introduzca el número de pagas",
    //   T68: "Por favor, introduzca el gasto",
    //   T69: "Por favor, suba un documento con su DNI",
    //   T70: "Por favor, suba un documento con su titularidad bancaria",
    //   T71: "Por favor, suba un documento con sus tres últimas nominas",
    T72: 'Please, Introduce a quantity higher than 0',
    //   T73: "Por favor, introduzca su contraseña actual",
    //   T74: "Por favor, introduzca su nueva contraseña",
    //   T75: "El código postal introducido no es válido.",
    //   T76: "Ese tipo de archivo no está permitido.",
    //   T77: "Para acceder a esta sección debe de estar identificado.",
    //   T78: "La solicitud de préstamos solo está disponible para particulares.",
    //   T79: "Por favor, añade sus ingresos.",
    //   T80: "Por favor, añade sus gastos.",
    //   T81: "Debe introducir al menos dos gastos para continuar.",
    //   T82: "Debe introducir la url de la promo.",
    T83: "La url introducida no es válida.",
    T84: "Por favor, introduzca su duda.",
    T85: "Importe incorrecto, debe ser un multiple de 50€ o entre el importe mínimo y máximo.",
    T86: 'Email provided is not available',
    T87: "Se ha enviado un email a la dirección indicada.",
    T88: "Por favor, introduzca el código de acceso.",
    T89: "El mensaje es demasiado largo.",
    T90: "Email o contraseña incorrecto",
    T91: "Una nueva contraseña ha sido enviado al email indicado",
    T92: "Por favor, introduzca el nombre",
    T93: "Por favor, introduzca los apellidos",
    T94: "Por favor, introduzca su mensaje",
    T95: "The IBAN is not valid",
    T96: "You must upload all the required files",
    T97: "You must select the currency",
    T98: "To use this service you must provide at least 2 fields",
    T99: "To use this service you must provide at least 1 field more",
    T100: "Select PFP",
    T101: "Select modality",
    T102: "Select service status"
};
