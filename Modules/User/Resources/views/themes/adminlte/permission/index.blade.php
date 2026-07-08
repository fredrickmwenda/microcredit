@extends('core::layouts.master')

@section('content')
<div class="container">
    <h2>Permission Groups</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <a href="{{ route('permission.create') }}" class="btn btn-primary mb-3">Create Permission</a>
    <div class="accordion" id="permissionGroups">
        @foreach($permissions as $module => $group)
            <div class="card mb-2">
                <div class="card-header" id="heading-{{ $module }}">
                    <h5 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-{{ $module }}" aria-expanded="true" aria-controls="collapse-{{ $module }}">
                            {{ ucfirst($module) }}
                        </button>
                    </h5>
                </div>
                <div id="collapse-{{ $module }}" class="collapse" aria-labelledby="heading-{{ $module }}" data-parent="#permissionGroups">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Display Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group as $permission)
                                    <tr>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->display_name ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('permission.edit', $permission->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('permission.delete', $permission->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
