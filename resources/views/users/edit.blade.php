@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Sửa User</h1>
    <form method="POST" action="{{ route('users.update', is_array($user) ? $user['id'] : $user->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Tên</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ is_array($user) ? $user['name'] : $user->name }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ is_array($user) ? $user['email'] : $user->email }}" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu mới (bỏ qua nếu không đổi)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
