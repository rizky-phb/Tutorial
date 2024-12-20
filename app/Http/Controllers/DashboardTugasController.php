<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\DashboardTugasRequest;

class DashboardTugasController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.dashboard.tugas.index', [
      'title' => 'Tugas Management',
      'tugas' => Tugas::with('category')->get()
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('pages.dashboard.tugas.form', [
      'title' => 'Add New Tugas',
      'categories' => Category::get()
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(DashboardTugasRequest $request)
  {
    $data = $request->validated();
    $cover = "{$data['slug']}.{$data['cover']->extension()}";
    $request->file('cover')->move(public_path('/img/tugas'), $cover);
    $data['cover'] = "/img/tugas/$cover";
    $data['added_by'] = auth()->user()->full_name;
    $data['last_edited_by'] = auth()->user()->full_name;
    $data['draft'] = $data['submit'] == 'draft' ? 1 : ($data['submit'] == 'done' ? 0 : 1);
    Tugas::create($data);
    return redirect(route('tugas.show', $data['slug']))
      ->with('alert', 'success')
      ->with('html', "Tugas <strong>{$data['title']}</strong> berhasil ditambahkan!");
  }


  /**
   * Display the specified resource.
   */
  public function show(Tugas $tugas)
  {
    return view('pages.dashboard.tugas.show', [
      'title' => $tugas->title,
      'tugas' => $tugas
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Tugas $tugas)
  {
    return view('pages.dashboard.tugas.form', [
      'title' => 'Edit Tugas: ' . $tugas->title,
      'tugas' => $tugas,
      'categories' => Category::get()
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(DashboardTugasRequest $request, Tugas $tugas)
  {
    $data = $request->validated();
    if (isset($data['cover'])) {
      try {
        unlink(public_path($tugas->cover));
      } catch (\Exception $e) {
        // Do nothing
      }
      $cover = $data['slug'] . '.' . $data['cover']->extension();
      $request->file('cover')->move(public_path('img/tugas'), $cover);
      $data['cover'] = "/img/tugas/$cover";
    } elseif (!isset($data['cover']) && $data['slug'] != $tugas->slug) {
      try {
        $file_extension = pathinfo($tugas->cover, PATHINFO_EXTENSION);
        $new_path = "/img/tugas/{$data['slug']}.$file_extension";
        File::move(public_path($tugas->cover), public_path($new_path));
        $data['cover'] = $new_path;
      } catch (\Exception $e) {
        // Do nothing
      }
    }
    ;
    $data['last_edited_by'] = auth()->user()->full_name;
    $data['draft'] = $data['submit'] == 'draft' ? 1 : ($data['submit'] == 'done' ? 0 : 1);
    $tugas->update($data);
    return response()
      ->redirectTo(route('tugas.show', $data['slug']))
      ->with('alert', 'success')
      ->with('html', "Tugas <strong>{$data['title']}</strong> berhasil diupdate!")
      ->withHeaders([
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
      ]);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Tugas $tugas)
  {
    $tugas->delete();
    if (str_contains($tugas->cover, "https://")) {
      // Tidak melakukan apa-apa jika mengandung "https://"
  } else {
      unlink(public_path($tugas->cover));
  }
    return redirect(route('tugas.index'))
      ->with('alert', 'success')
      ->with('html', 'Tugas <strong>' . $tugas->title . '</strong> berhasil dihapus!');
  }
}
