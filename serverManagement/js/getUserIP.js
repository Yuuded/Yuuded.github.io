// JavaScript Document

$(document).ready(function () {
    $.getJSON("http://jsonip.com/?callback=?", function (data) {
        console.log(data);
        alert(data.ip);
    });
});

//OR

$.get("http://ipinfo.io", function(response) {
    alert(response.ip);
}, "jsonp");