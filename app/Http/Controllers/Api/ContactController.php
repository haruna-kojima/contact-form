php<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\Tag;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $contacts = Contact::with(['category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get();

        return ContactResource::collection($contacts);
    }

    public function show(Contact $contact): ContactResource
    {
        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    public function store(ContactRequest $request)
    {
        $validated = $request->validated();

        $contact = Contact::create($validated);

        if (!empty($validated['tag_ids'])) {
            $contact->tags()->attach($validated['tag_ids']);
        }

        $contact->load(['category', 'tags']);

        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }

    public function update(ContactRequest $request, Contact $contact)
    {
        $validated = $request->validated();

        $contact->update($validated);

        if (isset($validated['tag_ids'])) {
            $contact->tags()->sync($validated['tag_ids']);
        }

        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    public function destroy(Contact $contact)
    {
        $contact->tags()->detach();

        $contact->delete();

        return response()->noContent();
    }
}