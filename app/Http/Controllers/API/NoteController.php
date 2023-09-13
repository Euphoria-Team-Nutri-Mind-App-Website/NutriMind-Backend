<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoteRequest;
use App\Models\Note;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    use GeneralTrait;

    public function index(Request $request)
    {
        return $this->getData($request, Note::class);
    }

    public function store(NoteRequest $request)
    {
        $validated = $request->validated();

        if (!empty($request->title)) {
            Note::create([
                'title' => $request->title,
                'body' => $request->body,
                'patient_id' => $request->patient_id
            ]);

            return $this->returnSuccess('Note added successfully.');
        } elseif (!empty($request->body)) {
            Note::create([
                'title' => substr($request->body, 0, 255),
                'body' => $request->body,
                'patient_id' => $request->patient_id
            ]);

            return $this->returnSuccess('Note added successfully.');
        }
    }

    public function edit($noteId)
    {
        return $this->viewOne($noteId, Note::class, 'notes', 'id');
    }

    public function update(NoteRequest $request, Note $note)
    {
        $validated = $request->validated();

        if (!empty($request->title)) {
            $note->update([
                'title' => $request->title,
                'body' => $request->body,
                'patient_id' => $request->patient_id
            ]);

            return $this->returnSuccess('Note updated successfully.');
        } elseif (!empty($request->body)) {
            $note->update([
                'title' => substr($request->body, 0, 255),
                'body' => $request->body,
                'patient_id' => $request->patient_id
            ]);

            return $this->returnSuccess('Note updated successfully.');
        }
    }

    public function destroy($noteId)
    {
        return $this->destroyData($noteId, 'App\Models\Note', 'notes');
    }

    public function search(NoteRequest $request)
    {
        $validated = $request->validated();

        $bodyFilter = $request->body;
        $titleFilter = $request->title;

        $notes = Note::query()
            ->where('body', 'LIKE', "%{$bodyFilter}%")
            ->orWhere('title', 'LIKE', "%{$titleFilter}%")
            ->get();

        return $this->returnData('notes', $notes);
    }
}
