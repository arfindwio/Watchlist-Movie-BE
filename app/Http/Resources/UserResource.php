<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Return hanya data user yang diperlukan
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'photo' => $this->photo ? asset('storage/photos/' . $this->photo) : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }

    // Response standar dengan user data + token (opsional)
    public static function responseWithUserAndToken($user, $token, $message = 'Success', $status = true, $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => [
                'user' => new self($user),
                'token' => $token,
            ],
        ], $code);
    }

    // Response standar tanpa token, hanya user
    public static function responseWithUser($user, $message = 'Success', $status = true, $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => new self($user),
        ], $code);
    }

    // Response sukses ganti password
    public static function responsePasswordChanged($message = 'Password berhasil diganti', $status = true, $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => null,
        ], $code);
    }


    // Response error standar
    public static function responseError($message = 'Error', $status = false, $code = 400)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => null,
        ], $code);
    }

    // Response logout
    public static function responseLogout($message = 'Logged out successfully', $status = true, $code = 200)
{
    return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => null,
    ], $code);
}

}
