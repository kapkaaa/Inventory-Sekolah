<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $search = $request->input('search');
        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
        })->orderBy('created_at', 'desc')->paginate(10);

        $pendingUsers = User::where('is_approved', false)->latest()->limit(5)->get();

        return view('users.index', compact('users', 'search', 'pendingUsers'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        
        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,user'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_approved' => true // Admin membuat langsung disetujui
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
            'is_approved' => 'required|boolean'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_approved' => $request->is_approved
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    // Fitur approval untuk user baru
    public function pending(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $search = $request->input('search');
        $users = User::where('is_approved', false)
                     ->when($search, function ($query, $search) {
                         return $query->where('name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%");
                     })
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);

        $totalUsers = User::count();
        $approvedUsers = User::where('is_approved', true)->count();
        $pendingUsersCount = User::where('is_approved', false)->count();

        return view('users.pending', compact('users', 'totalUsers', 'approvedUsers', 'pendingUsersCount', 'search'));
    }

    public function approve(User $user)
    {
        $this->authorize('update', $user);
        
        $user->update(['is_approved' => true]);

        return redirect()->route('users.pending')->with('success', 'User berhasil disetujui.');
    }

    public function reject(User $user)
    {
        $this->authorize('delete', $user);
        
        if ($user->id === auth()->id()) {
            return redirect()->route('users.pending')->with('error', 'Anda tidak dapat menolak akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.pending')->with('success', 'User berhasil ditolak dan dihapus.');
    }
}