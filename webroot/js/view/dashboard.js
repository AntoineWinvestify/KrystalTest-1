/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function successAjaxMain(result) {
    $("#MainContainer").html(result);
}

function errorAjaxMain(result) {
}

function overviewAjax() {
    link = "dashboardOverviewData";
    getServerData(link, null, successAjaxMain, errorAjaxMain);
}




function overviewDataJS() {
//Click on platform logo
    $(document).on("click", ".logo", function () {
        id = $(this).attr("id").split(" ")[0];
        name = $("#logo" + id).attr("alt");
        var params = {
            id: $(this).attr("id"),
            logo: $("#logo" + id).attr("src"),
            name: name
        };
        ga_company(id, name);
        var data = jQuery.param(params);
        link = $(this).attr("href");
        $(".togetoverlay_overview").addClass("togetoverlay");
        getServerData(link, data, successOverviewAjax, errorOverviewAjax);
    });

    function successOverviewAjax(result) {
        // alert("ok " + result);
        $(".togetoverlay_overview").removeClass("togetoverlay");
        $(".dashboardGlobalOverview").fadeOut();
        $(".ajaxResponse").html(result);

    }

    function errorOverviewAjax(result) {
        $(".togetoverlay_overview").removeClass("togetoverlay");
        //alert("not ok " + result);
    }
}


function graphOverview(labels, data) {
    $(document).on("click", ".chartIcon", function () {
        id = $(this).attr("id");
        $("#chart_" + id).slideToggle();
        $(this).toggleClass("active");
        ga_chart(id);
    });
    $('[data-toggle="tooltip"]').tooltip();
    var birdsCanvas = document.getElementById("birdsChart");
    var polarAreaChart = new Chart(birdsCanvas, {
        type: "line",
        data: {
            labels: labels,
            datasets: [{
                    label: "netReturn",
                    fill: false,
                    data: data,
                    borderColor: "rgba(0, 230, 77, 1)",
                    borderWidth: 2
                }]
        },
        options: {
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
            }
        }
    });
    return polarAreaChart;
}

function singlePfpJS() {
    $(document).on("click", "#activeTab", function (event) {
        event.preventDefault();
        id = $(this).attr("value");



        var data = null;
        link = $(this).attr("href");
        ga_allInvestments();
        getServerData(link, data, successLoansAjax, errorLoansAjax);
    });

    $(document).on("click", "#defaultedTab", function (event) {
        event.preventDefault();
        $(".togetoverlay_loans").addClass("togetoverlay");
        id = $(this).attr("value");
        var data = null;
        link = $(this).attr("href");
        getServerData(link, data, successLoansAjax, errorLoansAjax);
    });



    function successLoansAjax(result) {
        // alert("ok " + result);
        $(".togetoverlay_loans").removeClass("togetoverlay");
        $(".loans-table").html(result);
    }

    function errorLoansAjax(result) {
        $(".togetoverlay_loans").removeClass("togetoverlay");
        //alert("not ok " + result);
    }

}
