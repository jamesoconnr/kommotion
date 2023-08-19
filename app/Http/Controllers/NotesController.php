<?php

namespace App\Http\Controllers;

use App\Models\Notes;
use App\Models\Komote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('throttle:notes');
    }
    public function index()
    {
        $userID = Auth::id();
        $allNotes = Notes::all()->where('user_id', $userID);
        $selectedNote = $allNotes->where('user_id', $userID)->sortByDesc('updated_at')->first();
        $userKomote = Komote::all()->where('user_id', $userID)->first();
        return Inertia::render('Dashboard', [
            'allNotes' => $allNotes,
            'selectedNote' => $selectedNote,
            'userKomote' => $userKomote,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:15',
        ]);
 
        $request->user()->notes()->create($validated);
 
        #return Inertia::render('test');
        return redirect(route('notes.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $userID = Auth::id();
        $allNotes = Notes::all()->where('user_id', $userID);
        $selectedNote = $allNotes->where('id', $slug)->first();
        $userKomote = Komote::all()->where('user_id', $userID)->first(); 
        return Inertia::render('Dashboard', ['allNotes' => $allNotes, 'selectedNote' => $selectedNote, 'userKomote' => $userKomote]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notes $notes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $noteID): RedirectResponse
    {
        $selectedNote = Notes::find($noteID);
        $this->authorize('update', $selectedNote);
        $validated = $request->validate([
            'content' => '',
            'name' =>'required|string|max:20',
            'canvas' => ''
        ]);
        $selectedNote->update($validated);
        return(redirect(route('notes.index')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notes $notes)
    {
        //
    }
}
