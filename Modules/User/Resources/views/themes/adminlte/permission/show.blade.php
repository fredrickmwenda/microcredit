@extends('core::layouts.master')

@section('content')
<div class="container">
    <h2>Permission Details</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Name: {{ $permission->name }}</h5>
            <p class="card-text">Display Name: {{ $permission->display_name }}</p>
            <p class="card-text">Module: {{ $permission->module }}</p>
            <a href="{{ route('permission.edit', $permission->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('permission.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
