<!-- Bulk Record Payments Modal -->
<div id="bulkRecordPaymentsModal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Record Bulk School Fee Payments</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Select Class <span class="text-danger">*</span></label>
                    <select id="bulkPaymentClass" class="form-control select2">
                        <option value="">Choose a class...</option>
                        @foreach($classes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="studentsPaymentContainer" style="display: none;">
                    <h5>Record Payments for Students in <span id="selectedClassName"></span></h5>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-striped" id="studentPaymentsTable">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Student Name</th>
                                    <th style="width: 20%;">Amount Paid</th>
                                    <th style="width: 25%;">Payment Date</th>
                                    <th style="width: 20%;">Payment Method</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="paymentRowsContainer">
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-default" id="addPaymentRowBtn" style="margin-top: 10px;">
                        <i class="fa fa-plus"></i> Add Another Payment
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="bulkRecordPaymentsBtn" disabled>
                    <i class="fa fa-save"></i> Record All Payments
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-row {
        margin: 10px 0;
    }
    .payment-row input,
    .payment-row select {
        font-size: 12px;
        padding: 5px;
    }
</style>
