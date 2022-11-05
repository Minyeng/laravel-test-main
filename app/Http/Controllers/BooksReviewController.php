<?php

namespace App\Http\Controllers;

use App\BookReview;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookReviewResource;
use Illuminate\Http\Request;

class BooksReviewController extends Controller
{
    public function __construct()
    {

    }

    public function store(int $bookId, PostBookReviewRequest $request)
    {
        // @TODO implement
        $bookReview = new BookReview();
        $bookReview->book_id = $bookId;
        $bookReview->user_id = auth()->user()->id;
        $bookReview->review = $request->review;
        $bookReview->comment = $request->comment;

        if($bookReview->book) {
            $bookReview->save();
        } else {
            return response()->make(null, 404);
        }

        return new BookReviewResource($bookReview);
    }

    public function destroy(int $bookId, int $reviewId, Request $request)
    {
        // @TODO implement
        $bookReview = BookReview::where([
            'book_id' => $bookId,
            'id' => $reviewId
        ])->first();
        if($bookReview) {
            $bookReview->delete();
            return response()->make(null, 204);
        } else {
            return response()->make(null, 404);
        }
    }
}
