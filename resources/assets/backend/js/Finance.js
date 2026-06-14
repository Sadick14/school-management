import Chart from 'chart.js';

export default class Finance {
    static quickPayInit() {
        Finance.quickPayState = {
            registration: null,
            duesData: null,
            optionalFees: [],
            feeGroups: [],
            activeTerm: null,
            paymentMethod: null,
            currentStep: 1,
        };

        let searchTimer = null;
        $('#studentSearch').on('input', function () {
            const term = $(this).val().trim();
            clearTimeout(searchTimer);
            if (term.length < 1) {
                $('#searchResults').hide().empty();
                return;
            }
            searchTimer = setTimeout(() => Finance.searchStudents(term, $('#filterClass').val()), 300);
        });

        $('#filterClass').on('change', function () {
            const term = $('#studentSearch').val().trim();
            if (term.length >= 1) {
                Finance.searchStudents(term, $(this).val());
            }
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.student-search-wrap').length) {
                $('#searchResults').hide();
            }
        });

        $('#changeStudentBtn').on('click', Finance.resetStudent);

        $('#recalcBtn').on('click', Finance.loadDues);

        $('#feeRowsBody').on('input', '.pay-now-input', Finance.updateTotal);

        $('#toStep2Btn').on('click', function () {
            Finance.goToStep(2);
        });

        $('#backToStep1Btn').on('click', function () {
            Finance.goToStep(1);
        });

        $('#toStep3Btn').on('click', function () {
            Finance.renderReview();
            Finance.goToStep(3);
        });

        $('#backToStep2Btn').on('click', function () {
            Finance.goToStep(2);
        });

        $('#paymentMethodButtons').on('click', '.payment-method-btn', function () {
            $('#paymentMethodButtons .payment-method-btn').removeClass('selected');
            $(this).addClass('selected');
            Finance.quickPayState.paymentMethod = $(this).data('method');
            Finance.renderReview();
        });

        $('#payment_date').on('change', Finance.renderReview);

