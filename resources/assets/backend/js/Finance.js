import Chart from 'chart.js';

export default class Finance {
    static quickPayInit() {
        Finance.quickPayState = {
            registration: null,
            duesData: null,
            optionalFees: [],
            selectedAmounts: {},
        };

        let searchTimer = null;
        $('#studentSearch').on('input', function () {
            const term = $(this).val().trim();
            clearTimeout(searchTimer);
            if (term.length < 1) {
                $('#searchResults').hide().empty();
                return;
            }
            searchTimer = setTimeout(() => Finance.searchStudents(term), 300);
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.student-search-wrap').length) {
                $('#searchResults').hide();
            }
        });

        $('#changeStudentBtn').on('click', Finance.resetStudent);

        $('#feeCards').on('click', '.fee-card', function () {
            Finance.toggleFeeCard($(this));
        });

        $('#feeCards').on('input', '.pay-amount', function (e) {
            e.stopPropagation();
            const ledgerId = $(this).closest('.fee-card').data('ledger');
            Finance.quickPayState.selectedAmounts[ledgerId] = parseFloat($(this).val()) || 0;
            Finance.updateTotal();
        });

        $('#feeCards').on('click', '.pay-amount', function (e) {
            e.stopPropagation();
        });

        $('#recalcBtn').on('click', Finance.loadDues);

        $('#collectPaymentBtn').on('click', Finance.confirmAndSubmit);
    }

    static searchStudents(term) {
        axios.get(window.finance_search_students_url, { params: { q: term } })
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
        Finance.quickPayState.selectedAmounts = {};
        Finance.quickPayState.optionalFees = [];

        $('#searchResults').hide().empty();
        $('#studentSearch').val(student.name);

        $('#selStudentName').text(student.name);
        $('#selStudentMeta').text([student.regi_no, student.class, student.section].filter(Boolean).join(' · '));
        $('#studentPanel').show();

        Finance.loadDues();
    }

    static resetStudent() {
        Finance.quickPayState.registration = null;
        Finance.quickPayState.duesData = null;
        Finance.quickPayState.selectedAmounts = {};
        Finance.quickPayState.optionalFees = [];
        $('#studentSearch').val('').trigger('focus');
        $('#studentPanel').hide();
        $('#feeCards').empty();
        $('#optionalFeesContainer').empty();
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
            const studentData = response.data.students[0] || { items: [], total_due: 0, total_credit: 0 };
            Finance.quickPayState.duesData = studentData;
            Finance.renderOptionalFees(response.data.optional_fees);
            Finance.renderFeeCards(studentData);
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

    static renderFeeCards(student) {
        const $container = $('#feeCards');
        $container.empty();
        Finance.quickPayState.selectedAmounts = {};

        const dueItems = student.items.filter((item) => item.balance > 0);
        const creditItems = student.items.filter((item) => item.balance < 0);

        if (!dueItems.length) {
            $('#noDues').show();
        } else {
            $('#noDues').hide();
        }

        dueItems.forEach((item) => {
            Finance.quickPayState.selectedAmounts[item.ledger_id] = item.balance;

            const period = item.description || item.term || '';
            const $card = $(`
                <div class="fee-card selected" data-ledger="${item.ledger_id}" data-balance="${item.balance}">
                    <i class="fa fa-check-circle"></i>
                    <div class="fee-name">${item.fee_type}</div>
                    <div class="fee-desc">${period}</div>
                    <div class="fee-balance">GHS ${parseFloat(item.balance).toFixed(2)}</div>
                    <div class="fee-amount-row">
                        <input type="number" step="0.01" min="0" max="${item.balance}" class="form-control input-sm pay-amount" value="${parseFloat(item.balance).toFixed(2)}">
                    </div>
                </div>
            `);
            $container.append($card);
        });

        if (creditItems.length) {
            const totalCredit = student.total_credit || 0;
            $('#creditNote').show().text(`Student has a credit balance of GHS ${totalCredit.toFixed(2)} available.`);
        } else {
            $('#creditNote').hide();
        }

        Finance.updateTotal();
    }

    static toggleFeeCard($card) {
        const ledgerId = $card.data('ledger');
        const balance = parseFloat($card.data('balance'));

        if ($card.hasClass('selected')) {
            $card.removeClass('selected');
            delete Finance.quickPayState.selectedAmounts[ledgerId];
        } else {
            $card.addClass('selected');
            $card.find('.pay-amount').val(balance.toFixed(2));
            Finance.quickPayState.selectedAmounts[ledgerId] = balance;
        }

        Finance.updateTotal();
    }

    static updateTotal() {
        const total = Object.values(Finance.quickPayState.selectedAmounts)
            .reduce((sum, amount) => sum + (parseFloat(amount) || 0), 0);

        $('#totalAmount').text(total.toFixed(2));
        $('#collectPaymentBtn').prop('disabled', total <= 0);
    }

    static confirmAndSubmit() {
        const state = Finance.quickPayState;
        const total = Object.values(state.selectedAmounts).reduce((sum, a) => sum + (parseFloat(a) || 0), 0);

        if (total <= 0) {
            swal('No Payment', 'Select at least one fee to pay.', 'warning');
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
        const items = Object.keys(state.selectedAmounts)
            .map((ledgerId) => ({ ledger_id: parseInt(ledgerId, 10), amount: parseFloat(state.selectedAmounts[ledgerId]) }))
            .filter((item) => item.amount > 0);

        axios.post(window.finance_store_url, {
            academic_year_id: state.registration.academic_year_id,
            registration_id: state.registration.id,
            student_id: state.registration.student_id,
            payment_date: $('#payment_date').val(),
            payment_method: $('#payment_method').val(),
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
