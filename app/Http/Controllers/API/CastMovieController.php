<?php

namespace App\Http\Controllers\API;

use App\Models\CastMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\CastMovieRequest;

class CastMovieController extends Controller
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
        // Eager loading relasi cast dan movie
        $castMovies = CastMovie::with(['cast', 'movie'])->get();

        // Format data yang akan dikembalikan dalam respons
        $data = $castMovies->map(function ($castMovie) {
            return [
                'id' => $castMovie->id,
                'name' => $castMovie->cast->name . ' - ' . $castMovie->movie->title,
                'cast_id' => $castMovie->cast_id,
                'movie_id' => $castMovie->movie_id,
                'created_at' => $castMovie->created_at,
                'updated_at' => $castMovie->updated_at,
            ];
        });

        // Kembalikan respons JSON
        return response()->json([
            'message' => 'Detail Data Cast Movie',
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CastMovieRequest $request)
    {
        // Cek apakah kombinasi cast_id dan movie_id sudah ada
        $exists = CastMovie::where('cast_id', $request->cast_id)
            ->where('movie_id', $request->movie_id)
            ->exists();

        if ($exists) {
            return response()->json([
                "message" => "Kombinasi Cast ID dan Movie ID sudah ada."
            ], 400);
        }

        CastMovie::create($request->all());

        return response()->json([
            "message" => "Tambah Cast-Movie Berhasil"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id = null)
    {
        // Jika ID tidak diberikan, gunakan movie_id dan cast_id dari query parameter
        if (!$id) {
            $movie_id = $request->query('movie_id');
            $cast_id = $request->query('cast_id');

            if (!$movie_id || !$cast_id) {
                return response()->json([
                    "message" => "movie_id dan cast_id harus diberikan"
                ], 400);
            }

            $castMovie = DB::table('cast_movie')
                ->where('movie_id', $movie_id)
                ->where('cast_id', $cast_id)
                ->first();

            if (!$castMovie) {
                return response()->json([
                    "message" => "Cast movie not found"
                ], 404);
            }

            $id = $castMovie->id; // Menggunakan ID yang ditemukan untuk mendapatkan detail lengkap
        }

        // Mencari data castMovie dengan relasi cast dan movie
        $castMovie = CastMovie::with(['cast', 'movie'])->find($id);

        if (!$castMovie) {
            return response()->json([
                "message" => "ID tidak ditemukan"
            ], 404);
        }

        // Format data yang akan dikembalikan dalam respons
        $data = [
            'id' => $castMovie->id,
            'name' => $castMovie->cast->name . ' - ' . $castMovie->movie->title,
            'cast_id' => $castMovie->cast_id,
            'movie_id' => $castMovie->movie_id,
            'created_at' => $castMovie->created_at,
            'updated_at' => $castMovie->updated_at,
        ];

        // Kembalikan respons JSON
        return response()->json([
            "message" => "Detail Data Cast-Movie",
            "data" => $data,
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(CastMovieRequest $request, string $id)
    {
        // Cari data castMovie berdasarkan ID
        $castMovie = CastMovie::find($id);

        // Jika data tidak ditemukan, kembalikan respons 404
        if (!$castMovie) {
            return response()->json([
                "message" => "ID tidak ditemukan"
            ], 404);
        }

        // Cek apakah kombinasi cast_id dan movie_id sudah ada selain dari data yang sedang diupdate
        $exists = CastMovie::where('cast_id', $request->cast_id)
            ->where('movie_id', $request->movie_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                "message" => "Kombinasi Cast ID dan Movie ID sudah ada."
            ], 400);
        }

        // Perbarui data castMovie menggunakan metode update
        $castMovie->update($request->validated());

        // Kembalikan respons berhasil
        return response()->json([
            "message" => "Update Cast Movie berhasil",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cast = CastMovie::find($id);

        if (!$cast) {
            return response()->json([
                "message" => "id tidak ditemukan"
            ], 404);
        }

        $cast->delete();

        return response()->json([
            "message" => "Berhasil Menghapus Cast Movie dengan id $id",
        ], 201);
    }

    public function getCastsForMovie($movieId)
    {
        // Ambil daftar cast yang terkait dengan movie
        $casts = CastMovie::with('cast')
            ->where('movie_id', $movieId)
            ->get()
            ->map(function ($castMovie) {
                return [
                    'id' => $castMovie->cast->id,
                    'name' => $castMovie->cast->name,
                ];
            });

        return response()->json([
            'message' => 'Daftar Cast untuk Movie',
            'data' => $casts,
        ], 200);
    }

    public function getCastMovieId(Request $request)
    {
        $movie_id = $request->query('movie_id');
        $cast_id = $request->query('cast_id');

        Log::info('Request received:', ['movie_id' => $movie_id, 'cast_id' => $cast_id]);

        $castMovie = CastMovie::where('movie_id', $movie_id)
                              ->where('cast_id', $cast_id)
                              ->first();

        if ($castMovie) {
            Log::info('Cast movie found:', ['id' => $castMovie->id]);
            return response()->json(['id' => $castMovie->id]);
        }

        return response()->json(['message' => 'Cast movie not found'], 404);
    }
}
