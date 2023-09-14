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
}
