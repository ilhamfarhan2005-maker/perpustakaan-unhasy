<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Fine;
use App\Models\FinePayment;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::where('email', 'mahasiswa@unhasy.ac.id')->firstOrFail();
        $librarian = User::where('email', 'pustakawan@unhasy.ac.id')->firstOrFail();
        $book = Book::where('isbn', '978602003')->firstOrFail();

        $transaction = Transaction::firstOrCreate(
            [
                'user_id' => $student->id,
                'book_id' => $book->id,
                'borrow_date' => today()->subDays(17)->toDateString(),
            ],
            [
                'handled_by' => $librarian->id,
                'due_date' => today()->subDays(10)->toDateString(),
                'return_date' => today()->subDays(5)->toDateString(),
                'status' => 'selesai',
                'workflow_status' => 'Selesai',
            ],
        );

        $daysLate = 5;
        $fineAmount = $daysLate * (float) Setting::get('fine_per_day', 1000);
        $fine = Fine::firstOrCreate(
            ['transaction_id' => $transaction->id],
            [
                'user_id' => $student->id,
                'amount' => $fineAmount,
                'days_late' => $daysLate,
                'status' => 'belum_lunas',
                'payment_status' => 'menunggu_verifikasi',
            ],
        );

        if (! $fine->payments()->where('status', 'menunggu')->exists()) {
            $payment = FinePayment::create([
                'user_id' => $student->id,
                'method' => 'qris',
                'amount' => $fineAmount,
                'proof_path' => null,
                'status' => 'menunggu',
            ]);

            $payment->fines()->attach($fine->id);
            $fine->update(['payment_status' => 'menunggu_verifikasi']);
        }
    }
}
