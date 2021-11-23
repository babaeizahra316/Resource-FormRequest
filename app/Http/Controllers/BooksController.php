<?php

declare (strict_types=1);

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use App\BookReview;
use App\Http\Requests\PostBookRequest;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookReviewResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BooksController extends Controller
{
    public function getCollection(Request $request)
    {
        $title = $request->title;
        $authorID = $request->authorID;
        $books = Book::orderByDesc('title')
            ->where('title', $title)
            ->with(array('authors' =>
                function($query) use ($authorID)
                {
                    $query->where('id', $authorID);
                }))
            ->withCount('reviews')
            ->withAvg('reviews', 'review')
            ->orderByDesc('reviews_avg_review')
            ->paginate(15);
        $books = json_encode($books);
        $books = json_Decode($books);
        $data =  collect();
        foreach ($books->data as $book)
        {
            $avg = $book->reviews_avg_review;
            $count = $book->reviews_count;
            $review = ['avg' => $avg, 'count' => $count];
            $book = ['id' => $book->id, 'title' => $book->title, 'isbn' => $book->isbn, 'description' => $book->description, 'authors' => $book->authors, 'review' => $review];
            $data->push($book);
        }
        $books->data = $data;
        return new BookResource($books);
    }

    public function post(PostBookRequest $request)
    {
        $validated = $request->validated();
        $book = Book::create([
            'isbn' => $validated['isbn'],
            'title' => $validated['title'],
            'description' => $validated['description']
        ]);
        $authors = $validated['authors'];
        foreach ($authors as $author)
        {
            $book->authors()->attach($author);
        }
        $authors = $book->authors()->get();
        return response([
            'data' => [
                'id' => $book->id,
                'isbn' => $book->isbn,
                'title' => $book->title,
                'description' => $book->description,
                'authors' => $authors,
                'review' => ['avg' => 0, 'count' => 0]
            ]
        ]);
    }

    public function postReview(Book $book, PostBookReviewRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        $bookReview = new BookReview();
        $bookReview->comment = $validated['comment'];
        $bookReview->review = $validated['review'];
        $bookReview->book_id = $book->id;
        $bookReview->user_id = $user->id;
        $bookReview->save();
        $resource = [
            'bookReview' => $bookReview,
            'user_id' => $user->id,
            'name' => $user->name
        ];
        return new BookReviewResource($resource);
    }
}
