@extends('core::layouts.master')
@section('title')
    {{ trans_choice('user::general.user',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('user::general.user',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('user::general.user',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="card">
            <div class="card-header">
                @can('user.users.create')
                    <a href="{{ url('user/create') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-plus"></i> {{ trans_choice('core::general.add',1) }} {{ trans_choice('user::general.user',1) }}
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
                    <form class="form-inline ml-0 ml-md-3" action="{{url('user')}}">
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
                        <th><span
                            >{{ trans_choice('core::general.avatar',1) }}</span></th>
                        <th>
                            <a href="{{table_order_link('first_name')}}">
                                <span>{{ trans_choice('core::general.name',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('email')}}">
                                <span>{{ trans_choice('core::general.email',1) }}</span>
                            </a>
                        </th>
                        <th><span
                            >{{ trans_choice('user::general.role',2) }}</span></th>
                        <th>
                            <a href="{{table_order_link('first_name')}}">
                                <span>{{ trans('core::general.created_at') }}</span>
                            </a>
                        </th>
                        <th>{{ trans_choice('core::general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td class="nk-tb-col  tb-col-mb">
                                <a href="{{url('user/'.$key->id.'/show')}}">
                                    <span>{{$key->id}}</span>
                                </a>
                            </td>
                            <td>
                                <a href="{{url('user/'.$key->id.'/show')}}">
                                    @if(!empty(Auth::user()->photo))
                                        <img
                                                class="user-image"
                                                src="{{asset('storage/uploads/'.Auth::user()->photo)}}"
                                                alt="User Image">
                                    @else
                                        <img
                                                class="img-circle img-size-32 mr-2"
                                                src="{{asset('themes/adminlte/img/user.png')}}"
                                                alt="User Image">
                                    @endif
                                </a>
                            </td>
                            <td>
                                <a href="{{url('user/'.$key->id.'/show')}}">
                                    <span class="tb-lead">{{$key->first_name}} {{$key->last_name}}</span>
                                </a>
                            </td>
                            <td>
                                <span>{{$key->email}}</span>
                            </td>
                            <td>
                                @if(!empty(Auth::user()->roles))
                                    <!--span class="badge badge-primary">
                                        {{ucfirst(Auth::user()->roles->first()->name)}}
                                    </span-->
                                @endif

                                @if(!empty(Auth::user()->roles))
                                    <span class="badge-primary" style="color:aqua">
                                        {{$key->role_details}}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span>{{$key->created_at->format("Y-m-d")}}</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button href="#" class="btn btn-default dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                        <i class="ri-settings-3-line"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{url('user/' . $key->id . '/show')}}"
                                           class="dropdown-item">
                                            <i class="ri-eye-fill"></i>
                                            <span>{{trans_choice('core::general.detail',2)}}</span>
                                        </a>
                                        @can('user.users.edit')
                                            <a href="{{url('user/' . $key->id . '/edit')}}"
                                               class="dropdown-item">
                                                <i class="ri-edit-fill"></i>
                                                <span>{{trans_choice('core::general.edit',1)}}</span>
                                            </a>
                                        @endcan
                                        <div class="dropdown-divider"></div>
                                        @can('user.users.destroy')
                                            <a href="{{url('user/' . $key->id . '/destroy')}}"
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
