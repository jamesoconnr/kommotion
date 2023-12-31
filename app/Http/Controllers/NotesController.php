<?php

namespace App\Http\Controllers;

use App\Models\Notes;
use App\Models\Komote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
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
        if ($selectedNote != null){
            $canvasPath = $selectedNote['canvas'];
        } else {
            $canvasPath = null;
        }
        return Inertia::render('Dashboard', [
            'allNotes' => $allNotes,
            'selectedNote' => $selectedNote,
            'userKomote' => $userKomote,
            'canvasImg' => $canvasPath,
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
        if ($selectedNote != null){
            $canvasPath = $selectedNote['canvas'];
        } else {
            $canvasPath = 'Not Found In Controller';
        }
        return Inertia::render('Dashboard', [
            'allNotes' => $allNotes,
            'selectedNote' => $selectedNote,
            'userKomote' => $userKomote,
            'canvasImg' => $canvasPath,
        ]);
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
        $userID = Auth::id();
        $selectedNote = Notes::find($noteID);
        //$carbonCurrentTime = Carbon::now();
        //$currentTime = str_replace(' ', '_', $carbonCurrentTime);

        $canvasDataURL = $request['canvas'];
        list($type, $data) = explode(';', $canvasDataURL);
        list(, $data)      = explode(',', $data);
        $canvas = base64_decode($data);
        Storage::disk('public')->put('canvas/' . $userID . '/' . $noteID . '/canvas.png', $canvas);
        $canvasStorageLocation = asset('/storage/canvas/' . $userID . '/' . $noteID . '/canvas.png');
        
        $this->authorize('update', $selectedNote);
        $validated = $request->validate([
            'content' => '',
            'name' =>'required|string|max:20',
        ]);
        $validated['canvas'] = $canvasStorageLocation;

        $selectedNote->update($validated);
        
        return(redirect(route('notes.index')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notes $notes): RedirectResponse
    {
        $this->authorize('delete', $notes);
        //$this->authorize('delete', $chirp);
        $notes->delete();
        //$chirp->delete();
        //return redirect(route('chirps.index'));
    }
}
