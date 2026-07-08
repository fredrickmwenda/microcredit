@extends('core::layouts.master')

@section('title')
    Reports
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        Reports
                        <a href="#" onclick="window.history.back()"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>Back</span>
                        </a>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content" id="reportDashboard">
        <div class="container-fluid">
            {{-- Filter & View Controls --}}
            <div class="row mb-4 align-items-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary active" data-view="grid" id="gridViewBtn">
                            <i class="ri-grid-line"></i> Grid
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-view="list" id="listViewBtn">
                            <i class="ri-list-unordered"></i> List
                        </button>
                    </div>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <select id="categoryFilter" class="form-control">
                        <option value="all">All Categories</option>
                        <option value="savings">Savings</option>
                        <option value="loan">Loan</option>
                        <option value="accounting">Accounting</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                        </div>
                        <input type="text" id="searchReports" class="form-control" placeholder="Search reports...">
                    </div>
                </div>
            </div>

            {{-- Reports Container --}}
            <div id="reportsContainer">
                {{-- Grid View --}}
                <div class="report-grid-view row">
                    @php
                        // Define all reports with category, name, description, url, permission
                        $reports = [
                            // Savings Reports
                            ['category' => 'Savings', 'category_slug' => 'savings', 'name' => 'Transactions', 'description' => 'View all savings transactions with details and filters.', 'url' => 'report/savings/transaction', 'permission' => 'savings.savings.reports.transactions'],
                            ['category' => 'Savings', 'category_slug' => 'savings', 'name' => 'Balances', 'description' => 'Shows current balances of all savings accounts.', 'url' => 'report/savings/balance', 'permission' => 'savings.savings.reports.balances'],
                            ['category' => 'Savings', 'category_slug' => 'savings', 'name' => 'Savings Accounts', 'description' => 'List of all savings accounts with their status and details.', 'url' => 'report/savings/account', 'permission' => 'savings.savings.reports.accounts'],
                            ['category' => 'Savings', 'category_slug' => 'savings', 'name' => 'Account Statement', 'description' => 'Detailed account statement for a specific savings account.', 'url' => 'report/savings/account_statement', 'permission' => 'savings.savings.reports.income_statement'],
                            ['category' => 'Savings', 'category_slug' => 'savings', 'name' => 'Savings Deposit Report', 'description' => 'View all savings deposits with filters by client, date, and status.', 'url' => 'savings/deposit_report', 'permission' => 'savings.savings.reports.deposit'],
                            ['category' => 'Savings', 'category_slug' => 'savings', 'name' => 'Daily Savings  Report', 'description' => 'Daily summary of savings deposits, withdrawals, and net movement.', 'url' => 'savings/daily', 'permission' => 'savings.savings.reports.daily'],
                            ['category' => 'Savings', 'category_slug' => 'savings', 'name' => 'Staff Savings Summary', 'description' => 'Aggregated savings deposits and withdrawals per savings officer with drill‑down details.', 'url' => 'report/savings/staff-summary', 'permission' => 'savings.savings.reports.staff_summary'],
                            ['category' => 'Savings', 'category_slug' => 'savings', 'name' => 'Savings Officer Change Report', 'description' => 'Audit report of savings officer changes with details on old/new officers and change dates.', 'url' => 'report/savings-officer-changes', 'permission' => 'savings.officer.change.report.index'],


                            // Loan Reports
                            ['category' => 'Loan', 'category_slug' => 'loan', 'name' => 'Collection Sheet', 'description' => 'Shows expected collections and actual payments per loan officer.', 'url' => 'report/loan/collection_sheet', 'permission' => 'loan.loans.reports.collection_sheet'],
                            ['category' => 'Loan', 'category_slug' => 'loan', 'name' => 'Repayments', 'description' => 'Lists all loan repayments made within a period.', 'url' => 'report/loan/repayment', 'permission' => 'loan.loans.reports.repayments'],
                            ['category' => 'Loan', 'category_slug' => 'loan', 'name' => 'Expected Repayments', 'description' => 'Shows future expected repayments for all active loans.', 'url' => 'report/loan/expected_repayment', 'permission' => 'loan.loans.reports.expected_repayments'],
                            ['category' => 'Loan', 'category_slug' => 'loan', 'name' => 'Today Expected Repayments', 'description' => 'Expected repayments vs actual repayments within today.', 'url' => 'report/loan/specific_expected_repayment', 'permission' => 'loan.loans.reports.today_expected_repayments'],
                            ['category' => 'Loan', 'category_slug' => 'loan', 'name' => 'Arrears', 'description' => 'Loans that are overdue with aging analysis.', 'url' => 'report/loan/arrears', 'permission' => 'loan.loans.reports.arrears'],
                            ['category' => 'Loan', 'category_slug' => 'loan', 'name' => 'Disbursements', 'description' => 'List of loan disbursements within a date range.', 'url' => 'report/loan/disbursement', 'permission' => 'loan.loans.reports.disbursements'],
                            ['category' => 'Loan', 'category_slug' => 'loan', 'name' => 'Daily Collection Report', 'description' => 'Daily summary of loan collections & disbursements.', 'url' => 'report/loan/daily_collection', 'permission' => 'loan.loans.reports.daily_collection'],
                            ['category' => 'Loan', 'category_slug' => 'loan', 'name' => 'Loan Officer Change Report', 'description' => 'Audit report of loan officer changes with details on old/new officers and change dates.', 'url' => 'report/loan-officer-changes', 'permission' => 'loan.officer.change.report.index'],
                            
                            // Accounting Reports
                            ['category' => 'Accounting', 'category_slug' => 'accounting', 'name' => 'Balance Sheet', 'description' => 'Shows assets, liabilities, and equity at a specific date.', 'url' => 'report/accounting/balance_sheet', 'permission' => 'accounting.reports.balance_sheet'],
                            ['category' => 'Accounting', 'category_slug' => 'accounting', 'name' => 'Trial Balance', 'description' => 'Lists all ledger accounts with their debit/credit balances.', 'url' => 'report/accounting/trial_balance', 'permission' => 'accounting.reports.trial_balance'],
                            ['category' => 'Accounting', 'category_slug' => 'accounting', 'name' => 'Income Statement', 'description' => 'Profit and loss report for a given period.', 'url' => 'report/accounting/income_statement', 'permission' => 'accounting.reports.income_statement'],
                            ['category' => 'Accounting', 'category_slug' => 'accounting', 'name' => 'Ledger', 'description' => 'Detailed general ledger entries for any account.', 'url' => 'report/accounting/ledger', 'permission' => 'accounting.reports.ledger'],
                            
                            // User Reports
                            ['category' => 'User', 'category_slug' => 'user', 'name' => 'Performance Report', 'description' => 'User activity and performance metrics.', 'url' => 'report/user/performance', 'permission' => 'user.reports.performance'],
                        ];
                        
                        // Reports that bypass permission checks (always shown)
                        $alwaysShow = ['Savings Deposit Report', 'Daily Savings Report', 'Today Expected Repayments', 'Staff Savings Summary', 'Daily Collection Report'];
                    @endphp

                    @foreach($reports as $report)
                        @if(in_array($report['name'], $alwaysShow))
                            {{-- Always show these reports without permission check --}}
                            <div class="col-md-6 col-lg-3 mb-4 report-item"
                                 data-category="{{ $report['category_slug'] }}"
                                 data-report-name="{{ strtolower($report['name']) }}">
                                <div class="card h-100 shadow-sm border-0 rounded-3">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <i class="ri-file-list-line fs-2 text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="card-title mb-0">{{ $report['name'] }}</h5>
                                                <small class="text-muted">{{ $report['category'] }}</small>
                                            </div>
                                        </div>
                                        <p class="card-text text-muted small flex-grow-1">
                                            {{ $report['description'] }}
                                        </p>
                                        <a href="{{ url($report['url']) }}" class="btn btn-sm btn-outline-primary mt-2 stretched-link">
                                            View Report <i class="ri-arrow-right-line"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            @can($report['permission'])
                                <div class="col-md-6 col-lg-3 mb-4 report-item"
                                     data-category="{{ $report['category_slug'] }}"
                                     data-report-name="{{ strtolower($report['name']) }}">
                                    <div class="card h-100 shadow-sm border-0 rounded-3">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="flex-shrink-0">
                                                    <i class="ri-file-list-line fs-2 text-primary"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="card-title mb-0">{{ $report['name'] }}</h5>
                                                    <small class="text-muted">{{ $report['category'] }}</small>
                                                </div>
                                            </div>
                                            <p class="card-text text-muted small flex-grow-1">
                                                {{ $report['description'] }}
                                            </p>
                                            <a href="{{ url($report['url']) }}" class="btn btn-sm btn-outline-primary mt-2 stretched-link">
                                                View Report <i class="ri-arrow-right-line"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        @endif
                    @endforeach
                </div>

                {{-- List View (hidden by default) --}}
                <div class="report-list-view" style="display: none;">
                    <div class="list-group">
                        @foreach($reports as $report)
                            @if(in_array($report['name'], $alwaysShow))
                                <a href="{{ url($report['url']) }}"
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center report-item"
                                   data-category="{{ $report['category_slug'] }}"
                                   data-report-name="{{ strtolower($report['name']) }}">
                                    <div>
                                        <i class="ri-file-list-line me-2 text-primary"></i>
                                        {{ $report['name'] }}
                                        <span class="badge bg-secondary ms-2">{{ $report['category'] }}</span>
                                    </div>
                                    <i class="ri-arrow-right-s-line text-muted"></i>
                                </a>
                            @else
                                @can($report['permission'])
                                    <a href="{{ url($report['url']) }}"
                                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center report-item"
                                       data-category="{{ $report['category_slug'] }}"
                                       data-report-name="{{ strtolower($report['name']) }}">
                                        <div>
                                            <i class="ri-file-list-line me-2 text-primary"></i>
                                            {{ $report['name'] }}
                                            <span class="badge bg-secondary ms-2">{{ $report['category'] }}</span>
                                        </div>
                                        <i class="ri-arrow-right-s-line text-muted"></i>
                                    </a>
                                @endcan
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        (function() {
            const gridBtn = document.getElementById('gridViewBtn');
            const listBtn = document.getElementById('listViewBtn');
            const categoryFilter = document.getElementById('categoryFilter');
            const searchInput = document.getElementById('searchReports');
            const gridView = document.querySelector('.report-grid-view');
            const listView = document.querySelector('.report-list-view');
            const allItems = document.querySelectorAll('.report-item');

            let currentView = 'grid';

            function setView(view) {
                currentView = view;
                if (view === 'grid') {
                    gridView.style.display = '';
                    listView.style.display = 'none';
                    gridBtn.classList.add('active');
                    listBtn.classList.remove('active');
                } else {
                    gridView.style.display = 'none';
                    listView.style.display = '';
                    gridBtn.classList.remove('active');
                    listBtn.classList.add('active');
                }
                applyFilters();
            }

            function applyFilters() {
                const selectedCategory = categoryFilter.value;
                const searchTerm = searchInput.value.trim().toLowerCase();

                allItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');
                    const itemName = item.getAttribute('data-report-name');
                    const categoryMatch = (selectedCategory === 'all' || selectedCategory === itemCategory);
                    const searchMatch = searchTerm === '' || itemName.includes(searchTerm);
                    if (categoryMatch && searchMatch) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            gridBtn.addEventListener('click', () => setView('grid'));
            listBtn.addEventListener('click', () => setView('list'));
            categoryFilter.addEventListener('change', applyFilters);
            searchInput.addEventListener('input', applyFilters);

            // Initialize
            setView('grid');
        })();
    </script>

    <style>
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background-color: #2c3e50; /* Dark background */
            color: #ffffff; /* White text for all card content */
            border: none;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3) !important;
        }
        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #ffffff !important;
        }
        .card .text-muted {
            color: #d1d8e0 !important; /* Lighter gray for category */
        }
        .card-text {
            color: #ecf0f1 !important;
        }
        .fs-2 {
            font-size: 1.75rem;
            color: #ffffff !important;
        }
        .list-group-item {
            transition: background-color 0.2s;
        }
        .list-group-item:hover {
            background-color: #f8f9fa;
        }
        .btn-outline-primary {
            border-radius: 20px;
            border-color: #ffffff;
            color: #ffffff;
        }
        .btn-outline-primary:hover {
            background-color: #ffffff;
            color: #2c3e50;
            border-color: #ffffff;
        }
        /* Make all links inside cards white, including all states */
        .card a,
        .card a:link,
        .card a:visited,
        .card a:hover,
        .card a:active,
        .stretched-link,
        .stretched-link:link,
        .stretched-link:visited,
        .stretched-link:hover,
        .stretched-link:active {
            color: #000000 !important;
            text-decoration: none;
        }
        .stretched-link::after {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1;
            content: "";
        }
        .card-body {
            position: relative;
        }
        .badge.bg-secondary {
            background-color: #6c757d;
        }
        /* Adjust icon color */
        .card .ri-file-list-line {
            color: #ffffff !important;
        }
    </style>
@endsection