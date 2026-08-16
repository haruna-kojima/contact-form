<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Http\Requests\indexContactRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    public function index(indexContactRequest $request)
    {
        $categories = Category::all();
        $query = Contact::query();
        $query = $request->filter($query);
        $contacts = $query->with('category')->orderBy('created_at', 'desc')->paginate(7);
        $tags = Tag::orderBy('created_at', 'desc')->get();
        return view('admin.index', compact('tags', 'categories', 'contacts'));
    }

    public function store(TagRequest $request)
    {
        Tag::create($request->validated());
        return redirect()->route('admin.index');
    }

    public function show(Contact $contact)
    {
        return view('admin.show', compact('contact'));
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(TagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());
        return redirect()->route('admin.index');
    }

    public function destroy(Tag $tag)
    {
        if (method_exists($tag, 'contacts')) {
            $tag->contacts()->detach();
        }   
        $tag->delete();
        return redirect()->route('admin.index');
    }

    public function destroyContact(Contact $contact)
    {
        if (method_exists($contact, 'tags')) {
            $contact->tags()->detach();
        }
        $contact->delete();
        return redirect()->route('admin.index');
    }

    public function createAdmin()
    {
        return view('auth.register'); 
    }

    public function storeAdmin(RegisterRequest $request)
    {
        $validated = $request->validated();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        auth()->login($user);
        return redirect()->route('admin.index');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function createLogin()
    {
        return view('auth.login');
    }

    public function storeLogin(\Illuminate\Http\Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
        ]);

        if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.index');
        }
        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ])->onlyInput('email');
    }
}
