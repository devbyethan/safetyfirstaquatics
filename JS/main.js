

$(document).ready(function(){


    $("#header").load("Includes/header.html");
    $("#footer").load("Includes/footer.html");
    
    $('.carousel').slick({
        prevArrow:'<button class="slick-prev slick-arrow" aria-label="Previous" type="button" style=""><i  class="arrow left"></button></i>',
        nextArrow: '<button class="slick-next slick-arrow" aria-label="Next" type="button" style=""><i class="arrow right"></i></button>',
        lazyLoad: 'ondemand'
    });

    AOS.init();

});
