<?php

namespace App\Http\Controllers\API;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\Request;
use App\Http\Requests\GenreRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'isAdmin'])->except(['index', 'show', 'getMoviesByGenre']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $genre = Genre::all();

        return response()->json([
            'message' => 'tampil data berhasil',
            'data' => $genre,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(GenreRequest $request)
    // {
    //     Genre::create($request->all());

    //     return response()->json([
    //         "message" => "Tambah Genre berhasil"

    //     ], 201);
    // }
    // In your GenreController.php

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $genre = new Genre;
        $genre->name = $request->name;
        $genre->save();

        return response()->json($genre, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $genre = Genre::with('movies')->find($id);

        if (!$genre) {
            return response()->json([
                "message" => "id tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Berhasil Detail data dengan id $id",
            "data" => [
                "id" => $genre->id,
                "name" => $genre->name,
                "created_at" => $genre->created_at,
                "updated_at" => $genre->updated_at,
                "list_movies" => $genre->movies->map(function ($movie) {
                    return [
                        "id" => $movie->id,
                        "title" => $movie->title,
                        "summary" => $movie->summary,
                        "poster" => $movie->poster,
                        "year" => $movie->year,
                        "genre_id" => $movie->genre_id,
                        "created_at" => $movie->created_at,
                        "updated_at" => $movie->updated_at,
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
        $genre = Genre::find($id);

        if (!$genre) {
            return response()->json([
                "message" => "id tidak ditemukan"
            ], 404);
        }

        $genre->name = $request['name'];

        $genre->save();

        return response()->json([
            "message" => "Update Genre berhasil",
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $genre = Genre::find($id);

        if (!$genre) {
            return response()->json([
                "message" => "id tidak ditemukan"
            ], 404);
        }

        $genre->delete();

        return response()->json([
            "message" => "Berhasil Menghapus Genre dengan id $id",
        ], 201);
    }

    // Fungsi untuk mendapatkan film berdasarkan genre
    public function getMoviesByGenre($genreId)
    {
        // Cek jika genre ada di database
        $genre = Genre::find($genreId);
        if (!$genre) {
            return response()->json(['message' => 'Genre not found'], 404);
        }

        // Ambil film yang terkait dengan genre ini
        $movies = Movie::whereHas('genre', function ($query) use ($genreId) {
            $query->where('id', $genreId);
        })->get();

        return response()->json($movies);
    }

}
