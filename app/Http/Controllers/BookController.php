<?php

namespace App\Http\Controllers;

use App\Book;
use App\User;
use Mail;
use App\Mail\NotifyUserAboutBookPublish;
use App\Http\Resources\Book as BookResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index()
    {
        return BookResource::collection(Book::with('author')->paginate(10));
    }

    public function show(Book $book)
    {
        return \App\Http\Resources\Book::make($book->load('author'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(config('validations.book'));

        foreach(User::where('receives_notifications', true)->get() as $user){
            Mail::to($user->email)->send(new NotifyUserAboutBookPublish);
        }

        return \App\Http\Resources\Book::make(Book::create($data));
    }

    public function update(Book $book, Request $request)
    {
        $data = $request->validate(config('validations.book'));

        $book->update($data);
        return \App\Http\Resources\Book::make($book);
    }

    public function updateCover(Book $book, Request $request)
    {
        $data = $request->validate([
            'cover' => ['required', 'string']
        ]);

        $dataURL = $data['cover'];

        preg_match('/data:image\/([^;]+);base64,([^"]+)/i', $dataURL, $match);
        
        if (!$match || !isset($match[2])) {
            return response()->json('Invalid image.', 422);
        };

        $extension = $match[1];
        
        if($extension !== 'jpg' && $extension !== 'jpeg' && $extension !== 'png') {
            return response()->json('Only jpg and img files are allowed.', 422);
        }

        // Convert to bytes (via * 3 / 4) and then to MB (via  / (1024 * 1024))
        // Notice that the calculated number might be a bit off the size shown in OS file explorer. Like 3.9 MB instead of 4.1 MB.
        $sizeInMB = (strlen($dataURL) * 3 / 4) / (1024 * 1024);

        if($sizeInMB > 3.2) {
            return response()->json('Image size cannot be larger than 3 MB', 422);
        }

        $imgName = Str::random(50) . '.' . $extension;

        // Save image into the public folder
        File::put('books/' . $imgName, base64_decode($match[2]));

        $book->cover = $imgName;
        $book->save();

        return response()->json('Updated cover successfully.', 200);
    }

    public function destroy(Book $book)
    {
        return response()->json([
            'status' => $book->delete()
        ], 200);
    }
}
