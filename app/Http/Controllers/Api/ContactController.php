<?php

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
            ->get();

        return ContactResource::collection($contacts);
    }

    public function show(Contact $contact): ContactResource
    {
        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }
}