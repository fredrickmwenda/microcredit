@extends('core::layouts.master')

@section('content')
<div class="container">
    <h2>Create Permission</h2>
    <form action="{{ route('permission.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Permission Name</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. user.roles.index">
        </div>
        <div class="form-group">
            <label for="display_name">Display Name</label>
            <input type="text" name="display_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="module">Permission Group (Module)</label>
            <select name="module" class="form-control" required>
                @foreach($permission_groups as $group)
                    <option value="{{ $group }}">{{ ucfirst($group) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success mt-2">Create</button>
        <a href="{{ route('permission.index') }}" class="btn btn-secondary mt-2">Back</a>
    </form>
</div>
@endsection
