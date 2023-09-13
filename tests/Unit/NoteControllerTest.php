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

    public function testIndexReturnsAllNotesForPatient()
    {
        $patient = Patient::factory()->create();
        $notes = Note::factory(1)->create(['patient_id' => $patient->id]);

        $request = new Request(['patient_id' => $patient->id]);

        $this->actingAs($patient);

        $controller = new NoteController();
        $result = $controller->index($request);

        $this->assertEquals($notes, $result);
    }
}
