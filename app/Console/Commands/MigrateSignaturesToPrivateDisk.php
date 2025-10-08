<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\AgendaDetail;

class MigrateSignaturesToPrivateDisk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signatures:migrate-to-private {--dry-run : Jalankan tanpa melakukan perubahan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi file tanda tangan dari disk public ke disk private';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - Tidak ada perubahan yang akan dilakukan');
        }

        $this->info('📂 Memulai migrasi file tanda tangan...');

        // Ambil semua peserta yang memiliki gambar_ttd
        $participants = AgendaDetail::whereNotNull('gambar_ttd')->get();

        $this->info("📊 Ditemukan {$participants->count()} peserta dengan data tanda tangan");

        // Pisahkan antara file path dan base64
        $fileParticipants = $participants->filter(function($p) {
            return !str_contains($p->gambar_ttd, 'data:image/');
        });

        $base64Participants = $participants->filter(function($p) {
            return str_contains($p->gambar_ttd, 'data:image/');
        });

        $this->info("📁 File path: {$fileParticipants->count()} peserta");
        $this->info("📄 Base64 data: {$base64Participants->count()} peserta");

        $migrated = 0;
        $skipped = 0;
        $errors = 0;
        $converted = 0;

        // Proses file path participants
        $this->info("\n🔄 Memproses file path...");
        foreach ($fileParticipants as $participant) {
            $oldPath = $participant->gambar_ttd;

            // Cek apakah file ada di disk public
            if (!Storage::disk('public')->exists($oldPath)) {
                $this->warn("⚠️  File tidak ditemukan: {$oldPath} untuk peserta {$participant->nama}");
                $skipped++;
                continue;
            }

            try {
                // Copy file dari public ke private disk
                $fileContent = Storage::disk('public')->get($oldPath);

                if (!$isDryRun) {
                    Storage::disk('local')->put($oldPath, $fileContent);
                    $this->info("✅ Berhasil migrasi: {$oldPath}");
                } else {
                    $this->info("🔍 [DRY RUN] Akan dimigrasi: {$oldPath}");
                }

                $migrated++;

            } catch (\Exception $e) {
                $this->error("❌ Gagal migrasi {$oldPath}: " . $e->getMessage());
                $errors++;
            }
        }

        // Proses base64 participants
        $this->info("\n🔄 Memproses data base64...");
        foreach ($base64Participants as $participant) {
            try {
                // Ekstrak data base64
                if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $participant->gambar_ttd, $matches)) {
                    $base64Data = substr($participant->gambar_ttd, strpos($participant->gambar_ttd, ',') + 1);
                    $imageData = base64_decode($base64Data);

                    // Buat nama file unik
                    $filename = 'signature_' . time() . '_' . uniqid() . '.png';
                    $newPath = 'tandatangan/' . $filename;

                    if (!$isDryRun) {
                        // Simpan sebagai file di private disk
                        Storage::disk('local')->put($newPath, $imageData);

                        // Update database dengan path file baru
                        $participant->update(['gambar_ttd' => $newPath]);

                        $this->info("🔄 Berhasil konversi base64 ke file: {$newPath} untuk {$participant->nama}");
                    } else {
                        $this->info("🔍 [DRY RUN] Akan dikonversi: base64 -> {$newPath} untuk {$participant->nama}");
                    }

                    $converted++;
                } else {
                    $this->warn("⚠️  Format base64 tidak valid untuk peserta {$participant->nama}");
                    $skipped++;
                }

            } catch (\Exception $e) {
                $this->error("❌ Gagal konversi base64 untuk {$participant->nama}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("\n📈 Ringkasan Migrasi:");
        $this->info("✅ File berhasil dimigrasi: {$migrated}");
        $this->info("🔄 Base64 dikonversi ke file: {$converted}");
        $this->info("⚠️  Dilewati: {$skipped}");
        $this->info("❌ Error: {$errors}");

        if (!$isDryRun && $migrated > 0) {
            $this->warn("\n⚠️  PERHATIAN: File di disk public masih ada. Anda bisa menghapusnya secara manual jika migrasi berhasil.");
            $this->info("💡 Jalankan: php artisan storage:link (jika perlu membuat symbolic link baru)");
        }

        return self::SUCCESS;
    }
}
