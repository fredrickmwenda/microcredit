@extends('core::layouts.master')

@section('content')
<div class="container">
    <h2>Edit Permission</h2>
    <form action="{{ route('permission.update', $permission->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Permission Name</label>
            <input type="text" name="name" class="form-control" value="{{ $permission->name }}" required>
        </div>
        <div class="form-group">
            <label for="display_name">Display Name</label>
            <input type="text" name="display_name" class="form-control" value="{{ $permission->display_name }}" required>
        </div>
        <div class="form-group">
            <label for="module">Permission Group (Module)</label>
            <input type="text" name="module" class="form-control" value="{{ $permission->module }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Update</button>
        <a href="{{ route('permission.index') }}" class="btn btn-secondary mt-2">Back</a>
    </form>
</div>
@endsection
