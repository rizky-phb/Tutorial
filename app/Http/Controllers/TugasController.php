<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\Activity;
use App\Models\Category;
use Illuminate\Http\Request;

class TugasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tugas = Tugas::where('draft', false)
            ->with('category', 'quiz')
            ->search(request('search'))
            ->category(request('category'))
            ->sort(request('sort'))
            ->paginate(9)
            ->withQueryString();
        return view('pages.tugas.index', [
            'title' => 'tugas',
            'tugas' => $tugas,
            'categories' => Category::get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $tugas = Tugas::where('slug', $slug)->with('quiz')->firstOrFail();
        if ($tugas->draft) {
            return redirect(route('tugas.index'))
                ->with('alert', 'info')
                ->with('html', "Tugas <strong>{$tugas->title}</strong> belum dapat diakses untuk saat ini.");
        }
        Activity::updateOrInsert(
            [
                'user_id' => auth()->user()->id,
                'tugas_id' => $tugas->id
            ],
            [
                'status' => 'study',
                'updated_at' => now()
            ]
        );
        return view('pages.tugas.show', [
            'title' => $tugas->title,
            'tugas' => $tugas
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tugas $tugas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tugas $tugas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tugas $tugas)
    {
        //
    }

    /**
     * Autocomplete ajax.
     */
    public function search(Request $request)
    {
        $titles = Tugas::select('title')
            ->where('draft', false)
            ->where('title', 'LIKE', "%$request->keyword%")
            ->limit(5)
            ->orderBy('title')
            ->pluck('title')
            ->toArray();
        return response()->json($titles);
    }
}
