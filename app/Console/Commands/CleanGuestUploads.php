<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GuestUpload;
use Illuminate\Support\Facades\Storage;

class CleanGuestUploads extends Command
{
    protected $signature = 'guest:clean';
    protected $description = 'Ta bort utgångna gästuppladdningar och gamla använda poster';

    public function handle(): int
    {
        $disk = Storage::disk('guest');

        // 1) Utgångna som aldrig hämtades: ta bort fil + rad
        $expired = GuestUpload::whereNull('used_at')
            ->where('expires_at', '<', now())
            ->get();
        foreach ($expired as $u) {
            $disk->delete($u->storage_path);
            $u->delete();
        }

        // 2) Använda länkar äldre än t.ex. 7 dagar: rensa rad (filen har redan raderats i blob())
        $usedOld = GuestUpload::whereNotNull('used_at')
            ->where('used_at', '<', now()->subDays(7))
            ->delete();

        $this->info('Expired removed: '.$expired->count().' | Used-old removed: '.$usedOld);
        return self::SUCCESS;
    }
}
