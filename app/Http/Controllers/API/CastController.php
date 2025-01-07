<?php

namespace App\Http\Controllers\API;

use App\Models\Cast;
use Illuminate\Http\Request;
use App\Http\Requests\CastRequest;
use App\Http\Controllers\Controller;

class CastController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'isAdmin'])->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $casts = Cast::all();

        return response()->json([
            'message' => 'Detail Data Cast',
            'data' => $casts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CastRequest $request)
    {
        Cast::create($request->all());

        return response()->json([
            "message" => "Tambah Cast Berhasil"

        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cast = Cast::with('movies')->find($id);

        if (!$cast) {
            return response()->json([
                "message" => "id tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Berhasil Detail data dengan id $id",
            "data" => [
                "id" => $cast->id,
                "name" => $cast->name,
                "age" => $cast->age,
                "bio" => $cast->bio,
                "created_at" => $cast->created_at,
                "updated_at" => $cast->updated_at,
                "list_movie" => $cast->movies->map(function ($movie) {
                    return [
                        "id" => $movie->id,
                        "title" => $movie->title,
                        "summary" => $movie->summary,
                        "poster" => $movie->poster,
                        "year" => $movie->year,
                        "genre_id" => $movie->genre_id,
                        "created_at" => $movie->created_at,
                        "updated_at" => $movie->updated_at,
                        "pivot" => [
                            "cast_id" => $movie->pivot->cast_id,
                            "movie_id" => $movie->pivot->movie_id
                        ]
                    ];
                }),
            ],
        ], 201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cast = Cast::find($id);

        if (!$cast) {
            return response()->json([
                "message" => "id tidak ditemukan"
            ], 404);
        }

        $cast->name = $request['name'];
        $cast->age = $request['age'];
        $cast->bio = $request['bio'];

        $cast->save();

        return response()->json([
            "message" => "Update Cast berhasil",
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cast = Cast::find($id);

        if (!$cast) {
            return response()->json([
                "message" => "id tidak ditemukan"
            ], 404);
        }

        $cast->delete();

        return response()->json([
            "message" => "Berhasil Menghapus Cast dengan id $id",
        ], 201);
    }
}
