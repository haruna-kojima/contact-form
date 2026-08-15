<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TagRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TagController extends Controller
{
    use AuthorizesRequests;

    public function store(TagRequest $request)
    {
        Tag::create($request=>validated());

        return redirect('/admin');
    }

    public function edit(Tag $tag)
    {
        // Policyによる認可チェック
        $this->authorize('update', $tag);

        $categories = Category::orderBy('content')->get();

        return view('tags.edit', compact('tag', 'categories'));
    }

    /**
     * タグを更新
     */
    public function update(TagRequest $request, Tag $tag)
    {
        // Policyによる認可チェック
        $this->authorize('update', $tag);

        $tag->update($request->validated());

        return redirect('/admin');
    }

    /**
     * タグを削除
     */
    public function destroy(Tag $tag)
    {
        // Policyによる認可チェック
        $this->authorize('delete', $tag);

        $tag->delete();

        return redirect('/admin');
    }
}