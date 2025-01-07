<?php

namespace App\Http\Controllers\API;

use App\Models\Movie;
use Illuminate\Http\Request;
use App\Http\Requests\MovieRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
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
        try {
            // Mengambil semua data film beserta relasi genre
            $movies = Movie::with('genre')->get();

            // Mengubah data menjadi format yang diinginkan
            $data = $movies->map(function ($movie) {
                return [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'summary' => $movie->summary,
                    'year' => $movie->year,
                    'poster' => $movie->poster,
                    "genre" => [
                        "id" => $movie->genre->id,
                        "name" => $movie->genre->name,
                        "created_at" => $movie->genre->created_at,
                        "updated_at" => $movie->genre->updated_at,
                    ],
                    'created_at' => $movie->created_at,
                    'updated_at' => $movie->updated_at,
                ];
            });

            return response()->json([
                'message' => 'Tampil data berhasil',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('poster')) {
            // membuat unique name pada gambar yang diinput
            $imageName = time() . "-poster." . $request->poster->extension();

            // simpan gambar pada file storage
            $request->poster->storeAs('public/images', $imageName);

            // mengganti nilai request image menjadi $imageName yang baru bukan berdasarkan request
            $path = env('APP_URL') . '/storage/images/';
            $data['poster'] = $path . $imageName;
        }

        Movie::create($data);

        return response()->json([
            'message' => 'data berhasil ditambahkan'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $movie = Movie::with(['genre', 'casts', 'reviews'])->find($id);

        if (!$movie) {
            return response()->json([
                "message" => "id tidak ditemukan"
            ], 404);
        }

        $averageRating = $movie->reviews->avg('rating');

        Log::info('Average Rating: ', ['averageRating' => $averageRating]);

        return response()->json([
            "message" => "Data Detail ditampilkan",
            "data" => [
                "id" => $movie->id,
                "title" => $movie->title,
                "summary" => $movie->summary,
                "poster" => $movie->poster,
                "year" => $movie->year,
                "genre_id" => $movie->genre_id,
                "created_at" => $movie->created_at,
                "updated_at" => $movie->updated_at,
                "genre" => [
                    "id" => $movie->genre->id,
                    "name" => $movie->genre->name,
                    "created_at" => $movie->genre->created_at,
                    "updated_at" => $movie->genre->updated_at,
                ],
                "list_cast" => $movie->casts->map(function ($cast) {
                    return [
                        "id" => $cast->id,
                        "name" => $cast->name,
                        "age" => $cast->age,
                        "bio" => $cast->bio,
                        "created_at" => $cast->created_at,
                        "updated_at" => $cast->updated_at,
                        "pivot" => [
                            "movie_id" => $cast->pivot->movie_id,
                            "cast_id" => $cast->pivot->cast_id,
                        ],
                    ];
                }),
                "list_reviews" => $movie->reviews->map(function ($review) {
                    return [
                        "id" => $review->id,
                        "critic" => $review->critic,
                        "rating" => $review->rating,
                        "user_id" => $review->user_id,
                        "movie_id" => $review->movie_id,
                        "created_at" => $review->created_at,
                        "updated_at" => $review->updated_at,
                    ];
                }),
                "average_rating" => $averageRating,
            ],
        ], 201);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(MovieRequest $request, string $id)
    {

        Log::info('Request Data:', $request->all());

        $data = $request->validated();
        $movieData = Movie::find($id);

        if (!$movieData) {
            return response()->json([
                "message" => "id movie tidak ditemukan"
            ], 404);
        }

        if ($request->hasFile('poster')) {
            // Hapus gambar lama jika ada
            if ($movieData->poster) {
                $imageName = basename($movieData->poster);
                Storage::delete('public/images/' . $imageName);
            }

            // Buat unique name gambar yang baru
            $imageName = time() . "-poster." . $request->poster->extension();
            $request->poster->storeAs('public/images', $imageName);

            $path = env('APP_URL') . '/storage/images/';
            $data['poster'] = $path . $imageName;
        }

        $movieData->update($data);

        return response()->json([
            "message" => "Update Movie berhasil",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $movieData = Movie::find($id);

        if (!$movieData) {
            return response()->json([
                "message" => "id tidak ditemukan"
            ], 404);
        }

        if ($movieData->poster) {
            $imageName = basename($movieData->poster);
            Storage::delete('public/images/' . $imageName);
        }

        $movieData->delete();

        return response()->json([
            "message" => "Berhasil Menghapus Movie dengan id $id",
        ], 201);
    }
}
