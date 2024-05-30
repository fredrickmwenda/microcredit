@extends('core::layouts.master')
@section('title')
    {{ trans_choice('client::general.dormant_clients',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('client::general.dormant_clients',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('client::general.dormant_clients',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" id="app">

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dormant_duration"
                                class="control-label">{{trans_choice('client::general.dormant_duration',1)}}</label>
                            <select class="form-control"  @change="onChange($event)" name="dormant_duration"
                                    id="dormant_duration" v-model="dormant_duration" required>
                                <option value="">All</option>
                                <option value="N/A">N/A</option>
                                <option value="30">30</option>
                                <option value="60">60</option>
                                <option value="90">90</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="btn-group">
                    <div class="dropdown">
                        <a href="#" class="btn btn-trigger btn-icon dropdown-toggle"
                           data-bs-toggle="dropdown">
                            <i class="fas fa-wrench"></i>
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
                    <form class="form-inline ml-0 ml-md-3" action="{{url('client')}}">
                        <div class="input-group input-group-sm">
                            <input type="text" name="s" class="form-control" value="{{request('s')}}"
                                   placeholder="Search">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table  table-striped table-hover table-condensed" id="data-table">
                    <thead>
                    <tr>
                        <th>
                            <a href="{{table_order_link('id')}}">
                                {{ trans_choice('core::general.system',1) }} {{ trans_choice('core::general.id',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('name')}}">
                                {{ trans_choice('core::general.name',1) }}
                            </a>
                        </th>
                        <th>{{ trans('core::general.mobile') }}</th>
                        <th>
                            <a href="{{table_order_link('status')}}">
                                {{ trans_choice('core::general.status',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('client_group_name')}}">
                                {{ trans_choice('client::general.group',1) }}
                            </a>
                        </th>
                        <th>
                            Last Transaction Date
                        </th>
                        <th>
                            Type
                        </th>
                        <th>
                            Amount
                        </th>
                        <th>
                            Duration
                        </th>
                        
                        <th>{{ trans_choice('core::general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($dormant_clients as $key)
                        <tr>
                            <td>
                                <span>{{$key->id}}</span>
                            </td>
                            <td>
                                
                                <a href="{{url('client/' . $key->id . '/show')}}">
                                    <span>&nbsp;{{$key->name}}</span>
                                </a>
                            </td>
                            <td>
                                <span>{{$key->mobile}}</span>
                            </td>
                            <td>
                                @if($key->status == "pending")
                                    <span>{{trans_choice('core::general.pending',1)}}</span>
                                @endif
                                @if($key->status == "active")
                                    <span>{{trans_choice('core::general.active',1)}}</span>
                                @endif
                                @if($key->status == "inactive")
                                    <span>{{trans_choice('core::general.inactive',1)}}</span>
                                @endif
                                @if($key->status == "deceased")
                                    <span>{{trans_choice('client::general.deceased',1)}}</span>
                                @endif
                                @if($key->status == "unspecified")
                                    <span>{{trans_choice('core::general.unspecified',1)}}</span>
                                @endif
                            </td>
                            <td>
                                <span>{{$key->client_group_name}}</span>
                            </td>
                            <td>
                                <span>{{$key->last_transaction_date}}</span>
                            </td>
                            
                            <td>
                                <span>{{$key->transaction_name}}</span>
                            </td>
                            <td>
                                <span>{{$key->amount}}</span>
                            </td>
                            <td>
                                <span>{{$key->dormant_duration}}</span>
                            </td>
                            <td>
                                <a href="{{url('client/' . $key->id . '/show')}}" class="btn-round" style="margin-right:5px;">
                                    <i class="far fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
           
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {
                records:{!!json_encode($dormant_clients)!!},
                selectAll: false,
                selectedRecords: [],
                dormant_duration:"{{old('dormant_duration',$dormant_duration)}}",
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
                onChange(event) {
                    console.log(event.target.value);
                    var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?dormant_duration=" + event.target.value;
                    window.location = newUrl;
                    //window.location += '&client_group_id='+event.target.value;
                },
            },
        })
    </script>
@endsection
