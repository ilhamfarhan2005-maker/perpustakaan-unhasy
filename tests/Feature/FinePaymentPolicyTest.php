<?php

namespace Tests\Feature;

use App\Models\FinePayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinePaymentPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_access_other_students_payment_proof(): void
    {
        Storage::fake('local');

        $student1 = User::factory()->mahasiswa()->create();
        $student2 = User::factory()->mahasiswa()->create();

        $proofPath = 'fine-payments/proof_1.jpg';
        Storage::disk('local')->put($proofPath, 'dummy image content');

        $payment = FinePayment::factory()->create([
            'user_id' => $student1->id,
            'proof_path' => $proofPath,
        ]);

        // Student 1 can access own payment proof
        $this->actingAs($student1)
            ->get(route('mahasiswa.pembayaran.bukti', $payment))
            ->assertOk();

        // Student 2 accessing student 1's payment proof is forbidden (403)
        $this->actingAs($student2)
            ->get(route('mahasiswa.pembayaran.bukti', $payment))
            ->assertForbidden();
    }

    public function test_librarian_can_access_payment_proof_and_verify(): void
    {
        Storage::fake('local');

        $librarian = User::factory()->pustakawan()->create();
        $student = User::factory()->mahasiswa()->create();

        $proofPath = 'fine-payments/proof_2.jpg';
        Storage::disk('local')->put($proofPath, 'dummy image content');

        $payment = FinePayment::factory()->create([
            'user_id' => $student->id,
            'proof_path' => $proofPath,
        ]);

        $this->actingAs($librarian)
            ->get(route('pustakawan.pembayaran.bukti', $payment))
            ->assertOk();

        $this->actingAs($librarian)
            ->get(route('pustakawan.pembayaran.index'))
            ->assertOk();
    }
}

