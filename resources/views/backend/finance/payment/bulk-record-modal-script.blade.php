<script>
let classStudents = {};

$(document).ready(function() {
    // Class change - load students (both native change and select2 events)
    $('#bulkPaymentClass').on('change select2:select', function() {
        const classId = $(this).val();
        if (!classId) {
            $('#studentsPaymentContainer').hide();
            return;
        }

        loadStudentsForClass(classId);
    });

    // Add payment row button
    $(document).on('click', '#addPaymentRowBtn', function() {
        addPaymentRow('', '', '', '');
    });

    // Remove payment row
    $(document).on('click', '.removePaymentRow', function() {
        $(this).closest('tr').remove();
        updatePaymentButton();
    });

    // Update button state when values change
    $(document).on('change input', '.amount, .date, .method', function() {
        updatePaymentButton();
    });

    // Record payments
    $('#bulkRecordPaymentsBtn').on('click', function() {
        recordAllPayments();
    });
});

function loadStudentsForClass(classId) {
    $.ajax({
        url: '{{ route("finance.get_class_students") }}',
        method: 'POST',
        data: {
            class_id: classId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success && response.students && response.students.length > 0) {
                classStudents = response.students;
                renderStudentRows(response.students);
                $('#selectedClassName').text($('#bulkPaymentClass option:selected').text());
                $('#studentsPaymentContainer').show();
            } else if (response.success && (!response.students || response.students.length === 0)) {
                swal('No Students', 'No students found in this class for the current academic year.', 'warning');
                $('#studentsPaymentContainer').hide();
            } else {
                swal('Error', 'Error loading students: ' + (response.message || 'Unknown error'), 'error');
            }
        },
        error: function(xhr, status, error) {
            swal('Error', 'Error loading students: ' + error, 'error');
        }
    });
}

function renderStudentRows(students) {
    const $container = $('#paymentRowsContainer');
    $container.html('');

    students.forEach(function(student) {
        addPaymentRow(student.id, student.name, '', '');
    });

    updatePaymentButton();
}

function addPaymentRow(studentId, studentName, amount, date) {
    const $container = $('#paymentRowsContainer');
    const rowId = 'payment_' + Date.now() + Math.random();

    const $row = $(`
        <tr class="payment-row" data-row-id="${rowId}">
            <td>
                <input type="hidden" class="student-id" value="${studentId}">
                <span class="student-name">${studentName || 'Select Student'}</span>
                <select class="form-control student-select" style="display: none; width: 100%;">
                    <option value="">Choose student...</option>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" class="form-control amount" placeholder="Amount" value="${amount}">
            </td>
            <td>
                <input type="text" class="form-control date datepicker" placeholder="DD/MM/YYYY" value="${date}">
            </td>
            <td>
                <select class="form-control method">
                    <option value="">Select method</option>
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="momo">Mobile Money</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger removePaymentRow">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `);

    $container.append($row);

    // Initialize datepicker
    $row.find('.datepicker').datetimepicker({
        format: 'DD/MM/YYYY',
        viewMode: 'days',
        ignoreReadonly: true
    });

    // If no student ID, show dropdown
    if (!studentId) {
        const $select = $row.find('.student-select');
        classStudents.forEach(function(student) {
            $select.append(`<option value="${student.id}">${student.name}</option>`);
        });
        $select.show();
        $row.find('.student-name').hide();

        $select.on('change', function() {
            const selected = $(this).find('option:selected');
            $row.find('.student-id').val($(this).val());
            $row.find('.student-name').text(selected.text()).show();
            $(this).hide();
            updatePaymentButton();
        });
    }
}

function updatePaymentButton() {
    const $container = $('#paymentRowsContainer');
    let hasValidRows = false;

    $container.find('tr').each(function() {
        const studentId = $(this).find('.student-id').val();
        const amount = $(this).find('.amount').val();
        const date = $(this).find('.date').val();
        const method = $(this).find('.method').val();

        if (studentId && amount && date && method) {
            hasValidRows = true;
        }
    });

    $('#bulkRecordPaymentsBtn').prop('disabled', !hasValidRows);
}

function recordAllPayments() {
    const classId = $('#bulkPaymentClass').val();
    const payments = [];

    $('#paymentRowsContainer').find('tr').each(function() {
        const studentId = $(this).find('.student-id').val();
        const amount = $(this).find('.amount').val();
        const date = $(this).find('.date').val();
        const method = $(this).find('.method').val();

        if (studentId && amount && date && method) {
            payments.push({
                student_id: studentId,
                amount: amount,
                payment_date: date,
                payment_method: method
            });
        }
    });

    if (!payments.length) {
        swal('No Payments', 'Please fill in at least one payment record.', 'warning');
        return;
    }

    $('#bulkRecordPaymentsBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Recording...');

    $.ajax({
        url: '{{ route("finance.record_bulk_payments") }}',
        method: 'POST',
        data: {
            class_id: classId,
            payments: payments,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                swal('Success', response.message, 'success');
                $('#bulkRecordPaymentsModal').modal('hide');
                setTimeout(() => location.reload(), 1000);
            } else {
                swal('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'An error occurred';
            swal('Error', msg, 'error');
        },
        complete: function() {
            $('#bulkRecordPaymentsBtn').prop('disabled', false).html('<i class="fa fa-save"></i> Record All Payments');
        }
    });
}
</script>
