<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Cast;
use App\Models\CastMovie;
use App\Models\User;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\Review;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);

        Genre::create(['name' => "Action"]);
        Cast::create([
            'name' => 'Tony Stark',
            'age' => 20,
            'bio' => 'This is Tony Stark bio',
        ]);
        $dataUser = User::where('name', 'admin')->first();
        Profile::create([
            'age' => 20,
            'biodata' => 'This is admin biodata',
            'address' => 'planet Mars',
            'user_id' => $dataUser->id
        ]);
        $dataGenre = Genre::first();
        Movie::create([
            'title' => 'Captain America',
            'summary' => 'This is the summary of captain america movie',
            // 'poster' => 'image.jpg', 
            'year' => '2019',
            'genre_id' => $dataGenre->id
        ]);
        $dataMovie = Movie::first();
        Review::create([
            'critic' => 'This is captain america movie critics',
            'rating' => 9,
            'user_id' => $dataUser->id,
            'movie_id' => $dataMovie->id
        ]);
        $dataCast = Cast::first();
        CastMovie::create([
            'cast_id' => $dataCast->id,
            'movie_id' => $dataMovie->id
        ]);
    }
}
