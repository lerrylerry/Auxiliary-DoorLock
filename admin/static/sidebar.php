<!-- Sidebar -->
<div class="sidebar close">
    <div class="logo-details">
        <img src="../images/TUP-LOGO-modified.png" alt="logImg">
        <span class="logo_name">Auxiliary <br> System</span>
    </div>
    <ul class="nav-links">
        <li>
            <a href="homepage.php">
                <i class='bx bx-home-alt nav_icon'></i>
                <span class="link_name">Home</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="homepage.php">Home</a></li>
            </ul>
        </li>
        <li>
            <div class="iocn-link">
                <a>
                    <i class='bx bx-note nav_icon'></i>
                    <span class="link_name">Manage Inventory</span>
                </a>
                <i class='bx bxs-chevron-down arrow'></i>
            </div>
            <ul class="sub-menu">
                <li><a class="link_name">Manage Inventory</a></li>
                <li><a href="masterlist.php">Masterlist</a></li>
                <li><a href="inventory.php">Inventory</a></li>
            </ul>
        </li>
        <li>
            <a href="utilPersonnelList.php">
                <i class='bx bx-user nav_icon' ></i>
                <span class="link_name">Utility Personnels</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="utilPersonnelList.php">Utility Personnels</a></li>
            </ul>
        </li>
        <li>
            <div class="iocn-link">
                <a>
                    <i class="bi bi-exclamation-circle"></i>
                    <span class="link_name">Manage Requests</span>
                </a>
                <i class='bx bxs-chevron-down arrow'></i>
            </div>
            <ul class="sub-menu">
                <li><a class="link_name">Manage Requests</a></li>
                <li><a href="borrowedItems.php">Borrow Request</a></li>
                <li><a href="return-items.php">Return Request</a></li>
                <li><a href="minorRepair.php">Minor Repair Request</a></li>
            </ul>
        </li>
        <li>
            <div class="iocn-link">
                <a>
                    <i class='bx bx-history'></i>
                    <span class="link_name">History</span>
                </a>
                <i class='bx bxs-chevron-down arrow'></i>
            </div>
            <ul class="sub-menu">
                <li><a class="link_name">History</a></li>
                <li><a href="historyAdmin.php">Borrow/Return History</a></li>
                <li><a href="historyRepair.php">Repair History</a></li>
                <li><a href="historyItem.php">Items History</a></li>
            </ul>
        </li>
        <li>
            <a href="recorded.php">
                <i class='bx bx-video-plus' ></i>
                <span class="link_name">Camera</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="recorded.php">Camera</a></li>
            </ul>
        </li>
        <li>
            <a href="../logout.php">
                <i class='bx bx-log-out'></i>
                <span class="link_name">Logout</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="../logout.php">Logout</a></li>
            </ul>
        </li>
    </ul>
</div>

<!-- Include jQuery (make sure to include jQuery in your project) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // When an item with the class `.iocn-link` is clicked (which are the parent links with submenus)
    $('.iocn-link').click(function() {
        // Toggle the class 'showMenu' on the parent 'li' to display/hide the submenu
        $(this).parent('li').toggleClass('showMenu');
        
        // Optional: To animate the dropdown toggle effect, you can add slideToggle
        $(this).next('.sub-menu').slideToggle(300); // 300ms animation time
    });
});
</script>
