@extends('core::layouts.master')
@section('title')
    {{ trans_choice('customfield::general.custom_field',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('customfield::general.custom_field',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('customfield::general.custom_field',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="card">
            <div class="card-header">
                @can('customfield.custom_fields.create')
                    <a href="{{ url('custom_field/create') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-plus"></i> {{ trans_choice('core::general.add',1) }} {{ trans_choice('customfield::general.custom_field',1) }}
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
                    <form class="form-inline ml-0 ml-md-3" action="{{url('custom_field')}}">
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
                            <a href="{{table_order_link('name')}}">
                                <span>{{ trans_choice('core::general.name',1) }}</span>
                            </a>
                        </th>
                        <th
                            <a href="{{table_order_link('category')}}">
                                <span>{{ trans_choice('customfield::general.category',1) }}</span>
                            </a>
                        </th>
                        <th
                            <a href="{{table_order_link('type')}}">
                                <span>{{ trans_choice('customfield::general.type',1) }}</span>
                            </a>
                        </th>
                        <th
                            <a href="{{table_order_link('required')}}">
                                <span>{{ trans_choice('customfield::general.required',1) }}</span>
                            </a>
                        </th>
                        <th
                            <a href="{{table_order_link('active')}}">
                                <span>{{ trans_choice('core::general.active',1) }}</span>
                            </a>
                        </th>
                        <th>{{ trans_choice('core::general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>
                                <a href="#">
                                    <span>{{$key->name}}</span>
                                </a>
                            </td>
                            <td>
                                @if($key->category== "add_client")
                                    <span>{{trans_choice('core::general.add', 1)}} {{trans_choice('client::general.client', 1)}}</span>
                                @endif
                                @if($key->category== "add_loan")
                                    <span>{{trans_choice('core::general.add', 1)}} {{trans_choice('loan::general.loan', 1)}}</span>
                                @endif
                                @if($key->category== "add_repayment")
                                    <span>{{trans_choice('core::general.add', 1)}} {{trans_choice('loan::general.repayment', 1)}}</span>
                                @endif
                                @if($key->category== "add_savings")
                                    <span>{{trans_choice('core::general.add', 1)}} {{trans_choice('savings::general.savings', 1)}}</span>
                                @endif
                                @if($key->category== "add_collateral")
                                    <span>{{trans_choice('core::general.add', 1)}} {{trans_choice('loan::general.collateral', 1)}}</span>
                                @endif
                                @if($key->category== "add_user")
                                    <span>{{trans_choice('core::general.add', 1)}} {{trans_choice('user::general.user', 1)}}</span>
                                @endif
                                @if($key->category== "add_branch")
                                    <span>{{trans_choice('core::general.add', 1)}} {{trans_choice('core::general.branch', 1)}}</span>
                                @endif
                                @if($key->category== "add_journal_entry")
                                    <span>{{trans_choice('core::general.add', 1)}} {{trans_choice('accounting::general.journal', 1)}}</span>
                                @endif
                            </td>
                            <td>
                                @if($key->type== "textfield")
                                    <span>{{trans_choice('customfield::general.textfield', 1)}}</span>
                                @endif
                                @if($key->type== "select")
                                    <span>{{trans_choice('customfield::general.select', 1)}}</span>
                                @endif
                                @if($key->type== "number")
                                    <span>{{trans_choice('customfield::general.number', 1)}}</span>
                                @endif
                                @if($key->type== "date")
                                    <span>{{trans_choice('customfield::general.date', 1)}}</span>
                                @endif
                                @if($key->type== "checkbox")
                                    <span>{{trans_choice('customfield::general.checkbox', 1)}}</span>
                                @endif
                                @if($key->type== "textarea")
                                    <span>{{trans_choice('customfield::general.textarea', 1)}}</span>
                                @endif
                                @if($key->type== "select_db")
                                    <span>{{trans_choice('customfield::general.select_db', 1)}}</span>
                                @endif
                            </td>
                            <td>
                                @if($key->required==1)
                                    <span class="badge badge-success">{{trans_choice('core::general.yes',1)}}</span>
                                @else
                                    <span class="badge badge-danger">{{trans_choice('core::general.no',1)}}</span>
                                @endif
                            </td>
                            <td>
                                @if($key->active==1)
                                    <span class="badge badge-success">{{trans_choice('core::general.yes',1)}}</span>
                                @else
                                    <span class="badge badge-danger">{{trans_choice('core::general.no',1)}}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button href="#" class="btn btn-default dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                        <i class="ri-settings-3-line"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @can('customfield.custom_fields.edit')
                                            <a href="{{url('custom_field/' . $key->id . '/edit')}}"
                                               class="dropdown-item">
                                                <i class="ri-edit-fill"></i>
                                                <span>{{trans_choice('core::general.edit',1)}}</span>
                                            </a>
                                        @endcan
                                        <div class="dropdown-divider"></div>
                                        @can('customfield.custom_fields.destroy')
                                            <a href="{{url('custom_field/' . $key->id . '/destroy')}}"
                                               class="dropdown-item confirm">
                                                <i class="ri-delete-bin-fill"></i>
                                                <span>{{trans_choice('core::general.delete',1)}}</span>
                                            </a>
                                        @endcan
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
