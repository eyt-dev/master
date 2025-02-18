<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(\App\Models\Game::select('*'))
                ->addColumn('action', function($row){
                    $btn  = '<a class="edit-game btn btn-sm btn-success btn-icon mr-1 white" ';
                    $btn .= 'href="' . route('game.edit', ['game' => $row->id]) . '" ';
                    $btn .= 'data-name="' . $row->name . '" ';
                    $btn .= 'data-id="' . $row->id . '" title="Edit">';
                    $btn .= '<i class="fa fa-edit fa-1x"></i>';
                    $btn .= '</a>';
                    $btn .= '<a class="delete-game btn btn-sm btn-danger btn-icon mr-1 white" ';
                    $btn .= 'data-id="' . $row->id . '" title="Delete">';
                    $btn .= '<i class="fa fa-trash fa-1x"></i>';
                    $btn .= '</a>';
                    return $btn;
                })
                ->editColumn('visibility', function($row) {
                    // Assuming 'visibility' is stored as boolean/integer:
                    return $row->visibility ? 'Global' : 'Private';
                })
                ->editColumn('display', function($row) {
                    // Adjust display formatting if needed
                    return ucfirst($row->display);
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('game.index', ['game' => new Game()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('game.create', ['game' => new Game()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'type'              => 'required|in:Flixable,textable,standard',
            'visibility'        => 'required|in:private,global',
            'display'           => 'required|in:color,image',
            'clips_count'       => 'required|integer|min:1|max:30',
            'created_by'        => 'required|integer',
            // Clip fields are arrays; each row must have these values:
            'text_length.*'     => 'required|integer|min:1|max:5',
            'text_orientation.*'=> 'required|in:H,V',
            'color.*'           => 'nullable|string',
            'image.*'           => 'nullable|string',
        ]);

        // Create the game record.
        // Note: Adjust the visibility/storage as needed.
        $game = Game::create([
            'name'       => $validated['name'],
            'type'       => $validated['type'],
            // For example, we treat "global" as true and "private" as false.
            'visibility' => $validated['visibility'] === 'global',
            'display'    => $validated['display'],
            'clips'      => $validated['clips_count'],
            'created_by' => auth()->user()->id,
        ]);

        // Create the related game clip records.
        $clipsCount = $validated['clips_count'];
        for ($i = 0; $i < $clipsCount; $i++) {
            $game->clipData()->create([
                'text_length'     => $validated['text_length'][$i],
                'text_orientation'=> $validated['text_orientation'][$i],
                'color'           => $validated['color'][$i] ?? null,
                'image'           => $validated['image'][$i] ?? null,
            ]);
        }

        return redirect()->route('game.index')
                         ->with('success', 'Game created successfully.');
    }

    /**
     * Show the edit form for a game.
     */
    public function edit($id)
    {
        // Load the game with its related clips.
        $game = Game::with('clipData')->findOrFail($id);
        return view('game.edit', compact('game'));
    }

    /**
     * Update the specified game and its clips.
     */
    public function update(Request $request, $id)
    {
        // Validate incoming data.
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'type'               => 'required|in:Flixable,textable,standard',
            'visibility'         => 'required|in:private,global',
            'display'            => 'required|in:color,image',
            'clips_count'        => 'required|integer|min:1|max:30',
            'created_by'         => 'required|integer',
            'text_length.*'      => 'required|integer|min:1|max:5',
            'text_orientation.*' => 'required|in:H,V',
            'color.*'            => 'nullable|string',
            'image.*'            => 'nullable|string',
        ]);

        $game = Game::findOrFail($id);

        // Update game information.
        $game->update([
            'name'       => $validated['name'],
            'type'       => $validated['type'],
            // Assume: 'global' means true, 'private' means false.
            'visibility' => $validated['visibility'] === 'global',
            'display'    => $validated['display'],
            'clips'      => $validated['clips_count'],
            'created_by' => $validated['created_by'],
        ]);

        // Remove existing clips (you could also update them individually if you prefer).
        $game->clipData()->delete();

        // Create new clip records.
        $clipsCount = $validated['clips_count'];
        for ($i = 0; $i < $clipsCount; $i++) {
            $game->clipData()->create([
                'text_length'      => $validated['text_length'][$i],
                'text_orientation' => $validated['text_orientation'][$i],
                'color'            => $validated['color'][$i] ?? null,
                'image'            => $validated['image'][$i] ?? null,
            ]);
        }

        return redirect()->route('game.index')
                         ->with('success', 'Game updated successfully.');
    }

    public function destroy($id)
    {
        // Retrieve the game along with its clips
        $game = Game::with('clipData')
            ->where('id',$id)
            ->first();
        // dd($game,$id);

        // Delete the associated game clips first
        $game->clipData()->delete();

        // Delete the game record
        $game->delete();

        // Return a JSON response (you can adjust the response as needed)
        return response()->json([
            'msg' => 'Game and its clips have been deleted successfully.'
        ]);
    }

}
