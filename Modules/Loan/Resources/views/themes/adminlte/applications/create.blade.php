@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.loan_application_form',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.loan_application_form',1) }}
                        <a href="#" onclick="window.history.back()"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                        </a>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a></li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.loan_application',1) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <form method="post" action="{{ url('loan/application/apply') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-header" data-toggle="collapse" data-target="#personal-information-card" aria-expanded="true" style="cursor:pointer;">
                    <h3 class="card-title mb-0">{{ trans_choice('core::general.personal_information',1) }}</h3>
                    <span class="float-right"><i class="fa fa-chevron-down"></i></span>
                </div>
                <div id="personal-information-card" class="collapse show card-body">
                    <div class="form-group">
                        <label class="control-label">Client</label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-outline-primary {{ old('client_mode', 'new') === 'new' ? 'active' : '' }}">
                                <input type="radio" name="client_mode" value="new" {{ old('client_mode', 'new') === 'new' ? 'checked' : '' }}> New client
                            </label>
                            <label class="btn btn-outline-primary {{ old('client_mode') === 'existing' ? 'active' : '' }}">
                                <input type="radio" name="client_mode" value="existing" {{ old('client_mode') === 'existing' ? 'checked' : '' }}> Existing client
                            </label>
                        </div>
                    </div>

                    <div id="existing-client-section" class="form-group mt-3" style="display:none;">
                        <label for="client_id" class="control-label">Select existing client</label>
                        <select name="client_id" id="client_id" class="form-control">
                            <option value="">{{ trans_choice('core::general.select',1) }}</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                        data-client_id="{{ $client->id }}"
                                        data-first_name="{{ $client->first_name }}"
                                        data-middle_name="{{ $client->middle_name }}"
                                        data-last_name="{{ $client->last_name }}"
                                        data-gender="{{ $client->gender }}"
                                        data-dob="{{ $client->dob }}"
                                        data-country_id="{{ $client->country_id }}"
                                        data-phone_number="{{ $client->mobile ?: $client->phone }}"
                                        data-email="{{ $client->email }}"
                                        data-external_id="{{ $client->external_id }}"
                                        data-address="{{ $client->address }}"
                                        data-residential_address="{{ $client->address }}"
                                        {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ trim($client->first_name . ' ' . $client->middle_name . ' ' . $client->last_name) ?: $client->account_number }} ({{ $client->account_number ?? 'No account' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Choosing an existing client will prefill the personal information below.</small>
                    </div>

                    <div id="new-client-section">
                        <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name" class="control-label">{{trans_choice('core::general.first_name',1)}}</label>
                                <input type="text" name="first_name" id="first_name" v-model="first_name" class="form-control @error('first_name') is-invalid @enderror" required>
                                @error('first_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name" class="control-label">{{trans_choice('core::general.last_name',1)}}</label>
                                <input type="text" name="last_name" id="last_name" v-model="last_name" class="form-control @error('last_name') is-invalid @enderror" required>
                                @error('last_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender" class="control-label">{{ trans_choice('core::general.gender',1) }}</label>
                                <select class="form-control @error('gender') is-invalid @enderror" name="gender" id="gender" required>
                                    <option value="">{{ trans_choice('core::general.select',1) }}</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>{{ trans_choice('core::general.male',1) }}</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>{{ trans_choice('core::general.female',1) }}</option>
                                </select>
                                @error('gender')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dob" class="control-label">{{ trans_choice('core::general.dob',1) }}</label>
                                <input type="date" name="dob" id="date_of_birth" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob') }}" required>
                                @error('dob')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="country_id" class="control-label">{{ trans_choice('core::general.nationality',1) }}</label>
                                <!-- <input type="text" name="nationality" id="nationality" class="form-control @error('nationality') is-invalid @enderror" value="{{ old('nationality', 'Ghanaian') }}" required> -->
                                <select class="form-control @error('country_id') is-invalid @enderror" name="country_id" id="country_id">
                                    <option value="83">Ghana</option>
                                    @foreach($countries as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                              @enderror
                            </div>

                         
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ghana_card_number" class="control-label">{{ trans_choice('core::general.ghana_card_number',1) }}</label>
                                <input type="text" name="ghana_card_number" id="ghana_card_number" class="form-control @error('ghana_card_number') is-invalid @enderror" value="{{ old('ghana_card_number') }}" required>
                                @error('ghana_card_number')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                    </div> 
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number" class="control-label">{{trans_choice('core::general.phone_number',1)}}</label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror">
                                    @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="control-label">{{trans_choice('core::general.email',1)}}</label>
                                <input type="email" name="email" id="email" v-model="email" class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                    </div>
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="digital_address" class="control-label">{{ trans_choice('core::general.digital_address',1) }}</label>
                                    <input type="text" name="digital_address" id="digital_address" class="form-control @error('digital_address') is-invalid @enderror" value="{{ old('digital_address') }}">
                                    @error('digital_address')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="residential_address" class="control-label">{{ trans_choice('core::general.residential_address',1) }}</label>
                                    <textarea name="residential_address" id="residential_address" rows="2" class="form-control @error('residential_address') is-invalid @enderror" required>{{ old('residential_address') }}</textarea>
                                    @error('residential_address')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-bordered card-preview">
                <div class="card-header">
                    <h3 class="card-title">{{ trans_choice('core::general.employment_business_details',1) }}</h3>
                </div>
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employment_status" class="control-label">{{ trans_choice('core::general.employment_status',1) }}</label>
                                <select class="form-control @error('employment_status') is-invalid @enderror" name="employment_status" id="employment_status" required>
                                    <option value="">{{ trans_choice('core::general.select',1) }}</option>
                                    <option value="Employed" {{ old('employment_status') == 'Employed' ? 'selected' : '' }}>{{ trans_choice('core::general.employed',1) }}</option>
                                    <option value="Self-Employed" {{ old('employment_status') == 'Self-Employed' ? 'selected' : '' }}>{{ trans_choice('core::general.self_employed',1) }}</option>
                                    <option value="Unemployed" {{ old('employment_status') == 'Unemployed' ? 'selected' : '' }}>{{ trans_choice('core::general.unemployed',1) }}</option>
                                </select>
                                @error('employment_status')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employer_business_name" class="control-label">{{ trans_choice('core::general.employer_business_name',1) }}</label>
                                <input type="text" name="employer_business_name" id="employer_business_name" class="form-control @error('employer_business_name') is-invalid @enderror" value="{{ old('employer_business_name') }}">
                                @error('employer_business_name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="occupation" class="control-label">{{ trans_choice('core::general.occupation',1) }}</label>
                                <input type="text" name="occupation" id="occupation" class="form-control @error('occupation') is-invalid @enderror" value="{{ old('occupation') }}">
                                @error('occupation')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="monthly_net_income" class="control-label">{{ trans_choice('core::general.monthly_net_income',1) }} (GHS)</label>
                                <input type="number" step="0.01" name="monthly_net_income" id="monthly_net_income" class="form-control numeric @error('monthly_net_income') is-invalid @enderror" value="{{ old('monthly_net_income') }}">
                                @error('monthly_net_income')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="work_address" class="control-label">{{ trans_choice('core::general.work_address',1) }}</label>
                                <textarea name="work_address" id="work_address" rows="2" class="form-control @error('work_address') is-invalid @enderror">{{ old('work_address') }}</textarea>
                                @error('work_address')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="length_of_employment" class="control-label">{{ trans_choice('core::general.length_of_employment',1) }}</label>
                                <input type="text" name="length_of_employment" id="length_of_employment" class="form-control @error('length_of_employment') is-invalid @enderror" value="{{ old('length_of_employment') }}" placeholder="e.g. 2 years">
                                @error('length_of_employment')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-bordered card-preview">
                <div class="card-header">
                    <h3 class="card-title">{{ trans_choice('loan::general.loan_details',1) }}</h3>
                </div>
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loan_amount_requested" class="control-label">{{ trans_choice('loan::general.loan_amount_requested',1) }} (GHS)</label>
                                <input type="number" step="0.01" name="loan_amount_requested" id="loan_amount_requested" class="form-control numeric @error('loan_amount_requested') is-invalid @enderror" value="{{ old('loan_amount_requested') }}" required>
                                @error('loan_amount_requested')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loan_product_id" class="control-label">{{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.product',1) }}</label>
                                <div class="input-group">
                                    <select name="loan_product_id" id="loan_product_id" class="form-control @error('loan_product_id') is-invalid @enderror" required>
                                        <option value="">{{ trans_choice('core::general.select',1) }}</option>
                                        @foreach($loan_products as $product)
                                            <option value="{{ $product->id }}" {{ old('loan_product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <a href="{{ url('loan/product/create') }}" class="btn btn-outline-secondary" title="Add product" target="_blank"><i class="fa fa-plus"></i></a>
                                    </div>
                                </div>
                                @error('loan_product_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="repayment_period" class="control-label">{{ trans_choice('loan::general.repayment_period',1) }}</label>
                                <select class="form-control @error('repayment_period') is-invalid @enderror" name="repayment_period" id="repayment_period" required>
                                    <option value="">{{ trans_choice('core::general.select',1) }}</option>
                                    <option value="4" {{ old('repayment_period') == '4' ? 'selected' : '' }}>4 {{ trans_choice('loan::general.month',2) }}</option>
                                    <option value="6" {{ old('repayment_period') == '6' ? 'selected' : '' }}>6 {{ trans_choice('loan::general.month',2) }}</option>
                                    <option value="12" {{ old('repayment_period') == '12' ? 'selected' : '' }}>12 {{ trans_choice('loan::general.month',2) }}</option>
                                </select>
                                @error('repayment_period')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="preferred_repayment_method" class="control-label">{{ trans_choice('loan::general.preferred_repayment_method',1) }}</label>
                                <select class="form-control @error('preferred_repayment_method') is-invalid @enderror" name="preferred_repayment_method" id="preferred_repayment_method" required>
                                    <option value="">{{ trans_choice('core::general.select',1) }}</option>
                                    <option value="Bank" {{ old('preferred_repayment_method') == 'Bank' ? 'selected' : '' }}>{{ trans_choice('loan::general.bank',1) }}</option>
                                    <option value="Mobile Money" {{ old('preferred_repayment_method') == 'Mobile Money' ? 'selected' : '' }}>{{ trans_choice('loan::general.mobile_money',1) }}</option>
                                    <option value="Payroll" {{ old('preferred_repayment_method') == 'Payroll' ? 'selected' : '' }}>{{ trans_choice('loan::general.payroll',1) }}</option>
                                    <option value="Post-Dated Cheque" {{ old('preferred_repayment_method') == 'Post-Dated Cheque' ? 'selected' : '' }}>{{ trans_choice('loan::general.post_dated_cheque',1) }}</option>
                                    <option value="Standing Order" {{ old('preferred_repayment_method') == 'Standing Order' ? 'selected' : '' }}>{{ trans_choice('loan::general.standing_order',1) }}</option>
                                </select>
                                @error('preferred_repayment_method')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-bordered card-preview">
                <div class="card-header">
                    <h3 class="card-title">{{ trans_choice('loan::general.terms_and_conditions',1) }}</h3>
                </div>
                <div class="card-body">
                    <ol class="list-decimal list-inside space-y-1 text-muted">
                        <li>The borrower agrees to repay the loan in full within the agreed period.</li>
                        <li>Interest shall be charged at the agreed fixed rate stated in the loan agreement.</li>
                        <li>All repayments must be made through the approved repayment method.</li>
                        <li>Failure to repay on time may result in penalties and recovery action.</li>
                        <li>The lender reserves the right to verify all information provided.</li>
                        <li>Any false declaration may lead to disqualification or legal action.</li>
                    </ol>
                    <div class="form-group mt-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="terms_agree" required>
                            <label class="custom-control-label" for="terms_agree">{{ trans_choice('loan::general.agree_to_the_terms',1) }}</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-top">
                    <button type="submit" class="btn btn-primary float-right">{{ trans_choice('core::general.submit',1) }} {{ trans_choice('loan::general.application',1) }}</button>
                </div>
            </div>
        </form>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modeInputs = document.querySelectorAll('input[name="client_mode"]');
            var existingSection = document.getElementById('existing-client-section');
            var newSection = document.getElementById('new-client-section');
            var clientSelect = document.getElementById('client_id');

            function normalizeSelectValue(select, value) {
                if (!select) {
                    return;
                }
                if (!value) {
                    select.value = '';
                    return;
                }
                var normalized = value.trim().toLowerCase();
                var match = Array.from(select.options).find(function (option) {
                    return option.value.trim().toLowerCase() === normalized;
                });
                if (match) {
                    select.value = match.value;
                } else {
                    select.value = value;
                }
            }

            function normalizeDateValue(value) {
                if (!value) {
                    return '';
                }
                var raw = value.trim();
                var isoMatch = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                if (isoMatch) {
                    return raw;
                }
                var dmyMatch = raw.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
                if (dmyMatch) {
                    return dmyMatch[3] + '-' + dmyMatch[2] + '-' + dmyMatch[1];
                }
                var ymdSlashMatch = raw.match(/^(\d{4})\/(\d{2})\/(\d{2})$/);
                if (ymdSlashMatch) {
                    return ymdSlashMatch[1] + '-' + ymdSlashMatch[2] + '-' + ymdSlashMatch[3];
                }
                var parsed = new Date(raw);
                if (!isNaN(parsed.getTime())) {
                    return parsed.toISOString().slice(0, 10);
                }
                return raw;
            }

            function toggleClientMode() {
                var selectedMode = document.querySelector('input[name="client_mode"]:checked').value;
                existingSection.style.display = selectedMode === 'existing' ? 'block' : 'none';
                document.getElementById('first_name').required = selectedMode !== 'existing';
                document.getElementById('last_name').required = selectedMode !== 'existing';
                document.getElementById('gender').required = selectedMode !== 'existing';
                document.getElementById('date_of_birth').required = selectedMode !== 'existing';
                document.getElementById('ghana_card_number').required = selectedMode !== 'existing';
                document.getElementById('phone_number').required = selectedMode !== 'existing';
                document.getElementById('email').required = selectedMode !== 'existing';
                document.getElementById('residential_address').required = selectedMode !== 'existing';
            }

            function populateClientFields() {
                var selectedOption = clientSelect.options[clientSelect.selectedIndex];
                if (!selectedOption || !selectedOption.value) {
                    return;
                }

                document.getElementById('first_name').value = selectedOption.getAttribute('data-first_name') || '';
                document.getElementById('last_name').value = selectedOption.getAttribute('data-last_name') || '';
                normalizeSelectValue(document.getElementById('gender'), selectedOption.getAttribute('data-gender') || '');
                document.getElementById('date_of_birth').value = normalizeDateValue(selectedOption.getAttribute('data-dob') || '');
                document.getElementById('ghana_card_number').value = selectedOption.getAttribute('data-external_id') || '';
                document.getElementById('country_id').value = selectedOption.getAttribute('data-country_id') || '';
                document.getElementById('phone_number').value = selectedOption.getAttribute('data-phone_number') || '';
                document.getElementById('email').value = selectedOption.getAttribute('data-email') || '';
                
                // FIX: was 'address', should be 'data-address' or 'data-residential_address'
                document.getElementById('residential_address').value = selectedOption.getAttribute('data-address') || '';
            }

            modeInputs.forEach(function (input) {
                input.addEventListener('change', toggleClientMode);
            });

            if (clientSelect) {
                clientSelect.addEventListener('change', populateClientFields);
            }

            toggleClientMode();
            if (clientSelect && clientSelect.value) {
                populateClientFields();
            }
        });
    </script>
@endsection