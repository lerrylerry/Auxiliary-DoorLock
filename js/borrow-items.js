$(document).ready(function(){

    $('.dropdown-item').click(function(e){

        e.preventDefault();

        var selectedPage = $(this).attr('value');

        if (selectedPage){

            window.location.href = selectedPage;
        }
    });
    
    $('#optionModal').fadeIn(700)

    $('#borrow-btn').on('click', function(){
        $('#optionModal').fadeOut(700)
        $('.return-container').fadeOut(700)
        $('.borrow-container').fadeIn(700)
    })

    $('#return-btn').on('click', function(){
        $('#optionModal').fadeOut(700)
        $('.borrow-container').hide(700)
        $('.return-container').fadeIn(1000)
    })

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
          // $('.borrow-container').css({
          //     width: '25rem', 
          //     height: '36rem'
          // })
        } else {
          if ($('.navdiv').length === 0) {
            var elementsToWrap = $('.dropdown.mx-auto, .d-flex.justify-content-center');
            elementsToWrap.wrapAll('<div class="navdiv d-flex justify-content-center w-100"></div>');
          }
          $('.dropdown').removeClass('d-flex justify-content-center pb-1');
          $('.divLogin').removeClass('pb-1');
          $('.dropdown').addClass('mx-auto');
        //   $('.borrow-container').css({
        //     width: '33rem', 
        //     height: '36rem'
        // })
        }

        // if ($(window).width() >= 619) {
        //   $('.optionModal').css({
        //     width: '35rem', 
        //     height: '20rem'
        // })
        // } else if ($(window).width() < 619 && $(window).width() >= 496) {
        //   $('.optionModal').css({
        //     width: '28rem', 
        //     height: '20rem'
        // })
        // } else if ($(window).width() <= 496) {
        //   $('.optionModal').css({
        //     width: '25rem', 
        //     height: '20rem'
        // })
        // }

        if ($(window).width() <= 496) {
          $('.borrow-container').css({
              width: '22rem', 
              height: '38rem'
          })
          $('.return-container').css({
            width: '22rem', 
            height: '38rem'
        })
        $('.optionModal').css({
              width: '25rem', 
              height: '20rem'
          })
          $('.borrow-table').css({
            width: '100%',
          })
          $('#bg-logo').css({
              width: '16rem',
              height: '16rem',
              position: 'absolute',
              bottom: '95px',
              left: '33px'
          })
      } else {
          $('.borrow-container').css({
              width: '30rem', 
              height: '38rem'
          })
          $('.return-container').css({
            width: '30rem', 
            height: '38rem'
        })
        $('.optionModal').css({
              width: '31rem', 
              height: '20rem'
          })
          $('.borrow-table').css({
            width: '95%',
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
   




    $('.pinpin').on('input', function() {
      var pincodeInput = $(this);
      var pincode = pincodeInput.val().replace(/\D/g, ''); // Remove non-numeric characters
      pincodeInput.val(pincode); // Update input value
    
      var isValid = /^[1-9]{4}$/.test(pincode);
      if (!isValid) {
          pincodeInput[0].setCustomValidity("Please enter a 4-digit number from 1 to 9.");
      } else {
          pincodeInput[0].setCustomValidity("");
      }
    });
     

})
