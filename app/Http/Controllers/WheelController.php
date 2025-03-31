<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Wheel;
use App\Models\WheelClip;

class WheelController extends Controller
{
    /**
     * Display a listing of the wheel.
     */
    public function index()
    {
        // dd(Wheel::with('game')->first()->game->name);
        if (request()->ajax()) {
            return datatables()->of(Wheel::with('game')->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                $query->where('created_by', auth()->id());
            }))
                ->addColumn('action', function($row){
                    $btn  = '<a class="edit-wheel btn btn-sm btn-success btn-icon mr-1 white" ';
                    $btn .= 'href="' . route('wheel.edit', ['wheel' => $row->id]) . '" ';
                    $btn .= 'data-id="' . $row->id . '" title="Edit">';
                    $btn .= '<i class="fa fa-edit fa-1x"></i>';
                    $btn .= '</a>';
                    $btn .= '<a class="delete-wheel btn btn-sm btn-danger btn-icon mr-1 white" ';
                    $btn .= 'data-id="' . $row->id . '" title="Delete">';
                    $btn .= '<i class="fa fa-trash fa-1x"></i>';
                    $btn .= '</a>';
                    return $btn;
                })
                ->addColumn('creator', function($row) {
                    // dump($row->creator->name);
                    return $row->creator->name ?? 'N/A';
                })
                ->editColumn('game', function($row) {
                    return $row->game->name;
                })
                ->editColumn('clips_count', function($row) {
                    return $row->clips()->count();
                })
                ->editColumn('created_at', function($row) {
                    return date('d-m-Y',strtotime($row->created_at));
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('wheel.index', ['wheel' => new Wheel()]);
    }

    /**
     * Show the form for creating a new wheel.
     */
    public function create()
    {
        $games = Game::all(); // Assumes a Game model exists
        return view('wheel.create', compact('games'));
    }

    /**
     * Store a newly created wheel in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'clips' => 'required|array',
            'clips.*.text' => 'required|string'
        ]);

        $wheel = Wheel::create(['game_id' => $request->game_id, 'created_by' => auth()->id()]);

        foreach ($request->clips as $clip) {
            WheelClip::create([
                'wheel_id' => $wheel->id,
                'text' => $clip['text'],
                'game_clip_id' => $clip['id'],
            ]);
        }

        return redirect()->route('wheel.index')->with('success', 'Wheel created successfully!');
    }

    /**
     * Show the form for editing the specified wheel.
     */
    public function edit(Wheel $wheel)
    {
        $games = Game::all();
        // Eager load clips relation
        // $wheel->load('clips');
        
        $wheel = Wheel::with(['game','clips.gameClip'])->first();
        // dd($wheel->clips[0]->gameClip->text_length);
        return view('wheel.edit', compact('wheel', 'games'));
    }

    /**
     * Update the specified wheel in storage.
     */
    // public function update(Request $request, Wheel $wheel)
    // {
    //     $request->validate([
    //         'game_id' => 'required|exists:games,id',
    //         'clips.*.text' => 'required|string',
    //     ]);

    //     // Check if the game is changed
    //     if ($wheel->game_id != $request->game_id) {
    //         // If the game is changed, remove all old clips
    //         $wheel->clips()->delete();
    //     }

    //     // Update Wheel Game Selection
    //     $wheel->update(['game_id' => $request->game_id]);

    //     // Insert New Clips (after deleting old ones if the game was changed)
    //     $newClips = [];
    //     foreach ($request->clips as $clipData) {
    //         $newClips[] = [
    //             'wheel_id' => $wheel->id,
    //             'text' => substr($clipData['text'], 0, 255), // Limiting text length
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ];
    //     }

    //     // Insert new clips in bulk
    //     WheelClip::insert($newClips);

    //     return redirect()->route('wheel.index')->with('success', 'Wheel updated successfully.');
    // }

    public function update(Request $request, Wheel $wheel)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'clips' => 'required|array',
            'clips.*.text' => 'required|string|max:255',
        ]);

        // Update wheel details
        $wheel->update(['game_id' => $request->game_id]);

        // Store existing clip IDs
        $existingClipIds = $wheel->clips()->pluck('id')->toArray();
        $newClipIds = [];

        foreach ($request->clips as $clipData) {
            $clip = WheelClip::updateOrCreate(
                [
                    'wheel_id' => $wheel->id,
                    'id' => $clipData['id'] ?? null // Check if ID exists, otherwise insert
                ],
                [
                    'text' => substr($clipData['text'], 0, 255),
                    'game_clip_id' => $clipData['id'],
                    'updated_at' => now(),
                ]
            );

            // Keep track of updated/created clips
            $newClipIds[] = $clip->id;
        }

        // Delete clips that were removed from the request
        $clipsToDelete = array_diff($existingClipIds, $newClipIds);
        WheelClip::whereIn('id', $clipsToDelete)->delete();

        return redirect()->route('wheel.index')->with('success', 'Wheel updated successfully.');
    }


    /**
     * Remove the specified wheel from storage.
     */
    public function destroy(Wheel $wheel)
    {
       // Delete the wheel along with its associated clips
       $wheel->clips()->delete(); // Delete related clips first
       $wheel->delete(); // Then delete the wheel

       return response()->json(['msg' => 'Wheel deleted successfully!'], 200);
    }

    public function getClipsByGame(Request $request)
    {
        $gameId = $request->game_id;
        $game = Game::with('clipData')->find($gameId);

        if (!$game) {
            return response()->json(['clips' => []]);
        }

        // dd($game->clipData);
        // Fetch related clips and text_length
        $clips = $game->clipData->map(function ($clip) {
            return [
                'id' => $clip->id,
                'text_length' => $clip->text_length // Assuming text_length is based on the length of 'text'
            ];
        });

        return response()->json(['clips' => $clips]);
    }

}
