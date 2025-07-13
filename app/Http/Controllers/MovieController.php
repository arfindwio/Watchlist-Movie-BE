<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovieController extends Controller
{
    // Get list watchlist
    public function getWatchlists(Request $request)
    {
        $movies = $request->user()->movies()->get();

        return MovieResource::collectionResponseWith(true, 'List of watchlists', $movies);
    }

    // Get detail watchlist
    public function getDetailWatchlist($id, Request $request)
    {
        $movie = Movie::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$movie) {
            return MovieResource::responseWith(false, 'Movie not found', null, 404);
        }

        return MovieResource::responseWith(true, 'Movie detail', $movie);
    }

    // Create new watchlist movie
    public function createWatchlist(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'poster_url' => 'required|url',
            'release_year' => 'required|digits:4|integer',
            'genre' => 'required|string',
            'watched' => 'boolean',
            'score' => 'required|integer|between:1,100',
            'review' => 'required|string',
        ]);

        $movie = $request->user()->movies()->create($data);

        return MovieResource::responseWith(true, 'Movie added to watchlist', $movie, 201);
    }

    // Edit watchlist movie by id
    public function editWatchlistById($id, Request $request)
    {
        $movie = Movie::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$movie) {
            return MovieResource::responseWith(false, 'Movie not found', null, 404);
        }

        $data = $request->validate([
            'title' => 'sometimes|string',
            'poster_url' => 'sometimes|url',
            'release_year' => 'sometimes|digits:4|integer',
            'genre' => 'sometimes|string',
            'watched' => 'sometimes|boolean',
            'score' => 'sometimes|integer|between:1,100',
            'review' => 'sometimes|string',
        ]);

        $movie->update($data);

        return MovieResource::responseWith(true, 'Movie updated successfully', $movie);
    }

    // Delete watchlist movie by id
    public function deleteWatchlistById($id, Request $request)
    {
        $movie = Movie::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$movie) {
            return MovieResource::responseWith(false, 'Movie not found', null, 404);
        }

        $movie->delete();

        return MovieResource::responseDelete(true, 'Movie deleted successfully');
    }
}
