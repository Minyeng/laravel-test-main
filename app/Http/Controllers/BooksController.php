<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\PostBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        // @TODO implement
        $books = Book::select(\DB::raw('books.*, (SELECT AVG(review) b FROM book_reviews where books.id = book_id) as `avg_review`'))
            ->withCount('reviews');
        
        if($request->has('title')) {
            $books->where('title', 'LIKE', "%".strtolower($request->title)."%");
        }
        if($request->has('authors')) {
            $authors = explode(',', $request->authors);
            $books->whereHas('authors', function($query) use($authors){
                $query->whereIn('id', $authors);
            });
        }

        if($request->has('sortColumn')) {
            $books->orderBy($request->sortColumn, $request->has('sortDirection') ? $request->sortDirection : 'ASC');
        }

        if($request->has('page')) {
            $books->paginate($request->page);
        }
        
        return BookResource::collection($books->paginate());
    }

    public function store(PostBookRequest $request)
    {
        // @TODO implement
        $request->validate([ 'isbn' => 'digits:13' ]);
        $book = new Book();
        $input = $request->all();
        $book->isbn = $request->isbn;
        $book->title = $request->title;
        $book->description = $request->description;
        $book->published_year = $request->published_year;
        $book->save();
        $book->authors()->attach($request->authors);

        return new BookResource($book);
    }
}
