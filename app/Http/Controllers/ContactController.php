<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();
        // 取得したデータをBladeに渡す
        return view('contact.index', compact('categories', 'tags'));
    }

    public function confirm(ContactRequest $request) 
    {
        $validated = $request->validated();

        $category = Category::findOrFail($validated['category_id']);
        
        $tags = isset($validated['tag_ids']) 
            ? Tag::whereIn('id', $validated['tag_ids'])->get() 
            : collect();

        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    public function store(ContactRequest $request)
    {
        $validated = $request->validated();

        $contact = Contact::create($validated);

        if (!empty($validated['tag_ids'])) {
            $contact->tags()->attach($validated['tag_ids']);
        }

        return view('contact.thanks');
    }
}