<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Http\Resources\MovieResource;
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
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'poster' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'release_year' => 'required|digits:4|integer',
                'genre' => 'required|string',
                'watched' => 'boolean',
                'score' => 'required|integer|between:1,100',
                'review' => 'required|string',
            ]);

            // Simpan file poster
            $file = $request->file('poster');
            $filename = $file->hashName();
            $path = $file->storeAs('photos', $filename, 'public');

            $data['poster'] = $path;

            $movie = $request->user()->movies()->create($data);

            return MovieResource::responseWith(true, 'Movie added to watchlist', $movie, 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
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
            'poster' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'release_year' => 'sometimes|digits:4|integer',
            'genre' => 'sometimes|string',
            'watched' => 'sometimes|boolean',
            'score' => 'sometimes|integer|between:1,100',
            'review' => 'sometimes|string',
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
