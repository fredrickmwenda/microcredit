@extends('core::layouts.master')
@section('title')
    Create Bulk Savings Entry
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        Create Bulk Entry for Officer's Clients
                        <a href="#" onclick="window.history.back()"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                        </a>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a></li>
                        <li class="breadcrumb-item"><a href="{{url('entry/savings/bulk_entry')}}">Bulk Entry</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content" id="app">
        <form method="post" action="{{ url('entry/savings/bulk_entry/store') }}" id="bulkForm">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="savings_officer_id" class="control-label">Select Officer</label>
                                <select class="form-control @error('savings_officer_id') is-invalid @enderror" 
                                        name="savings_officer_id" id="savings_officer_id" v-model="officer_id" 
                                        @change="loadOfficerClients">
                                    <option value="">-- Select Officer --</option>
                                    @foreach($savings_officers as $officer)
                                        <option value="{{ $officer->id }}" 
                                                {{ $selected_officer_id == $officer->id ? 'selected' : '' }}>
                                            {{ $officer->first_name }} {{ $officer->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('savings_officer_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if(!empty($clients))
                        <div class="row">
                            <div class="col-md-12">

                                <!-- Tabs for Deposit and Withdrawal -->
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link" :class="{active: activeTab === 'deposit'}" @click.prevent="setTab('deposit')" href="#" role="tab">Deposit</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" :class="{active: activeTab === 'withdrawal'}" @click.prevent="setTab('withdrawal')" href="#" role="tab">Withdrawal</a>
                                    </li>
                                </ul>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Client Name</th>
                                                <th>Account Number</th>
                                                <th>Balance</th>
                                                <th>Amount</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody id="entriesTable">
                                            <tr v-for="(entry, index) in entries" :key="index">
                                                <td>@{{ entry.client_name }}</td>
                                                <td>@{{ entry.account_number }}</td>
                                                <td>@{{ formatCurrency(entry.balance) }}</td>
                                                <td>
                                                    <input type="number" v-model="entry.amount" step="0.01" min="0" class="form-control form-control-sm" placeholder="0.00">
                                                </td>
                                                <td>
                                                    <input type="text" v-model="entry.notes" class="form-control form-control-sm" placeholder="Notes">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="alert alert-info">
                                    <strong>Total Deposits:</strong> @{{ formatCurrency(totalDeposits) }}<br>
                                    <strong>Total Withdrawals:</strong> @{{ formatCurrency(totalWithdrawals) }}
                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields for form submission -->
                        <div v-for="(entry, index) in filteredEntries" :key="'hidden_' + index">
                            <input type="hidden" :name="'entries[' + index + '][savings_id]'" :value="entry.savings_id">
                            <input type="hidden" :name="'entries[' + index + '][transaction_type]'" :value="activeTab">
                            <input type="hidden" :name="'entries[' + index + '][amount]'" :value="entry.amount">
                            <input type="hidden" :name="'entries[' + index + '][notes]'" :value="entry.notes">
                        </div>
                    @else
                        @if($selected_officer_id)
                            <div class="alert alert-warning mt-3">
                                This officer has no active clients or savings accounts.
                            </div>
                        @else
                            <div class="alert alert-info mt-3">
                                Please select an officer to view their clients' savings accounts.
                            </div>
                        @endif
                    @endif
                </div>

                @if(!empty($clients))
                    <div class="card-footer border-top">
                        <button type="submit" class="btn btn-primary float-right" :disabled="filteredEntries.length === 0">
                            {{ trans_choice('core::general.save',1) }}
                        </button>
                    </div>
                @endif
            </div>
        </form>
    </section>
@endsection

@section('scripts')
<script>
    if (document.getElementById('app')) {
        var app = new Vue({
            el: "#app",
            data: {
                officer_id: "{{ $selected_officer_id ?? '' }}",
                activeTab: 'deposit',
                entries: [
                    @foreach($clients as $client)
                        @foreach($client->savings as $saving)
                            {
                                savings_id: {{ $saving->id }},
                                client_id: {{ $client->id }},
                                client_name: "{{ $client->first_name }} {{ $client->last_name }}",
                                account_number: "{{ $saving->account_number }}",
                                balance: {{ $savings_data[$saving->id]['balance'] ?? 0 }},
                                amount: "",
                                notes: ""
                            },
                        @endforeach
                    @endforeach
                ]
            },
            computed: {
                filteredEntries() {
                    // Only entries with amount > 0
                    return this.entries.filter(e => e.amount && parseFloat(e.amount) > 0);
                },
                totalDeposits() {
                    if (this.activeTab === 'deposit') {
                        return this.filteredEntries.reduce((sum, e) => sum + (parseFloat(e.amount) || 0), 0);
                    }
                    return 0;
                },
                totalWithdrawals() {
                    if (this.activeTab === 'withdrawal') {
                        return this.filteredEntries.reduce((sum, e) => sum + (parseFloat(e.amount) || 0), 0);
                    }
                    return 0;
                }
            },
            methods: {
                setTab(tab) {
                    this.activeTab = tab;
                },
                loadOfficerClients() {
                    if (this.officer_id) {
                        window.location.href = "{{ url('entry/savings/bulk_entry/create') }}?officer_id=" + this.officer_id;
                    }
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'GHS',
                        minimumFractionDigits: 2
                    }).format(value);
                }
            }
        });
    }
</script>
@endsection
