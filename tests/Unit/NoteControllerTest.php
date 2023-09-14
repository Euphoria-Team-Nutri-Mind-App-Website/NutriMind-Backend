<?php

namespace Tests\Unit;

use App\Models\Note;
use App\Models\Patient;
use App\Http\Controllers\API\NoteController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    use WithFaker;

    public function testIndexReturnsAllNotesForPatient()
    {
        $patient = Patient::factory()->create();

        $notes = Note::factory(10)->create([
            'patient_id' => $patient->id,
        ]);

        $this->actingAs($patient);

        $controller = new NoteController();

        $response = $controller->index();
        $result = $response->getData();

        $this->assertTrue($result->success);

        $this->assertNull($result->message);

        $this->assertCount(10, $result->notes);
        $this->assertEquals($notes[0]->body, $result->notes[0]->body);
        $this->assertEquals($notes[0]->patient_id, $result->notes[0]->patient_id);
    }

    public function testIndexHaveNoNotes()
    {
        $patient = Patient::factory()->create();

        $this->actingAs($patient);

        $controller = new NoteController();

        $response = $controller->index();
        $result = $response->getData();

        $this->assertTrue($result->success);

        $this->assertNull($result->message);

        $this->assertCount(0, $result->notes);
        $this->assertEquals([], $result->notes);
    }

    public function testStoreAddsNoteSuccessfully()
    {
        $patient = Patient::factory()->create();

        $this->actingAs($patient);

        $noteBody = $this->faker->sentence;

        $request = Request::create('/notes', 'POST', ['body' => $noteBody]);

        $controller = new NoteController();

        $response = $controller->store($request);

        $this->assertTrue($response->getData()->success);

        $this->assertCount(1, Note::all());

        $createdNote = Note::first();
        $this->assertEquals($noteBody, $createdNote->body);
        $this->assertEquals($patient->id, $createdNote->patient_id);

        $this->assertEquals('Note added successfully.', $response->getData()->message);
    }

    public function testStoreDoesNotAddNoteWhenRequestBodyIsEmpty()
    {
        $patient = Patient::factory()->create();

        $this->actingAs($patient);

        $request = Request::create('/notes', 'POST', ['body' => '']);

        $controller = new NoteController();

        $response = $controller->store($request);

        $this->assertNull($response);
    }

    public function testEditReturnsNoteSuccessfully()
    {
        $patient = Patient::factory()->create();

        $this->actingAs($patient);

        $note = Note::factory()->create();


        $request = Request::create('/notes/' . $note->id . '/edit', 'GET');

        $controller = new NoteController();

        $response = $controller->edit($note->id);

        $this->assertTrue($response->getData()->success);

        $this->assertEquals($note->id, $response->getData()->data->id);
        $this->assertEquals($note->body, $response->getData()->data->body);
        $this->assertEquals($note->patient_id, $response->getData()->data->patient_id);
    }

    public function testEditReturnsNotFoundForInvalidNoteId()
    {
        $patient = Patient::factory()->create();
        $this->actingAs($patient);

        $noteId = 9999;

        $controller = new NoteController();

        $response = $controller->edit($noteId);

        $this->assertObjectHasProperty('message', $response->getData());

        $this->assertNotNull($response->getData()->message);

        $this->assertEquals(
            ["You are not authorized to access this information."],
            $response->getData()->message->id
        );
    }
    public function testUpdateNoteSuccessfully()
    {
        $patient = Patient::factory()->create();
        $this->actingAs($patient);

        $note = Note::factory()->create();

        $request = Request::create('/notes/' . $note->id, 'POST', ['body' => 'Updated note body']);

        $controller = new NoteController();

        $response = $controller->update($request, $note->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertObjectHasProperty('success', $response->getData());
        $this->assertTrue($response->getData()->success);
        $this->assertEquals('Note updated successfully.', $response->getData()->message);
    }

    public function testUpdateNoteValidationFailure()
    {
        $patient = Patient::factory()->create();
        $this->actingAs($patient);

        $note = Note::factory()->create();

        $request = Request::create('/notes/' . $note->id, 'POST');

        $controller = new NoteController();

        $response = $controller->update($request, $note->id);

        $this->assertNull($response);
    }

    public function testUpdateNoteNotFound()
    {
        $patient = Patient::factory()->create();
        $this->actingAs($patient);

        $noteId = 9999;

        $request = Request::create('/notes/' . $noteId, 'POST', ['body' => 'Updated note body']);

        $controller = new NoteController();

        $response = $controller->update($request, $noteId);

        $this->assertObjectHasProperty('message', $response->getData());

        $this->assertNotNull($response->getData()->message);

        $this->assertEquals(
            ["You are not authorized to access this information."],
            $response->getData()->message->id
        );
    }
    public function testDestroyNoteSuccessfully()
    {
        $patient = Patient::factory()->create();
        $this->actingAs($patient);

        $note = Note::factory()->create();

        $controller = new NoteController();

        $response = $controller->destroy($note->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertObjectHasProperty('success', $response->getData());
        $this->assertTrue($response->getData()->success);
        $this->assertEquals('Note deleted successfully.', $response->getData()->message);
    }

    public function testDestroyNoteNotFound()
    {
        $patient = Patient::factory()->create();
        $this->actingAs($patient);

        $noteId = 9999;

        $controller = new NoteController();

        $response = $controller->destroy($noteId);

        $this->assertObjectHasProperty('message', $response->getData());

        $this->assertNotNull($response->getData()->message);

        $this->assertEquals(
            ["You are not authorized to access this information."],
            $response->getData()->message->id
        );
    }
    public function testSearchNotesSuccessfully()
{
    $patient = Patient::factory()->create();
    $this->actingAs($patient);

    $keyword="keyword";

    $note1 = Note::factory()->create(['body' => 'Note with keyword']);
    $note2 = Note::factory()->create();
    $note3 = Note::factory()->create([ 'body' => 'Another note with keyword']);

    $request = Request::create('/search', 'GET', ['body' => "$keyword"]);

    $controller = new NoteController();

    $response = $controller->search($request);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertObjectHasProperty('notes', $response->getData());
    $this->assertCount(2, $response->getData()->notes);
    $this->assertEquals($note1->id, $response->getData()->notes[0]->id);
    $this->assertEquals($note3->id, $response->getData()->notes[1]->id);
}

public function testSearchNotesNoResults()
{
    $patient = Patient::factory()->create();
    $this->actingAs($patient);

    $request = Request::create('/search', 'GET', ['body' => 'keyword']);

    $controller = new NoteController();

    $response = $controller->search($request);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertObjectHasProperty('notes', $response->getData());
    $this->assertCount(0, $response->getData()->notes);
}
}
