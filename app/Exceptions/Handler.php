<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Tangani jika terjadi TokenMismatch (419 Page Expired) agar otomatis redirect ke login
        $this->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return redirect()->route('login')->with('warning', 'Sesi Anda telah kedaluwarsa. Silakan masuk kembali.');
        });
    }
}
