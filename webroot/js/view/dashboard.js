/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



function overviewDataJS() {
//Click on platform logo
    $(document).on("click", ".logo", function () {
        id = $(this).attr("id").split(" ")[0];
        name = $("#logo" + id).attr("alt");
        var params = {
            id: $(this).attr("id"),
            logo: $("#logo" + id).attr("src"),
            name: name,
        };
        ga_company(id, name);
        var data = jQuery.param(params);
        link = $(this).attr("href");
        getServerData(link, data, successOverviewAjax, errorOverviewAjax);
    });

    function successOverviewAjax(result) {
        // alert("ok " + result);
        $(".dashboardGlobalOverview").fadeOut();
        $(".ajaxResponse").html(result);

    }

    function errorOverviewAjax(result) {
        //alert("not ok " + result);
    }
}



function singlePfpJS() {

    $(document).on("click", "#activeTab", function () {
        event.preventDefault();
        id = $(this).attr("value");
        var params = {
            id: id,
        };
        var data = jQuery.param(params);
        link = $(this).attr("href");
        getServerData(link, data, successLoansAjax, errorLoansAjax);
    });

    $(document).on("click", "#defaultedTab", function () {
        event.preventDefault();
        id = $(this).attr("value");
        var params = {
            id: id,
        };
        var data = jQuery.param(params);
        link = $(this).attr("href");
        getServerData(link, data, successLoansAjax, errorLoansAjax);
    });


    function successLoansAjax(result) {
        // alert("ok " + result);
        $(".loans-table").html(result);
    }

    function errorLoansAjax(result) {
        //alert("not ok " + result);
    }

}