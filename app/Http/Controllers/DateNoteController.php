<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DateNote;

class DateNoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'note' => 'nullable|string'
        ]);
        
        DateNote::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'date' => $request->date,
            ],
            [
                'note' => $request->note
            ]
        );

        return response()->json(['status' => 'success']);
    }
    
    public function index()
    {
        $notes = DateNote::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('date')
            ->map(function ($items) {
                return [
                    'latest' => $items->first()->note,
                    'history' => $items->map(function ($item) {
                        return [
                            'note' => $item->note,
                            'created_at' => $item->created_at
                        ];
                    })
                ];
            });

        return response()->json($notes);
    }
}
