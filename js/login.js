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

    $('#username').change(function(){
        if($('#username').val().trim() === ''){
            $('#usernameError').text('Fill up required')
        } else{
            $('#usernameError').text('')
        }
    })


    $('#password').change(function(){
        if($('#password').val().trim() === ''){
            $('#passwordError').text('Fill up required')
        } else{
            $('#passwordError').text('')
        }
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

        if ($(window).width() <= 496) {
            $('.login-container').css({
                width: '20rem', 
                height: '35rem'
            })
            $('#username').css({
                width: '250px'
            })
            $('#password').css({
                width: '250px'
            })
            $('#email').css({
                width: '250px'
            })
            $('#changepass').css({
                width: '250px'
            })
            $('#bg-logo').css({
                width: '16rem',
                height: '16rem',
                position: 'absolute',
                bottom: '95px',
                left: '33px'
            })
        } else {
            $('.login-container').css({
                width: '25rem', 
                height: '33rem'
            })
            
            $('#username').css({
                width: '305px'
            })
            $('#password').css({
                width: '305px'
            })
            $('#email').css({
                width: '305px'
            })
            $('#changepass').css({
                width: '305px'
            })
            $('#bg-logo').css({
                width: '20rem',
                height: '20rem',
                position: 'absolute',
                bottom: '50px',
                left: '35px'
            })
        }
      }


    
    
    checkWidth();

    $(window).resize(function(){
        checkWidth();
    })




});