<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

//inport model
use App\Models\Book;

//import resource
use App\Http\Resources\BookResource;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::latest()->paginate(5);

        return new BookResource(true, "List Data Book", $books);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "image" => "required|image|mimes:jpeg,jpg,png,svg|max:2048",
            "title" => "required",
            "body" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->error(), 422);
        }

        $image = $request->file("image");
        $image->storeAs("public/books", $image->hashName());

        $book = Book::create([
            "image" => $image->hashName(),
            "title" => $request->title,
            "body" => $request->body
        ]);

        return new BookResource(true, "Book added successfully", $book);
    }

    public function show($id)
    {
        $book = Book::find($id);

        return new BookResource(true, "Detail Data Book", $book);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "title" => "required",
            "body" => "required",
            "image" => "nullable|image|mimes:jpeg,jpg,png,svg|max:2048"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $book = Book::find($id);

        //check image is not empty
        if ($request->hasFile("image")) {

            //upload image
            $image = $request->file("image");
            $image->storeAs("public/books", $image->hashName());

            //delete old image
            Storage::delete('public/books/' . basename($book->image));

            //update book with new image
            $book->update([
                "image" => $image->hashName(),
                "title" => $request->title,
                "body" => $request->body
            ]);
        } else {
            $book->update([
                "title" => $request->title,
                "body" => $request->body
            ]);
        }

        return new BookResource(true, "Book updated successfully", $book);
    }

    public function destroy($id)
    {
        $book = Book::find($id);

        //delete image
        Storage::delete("public/books/" . basename($book->image));

        $book->delete();

        return new BookResource(true, "Book deleted successfully", null);
    }
}
