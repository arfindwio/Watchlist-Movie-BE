<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'poster_url' => $this->poster_url,
            'release_year' => $this->release_year,
            'genre' => $this->genre,
            'watched' => (bool) $this->watched,
            'score' => $this->score,
            'review' => $this->review,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }

    // Response untuk single movie
    public static function responseWith(bool $status, string $message, $movie = null, int $code = 200)
    {
        $data = $movie ? new self($movie) : null;

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    // Response untuk collection movies
    public static function collectionResponseWith(bool $status, string $message, $movies = [], int $code = 200)
    {
        $data = self::collection($movies);

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    // Response untuk delete (no data)
    public static function responseDelete(bool $status, string $message, int $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => null,
        ], $code);
    }

    
}
