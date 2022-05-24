<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Resources\Book as BookResource;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        return BookResource::collection(Book::with('author')->paginate(100));
    }

    public function show(Book $book)
    {
        return \App\Http\Resources\Book::make($book->load('author'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(config('validations.book'));

        return \App\Http\Resources\Book::make(Book::create($data));
    }

    public function update(Book $book, Request $request)
    {
        $data = $request->validate(config('validations.book'));

        return \App\Http\Resources\Book::make($book->update($data));
    }

    public function destroy(Book $book)
    {
        return response()->json([
            'status' => $book->delete()
        ], 200);
    }
}
