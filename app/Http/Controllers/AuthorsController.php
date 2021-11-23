<?php

namespace App\Http\Controllers;

use App\Author;
use App\Http\Requests\PostAuthorRequest;
use App\Http\Resources\AuthorResource;

class AuthorsController extends Controller
{
    public function create(PostAuthorRequest $request)
    {
        $validated = $request->validated();
        $author = Author::create([
            'name' => $validated['name'],
            'surname' => $validated['surname']
        ]);
        return new AuthorResource($author);
    }
}
