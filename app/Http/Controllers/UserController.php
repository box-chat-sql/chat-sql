<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = Cache::get('users', []);
        $search = $request->input('search');
        if ($search) {
            $users = array_filter($users, function($user) use ($search) {
                return stripos($user['name'], $search) !== false || stripos($user['email'], $search) !== false;
            });
            $users = array_values($users); // reset key
        }
        // Phân trang thủ công
        $perPage = 10;
        $page = $request->input('page', 1);
        $total = count($users);
        $users = array_slice($users, ($page-1)*$perPage, $perPage);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($users, $total, $perPage, $page, [
            'path' => url()->current(),
            'query' => $request->query(),
        ]);
        return view('users.index', ['users' => $paginator]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        $users = Cache::get('users', []);
        // Kiểm tra email trùng
        foreach ($users as $user) {
            if ($user['email'] === $validated['email']) {
                return back()->withErrors(['email' => 'Email đã tồn tại!'])->withInput();
            }
        }
        $validated['id'] = count($users) ? max(array_column($users, 'id')) + 1 : 1;
        $validated['password'] = bcrypt($validated['password']);
        $users[] = $validated;
        Cache::put('users', $users);
        return redirect()->route('users.index')->with('success', 'Tạo user thành công!');
    }

    public function edit($id)
    {
        $users = Cache::get('users', []);
        $user = collect($users)->firstWhere('id', (int)$id);
        if (!$user) {
            abort(404);
        }
        // Truyền user dạng array sang view để không bị lỗi khi truyền vào route
        return view('users.edit', ['user' => $user]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|string|min:6',
        ]);
        $users = Cache::get('users', []);
        foreach ($users as &$user) {
            if ($user['id'] == $id) {
                // Kiểm tra email trùng với user khác
                foreach ($users as $u) {
                    if ($u['id'] != $id && $u['email'] === $validated['email']) {
                        return back()->withErrors(['email' => 'Email đã tồn tại!'])->withInput();
                    }
                }
                $user['name'] = $validated['name'];
                $user['email'] = $validated['email'];
                if (!empty($validated['password'])) {
                    $user['password'] = bcrypt($validated['password']);
                }
                break;
            }
        }
        Cache::put('users', $users);
        return redirect()->route('users.index')->with('success', 'Cập nhật user thành công!');
    }

    public function destroy($id)
    {
        $users = Cache::get('users', []);
        $users = array_filter($users, function($user) use ($id) {
            return $user['id'] != $id;
        });
        Cache::put('users', array_values($users));
        return redirect()->route('users.index')->with('success', 'Xóa user thành công!');
    }
}
