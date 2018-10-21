
$(window).scroll(function(){
    if($(document).scrollTop() > 100) {
        $('#header').addClass('small');
    } else {
        $('#header').removeClass('small');
    }
});

$(".hamburger").click(function(){
    $(this).toggleClass("is-active");
    $(".mobile_menu_content").toggle(500);
    $("body").toggleClass("noscroll");

});

var acc = document.getElementsByClassName("dropdown_mobile");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight){
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  });
}
