@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.loan',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('loan::general.loan',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('loan::general.loan',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="card">
            <div class="card-header">
                @can('loan.loans.create')
                    <a href="{{ url('loan/create') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-plus"></i> {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.loan',1) }}
                    </a>
                @endcan
                <div class="btn-group">
                    <div class="dropdown">
                        <a href="#" class="btn btn-trigger btn-icon dropdown-toggle"
                           data-bs-toggle="dropdown">
                            <i class="ri-tools-line"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-xs">
                            <a class="dropdown-item"><span>Show</span></a>
                            <a href="{{request()->fullUrlWithQuery(['per_page'=>10])}}"
                               class="dropdown-item {{request('per_page')==10?'active':''}}">
                                10
                            </a>
                            <a href="{{request()->fullUrlWithQuery(['per_page'=>20])}}"
                               class="dropdown-item {{(request('per_page')==20||!request('per_page'))?'active':''}}">
                                20
                            </a>
                            <a href="{{request()->fullUrlWithQuery(['per_page'=>50])}}"
                               class="dropdown-item {{request('per_page')==50?'active':''}}">50</a>
                            <a class="dropdown-item">Order</a>
                            <a href="{{request()->fullUrlWithQuery(['order_by_dir'=>'asc'])}}"
                               class="dropdown-item {{(request('order_by_dir')=='asc'||!request('order_by_dir'))?'active':''}}">
                                ASC
                            </a>
                            <a href="{{request()->fullUrlWithQuery(['order_by_dir'=>'desc'])}}"
                               class="dropdown-item {{request('order_by_dir')=='desc'?'active':''}}">
                                DESC
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-tools">
                    <form class="form-inline ml-0 ml-md-3" action="{{url('loan')}}">
                        <div class="input-group input-group-sm">
                            <input type="text" name="s" class="form-control" value="{{request('s')}}"
                                   placeholder="Search">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="ri-search-line"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>
                            <a href="{{table_order_link('id')}}">
                                <span>{{ trans_choice('core::general.id',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('branch')}}">
                                <span>{{ trans_choice('core::general.branch',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('loan_officer')}}">
                                <span>{{ trans_choice('loan::general.loan_officer',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('client')}}">
                                <span>{{ trans_choice('client::general.client',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('amount')}}">
                                <span>{{ trans_choice('core::general.amount',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('amount')}}">
                                <span>{{ trans_choice('loan::general.balance',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('disbursed_on_date')}}">
                                <span>{{ trans('loan::general.disbursed') }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('loan_product')}}">
                                <span>{{ trans_choice('loan::general.product',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('status')}}">
                                <span>{{ trans_choice('loan::general.status',1) }}</span>
                            </a>
                        </th>
                        <th>{{ trans_choice('core::general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>
                                <a href="{{url('loan/'.$key->id.'/show')}}">
                                    <span>{{$key->id}}</span>
                                </a>
                            </td>
                            <td>
                                <a href="{{url('branch/'.$key->branch_id.'/show')}}">
                                    <span>{{$key->branch}}</span>
                                </a>
                            </td>
                            <td>
                                <a href="{{url('user/'.$key->loan_officer_id.'/show')}}">
                                    <span>{{$key->loan_officer}}</span>
                                </a>
                            </td>
                            <td>
                                @php
                                    $client = Modules\Client\Entities\Client::find($key->client_id);
                                @endphp
                                <a href="{{url('client/'.$key->client_id.'/show')}}">
                                    <span @if($client && $client->isBlacklisted()) style="color:red;" @endif>{{$key->client}}</span>
                                </a>
                            </td>
                            <td>
                                <span>{{number_format($key->principal,2)}}</span>
                            </td>
                            <td>
                                <span>{{number_format(($key->total_principal - $key->principal_repaid_derived - $key->principal_written_off_derived) + ($key->total_interest - $key->interest_repaid_derived - $key->interest_written_off_derived - $key->interest_waived_derived) + ($key->total_fees - $key->fees_repaid_derived - $key->fees_written_off_derived - $key->fees_waived_derived) + ($key->total_penalties - $key->penalties_repaid_derived - $key->penalties_written_off_derived - $key->penalties_waived_derived), $key->decimals)}}</span>
                            </td>
                            <td>
                                <span>{{$key->disbursed_on_date}}</span>
                            </td>
                            <td>
                                <span>{{$key->loan_product}}</span>
                            </td>
                            <td>
                                @if($key->status == 'pending')
                                    <span class="bg-warning-subtle text-warning" data-class="bg-warning">{{trans_choice('loan::general.pending_approval', 1)}}</span>
                                @endif
                                @if($key->status == 'submitted')
                                    <span class="bg-warning-subtle text-warning" data-class="bg-warning">{{trans_choice('loan::general.pending_approval', 1)}}</span>
                                @endif
                                @if($key->status == 'overpaid')
                                    <span class="bg-warning-subtle text-warning" data-class="bg-warning">{{trans_choice('loan::general.overpaid', 1)}}</span>
                                @endif
                                @if($key->status == 'approved')
                                    <span class="bg-warning-subtle text-warning" data-class="bg-warning">{{trans_choice('loan::general.awaiting_disbursement', 1)}}</span>
                                @endif
                                @if($key->status == 'active')
                                    <span class="bg-info-subtle text-info" data-class="bg-info">{{trans_choice('loan::general.active', 1)}}</span>
                                @endif
                                @if($key->status == 'rejected')
                                    <span class="bg-danger-subtle text-danger" data-class="bg-danger">{{trans_choice('loan::general.rejected', 1)}}</span>
                                @endif
                                @if($key->status == 'withdrawn')
                                    <span class="bg-danger-subtle text-danger" data-class="bg-danger">{{trans_choice('loan::general.withdrawn', 1)}}</span>
                                @endif
                                @if($key->status == 'written_off')
                                    <span class="bg-danger-subtle text-danger" data-class="bg-danger">{{trans_choice('loan::general.written_off', 1)}}</span>
                                @endif
                                @if($key->status == 'closed')
                                    <span class="bg-success-subtle text-success" data-class="bg-success">{{trans_choice('loan::general.closed', 1)}}</span>
                                @endif
                                @if($key->status == 'pending_reschedule')
                                    <span  class="bg-warning-subtle text-warning" data-class="bg-warning">{{trans_choice('loan::general.pending_reschedule', 1)}}</span>
                                @endif
                                @if($key->status == 'rescheduled')
                                    <span class="bg-info-subtle text-info" data-class="bg-info">{{trans_choice('loan::general.rescheduled', 1)}}</span>
                                @endif
                                @if($key->status == 'pending_ceo_approval')
                                    <span class="bg-info-subtle text-info" data-class="bg-info">Awaiting CEO Approval</span>
                                @endif
                            </td>

                            <td>
                                <div class="btn-group">
                                    <button href="#" class="btn btn-default dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                        <i class="ri-settings-3-line"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{url('loan/' . $key->id . '/show')}}" class="dropdown-item">
                                            <i class="ri-eye-fill"></i>
                                            <span>{{trans_choice('core::general.detail',2)}}</span>
                                        </a>

                                        @if(($key->status=='submitted'||$key->status=='pending') && Auth::user()->can('loan.loans.edit'))
                                            


                                            <a href="{{url('loan/' . $key->id . '/edit')}}" class="dropdown-item">
                                                <i class="ri-edit-fill"></i>
                                                <span>{{trans_choice('core::general.edit',1)}}</span>
                                            </a>

                                        @endif

                                        <!-- If the user can edit which is in ceo_approval -->


                                        <div class="divider"></div>
                                        @if(($key->status=='submitted'||$key->status=='pending') && Auth::user()->can('loan.loans.destroy'))

                                            <a href="{{url('loan/' . $key->id . '/destroy')}}"
                                               class="dropdown-item confirm">
                                                <i class="ri-delete-bin-fill"></i>
                                                <span>{{trans_choice('core::general.delete',1)}}</span>
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
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-4">
                        <div>{{ trans_choice('core::general.page',1) }} {{$data->currentPage()}} {{ trans_choice('core::general.of',1) }} {{$data->lastPage()}}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-center">
                            {{$data->links()}}
                        </div>
                    </div>
                    <div class="col-md-4">

                    </div>
                </div>

            </div>

        </div>

    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {
                records:{!!json_encode($data)!!},
                selectAll: false,
                selectedRecords: []
            },
            methods: {
                selectAllRecords() {
                    this.selectedRecords = [];
                    if (this.selectAll) {
                        this.records.data.forEach(item => {
                            this.selectedRecords.push(item.id);
                        });
                    }
                },
            },
        })
    </script>
@endsection
