<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center mt-5">
                <div class="text-success">
                    <svg width="3em" height="3em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM7.97 10.97l-3.5-3.5a.75.75 0 0 1 1.06-1.06l2.47 2.47 4.97-4.97a.75.75 0 0 1 1.06 1.06l-5.5 5.5a.75.75 0 0 1-1.06 0z"/>
                    </svg>
                </div>
                <h5 class="mt-3">Success</h5>
                <p id="successMessage"><?php echo $successMessage; ?></p>
                <button type="button" class="btn btn-primary mb-3" id="modalRedirectButton" data-bs-dismiss="modal" onclick="redirectToPage()">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to redirect after modal close
    function redirectToPage() {
        $('#successModal').modal('hide');  // Close the modal
        // Optionally, add redirection or refresh page logic here
        // window.location.href = "somepage.php";
    }
</script>
