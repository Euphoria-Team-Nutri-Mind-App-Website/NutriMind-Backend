<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\Matcher\Not;
use Tests\TestCase;

class NoteTest extends TestCase
{
    // use RefreshDatabase; // Uncomment this line to enable database refreshing

    private Patient $patient;

    public function setUp(): void
    {
        parent::setUp();
        $this->patient = $this->create_patient();
    }

    public function create_patient()
    {
        return Patient::factory()->create();
    }

    public function test_notes(): void
    {
        $response = $this->actingAs($this->patient)->postJson('/api/notes', [
            'title' => 'coco',
            'body' => 'coco',
            'patient_id' => $this->patient->id,
        ]);

        $response->assertStatus(200);
    }

    public function test_notes_validation(): void
    {
        $response = $this->actingAs($this->patient)->postJson('/api/notes', [
            'title' => 'coco',
            'body' => 'coco',
            'patient_id' => 10000000, // An invalid patient_id
        ]);

        $response->assertStatus(422); // Expecting a validation error
        $response->assertJsonStructure(['errors']);
    }

    public function test_create_note_successfully()
    {
        $note = [
            'title' => 'first note title',
            'body' => 'first note body',
            'patient_id' => $this->patient->id,
        ];

        $response = $this->actingAs($this->patient)->postJson('/api/notes', $note);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Note added Successfully.',
            ]);

        $this->assertDatabaseHas('notes', $note);

        $lastNote = Note::latest()->first(['title','body','patient_id']);
        $this->assertEquals($note, $lastNote->toArray());
    }
}
