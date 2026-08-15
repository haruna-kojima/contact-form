<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Http\Requests\indexContactRequest;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Contact;


class AdminController extends Controller
{
    public function index(indexContactRequest $request)
    {
        $tags = Tag::withCount('contacts')->orderBy('created_at', 'desc')->get();
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
}
