<button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal"><i class="bi bi-check-circle-fill"></i> Approve</button>

<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel<?php echo $data['id'] ?>"aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel<?php echo $data['id'] ?>">Approve Repair Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to accept this request?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel
                </button>
                <form method="post" action="">
                    <input type="hidden" class="form-control" name="approverepair"
                        value="<?php echo $data['id'] ?>">
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>
            </div>
        </div>
    </div>
</div>





<button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="bi bi-x-circle-fill"></i> Reject</button>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel<?php echo $data['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel<?php echo $data['id'] ?>">Reject Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form method="post" action="">
                    <label for="productName" class="form-label">Rejection Reason</label>
                    <input type="text" class="form-control" name="reason" required placeholder="Reason for Rejection">
                    Are you sure you want to reject this request?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel
                </button>
                <form method="post" action="">
                    <input type="hidden" class="form-control" name="rejectmr"
                            value="<?php echo $data['id'] ?>">
                    <button type="submit" class="btn btn-danger">Reject</button>
                </form>
            </div>
        </div>
    </div>
</div>





<a href="generatepdf.php?id=<?php echo $data['id']; ?>&name=<?php echo urlencode($data['name']); ?>&position=<?php echo urlencode($data['position']); ?>&department=<?php echo urlencode($data['department']); ?>&email=<?php echo urlencode($data['email']); ?>&type=<?php echo urlencode($data['type']); ?>&serial=<?php echo urlencode($data['serial']); ?>&brandmodel=<?php echo urlencode($data['brandmodel']); ?>&propertyno=<?php echo urlencode($data['propertyno']); ?>&acqcost=<?php echo urlencode($data['acqcost']); ?>&acqdate=<?php echo urlencode($data['acqdate']); ?>&scope=<?php echo urlencode($data['scope']); ?>&datetime=<?php echo urlencode($data['datetime']); ?>" class="btn btn-secondary btn-sm"><i class="bi bi-printer"></i> Print</a>

