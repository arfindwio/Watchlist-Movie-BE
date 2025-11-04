<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Http\Resources\MovieResource;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    // Get list watchlist
    public function getMovies(Request $request)
    {
        $user = $request->user();

        $unwatched = $user->movies()
            ->where(function ($query) {
                $query->where('watched', false)->orWhereNull('watched');
            })
            ->get();

        $watched = $user->movies()
            ->where('watched', true)
            ->get();

        return MovieResource::responseWithWatchedAndUnwatched(true, 'Movies list', $watched, $unwatched);
    }


    // Get detail watchlist
    public function getDetailMovie($id, Request $request)
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
            'poster' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'release_year' => 'required|digits:4|integer',
            'genre' => 'required|string',
            'watched' => 'boolean',
            'score' => 'required|integer|between:1,100',
        ]);

        // Simpan file poster
        $file = $request->file('poster');
        $filename = $file->hashName();
        $path = $file->storeAs('photos', $filename, 'public');

        $data['poster'] = $path;

        $movie = $request->user()->movies()->create($data);

        return MovieResource::responseWith(true, 'Movie added to watchlist', $movie, 201);
    }

    // Get all unwatched watchlists
    public function getAllUnwatched(Request $request)
    {
        $user = $request->user();

        $unwatched = $user->movies()
            ->where(function ($query) {
                $query->where('watched', false)->orWhereNull('watched');
            })
            ->get();

        return MovieResource::collectionResponseWith(true, 'All unwatched watchlists', $unwatched);
    }

    // Get all watched watchlists
    public function getAllWatched(Request $request)
    {
        $user = $request->user();

        $watched = $user->movies()
            ->where('watched', true)
            ->get();

        return MovieResource::collectionResponseWith(true, 'All watched watchlists', $watched);
    }

    // Edit movie by id
    public function editMovieById($id, Request $request)
    {
        $movie = Movie::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$movie) {
            return MovieResource::responseWith(false, 'Movie not found', null, 404);
        }

        $data = $request->validate([
            'title' => 'sometimes|string',
            'poster' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'release_year' => 'sometimes|digits:4|integer',
            'genre' => 'sometimes|string',
            'watched' => 'sometimes|boolean',
            'score' => 'sometimes|integer|between:1,100',
        ]);

        if ($request->hasFile('poster')) {
            // Hapus file lama jika ada
            if ($movie->poster && \Storage::disk('public')->exists($movie->poster)) {
                \Storage::disk('public')->delete($movie->poster);
            }

            // Simpan file baru dengan nama hash di folder 'photos'
            $file = $request->file('poster');
            $filename = $file->hashName(); // nama file unik
            $path = $file->storeAs('photos', $filename, 'public'); // simpan di storage/app/public/photos

            $data['poster'] = $path; // simpan path relatif ke database
        }

        $movie->update($data);

        return MovieResource::responseWith(true, 'Movie updated successfully', $movie);
    }

    // Delete movie by id
    public function deleteMovieById($id, Request $request)
    {
        $movie = Movie::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$movie) {
            return MovieResource::responseWith(false, 'Movie not found', null, 404);
        }

        $movie->delete();

        return MovieResource::responseDelete(true, 'Movie deleted successfully');
    }
}
