

$(document).ready(function(){

  let arrow = $(".arrow");
  arrow.on("click", function(){
    let arrowParent = $(this).parents().eq(1); // selecting main parent of arrow
    arrowParent.toggleClass("showMenu");
  });

  let sidebar = $(".sidebar");
  let sidebarBtn = $(".bx-menu");
  console.log(sidebarBtn);
  sidebarBtn.on("click", function(){
    sidebar.toggleClass("close");
  });


  const showNavbar = function(toggleId, navId, bodyId, headerId){
    const toggle = $("#" + toggleId),
      nav = $("#" + navId),
      bodypd = $("#" + bodyId),
      headerpd = $("#" + headerId)

    // Validate that all variables exist
    if (toggle && nav && bodypd && headerpd) {
      toggle.on('click', function(){
        // show navbar
        nav.toggleClass('showNav')
        // change icon
        toggle.toggleClass('bx-x')
        // add padding to body
        bodypd.toggleClass('body-pd')
        // add padding to header
        headerpd.toggleClass('body-pd')
      });
    };
  };

  showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header')
  const linkColor = $(".nav_link")

  function colorLink() {
    if (linkColor) {
      linkColor.removeClass('active')
      $(this).addClass('active')
    };
  };
  linkColor.on('click', colorLink)
  var $pincode = $(".pincode");
  $pincode.hide();
  $(".pinVisibility").on("click", function() {
      var $icon = $(this).find("i");
      var showRow =$(this).closest("tr").find(".pincode")
      var isVisible = showRow.is(":visible")

      if (isVisible) {
        showRow.hide();
        $icon.removeClass("bi-eye-slash").addClass("bi-eye");
    } else {
        showRow.show();
        $icon.removeClass("bi-eye").addClass("bi-eye-slash");
    }
  });


  $("#addpincode").on("input", function() {
    let pincodeInput = $(this).val();
    if (pincodeInput.length > 4) {
        $(this).val(pincodeInput.slice(0, 4));
    }
});


$('#togglePassword2').on("click",function() {
  var passwordInput2 = $(".newpin");
  var icon = $(this);

  if (passwordInput2.attr('type') === 'password') {
      console.log("hapihapi")
      passwordInput2.attr('type', 'text');
      console.log("sad")
      icon.removeClass('bi-eye').addClass('bi-eye-slash');
  } else {
      console.log("hai")
      passwordInput2.attr('type', 'password');
      console.log("sadsad")
      icon.removeClass('bi-eye-slash').addClass('bi-eye');
  }
});

$('#pincode1').on('input', function() {
  var pincodeInput = $(this);
  var pincode = pincodeInput.val().replace(/\D/g, ''); // Remove non-numeric characters
  pincodeInput.val(pincode); // Update input value

  var isValid = /^[0-9]{4}$/.test(pincode);
  if (!isValid) {
      pincodeInput[0].setCustomValidity("Please enter a 4-digit number from 0 to 9.");
  } else {
      pincodeInput[0].setCustomValidity("");
  }
});

// Use class selectors to apply the function to all rows
$('.togglePassword').click(function() {
  var passwordInput = $(this).closest('.input-group').find('.addpincode'); // Find the input within the same group
  var icon = $(this);

  if (passwordInput.attr('type') === 'password') {
      passwordInput.attr('type', 'text');
      icon.removeClass('bi-eye').addClass('bi-eye-slash');
  } else {
      passwordInput.attr('type', 'password');
      icon.removeClass('bi-eye-slash').addClass('bi-eye');
  }
});


$('#addpincode').on('input', function() {
  var pincodeInput = $(this);
  var pincode = pincodeInput.val().replace(/\D/g, ''); // Remove non-numeric characters
  pincodeInput.val(pincode); // Update input value

  var isValid = /^[0-9]{4}$/.test(pincode);
  if (!isValid) {
      pincodeInput[0].setCustomValidity("Please enter a 4-digit number from 0 to 9.");
  } else {
      pincodeInput[0].setCustomValidity("");
  }
});

$('.newpin').on('input', function() {
  var pincodeInput = $(this);
  var pincode = pincodeInput.val().replace(/\D/g, ''); // Remove non-numeric characters
  pincodeInput.val(pincode); // Update input value

  var isValid = /^[0-9]{4}$/.test(pincode);
  if (!isValid) {
      pincodeInput[0].setCustomValidity("Please enter a 4-digit number from 0 to 9.");
  } else {
      pincodeInput[0].setCustomValidity("");
  }
});

$('.plus').on('input', function() {
  var pincodeInput = $(this);
  var pincode = pincodeInput.val().replace(/\D/g, ''); // Remove non-numeric characters
  pincodeInput.val(pincode); // Update input value

  var isValid = /^[0-9]{4}$/.test(pincode);
  if (!isValid) {
      pincodeInput[0].setCustomValidity("Please enter a 4-digit number from 0 to 9.");
  } else {
      pincodeInput[0].setCustomValidity("");
  }
});










});