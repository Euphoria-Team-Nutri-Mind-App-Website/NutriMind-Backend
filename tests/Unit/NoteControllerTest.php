<?php

namespace Tests\Unit;

use App\Models\Note;
use App\Models\Patient;
use App\Http\Controllers\API\NoteController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    private $patient;
    private $noteController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patient = Patient::factory()->create();
        $this->actingAs($this->patient);
        $this->noteController = new NoteController();
    }

    public function testIndexReturnsAllNotesForPatient()
    {
        $notes = Note::factory(10)->create([
            'patient_id' => $this->patient->id,
        ]);

        $response = $this->noteController->index();
        $result = $response->getData();

        $this->assertTrue($result->success);
        $this->assertNull($result->message);
        $this->assertCount(10, $result->notes);
        $this->assertEquals($notes[0]->body, $result->notes[0]->body);
        $this->assertEquals($notes[0]->patient_id, $result->notes[0]->patient_id);
    }

    public function testIndexHaveNoNotes()
    {
        $response = $this->noteController->index();
        $result = $response->getData();

        $this->assertTrue($result->success);
        $this->assertNull($result->message);
        $this->assertCount(0, $result->notes);
        $this->assertEquals([], $result->notes);
    }

    public function testStoreAddsNoteSuccessfully()
    {
        $noteBody = fake()->sentence;

        $request = Request::create('/notes', 'POST', ['body' => $noteBody]);


        $response = $this->noteController->store($request);

        $this->assertTrue($response->getData()->success);
        $this->assertCount(1, Note::all());

        $createdNote = Note::first();
        $this->assertEquals($noteBody, $createdNote->body);
        $this->assertEquals($this->patient->id, $createdNote->patient_id);

        $this->assertEquals('Note added successfully.', $response->getData()->message);
    }

    public function testStoreDoesNotAddNoteWhenRequestBodyIsEmpty()
    {

        $request = Request::create('/notes', 'POST', ['body' => '']);

        $response = $this->noteController->store($request);

        $this->assertNull($response);
    }

    public function testEditReturnsNoteSuccessfully()
    {
        $note = Note::factory()->create();


        $request = Request::create('/notes/' . $note->id . '/edit', 'GET');
        $response = $this->noteController->edit($note->id);

        $this->assertTrue($response->getData()->success);
        $this->assertEquals($note->id, $response->getData()->data->id);
        $this->assertEquals($note->body, $response->getData()->data->body);
        $this->assertEquals($note->patient_id, $response->getData()->data->patient_id);
    }

    public function testEditReturnsNotFoundForInvalidNoteId()
    {
        $noteId = 9999;
        $response = $this->noteController->edit($noteId);

        $this->assertObjectHasProperty('message', $response->getData());
        $this->assertNotNull($response->getData()->message);
        $this->assertEquals(
            ["You are not authorized to access this information."],
            $response->getData()->message->id
        );
    }
    public function testUpdateNoteSuccessfully()
    {
        $note = Note::factory()->create();

        $request = Request::create('/notes/' . $note->id, 'POST', ['body' => 'Updated note body']);

        $response = $this->noteController->update($request, $note->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertObjectHasProperty('success', $response->getData());
        $this->assertTrue($response->getData()->success);
        $this->assertEquals('Note updated successfully.', $response->getData()->message);
    }

    public function testUpdateNoteValidationFailure()
    {

        $note = Note::factory()->create();

        $request = Request::create('/notes/' . $note->id, 'POST');

        $response = $this->noteController->update($request, $note->id);

        $this->assertNull($response);
    }

    public function testUpdateNoteNotFound()
    {
        $noteId = 9999;

        $request = Request::create('/notes/' . $noteId, 'POST', ['body' => 'Updated note body']);

        $response = $this->noteController->update($request, $noteId);

        $this->assertObjectHasProperty('message', $response->getData());
        $this->assertNotNull($response->getData()->message);
        $this->assertEquals(
            ["You are not authorized to access this information."],
            $response->getData()->message->id
        );
    }
    public function testDestroyNoteSuccessfully()
    {
        $note = Note::factory()->create();

        $response = $this->noteController->destroy($note->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertObjectHasProperty('success', $response->getData());
        $this->assertTrue($response->getData()->success);
        $this->assertEquals('Note deleted successfully.', $response->getData()->message);
    }

    public function testDestroyNoteNotFound()
    {
        $noteId = 9999;
        $response = $this->noteController->destroy($noteId);

        $this->assertObjectHasProperty('message', $response->getData());
        $this->assertNotNull($response->getData()->message);
        $this->assertEquals(
            ["You are not authorized to access this information."],
            $response->getData()->message->id
        );
    }
    public function testSearchNotesSuccessfully()
    {
        $keyword = "keyword";

        $note1 = Note::factory()->create(['body' => 'Note with keyword']);
        $note2 = Note::factory()->create();
        $note3 = Note::factory()->create(['body' => 'Another note with keyword']);

        $request = Request::create('/search', 'GET', ['body' => "$keyword"]);

        $response = $this->noteController->search($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertObjectHasProperty('notes', $response->getData());
        $this->assertCount(2, $response->getData()->notes);
        $this->assertEquals($note1->id, $response->getData()->notes[0]->id);
        $this->assertEquals($note3->id, $response->getData()->notes[1]->id);
    }

    public function testSearchNotesNoResults()
    {
        $request = Request::create('/search', 'GET', ['body' => 'keyword']);

        $response = $this->noteController->search($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertObjectHasProperty('notes', $response->getData());
        $this->assertCount(0, $response->getData()->notes);
    }
}