        $('#collectPaymentBtn').on('click', Finance.confirmAndSubmit);
    }

    static searchStudents(term, classId) {
        axios.get(window.finance_search_students_url, { params: { q: term, class_id: classId || null } })
            .then((response) => {
                const $results = $('#searchResults');
                $results.empty();

                if (!response.data.length) {
                    $results.append('<div class="result-item text-muted">No matching students found.</div>');
                    $results.show();
                    return;
                }

                response.data.forEach((student) => {
                    const meta = [student.regi_no, student.class, student.section].filter(Boolean).join(' · ');
                    const $item = $(`
                        <div class="result-item">
                            <div class="res-name">${student.name}</div>
                            <div class="res-meta">${meta}</div>
                        </div>
                    `);
                    $item.on('click', () => Finance.selectStudent(student));
                    $results.append($item);
                });

                $results.show();
            });
    }

    static selectStudent(student) {
        Finance.quickPayState.registration = student;
        Finance.quickPayState.optionalFees = [];
        Finance.quickPayState.feeGroups = [];

        $('#searchResults').hide().empty();
        $('#studentSearch').val(student.name);

        $('#selStudentName').text(student.name);
        $('#selStudentMeta').text([student.regi_no, student.class, student.section].filter(Boolean).join(' · '));
        $('#selectedStudentCard').show();
        $('#toStep2Btn').prop('disabled', false);

        Finance.loadDues();
    }

    static resetStudent() {
        Finance.quickPayState.registration = null;
        Finance.quickPayState.duesData = null;
        Finance.quickPayState.optionalFees = [];
        Finance.quickPayState.feeGroups = [];
        Finance.quickPayState.activeTerm = null;
        Finance.quickPayState.paymentMethod = null;

        $('#studentSearch').val('').trigger('focus');
        $('#selectedStudentCard').hide();
        $('#termSummaryWrap').hide();
        $('#toStep2Btn').prop('disabled', true);
        $('#feeRowsBody').empty();
        $('#optionalFeesContainer').empty();
        $('#noDues').hide();
        $('#creditNote').hide();
        $('#paymentMethodButtons .payment-method-btn').removeClass('selected');

        Finance.goToStep(1);
    }

    static loadDues() {
        const registration = Finance.quickPayState.registration;
        if (!registration) {
            return;
        }

        const optionalFees = Finance.quickPayState.optionalFees || [];

        axios.post(window.finance_dues_url, {
            registration_ids: [registration.id],
            feeding_from: Finance.formatDateForServer($('#feeding_from').val()),
            feeding_to: Finance.formatDateForServer($('#feeding_to').val()),
            optional_fees: optionalFees,
        }).then((response) => {
            const studentData = response.data.students[0] || { items: [], total_due: 0, total_credit: 0, total_expected: 0, total_paid: 0 };
            Finance.quickPayState.duesData = studentData;
            Finance.quickPayState.activeTerm = response.data.active_term;

            $('#activeTermBadge').text('Active Term: ' + (response.data.active_term || 'N/A'));
            $('#sumExpected').text(parseFloat(studentData.total_expected || 0).toFixed(2));
            $('#sumPaid').text(parseFloat(studentData.total_paid || 0).toFixed(2));
            $('#sumOutstanding').text(parseFloat(studentData.total_due || 0).toFixed(2));
            $('#termSummaryWrap').show();

            Finance.renderOptionalFees(response.data.optional_fees);
            Finance.renderFeeTable(studentData);
        }).catch((error) => {
            const msg = error.response && error.response.data && error.response.data.message
                ? error.response.data.message
                : 'Failed to load dues.';
            swal('Error', msg, 'error');
        });
    }

    static renderOptionalFees(fees) {
        const $container = $('#optionalFeesContainer');
        $container.empty();
        if (!fees || !fees.length) {
            return;
        }

        $container.append('<label class="text-muted" style="display:block;margin-bottom:5px;">Add optional fee:</label>');
        fees.forEach((fee) => {
            const $btn = $(`<button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus"></i> ${fee.name}</button>`);
            $btn.on('click', () => {
                Finance.quickPayState.optionalFees.push(fee.id);
                Finance.loadDues();
            });
            $container.append($btn);
        });
    }

    static renderFeeTable(student) {
        const $body = $('#feeRowsBody');
        $body.empty();

        const groups = {};
        const order = [];

        (student.items || []).forEach((item) => {
            const key = item.fee_type || 'Other';
            if (!groups[key]) {
                groups[key] = {
                    feeType: key,
                    isFeeding: item.fee_type_code === 'FEEDING',
                    expected: 0,
                    paid: 0,
                    balance: 0,
                    ledgers: [],
                };
                order.push(key);
            }
            groups[key].expected += parseFloat(item.amount) || 0;
            groups[key].paid += parseFloat(item.amount_paid) || 0;
            groups[key].balance += parseFloat(item.balance) || 0;
            groups[key].ledgers.push({ ledger_id: item.ledger_id, balance: parseFloat(item.balance) || 0 });
        });

        const feeGroups = order.map((key) => groups[key]);
        Finance.quickPayState.feeGroups = feeGroups;

        const hasDues = feeGroups.some((g) => g.balance > 0);
        $('#noDues').toggle(!hasDues);

        feeGroups.forEach((group, index) => {
            const payNow = group.balance > 0 ? group.balance : 0;
            const maxAttr = group.isFeeding ? '' : `max="${Math.max(group.balance, 0).toFixed(2)}"`;
            const $row = $(`
                <tr data-group-index="${index}">
                    <td>${group.feeType}</td>
                    <td>${group.expected.toFixed(2)}</td>
                    <td>${group.paid.toFixed(2)}</td>
                    <td>${group.balance.toFixed(2)}</td>
                    <td>
                        <input type="number" step="0.01" min="0" ${maxAttr}
                            class="form-control input-sm pay-now-input" value="${payNow.toFixed(2)}">
                    </td>
                </tr>
            `);
            $body.append($row);
        });

        if (student.total_credit > 0) {
            $('#creditNote').show().text(`Student has a credit balance of GHS ${parseFloat(student.total_credit).toFixed(2)} available.`);
        } else {
            $('#creditNote').hide();
        }

        Finance.updateTotal();
    }

    static updateTotal() {
        let total = 0;
        $('#feeRowsBody .pay-now-input').each(function () {
            total += parseFloat($(this).val()) || 0;
        });

        $('#totalAmount').text(total.toFixed(2));
        $('#toStep3Btn').prop('disabled', total <= 0);
    }

    static goToStep(step) {
        Finance.quickPayState.currentStep = step;

        $('.step-panel').removeClass('active');
        $(`.step-panel[data-step="${step}"]`).addClass('active');

        $('.payment-step').each(function () {
            const stepNum = parseInt($(this).data('step'), 10);
            $(this).removeClass('active done');
            if (stepNum < step) {
                $(this).addClass('done');
            } else if (stepNum === step) {
                $(this).addClass('active');
            }
        });
    }

    static renderReview() {
        const state = Finance.quickPayState;

        $('#reviewStudent').text(state.registration ? state.registration.name : '');
        $('#reviewTerm').text(state.activeTerm || 'N/A');
        $('#reviewMethod').text(state.paymentMethod
            ? $(`.payment-method-btn[data-method="${state.paymentMethod}"]`).text().trim()
            : '-');
        $('#reviewDate').text($('#payment_date').val());
        $('#reviewTotal').text($('#totalAmount').text());
    }

    static confirmAndSubmit() {
        const state = Finance.quickPayState;

        if (!state.paymentMethod) {
            swal('Select Method', 'Please select a payment method.', 'warning');
            return;
        }

        let total = 0;
        $('#feeRowsBody .pay-now-input').each(function () {
            total += parseFloat($(this).val()) || 0;
        });

        if (total <= 0) {
            swal('No Payment', 'Enter an amount to pay.', 'warning');
            return;
        }

        swal({
            title: 'Confirm Payment',
            text: `Collect GHS ${total.toFixed(2)} from ${state.registration.name}?`,
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Collect Payment',
        }).then((result) => {
            if (result && (result.value || result === true)) {
                Finance.submitPayment();
            }
        }).catch(() => {});
    }

    static submitPayment() {
        const state = Finance.quickPayState;
        const items = [];

        $('#feeRowsBody tr').each(function () {
            const group = state.feeGroups[parseInt($(this).data('group-index'), 10)];
            let payNow = parseFloat($(this).find('.pay-now-input').val()) || 0;

            if (payNow <= 0 || !group) {
                return;
            }

            if (group.isFeeding) {
                const ledger = group.ledgers[0];
                if (ledger) {
                    items.push({ ledger_id: ledger.ledger_id, amount: payNow });
                }
                return;
            }

            group.ledgers.forEach((ledger) => {
                if (payNow <= 0 || ledger.balance <= 0) {
                    return;
                }

                const amountApplied = Math.min(payNow, ledger.balance);
                items.push({ ledger_id: ledger.ledger_id, amount: amountApplied });
                payNow -= amountApplied;
            });
        });

        if (!items.length) {
            swal('No Payment', 'Enter an amount to pay.', 'warning');
            return;
        }

        axios.post(window.finance_store_url, {
            academic_year_id: state.registration.academic_year_id,
            registration_id: state.registration.id,
            student_id: state.registration.student_id,
            payment_date: $('#payment_date').val(),
            payment_method: state.paymentMethod,
            paid_by: $('#paid_by').val(),
            items: items,
        }).then((response) => {
            swal('Success', 'Payment recorded. Receipt: ' + response.data.receipt_no, 'success').then(() => {
                if (response.data.receipt_url) {
                    window.open(response.data.receipt_url, '_blank');
                }
                Finance.resetStudent();
            });
        }).catch((error) => {
            const msg = error.response && error.response.data && error.response.data.message
                ? error.response.data.message
                : 'Payment failed.';
            swal('Error', msg, 'error');
        });
    }

    static formatDateForServer(displayDate) {
        if (!displayDate) {
            return null;
        }
        const parts = displayDate.split('/');
        if (parts.length !== 3) {
            return displayDate;
        }
        return `${parts[2]}-${parts[1]}-${parts[0]}`;
    }

    static expenseChartInit() {
        const el = document.getElementById('expenseChart');
        if (!el || !window.expenseChartLabels) {
            return;
        }
        new Chart(el.getContext('2d'), {
            type: 'pie',
            data: {
                labels: window.expenseChartLabels,
                datasets: [{
                    data: window.expenseChartValues,
                    backgroundColor: ['#3c8dbc', '#00a65a', '#f39c12', '#dd4b39', '#605ca8', '#001f3f', '#d81b60', '#39cccc'],
                }],
            },
            options: { responsive: true },
        });
    }

    static reportInit() {
        const trendEl = document.getElementById('financeTrendChart');
        if (trendEl) {
            const labels = window.financeTrendLabels || [];
            new Chart(trendEl.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Revenue', data: window.financeRevenueData || [], backgroundColor: '#3c8dbc' },
                        { label: 'Expenses', data: window.financeExpenseData || [], backgroundColor: '#dd4b39' },
                    ],
                },
                options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true } }] } },
            });
        }

        const catEl = document.getElementById('financeCategoryChart');
        if (catEl) {
            new Chart(catEl.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: window.financeCategoryLabels || [],
                    datasets: [{
                        data: window.financeCategoryData || [],
                        backgroundColor: ['#3c8dbc', '#00a65a', '#f39c12', '#dd4b39', '#605ca8', '#001f3f'],
                    }],
                },
                options: { responsive: true },
            });
        }
    }

    static dashboardInit() {
        const trendEl = document.getElementById('financeDashboardTrend');
        if (trendEl && window.financeDashboardTrendLabels) {
            new Chart(trendEl.getContext('2d'), {
                type: 'line',
                data: {
                    labels: window.financeDashboardTrendLabels,
                    datasets: [
                        { label: 'Revenue', data: window.financeDashboardRevenue, borderColor: '#3c8dbc', fill: false },
                        { label: 'Expenses', data: window.financeDashboardExpenses, borderColor: '#dd4b39', fill: false },
                    ],
                },
                options: { responsive: true },
            });
        }

        const catEl = document.getElementById('financeDashboardCategory');
        if (catEl && window.financeDashboardCategoryLabels) {
            new Chart(catEl.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: window.financeDashboardCategoryLabels,
                    datasets: [{
                        data: window.financeDashboardCategoryData,
                        backgroundColor: ['#3c8dbc', '#00a65a', '#f39c12', '#dd4b39', '#605ca8', '#001f3f'],
                    }],
                },
                options: { responsive: true },
            });
        }
    }
}

window.Finance = Finance;
