<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        $books = Book::when($title, function($query,  $title) {
            return $query->title($title);
        });

        $books = match($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6month' => $books->popularLast6Month(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6month' => $books->highestRatedLast6Month(),
            default => $books->latest()->withAverageRating()->withReviewsCount()
        };
        // $books = $books->get();

        $cacheKey = 'books:' . $filter . ':' . $title;
        $books =
        // cache()->remember(
            // $cacheKey,
            // 3600,
            // fn() =>
            $books->simplePaginate(10);
        // );

        return view('books.index', ['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {

        $cacheKey = 'book:' . $id;

        $book = cache()->remember(
            $cacheKey,
            3600,
            fn() =>
            Book::with(
            [
                'reviews' => fn($query) => $query->latest(),
            ])->withAverageRating()->withReviewsCount()->findOrFail($id)
        );

        return view(
            'books.show',
             ['book' => $book],
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
