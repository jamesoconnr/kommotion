<?php

namespace App\Http\Controllers;

use App\Models\Komote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KomoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(Komote $komote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Komote $komote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $komoteID)
    {
        $selectedKomote = Komote::find($komoteID);
        $this->authorize('update', $selectedKomote);

        $validated = $request->validate([
            'content' => '',
        ]);

        $selectedKomote->update($validated);
        #return Inertia::render('test', ['test' => $komoteID]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Komote $komote)
    {
        //
    }
}
