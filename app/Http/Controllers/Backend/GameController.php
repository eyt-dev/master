<?php

namespace App\Http\Controllers\Backend;

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
            return datatables()->of(\App\Models\Game::select('*')->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                }))
                ->addColumn('action', function($row){
                    $btn  = '<a class="edit-game btn btn-sm btn-success btn-icon mr-1 white" ';
                    $btn .= 'href="' . route('game.edit', ['username' => request()->segment(1), 'game' => $row->id]) . '" ';
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
                ->editColumn('created_by', function($row) {
                    // Assuming 'visibility' is stored as boolean/integer:
                    return $row->admin->name;
                })
                ->editColumn('display', function($row) {
                    // Adjust display formatting if needed
                    return ucfirst($row->display);
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.game.index', ['game' => new Game()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($siteUrl)
    {
        return view('backend.game.create', ['game' => new Game()]);
    }

    public function store(Request $request, $siteUrl)
    {
        // If display is not provided and type is not Flixable, force it to image.
        if (!$request->display && $request->type != 'Flixable') {
            $request['display'] = 'image';
        }
        if ($request->type == 'standard') {
            $request['clips_count'] = 0;
        }
        
        // Validate the incoming request data.
        // You may want to add a file rule for standard_image and clip image uploads.
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'type'              => 'required|in:Flixable,textable,standard',
            'visibility'        => 'required|in:private,global',
            'display'           => 'required|in:color,image',
            'clips_count'       => 'required|integer',
            'standard_image'    => 'sometimes|required|file',  // file upload for standard type.
            // Clip fields are arrays; each row must have these values:
            'text_length.*'     => 'required|integer|min:1|max:30',
            'text_orientation.*'=> 'required|in:H,V',
            'color.*'           => 'nullable|string',
            'image.*'           => 'nullable|file',  // file upload for clip image.
        ]);

        // Create the game record.
        // (Make sure your games table has a 'standard_image' column if needed.)
        $game = Game::create([
            'name'       => $validated['name'],
            'type'       => $validated['type'],
            // For example, we treat "global" as true and "private" as false.
            'visibility' => $validated['visibility'] === 'global',
            'display'    => $validated['display'],
            'clips'      => $validated['clips_count'],
            'created_by' => auth()->user()->id,
        ]);

        // If the game is standard, process the standard image upload.
        if ($request->type != 'Flixable') {
            if ($request->hasFile('standard_image')) {
                $standardImagePath = $request->file('standard_image')->store('standardgames', 'public');
                // Update game with the standard image path.
                $game->update(['image' => $standardImagePath]);
            }
        } 

        if ($request->type != 'standard') {
            // Create the related game clip records.
            $clipsCount = $validated['clips_count'];
            for ($i = 0; $i < $clipsCount; $i++) {
                // Process each clip's image upload if available.
                $clipImagePath = null;
                if ($request->hasFile("image.$i")) {
                    // Store in 'games' folder on the public disk.
                    $clipImagePath = $request->file('image')[$i]->store('games', 'public');
                }
                $game->clipData()->create([
                    'text_length'      => $validated['text_length'][$i],
                    'text_orientation' => $validated['text_orientation'][$i],
                    'color'            => $validated['color'][$i] ?? null,
                    // If there's an uploaded file, use its path; otherwise, fallback to any provided string.
                    'image'            => $clipImagePath ?? ($validated['image'][$i] ?? null),
                ]);
            }
        }

        return redirect()->route('game.index', ['username' => request()->segment(1)])
                        ->with('success', 'Game created successfully.');
    }


    /**
     * Show the edit form for a game.
     */
    public function edit($siteUrl, $id)
    {
        // Load the game with its related clips.
        $game = Game::with('clipData')->findOrFail($id);
        return view('backend.game.edit', compact('game'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        if (!$request->display && $request->type != 'Flixable') {
            $request['display'] = 'image';
        }
        if ($request->type == 'standard') {
            $request['clips_count'] = 0;
        }

        // Validate incoming data.
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'type'               => 'required|in:Flixable,textable,standard',
            'visibility'         => 'required|in:private,global',
            'display'            => 'required|in:color,image',
            'clips_count'        => 'required|integer',
            'standard_image'     => 'sometimes|nullable|file',
            'text_length.*'      => 'required_if:type,Flixable,textable|integer|min:1|max:30',
            'text_orientation.*' => 'required_if:type,Flixable,textable|in:H,V',
            'color.*'            => 'nullable|string',
            'image.*'            => 'nullable|file',
        ]);

        $game = Game::findOrFail($id);

        // Update game details.
        $game->update([
            'name'       => $validated['name'],
            'type'       => $validated['type'],
            'visibility' => $validated['visibility'] === 'global',
            'display'    => $validated['display'],
            'clips'      => $validated['clips_count'],
        ]);

        // Handle standard image upload.
        if ($request->type != 'Flixable') {
            if ($request->hasFile('standard_image')) {
                $standardImagePath = $request->file('standard_image')->store('standardgames', 'public');
                $game->update(['image' => $standardImagePath]);
            }
        } 
        
        if ($request->type != 'standard') {
            // Fetch existing clips
            $existingClips = $game->clipData;
            $existingCount = $existingClips->count();
            $newCount = $validated['clips_count'];

            // Loop through existing clips and update them.
            
            for ($i = 0; $i < $existingCount; $i++) {
                if ($i < $newCount) {
                    $clip = $existingClips[$i];
                    // dump($validated['text_length'][3]);
                    $clip->update([
                        'text_length'      => $validated['text_length'][$i],
                        'text_orientation' => $validated['text_orientation'][$i],
                        'color'            => $validated['color'][$i] ?? $clip->color,
                    ]);

                    // Process clip image upload if a new image is provided.
                    if ($request->hasFile("image.$i")) {
                        $clipImagePath = $request->file('image')[$i]->store('games', 'public');
                        $clip->update(['image' => $clipImagePath]);
                    }
                } else {
                    // If the new count is lower than existing clips, remove the extra ones.
                    $existingClips[$i]->delete();
                }
            }

            // Append new clips if count increased.
            for ($i = $existingCount; $i < $newCount; $i++) {
                $clipData = [
                    'text_length'      => $validated['text_length'][$i],
                    'text_orientation' => $validated['text_orientation'][$i],
                    'color'            => $validated['color'][$i] ?? null,
                    'game_id'          => $game->id, // Ensure association with the game
                ];

                // Handle image upload for new clips.
                if ($request->hasFile("image.$i")) {
                    $clipImagePath = $request->file('image')[$i]->store('games', 'public');
                    $clipData['image'] = $clipImagePath;
                }

                $game->clipData()->create($clipData);
            }
        }

        return redirect()->route('game.index', ['username' => request()->segment(1)])
            ->with('success', 'Game updated successfully.');
    }

    public function destroy($siteUrl, $id)
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
