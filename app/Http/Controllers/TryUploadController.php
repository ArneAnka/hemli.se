<?php

namespace App\Http\Controllers;

use App\Models\GuestUpload;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TryUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120'], // 5 MB
        ]);

        $file = $request->file('file');
        $path = $file->store('files', 'guest');

        $upload = GuestUpload::create([
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType() ?: 'application/octet-stream',
            'size_bytes' => $file->getSize(),
            'storage_path' => $path,
            // 'iv_base64'     => $request->string('iv'), // ⟵ ta bort
            'expires_at' => now()->addDay(),
        ]);

        return response()->json([
            'token' => $upload->download_token,
            'name' => $upload->original_name,
            'expires_at' => $upload->expires_at->toIso8601String(),
            'view_url' => route('try.view', ['token' => $upload->download_token]),
            // 'download_url' => route('try.download', ...), // ⟵ ta bort, route saknas
        ]);
    }

    // GET /try/v/{token} – visar Inertia-sida för dekryptering
public function view(string $token)
{
    $u = \App\Models\GuestUpload::where('download_token', $token)->firstOrFail();

    // Om utgången: vi låter sidan renderas men den kan visa "utgången"
    $isExpired = $u->expires_at->isPast();
    $usedAt    = $u->used_at;

    return \Inertia\Inertia::render('TryViewer', [
        'token'      => $token,
        'name'       => $u->original_name,
        'size'       => $u->size_bytes,
        'expires_at' => $u->expires_at->toIso8601String(),
        'used_at'    => $usedAt?->toIso8601String(),
        'expired'    => $isExpired,
    ]);
}

    // GET /try/blob/{token} – streama krypterad blob (ingen content-disposition)
    public function blob(string $token): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $u = \App\Models\GuestUpload::where('download_token', $token)->firstOrFail();

        // 410 om utgången
        abort_if($u->expires_at->isPast(), 410, 'Länken har gått ut.');

        // Engång: sätt used_at atomiskt om den är null
        $updated = \App\Models\GuestUpload::where('id', $u->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        if (! $updated) {
            // Någon hann redan använda den
            abort(410, 'Länken har redan använts.');
        }

        $disk = \Storage::disk('guest');
        $stream = $disk->readStream($u->storage_path);
        abort_unless($stream, 404);

        // Streama krypterad blob och ta bort FILEN efteråt (raden sparas kvar)
        return \Response::stream(function () use ($stream, $disk, $u) {
            try {
                fpassthru($stream);
            } finally {
                @fclose($stream);
                $disk->delete($u->storage_path); // radera filen, rad finns kvar (used_at satt)
            }
        }, 200, [
            'Content-Type' => 'application/octet-stream',
            'Cache-Control' => 'no-store',
        ]);
    }
}
