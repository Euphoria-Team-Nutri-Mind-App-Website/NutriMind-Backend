<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoteRequest;
use App\Models\Note;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    use GeneralTrait;

    public function index()
    {
        $notes=Note::where('patient_id',Auth()->user()->id);
        return $this->returnData('notes',$notes);
    }

    public function store(Request $request)
    {
        if (!empty($request->body)) {
            Note::create([
                'body' => $request->body,
                'patient_id' => Auth()->user()->id
            ]);
            return $this->returnSuccess('Note added successfully.');
        }
    }

    public function edit($noteId)
    {
        return $this->viewOne($noteId, Note::class, 'notes', 'id');
    }

    public function update(Request $request, Note $note)
    {
        if (!empty($request->body)) {
            $note->update([
                'body' => $request->body,
            ]);

            return $this->returnSuccess('Note updated successfully.');
        }
    }

    public function destroy($noteId)
    {
        return $this->destroyData($noteId, 'App\Models\Note', 'notes');
    }

    public function search(Request $request)
    {
        $bodyFilter = $request->body;
        $notes = Note::query()
            ->where('body', 'LIKE', "%{$bodyFilter}%")
            ->where('patient_id',Auth()->user()->id)
            ->get();

        return $this->returnData('notes', $notes);
    }
}
