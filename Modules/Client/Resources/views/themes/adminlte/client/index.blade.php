@extends('core::layouts.master')
@section('title')
    {{ trans_choice('client::general.client',2) }}
@endsection
@section('styles')
@stop
@section('content') 
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('client::general.client',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('client::general.client',2) }}</li>
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
                            <label for="client_group_id"
                                class="control-label">{{trans_choice('client::general.group',1)}}</label>
                            <select class="form-control"  @change="onChange($event)" name="client_group_id"
                                    id="client_group_id" v-model="client_group_id" required>
                                <option value="" selected>{{trans_choice('core::general.select',1)}}</option>
                                @foreach($client_groups as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
           
            <div class="card-header">
                @can('client.clients.create')
                    <a href="{{ url('client/create') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-plus"></i> {{ trans_choice('core::general.add',1) }} {{ trans_choice('client::general.client',1) }}
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
                    <form class="form-inline ml-0 ml-md-3" action="{{url('client')}}">
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
                        <th>
                            <a href="{{table_order_link('external_id')}}">
                                {{ trans_choice('core::general.external_id',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('gender')}}">
                                {{ trans('core::general.gender') }}
                            </a>
                        </th>
                        <th>{{ trans('core::general.mobile') }}</th>
                        <th>
                            <a href="{{table_order_link('status')}}">
                                {{ trans_choice('core::general.status',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('branch')}}">
                                {{ trans_choice('core::general.branch',1) }}/ Team
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('client_group_name')}}">
                                {{ trans_choice('client::general.group',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('staff')}}">
                                {{ trans_choice('core::general.staff',1) }}
                            </a>
                        </th>
                        <th>{{ trans_choice('core::general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
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
                                <span>{{$key->external_id}}</span>
                            </td>
                            <td>
                                @if($key->gender == "male")
                                    <span>{{trans_choice('core::general.male',1)}}</span>
                                @endif
                                @if($key->gender == "female")
                                    <span>{{trans_choice('core::general.female',1)}}</span>
                                @endif
                                @if($key->gender == "other")
                                    <span>{{trans_choice('core::general.other',1)}}</span>
                                @endif
                                @if($key->gender == "unspecified")
                                    <span>{{trans_choice('core::general.unspecified',1)}}</span>
                                @endif
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
                                <a href="{{url('branch/' . $key->branch_id . '/show')}}">
                                    <span>{{$key->branch}}</span>
                                </a>
                            </td>
                            <td>
                                <span>{{$key->client_group_name}}</span>
                            </td>
                            <td>
                                <a href="{{url('user/' . $key->loan_officer_id . '/show')}}">
                                    <span>{{$key->staff}}</span>
                                </a>
                            </td>
                            <td>
                                <a href="{{url('client/' . $key->id . '/show')}}" class="btn-round" style="margin-right:5px;">
                                    <i class="ri-eye-fill"></i>
                                </a>
                                <div class="btn-group mt-2">
                                    <button href="#" class="btn btn-default dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                        <i class="ri-settings-3-line"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{url('client/' . $key->id . '/show')}}" class="dropdown-item">
                                            <i class="ri-eye-fill"></i>
                                            <span>{{trans_choice('core::general.detail',2)}}</span>
                                        </a>
                                        @can('core.payment_types.edit')
                                            <a href="{{url('client/' . $key->id . '/edit')}}" class="dropdown-item">
                                                <i class="ri-edit-fill"></i>
                                                <span>{{trans_choice('core::general.edit',1)}}</span>
                                            </a>
                                        @endcan
                                        <div class="divider"></div>
                                        @can('core.payment_types.destroy')
                                            <a href="{{url('client/' . $key->id . '/destroy')}}"
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
                selectedRecords: [],
                client_group_id: null,
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
                    var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?client_group_id=" + event.target.value;
                    window.location = newUrl;
                    //window.location += '&client_group_id='+event.target.value;
                },
            },
        })
    </script>
@endsection
