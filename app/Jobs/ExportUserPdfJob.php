<?php

namespace App\Jobs;

use App\Events\UserPdfCreated;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ExportUserPdfJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Collection|LengthAwarePaginator $users)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filename = '/pdf/file_' . now()->toTimeString() . '.pdf';

        $pdf = Pdf::loadView('pdf.users', compact('users'))->output();

        Storage::disk('s3')->put($filename, $pdf);

        broadcast(new UserPdfCreated(Storage::disk('s3')->url($filename)));
    }
}
