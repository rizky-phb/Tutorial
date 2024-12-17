<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\User;
use App\Models\QuizAnswer;
use Illuminate\Support\Str;
use App\Models\QuizQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class tugasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        
        $faker = fake('id_ID');

        // Tugas
        foreach (range(1, 20) as $x) {
            $category = $faker->numberBetween(1, 10);
            $title = ucwords($faker->words($faker->numberBetween(2, 4), true));
            DB::table('tugas')->insert([
                [
                    'draft' => false,
                    'category_id' => $category,
                    'title' => $title,
                    'slug' => Str::slug($title),
                    'cover' => "https://picsum.photos/640/360?random=$x",
                    'desc' => $faker->text(200),
                    'body' => $faker->text($faker->numberBetween(1000, 3000)),
                    // 'favorite' => $faker->numberBetween(1, 100),
                    'added_by' => $faker->name(),
                    'last_edited_by' => $faker->name(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);


            // Quizzes
            $quiz = Quiz::create([
                'name' => $title,
                'status' => 'Published', //$faker->randomElement(['Published', 'Draft']),
                'course_id' => 0,
                'tugas_id' => $x,
                // 'time_limit' => $x % 2 ? $faker->numberBetween(1000, 2000) : null,
                'time_limit' => $faker->numberBetween(1000, 2000),
                'added_by' => $faker->name(),
                'last_edited_by' => $faker->name(),
            ]);
            foreach (range(1, 10) as $i) {
                $question = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question' => $faker->text(100),
                    'answer_explanation' => $faker->text(200),
                ]);
                foreach (range(1, 4) as $j) {
                    $exist = QuizAnswer::where('quiz_question_id', $question->id)->where('is_correct', 1)->exists();
                    $correct = $faker->numberBetween(0, 1);
                    if ($exist) {
                        $correct = 0;
                    } elseif (!$exist && $j == 4) { //jika belum ada jawaban benar, jawaban ke 4 dibuat benar
                        $correct = 1;
                    }
                    QuizAnswer::create([
                        'quiz_question_id' => $question->id,
                        'answer' => $faker->text(50),
                        'is_correct' => $correct,
                    ]);
                }
            }
        }
    }
}
