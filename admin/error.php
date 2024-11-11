<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center mt-5">
                <div class="text-danger">
                    <svg width="3em" height="3em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8 1a7 7 0 0 0-7 7 7 7 0 0 0 7 7 7 7 0 0 0 7-7 7 7 0 0 0-7-7zm4.354 10.646a.5.5 0 0 1-.708 0L8 8.707 4.354 12.354a.5.5 0 1 1-.708-.708L7.293 8 3.646 4.354a.5.5 0 0 1 .708-.708L8 7.293l3.646-3.647a.5.5 0 0 1 .708.708L8.707 8l3.647 3.646a.5.5 0 0 1 0 .708z"/>
                    </svg>
                </div>
                <h5 class="mt-3">Error</h5>
                <p>There was an error processing your transaction.<br>Please try again later.</p>
                <button type="button" class="btn btn-primary mb-3" id="modalRedirectButton" onclick="redirectToPage()">OK</button>
            </div>
        </div>
    </div>
</div>