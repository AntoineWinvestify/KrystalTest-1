function successLanguage(data) {
    location.reload(true);
}

function sendLocationDataSuccess(data) {
}

function sendLocationDataError(data) {
    }

$(document).ready(function () {
    $.getJSON('https://freegeoip.net/json/?callback=?', function (data) {		// 10.000 /hour, only IP
        var link = "/marketplaces/location";
        console.log(JSON.stringify(data, null, 2));
        getServerData(link);
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
        console.log("Error detected in input parameters of login function");
        event.stopPropagation();
        event.preventDefault();
        return false;
    });
});