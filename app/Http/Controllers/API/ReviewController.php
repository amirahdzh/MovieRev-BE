<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'critic' => 'required|string',
            'rating' => 'required|integer',
            'movie_id' => 'required|exists:movies,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $currentUser = auth()->user();

        $user = User::find($currentUser->id);

        // apakah review bisa berkali-kali dilakukan oleh 1 user kepada movie yang sama?
        // atau user hanya bisa sekali mereview setiap filmnya?

        // kode di bawah ditulis dengan asumsi bahwa user hanya bisa 1x review/film

        $reviewData = $user->reviews()->updateOrCreate(
            ['user_id' => $user->id], // Kondisi untuk menentukan apakah harus mengupdate atau membuat baru
            [
                'critic' => $request->input('critic'),
                'rating' => $request->input('rating'),
                'movie_id' => $request->input('movie_id'),
            ]
        );
    
        // Mengembalikan respons berhasil
        return response()->json([
            'message' => 'Review created!',
            'profile' => $reviewData,
        ], 200);
    }
}
