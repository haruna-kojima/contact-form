<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Contact;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::withCount('contacts')->orderBy('created_at', 'desc')->get();
        $categories = Category::all();
        $contacts = Contact::orderBy('created_at', 'desc')->paginate(7);
        return view('admin.index', compact('tags', 'categories', 'contacts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TagRequest $request)
    {
        Tag::create($request->validated());
        return redirect('/admin')->with('success', 'タグを追加しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());
        return redirect('/admin')->with('success', 'タグを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $tag->contacts()->detach();
        $tag->delete();
        return redirect('/admin')->with('success', 'タグを削除しました');
    }
}
