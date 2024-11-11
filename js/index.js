$(document).ready(function(){

    $('.dropdown-item').click(function(e){

        e.preventDefault();

        var selectedPage = $(this).attr('value');

        if (selectedPage){

            window.location.href = selectedPage;
        }
    });

    $('.navLogo').on('click', function(){
        window.location.href = "index.html";
    })

    $('#login').on('click', function(){
        window.location.href ="login.php";
    })

    function checkWidth() {
        if ($(window).width() < 768) {
          if ($('.navdiv').length > 0) {
            $('.navdiv').children().unwrap();
          }
          $('.dropdown').removeClass('mx-auto');
          $('.dropdown').addClass('d-flex justify-content-center pb-1');
          $('.divLogin').addClass('pb-1');
        } else {
          if ($('.navdiv').length === 0) {
            var elementsToWrap = $('.dropdown.mx-auto, .d-flex.justify-content-center');
            elementsToWrap.wrapAll('<div class="navdiv d-flex justify-content-center w-100"></div>');
          }
          $('.dropdown').removeClass('d-flex justify-content-center pb-1');
          $('.divLogin').removeClass('pb-1');
          $('.dropdown').addClass('mx-auto');
          
        }


        if ($(window).width() <= 496){
          $('.repair-container').css({
            width: '22rem',
            height: '35rem'
          })
          $('#bg-logo').css({
            width: '12rem',
            height: '12rem'
          })
        } else if ($(window).width() > 496 && $(window).width() <= 728 ) {
          $('.repair-container').css({
            width: '28rem',
            height: '40rem'
          })
          $('#bg-logo').css({
            width: '18rem',
            height: '18rem'
          })
        } else if (($(window).width() > 728)) {
          $('.repair-container').css({
            width: '40rem',
            height: '55rem'
          })
          $('#bg-logo').css({
            width: '30rem',
            height: '30rem'
          })
        }
      }

    checkWidth();

    $(window).resize(function(){
        checkWidth();
    })

});