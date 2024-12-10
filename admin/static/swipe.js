$(document).ready(function () {
    const $sidebar = $(".sidebar");
    const $hamburgerIcon = $(".hamburger-icon");
  
    // Toggle sidebar when clicking the hamburger icon
    $hamburgerIcon.on("click", function () {
      $sidebar.toggleClass("close");
    });
  
    // Handle swipe gesture for opening/closing sidebar (mobile-friendly)
    let touchStartX = 0;
    let touchEndX = 0;
  
    $sidebar.on("touchstart", function (event) {
      touchStartX = event.changedTouches[0].screenX;
    });
  
    $sidebar.on("touchend", function (event) {
      touchEndX = event.changedTouches[0].screenX;
      handleSwipe();
    });
  
    function handleSwipe() {
      if (touchStartX - touchEndX > 100) {
        // Swiped left, close the sidebar
        $sidebar.addClass("close");
      } else if (touchEndX - touchStartX > 100) {
        // Swiped right, open the sidebar
        $sidebar.removeClass("close");
      }
    }
  });
  