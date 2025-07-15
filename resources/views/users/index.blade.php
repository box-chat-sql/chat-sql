@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Quản lý User</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Thêm User</a>
    <form method="GET" action="{{ route('users.index') }}" class="row g-2 mb-3">
        <div class="col-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm user..." class="form-control" />
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Tìm kiếm</button>
        </div>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ is_array($user) ? $user['id'] : $user->id }}</td>
                <td>{{ is_array($user) ? $user['name'] : $user->name }}</td>
                <td>{{ is_array($user) ? $user['email'] : $user->email }}</td>
                <td>
                    <a href="{{ route('users.edit', (is_array($user) ? $user['id'] : (isset($user->id) ? $user->id : '')) ) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('users.destroy', (is_array($user) ? $user['id'] : (isset($user->id) ? $user->id : '')) ) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa user này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $users->links() }}
</div>
@endsection
