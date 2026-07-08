@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.loan_application',2) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('loan::general.loan_application',2) }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a></li>
                        <li class="breadcrumb-item active">{{ trans_choice('loan::general.loan_application',2) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card card-bordered card-preview">
            <div class="card-body">
                <table class="table table-bordered table-striped" id="loan_applications_table">
                    <thead>
                        <tr>
                            <th>{{ trans_choice('loan::general.reference_number',1) }}</th>
                            <th>{{ trans_choice('core::general.name',1) }}</th>
                            <th>{{ trans_choice('loan::general.amount_requested',1) }}</th>
                            <th>{{ trans_choice('core::general.status',1) }}</th>
                            <th>{{ trans_choice('loan::general.level1_review',1) }}</th>
                            <th>{{ trans_choice('loan::general.level2_review',1) }}</th>
                            <th>{{ trans_choice('core::general.action',2) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                        <tr>
                            <td>{{ $application->reference_number }}</td>
                            <td>{{ $application->first_name }}</td>
                            <td>GHS {{ number_format($application->loan_amount_requested, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $application->overall_status == 'Converted' ? 'success' : ($application->overall_status == 'Declined' ? 'danger' : ($application->overall_status == 'Under Review' ? 'warning' : 'info')) }}">
                                    {{ $application->overall_status }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $application->level1_status == 'Approved' ? 'success' : ($application->level1_status == 'Declined' ? 'danger' : 'warning') }}">
                                    {{ $application->level1_status }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $application->level2_status == 'Approved' ? 'success' : ($application->level2_status == 'Declined' ? 'danger' : ($application->level2_status == 'Deferred' ? 'info' : 'warning')) }}">
                                    {{ $application->level2_status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        <i class="ri-settings-3-line"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ url('loan/application/' . $application->id . '/view') }}" class="dropdown-item">
                                            <i class="ri-eye-fill"></i>
                                            <span>{{ trans_choice('core::general.detail',2) }}</span>
                                        </a>
                                        @if($application->client)
                                        <a href="{{ url('client/' . $application->client->id . '/credit_score') }}" class="dropdown-item">
                                            <i class="ri-bar-chart-box-fill"></i>
                                            <span>{{ trans_choice('core::general.credit_score',1) }}</span>
                                        </a>
                                        @endif
                                        @if($application->level1_status === 'Pending')
                                        <a href="{{ url('loan/application/' . $application->id . '/view') }}#level1" class="dropdown-item">
                                            <i class="ri-user-star-fill"></i>
                                            <span>{{ trans_choice('loan::general.level1_review',1) }}</span>
                                        </a>
                                        @endif
                                        @if($application->level2_status === 'Pending' && $application->level1_status === 'Approved')
                                        <a href="{{ url('loan/application/' . $application->id . '/view') }}#level2" class="dropdown-item">
                                            <i class="ri-shield-user-fill"></i>
                                            <span>{{ trans_choice('loan::general.level2_approve',1) }}</span>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            {{ $applications->links() }}
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        $('#loan_applications_table').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
        });
    </script>
@endsection