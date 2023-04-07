<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoteResource;
use App\Models\Notes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    public function index()
    {
        //get all posts
        $notes = Notes::latest()->paginate(10);

        //return collection of posts as a resource
        return new NoteResource(true, 'List Notes', $notes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $new_notes = Notes::create([
            'title'         => $request->title,
            'description'   => $request->description,
        ]);

        return new NoteResource(true,'Notes Baru Berhasil Ditambahkan',$new_notes);
    }

    public function show($id)
    {
        //find post by ID
        $note = Notes::find($id);

        //return single post as a resource
        return new NoteResource(true, 'Detail Data Note!', $note);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $note = Notes::find($id);

        if (!$note){
            return response()->json("note not found", 404);
        }

        $note->update([
            'title'         => $request->title,
            'description'   => $request->description,
        ]);

        return new NoteResource(true,'Notes berhasil diupdate',$note);
    }

    public function destroy($id)
    {
        $note = Notes::find($id);

        if (!$note){
            return response()->json("note not found", 404);
        }

        $note->delete();

        return new NoteResource(true,'Notes berhasil dihapus',null);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'text' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $results = Notes::where('title','LIKE','%'.$request->text.'%')
        ->orWhere('description','LIKE','%'.$request->text.'%')
        ->paginate(10);

        return new NoteResource(true, 'List Searched Notes', $results);
    }
}
