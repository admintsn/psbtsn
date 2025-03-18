<?php

namespace App\Filament\Naikqism\Resources\SantriResource\Widgets;

use App\Models\BersediaTidak;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelas;
use App\Models\KelasSantri;
use App\Models\Kelurahan;
use App\Models\Kodepos;
use App\Models\Provinsi;
use App\Models\Qism;
use App\Models\QismDetail;
use App\Models\QismDetailHasKelas;
use App\Models\Santri;
use App\Models\Semester;
use App\Models\StatusAdmPendaftar;
use App\Models\TahunAjaran;
use App\Models\TahunBerjalan;
use App\Models\Walisantri;
use App\Models\YaTidak;
use Carbon\Carbon;
use Closure;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Grid as TableGrid;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DaftarkanSantriNaikQism extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    // public static function canView(): bool
    // {
    //     // dd(Auth::user());

    //     $walisantri_id = Walisantri::where('kartu_keluarga_santri', Auth::user()->username)->first();
    //     // dd($walisantri_id->is_collapse);



    //     if ($walisantri_id->is_collapse === true) {
    //         return true;
    //     } elseif ($walisantri_id->is_collapse === false) {
    //         return false;
    //     }

    //     // return auth()->user()->isAdmin();
    // }

    public function table(Table $table): Table
    {
        $walisantri = Walisantri::where('user_id', Auth::user()->id)->first();

        $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
        $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

        return $table
            ->heading('2. Isi Formulir dan Upload Dokumen')
            ->paginated(false)
            ->emptyStateHeading('Belum ada data ananda')
            ->emptyStateDescription('Belum ada data ananda yang bisa naik qism')
            ->emptyStateIcon('heroicon-o-book-open')
            ->query(

                Santri::where('walisantri_id', $walisantri->id)
                    ->where('jenis_pendaftar_id', 2)
                    ->where('tahun_berjalan_id', $ts->id)
            )
            ->columns([

                Stack::make([
                    TextColumn::make('index')
                        ->description(fn($record): string => "Nomor", position: 'above')
                        ->rowIndex(),

                    TextColumn::make('nama_lengkap')
                        ->description(fn($record): string => "Nama Santri:", position: 'above'),

                    TextColumn::make('qismsaatini')
                        ->default(function ($record) {
                            $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();

                            $qism = KelasSantri::where('santri_id', $record->id)->where('tahun_berjalan_id', $tahunberjalanaktif->id)->first();

                            return $qism->qism->qism;
                        })
                        ->description(fn($record): string => "Qism saat ini", position: 'above'),

                    TextColumn::make('kelassaatini')
                        ->default(function ($record) {
                            $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();

                            $kelas = KelasSantri::where('santri_id', $record->id)->where('tahun_berjalan_id', $tahunberjalanaktif->id)->first();

                            return $kelas->kelas->kelas;
                        })
                        ->description(fn($record): string => "Kelas:", position: 'above'),

                    TextColumn::make('qism.qism')
                        ->description(fn($record): string => "ke Qism:", position: 'above'),

                    TextColumn::make('qism_detail.qism_detail')
                        ->description(fn($record): string => "", position: 'above'),

                    TextColumn::make('daftarnaikqism')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'Belum Mendaftar' => 'danger',
                            'Mendaftar' => 'success',
                        })
                        ->description(fn($record): string => "Status:", position: 'above'),
                ])


            ])
            ->defaultSort('nama_lengkap')
            ->actions([

                Tables\Actions\EditAction::make('uploadnaikqism')
                    ->hidden(function ($record) {
                        // dd($record->is_collapse);
                        if ($record->daftarnaikqism == 'Belum Mendaftar') {
                            return true;
                        } elseif ($record->daftarnaikqism == 'Mendaftar') {
                            return false;
                        }
                    })
                    ->label('Mulai Upload')
                    ->button()
                    ->color('info')
                    // ->outlined()
                    ->icon('heroicon-m-cloud-arrow-up')
                    ->modalHeading('Upload Dokumen')
                    ->modalDescription(new HtmlString('<div class="">
                                                                    <p>Butuh bantuan?</p>
                                                                    <p>Silakan mengubungi admin di bawah ini:</p>
        
                                                                    <table class="table w-fit">
                                                <!-- head -->
                                                <thead>
                                                    <tr class="border-tsn-header">
                                                        <th class="text-tsn-header text-xs" colspan="2"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- row 1 -->
                                                    <tr>
                                                        <th><a href="https://wa.me/6282210862400"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                                                        </svg>
                                                        </a></th>
                                                        <td class="text-xs"><a href="https://wa.me/6282210862400">WA Admin Putra</a></td>
                                                    </tr>
                                                    <tr>
                                                        <th><a href="https://wa.me/628175765767"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                                                        </svg>
                                                        </a></th>
                                                        <td class="text-xs"><a href="https://wa.me/628175765767">WA Admin Putri</a></td>
                                                    </tr>
        
        
                                                </tbody>
                                                </table>
        
                                                                </div>'))
                    ->modalWidth('full')
                    ->closeModalByClickingAway(false)
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal'))
                    ->form([

                        Section::make()
                            ->schema([

                                Placeholder::make('')

                                    ->content(function (Model $record) {
                                        return (new HtmlString('<div><p class="text-3xl"><strong>' . $record->nama_lengkap . '</strong></p></div>'));
                                    }),

                                Placeholder::make('')
                                    ->content(function (Model $record) {

                                        $qd = QismDetail::where('id', $record->qism_detail_id)->first();

                                        $kelas = Kelas::where('id', $record->kelas_id)->first();

                                        return (new HtmlString('<div class="">
                                            <table class="table w-fit">
                        <!-- head -->
                        <thead>
                            <tr class="border-tsn-header">
                                <th class="text-tsn-header text-lg text-start" colspan="3">PENDAFTAR:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- row 1 -->
                            <tr>
                                <th class="text-lg">Qism</th>
                                <td class="text-lg">:</td>
                                <td class="text-lg">' . $qd->qism_detail . '</td>
                            </tr>
                            <tr>
                                <th class="text-lg">Kelas</th>
                                <td class="text-lg">:</td>
                                <td class="text-lg">' . $kelas->kelas . '</td>
                            </tr>
        
        
        
                        </tbody>
                        </table>
        
                                        </div>'));
                                    }),

                            ]),


                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                            <p class="text-lg strong"><strong>PETUNJUK UPLOAD DOKUMEN</strong></p>
                                                        </div>')),

                        Placeholder::make('')
                            ->content(new HtmlString('<div>
                                                        <table class="table w-fit">
                                                        <!-- head -->
                                                        <thead>
                                                            <tr>
                                                                <th class="text-tsn-header text-xs" colspan="2"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                        <th class="text-xs align-top">-</th>
                                                        <td class="text-xs">Klik tombol "Simpan" di bagian bawah setelah selesai upload atau menghapus dokumen</td>
                                                        </tr>
                                                        <tr>
                                                        <th class="text-xs align-top">-</th>
                                                        <td class="text-xs">Klik tombol "Simpan" dapat dilakukan setelah upload beberapa dokumen atau menghapus beberapa dokumen</td>
                                                        </tr>
                                                            <tr>
                                                                <th class="text-xs align-top">-</th>
                                                                <td class="text-xs">Klik "Browse" untuk memilih gambar/foto dokumen</td>
                                                            </tr>
                                                            <tr>
                                                            <th class="text-xs align-top">-</th>
                                                            <td class="text-xs">Klik tombol <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 0 0-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 0 0 .75-.75v-4a.75.75 0 0 1 1.5 0v4A2.25 2.25 0 0 1 12.75 17h-8.5A2.25 2.25 0 0 1 2 14.75v-8.5A2.25 2.25 0 0 1 4.25 4h5a.75.75 0 0 1 0 1.5h-5Z" clip-rule="evenodd" />
                              <path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 0 0 1.06.053L16.5 4.44v2.81a.75.75 0 0 0 1.5 0v-4.5a.75.75 0 0 0-.75-.75h-4.5a.75.75 0 0 0 0 1.5h2.553l-9.056 8.194a.75.75 0 0 0-.053 1.06Z" clip-rule="evenodd" />
                            </svg>
                             untuk membuka dokumen yang terupload</td>
                                                            </tr>
                                                            <tr>
                                                            <th class="text-xs align-top">-</th>
                                                            <td class="text-xs">Klik tombol <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="currentColor" >
                              <path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z" />
                              <path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" />
                            </svg> untuk download dokumen yang terupload</td>
                                                            </tr>
                                                            <tr>
                                                            <th class="text-xs align-top">-</th>
                                                            <td class="text-xs">Klik tombol <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="currentColor">
                              <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                            </svg>
                             untuk menghapus dokumen yang terupload</td>
                                                            </tr>
                                                            <tr>
                                                            <th class="text-xs align-top">-</th>
                                                            <td class="text-xs">Disarankan untuk upload dokumen satu persatu, dan tunggu sampai muncul tulisan "Upload Complete"</td>
                                                            </tr>
                                                            <tr>
                                                            <th class="text-xs align-top">-</th>
                                                            <td class="text-xs">Silakan melanjutkan upload dokumen selanjutnya setelah muncul tulisan "Upload Complete"</td>
                                                            </tr>
                                                            <tr>
                                                            <th class="text-xs align-top">-</th>
                                                            <td class="text-xs">Tulisan "Uploading 100%" artinya masih proses upload!</td>
                                                            </tr>
        
        
        
                                                        </tbody>
                                                        </table>
                                                                            </div>')),


                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                        </div>')),

                        FileUpload::make('file_kk')
                            ->label('1. Kartu Keluarga')
                            // ->helperText(new HtmlString('*Untuk <strong>santri baru</strong> dan <strong>santri lama</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/lama/01_file_kk')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->kartu_keluarga . "-" . $record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('01 Kartu Keluarga-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                        </div>')),

                        FileUpload::make('file_skt')
                            ->label('2. Surat Keterangan Taklim Orang Tua')
                            // ->helperText(new HtmlString('*Untuk <strong>santri baru</strong> dan <strong>santri lama</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/lama/02_file_skt')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('02 Surat Keterangan Taklim-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                        </div>')),

                        FileUpload::make('file_spkm')
                            ->label('3. Surat Pernyataan Kesanggupan (Bermaterai (10000))')
                            // ->helperText(new HtmlString('*Untuk <strong>santri baru</strong> dan <strong>santri lama</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/lama/03_file_spkm')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('03 Surat Penyataan Kesanggupan-'),
                            ),

                        Placeholder::make('')
                            ->visible(fn($record) => $record->qism_id == 2)
                            ->content(new HtmlString('<div>
                            Unduh format dokumen surat pernyataan kesanggupan:<br>
                            Pilih salah satu<br>
                                            <table class="table max-w-fit bg-white">
                                                <!-- head -->
                                                <thead>
                                                    <tr class="border-b">
                                                        <th class="text-tsn-header">Qism</th>
                                                        <th class="text-tsn-header">Unduh</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="border-b">
                                                        <td class="text-tsn-header">Pra Tahfidz (Menginap)
                                                        </td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-PT Surat Kesanggupan Orang Tua (menginap).pdf" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" color="blue" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75v6.75m0 0-3-3m3 3 3-3m-8.25 6a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
</svg></a></td>
                                                    </tr>
                                                    <tr class="border-b">
                                                        <td class="text-tsn-header">Pra Tahfidz (Fullday tanpa makan)
                                                        </td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-PT Surat Kesanggupan Orang Tua (fullday tanpa makan).pdf" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" color="blue" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75v6.75m0 0-3-3m3 3 3-3m-8.25 6a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
</svg></a></td>
                                                    </tr>
                                                    <tr class="border-b">
                                                        <td class="text-tsn-header">Pra Tahfidz (Fullday dengan makan)
                                                        </td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-PT Surat Kesanggupan Orang Tua (fullday dengan makan).pdf" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" color="blue" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75v6.75m0 0-3-3m3 3 3-3m-8.25 6a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
</svg></a></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                                        </div>')),

                        Placeholder::make('')
                            ->visible(fn($record) => $record->qism_id == 3)
                            ->content(new HtmlString('<div class="border-b">
                            Unduh format dokumen surat pernyataan kesanggupan:<br><br>
                                            <table class="table max-w-fit bg-white">
                                                <!-- head -->
                                                <thead>
                                                    <tr class="border-b">
                                                        <th class="text-tsn-header">Qism</th>
                                                        <th class="text-tsn-header">Unduh</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <tr class="border-b">
                                                        <td class="text-tsn-header">Tahfidzul Qur\'an</td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-TQ Surat Kesanggupan Orang Tua.pdf" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" color="blue" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75v6.75m0 0-3-3m3 3 3-3m-8.25 6a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
</svg></a></td>
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                                        </div>')),

                        Placeholder::make('')
                            ->visible(fn($record) => $record->qism_id == 4)
                            ->content(new HtmlString('<div class="border-b">
                            Unduh format dokumen surat pernyataan kesanggupan:<br><br>
                                            <table class="table max-w-fit bg-white">
                                                <!-- head -->
                                                <thead>
                                                    <tr class="border-b">
                                                        <th class="text-tsn-header">Qism</th>
                                                        <th class="text-tsn-header">Unduh</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <tr class="border-b">
                                                        <td class="text-tsn-header">I\'dad Lughoh</td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-ID Surat Kesanggupan Orang Tua.pdf" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" color="blue" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75v6.75m0 0-3-3m3 3 3-3m-8.25 6a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
</svg></a></td>
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                                        </div>')),

                        Placeholder::make('')
                            ->visible(fn($record) => $record->qism_id == 5)
                            ->content(new HtmlString('<div class="border-b">
                            Unduh format dokumen surat pernyataan kesanggupan:<br><br>
                                            <table class="table max-w-fit bg-white">
                                                <!-- head -->
                                                <thead>
                                                    <tr class="border-b">
                                                        <th class="text-tsn-header">Qism</th>
                                                        <th class="text-tsn-header">Unduh</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <tr class="border-b">
                                                        <td class="text-tsn-header">Mutawasithoh</td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-TNMTW Surat Kesanggupan Orang Tua.pdf" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" color="blue" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75v6.75m0 0-3-3m3 3 3-3m-8.25 6a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
</svg></a></td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                                        </div>')),

                        Placeholder::make('')
                            ->visible(fn($record) => $record->qism_id == 6)
                            ->content(new HtmlString('<div class="border-b">
                            Unduh format dokumen surat pernyataan kesanggupan:<br><br>
                                            <table class="table max-w-fit bg-white">
                                                <!-- head -->
                                                <thead>
                                                    <tr class="border-b">
                                                        <th class="text-tsn-header">Qism</th>
                                                        <th class="text-tsn-header">Unduh</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <tr class="border-b">
                                                        <td class="text-tsn-header">Tarbiyatunnisaa</td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-TNMTW Surat Kesanggupan Orang Tua.pdf" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" color="blue" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75v6.75m0 0-3-3m3 3 3-3m-8.25 6a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
</svg></a></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                                        </div>')),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                        </div>')),

                        FileUpload::make('file_pka')
                            ->label('4. Surat Permohonan Keringanan Administrasi (bagi yang mengajukan keringanan)')
                            ->helperText(new HtmlString('*Hanya bagi yang <strong>mengajukan keringanan</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/lama/04_file_pka')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('04 Surat Permohonan Keringanan Administrasi-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                        </div>')),

                        FileUpload::make('file_ktmu')
                            ->label('5. Surat Keterangan Tidak Mampu dari Ustadz setempat (bagi yang mengajukan keringanan)')
                            ->helperText(new HtmlString('*Hanya bagi yang <strong>mengajukan keringanan</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/lama/05_file_ktmu')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('05 Surat Keterangan Tidak Mampu (U)-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                        </div>')),

                        FileUpload::make('file_ktmp')
                            ->label('6. Surat Keterangan Tidak Mampu dari aparat pemerintah setempat (bagi yang mengajukan keringanan)')
                            ->helperText(new HtmlString('*Hanya bagi yang <strong>mengajukan keringanan</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/lama/06_file_ktmp')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('06 Surat Keterangan Tidak Mampu (P)-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                        </div>')),

                        FileUpload::make('file_cvd')
                            ->label('7. Surat Keterangan Sehat dari RS/Puskesmas/Klinik')
                            // ->helperText(new HtmlString('*Hanya bagi yang <strong>mengajukan keringanan</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/lama/07_file_cvd')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('07 Surat Keterangan Sehat-'),
                            ),


                    ])
                    ->after(function ($record) {


                        Notification::make()
                            ->success()
                            ->title('Alhamdulillah data calon santri telah tersimpan')
                            ->body('Lanjutkan upload dokumen calon santri, atau keluar jika telah selesai')
                            // ->persistent()
                            ->color('success')
                            ->send();
                    })->modalCloseButton(false),

                Tables\Actions\EditAction::make('daftarnaikqism')
                    ->label('Daftarkan Santri Naik Qism')
                    ->modalHeading('Daftar Naik Qism')
                    ->modalDescription(new HtmlString('<div class="">
                                                            <p>Butuh bantuan?</p>
                                                            <p>Silakan mengubungi admin di bawah ini:</p>

                                                            <table class="table w-fit">
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-tsn-header">
                                                <th class="text-tsn-header text-xs" colspan="2"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- row 1 -->
                                            <tr>
                                                <th><a href="https://wa.me/6282210862400"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                                                </svg>
                                                </a></th>
                                                <td class="text-xs"><a href="https://wa.me/6282210862400">WA Admin Putra</a></td>
                                            </tr>
                                            <tr>
                                                <th><a href="https://wa.me/628175765767"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                                                </svg>
                                                </a></th>
                                                <td class="text-xs"><a href="https://wa.me/628175765767">WA Admin Putri</a></td>
                                            </tr>


                                        </tbody>
                                        </table>

                                                        </div>'))
                    ->modalCloseButton(false)
                    ->modalWidth('full')
                    ->closeModalByClickingAway(false)
                    ->closeModalByEscaping(false)
                    ->button()
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal'))
                    ->after(function ($record) {
                        Notification::make()
                            ->success()
                            ->title('Alhamdulillah ananda telah didaftarkan untuk naik qism')
                            ->body('Keluar jika telah selesai')
                            // ->persistent()
                            ->color('success')
                            ->send();

                        $santri = Santri::find($record->id);
                        $santri->daftarnaikqism = 'Mendaftar';
                        $santri->tahap_pendaftaran_id = 1;
                        $santri->nama_lengkap = Str::ucwords(strtolower($record->nama_lengkap));
                        $santri->nama_panggilan = Str::ucwords(strtolower($record->nama_panggilan));
                        $santri->tempat_lahir = Str::ucwords(strtolower($record->tempat_lahir));
                        $santri->nama_kpl_kel = Str::ucwords(strtolower($record->nama_kpl_kel));
                        $santri->save();
                    })
                    ->steps([

                        Step::make('DAFTAR NAIK QISM')
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div>
                                                    <p class="text-lg"><strong>SANTRI</strong></p>
                                                </div>')),

                                Placeholder::make('')

                                    ->content(function (Model $record) {
                                        return (new HtmlString('<div><p class="text-3xl"><strong>' . $record->nama_lengkap . '</strong></p></div>'));
                                    }),

                                Placeholder::make('')
                                    ->content(function (Model $record) {

                                        $qd = QismDetail::where('id', $record->qism_detail_id)->first();

                                        $kelas = Kelas::where('id', $record->kelas_id)->first();

                                        return (new HtmlString('<div class="">
                                            <table class="table w-fit">
                        <!-- head -->
                        <thead>
                            <tr class="border-tsn-header">
                                <th class="text-tsn-header text-lg text-start" colspan="3">PENDAFTAR:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- row 1 -->
                            <tr>
                                <th class="text-lg">Qism</th>
                                <td class="text-lg">:</td>
                                <td class="text-lg">' . $qd->qism_detail . '</td>
                            </tr>
                            <tr>
                                <th class="text-lg">Kelas</th>
                                <td class="text-lg">:</td>
                                <td class="text-lg">' . $kelas->kelas . '</td>
                            </tr>
        
        
        
                        </tbody>
                        </table>
                        <br>

                <p class="text-lg text-end w-full">Klik Next</p>
        
                                        </div>'));
                                    }),

                            ]),
                        // end of step 1

                        Step::make('KUESIONER KESEHATAN')
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">KUESIONER KESEHATAN</p>
                                                </div>')),
                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_sakit_serius_id')
                                            ->label('1. Apakah ananda pernah mengalami sakit yang cukup serius?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextArea::make('ps_kkes_sakit_serius_nama_penyakit')
                                            ->label('Jika iya, kapan dan penyakit apa?')
                                            ->required()
                                            //->default('asdad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_sakit_serius_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_terapi_id')
                                            ->label('2. Apakah ananda pernah atau sedang menjalani terapi kesehatan?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkes_terapi_nama_terapi')
                                            ->label('Jika iya, kapan dan terapi apa?')
                                            ->required()
                                            //->default('asdasd')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_terapi_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_kambuh_id')
                                            ->label('3. Apakah ananda memiliki penyakit yang dapat/sering kambuh?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkes_kambuh_nama_penyakit')
                                            ->label('Jika iya, penyakit apa?')
                                            ->required()
                                            //->default('asdad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_kambuh_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_alergi_id')
                                            ->label('4. Apakah ananda memiliki alergi terhadap perkara-perkara tertentu?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkes_alergi_nama_alergi')
                                            ->label('Jika iya, sebutkan!')
                                            ->required()
                                            //->default('asdadsd')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_alergi_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_pantangan_id')
                                            ->label('5. Apakah ananda mempunyai pantangan yang berkaitan dengan kesehatan?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkes_pantangan_nama')
                                            ->label('Jika iya, sebutkan dan jelaskan alasannya!')
                                            ->required()
                                            //->default('asdadssad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_pantangan_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_psikologis_id')
                                            ->label('6. Apakah ananda pernah mengalami gangguan psikologis (depresi dan gejala-gejalanya)?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkes_psikologis_kapan')
                                            ->label('Jika iya, kapan?')
                                            ->required()
                                            //->default('asdad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_psikologis_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_gangguan_id')
                                            ->label('7. Apakah ananda pernah mengalami gangguan jin?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextArea::make('ps_kkes_gangguan_kapan')
                                            ->label('Jika iya, kapan?')
                                            ->required()
                                            //->default('asdadsad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_gangguan_id') != 1
                                            ),

                                    ]),
                            ]),
                        // end of step 2

                        Step::make('KUESIONER KEMANDIRIAN')
                            ->hidden(function ($record) {

                                if ($record->qism_id == 1) {
                                    return false;
                                } elseif ($record->qism_id == 2 && $record->kelas_id == 1) {
                                    return false;
                                } elseif ($record->qism_id == 2 && $record->kelas_id == 2) {
                                    return false;
                                } elseif ($record->qism_id == 2 && $record->kelas_id == 3) {
                                    return false;
                                } elseif ($record->qism_id == 2 && $record->kelas_id == 4) {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">KUESIONER KEMANDIRIAN</p>
                                                </div>')),
                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkm_bak_id')
                                            ->label('1. Apakah ananda sudah bisa BAK sendiri?')
                                            ->required()
                                            ->required()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkm_bab_id')
                                            ->label('2. Apakah ananda sudah bisa BAB sendiri?')
                                            ->required()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkm_cebok_id')
                                            ->label('3. Apakah ananda sudah bisa cebok sendiri?')
                                            ->required()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkm_ngompol_id')
                                            ->label('4. Apakah ananda masih mengompol?')
                                            ->required()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkm_disuapin_id')
                                            ->label('5. Apakah makan ananda masih disuapi?')
                                            ->required()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),
                            ]),
                        // end of step 3

                        Step::make('KUESIONER KEMAMPUAN PEMBAYARAN ADMINISTRASI')
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div>
                                                    <p class="text-lg strong"><strong>KUESIONER KEMAMPUAN PEMBAYARAN ADMINISTRASI</strong></p>
                                                </div>')),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>RINCIAN BIAYA AWAL DAN SPP</strong></p>
                                                </div>')),

                                Placeholder::make('')
                                    ->content(function ($record) {
                                        if ($record->qism_id == 1) {
                                            return (new HtmlString(
                                                '<div class="grid grid-cols-1 justify-center">
                                                                            <div class="border rounded-xl p-4">
                                                                            <table>
                                                                                <!-- head -->
                                                                                <thead>
                                                                                    <tr class="border-b">
                                                                                        <th class="text-lg text-tsn-header" colspan="4">QISM TARBIYATUL AULAAD</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                            <!-- row 1 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Pendaftaran     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">50.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 2 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Gedung      </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">150.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 3 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Sarpras     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">100.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 4 -->
                                                                            <tr class="border-tsn-header">
                                                                                <th class="text-start">SPP*     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">75.000</td>
                                                                                <td class="text-end">(per bulan)</td>
                                                                            </tr>
                                                                            <tr class="border-t">
                                                                                <th>Total       </th>
                                                                                <td class="text-end"><strong>Rp.</strong></td>
                                                                                <td class="text-end"><strong>375.000</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk SPP bulan pertama</td>
                                                                            </tr>
                                                                            </tbody>
                                                                                </table>
                                                                            </div>
                                                                            </div>'
                                            ));
                                        } elseif ($record->qism_id == 2) {
                                            return (new HtmlString(
                                                '<div class="grid grid-cols-1 justify-center">
                                                                            <div class="border rounded-xl p-4">
                                                                            <table>
                                                                                <!-- head -->
                                                                                <thead>
                                                                                    <tr class="border-b">
                                                                                        <th class="text-lg text-tsn-header" colspan="4">QISM PRA TAHFIDZ-FULLDAY (tanpa makan)</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                            <!-- row 1 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Pendaftaran     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">100.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 2 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Gedung      </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">400.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 3 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Sarpras     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">300.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 4 -->
                                                                            <tr class="border-tsn-header">
                                                                                <th class="text-start">SPP*     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">200.000</td>
                                                                                <td class="text-end">(per bulan)</td>
                                                                            </tr>
                                                                            <tr class="border-t">
                                                                                <th>Total       </th>
                                                                                <td class="text-end"><strong>Rp.</strong></td>
                                                                                <td class="text-end"><strong>1.000.000</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk SPP bulan pertama</td>
                                                                            </tr>
                                                                            </tbody>
                                                                                </table>
                                                                            </div>
                                
                                                                            <br>
                                
                                                                            <div class="border rounded-xl p-4">
                                                                            <table>
                                                                                <!-- head -->
                                                                                <thead>
                                                                                    <tr class="border-b">
                                                                                        <th class="text-lg text-tsn-header" colspan="4">QISM PRA TAHFIDZ-FULLDAY (dengan makan)</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                            <!-- row 1 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Pendaftaran     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">100.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 2 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Gedung      </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">400.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 3 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Sarpras     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">300.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 4 -->
                                                                            <tr class="border-tsn-header">
                                                                                <th class="text-start">SPP*     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">300.000</td>
                                                                                <td class="text-end">(per bulan)</td>
                                                                            </tr>
                                                                            <tr class="border-t">
                                                                                <th>Total       </th>
                                                                                <td class="text-end"><strong>Rp.</strong></td>
                                                                                <td class="text-end"><strong>1.100.000</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk SPP bulan pertama</td>
                                                                            </tr>
                                                                            </tbody>
                                                                                </table>
                                                                            </div>
                                
                                                                            <br>
                                
                                                                            <div class="border rounded-xl p-4">
                                                                            <table>
                                                                                <!-- head -->
                                                                                <thead>
                                                                                    <tr class="border-b">
                                                                                        <th class="text-lg text-tsn-header" colspan="4">QISM PT (menginap)</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                            <!-- row 1 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Pendaftaran     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">100.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 2 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Gedung      </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">400.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 3 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Sarpras     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">300.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 4 -->
                                                                            <tr class="border-tsn-header">
                                                                                <th class="text-start">SPP*     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">550.000</td>
                                                                                <td class="text-end">(per bulan)</td>
                                                                            </tr>
                                                                            <tr class="border-t">
                                                                                <th>Total       </th>
                                                                                <td class="text-end"><strong>Rp.</strong></td>
                                                                                <td class="text-end"><strong>1.350.000</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk SPP bulan pertama</td>
                                                                            </tr>
                                                                            </tbody>
                                                                                </table>
                                                                            </div>
                                                                            </div>'
                                            ));
                                        } elseif ($record->qism_id != 1 || $record->qism_id != 2) {
                                            return (new HtmlString(
                                                '<div class="grid grid-cols-1 justify-center">
                                                                            
                                                                            <div class="border rounded-xl p-4">
                                                                            <table>
                                                                                <!-- head -->
                                                                                <thead>
                                                                                    <tr class="border-b">
                                                                                        <th class="text-lg text-tsn-header" colspan="4">QISM TQ, IDD, MTW, TN</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                            <!-- row 1 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Pendaftaran     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">100.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 2 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Gedung      </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">400.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 3 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Sarpras     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">300.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 4 -->
                                                                            <tr class="border-tsn-header">
                                                                                <th class="text-start">SPP*     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">550.000</td>
                                                                                <td class="text-end">(per bulan)</td>
                                                                            </tr>
                                                                            <tr class="border-t">
                                                                                <th>Total       </th>
                                                                                <td class="text-end"><strong>Rp.</strong></td>
                                                                                <td class="text-end"><strong>1.350.000</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk SPP bulan pertama</td>
                                                                            </tr>
                                                                            </tbody>
                                                                                </table>
                                                                            </div>
                                                                            </div>'
                                            ));
                                        }
                                    }),


                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_status_id')
                                            ->label('Status anak didik terkait dengan administrasi?')
                                            ->required()
                                            ->live()
                                            ->options(StatusAdmPendaftar::whereIsActive(1)->pluck('status_adm_pendaftar', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        Placeholder::make('')
                                            ->content(new HtmlString('<div class="border-b">
                                                                        <p><strong>Bersedia memenuhi persyaratan sebagai berikut:</strong></p>
                                                                    </div>'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_surat_subsidi_id')
                                            ->label('1. Wali harus membuat surat permohonan subsidi/ keringanan biaya administrasi')
                                            ->required()
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_surat_kurang_mampu_id')
                                            ->label('2. Wali harus menyertakan surat keterangan kurang mampu')
                                            ->helperText(' Surat keterangan kurang mampu dari ustadz salafy setempat SERTA dari aparat pemerintah setempat, yang isinya menyatakan bahwa memang keluarga tersebut "perlu dibantu"')
                                            ->required()
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_atur_keuangan_id')
                                            ->label('3. Keuangan ananda akan dipegang dan diatur oleh Mahad')
                                            ->required()
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_penentuan_subsidi_id')
                                            ->label('4. Yang menentukan bentuk keringanan yang diberikan adalah Mahad')
                                            ->required()
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_hidup_sederhana_id')
                                            ->label('5. Ananda harus berpola hidup sederhana agar tidak menimbulkan pertanyaan pihak luar')
                                            ->required()
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_kebijakan_subsidi_id')
                                            ->label('6. Kebijakan subsidi bisa berubah sewaktu waktu')
                                            ->required()
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),


                                // end of step 6
                            ]),

                        // end of action steps
                    ]),

                Tables\Actions\ViewAction::make()
                    ->label('Lihat Data Santri')
                    ->hidden(function ($record) {
                        // dd($record->is_collapse);
                        if ($record->daftarnaikqism == 'Belum Mendaftar') {
                            return true;
                        } elseif ($record->daftarnaikqism == 'Mendaftar') {
                            return false;
                        }
                    })
                    ->modalHeading('Lihat Data Santri')
                    ->modalDescription(new HtmlString('<div class="">
                                                            <p>Butuh bantuan?</p>
                                                            <p>Silakan mengubungi admin di bawah ini:</p>

                                                            <table class="table w-fit">
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-tsn-header">
                                                <th class="text-tsn-header text-xs" colspan="2"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- row 1 -->
                                            <tr>
                                                <th><a href="https://wa.me/6282210862400"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                                                </svg>
                                                </a></th>
                                                <td class="text-xs"><a href="https://wa.me/6282210862400">WA Admin Putra</a></td>
                                            </tr>
                                            <tr>
                                                <th><a href="https://wa.me/628175765767"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                                                </svg>
                                                </a></th>
                                                <td class="text-xs"><a href="https://wa.me/628175765767">WA Admin Putri</a></td>
                                            </tr>


                                        </tbody>
                                        </table>

                                                        </div>'))
                    ->modalWidth('full')
                    // ->stickyModalHeader()
                    ->button()
                    ->closeModalByClickingAway(false)
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Tutup'))
                    ->steps([

                        Step::make('DAFTAR NAIK QISM')
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div>
                                                    <p class="text-lg"><strong>SANTRI</strong></p>
                                                </div>')),

                                Placeholder::make('')

                                    ->content(function (Model $record) {
                                        return (new HtmlString('<div><p class="text-3xl"><strong>' . $record->nama_lengkap . '</strong></p></div>'));
                                    }),

                                Placeholder::make('')
                                    ->content(function (Model $record) {

                                        $qd = QismDetail::where('id', $record->qism_detail_id)->first();

                                        $kelas = Kelas::where('id', $record->kelas_id)->first();

                                        return (new HtmlString('<div class="">
                                            <table class="table w-fit">
                        <!-- head -->
                        <thead>
                            <tr class="border-tsn-header">
                                <th class="text-tsn-header text-lg text-start" colspan="3">PENDAFTAR:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- row 1 -->
                            <tr>
                                <th class="text-lg">Qism</th>
                                <td class="text-lg">:</td>
                                <td class="text-lg">' . $qd->qism_detail . '</td>
                            </tr>
                            <tr>
                                <th class="text-lg">Kelas</th>
                                <td class="text-lg">:</td>
                                <td class="text-lg">' . $kelas->kelas . '</td>
                            </tr>
        
        
        
                        </tbody>
                        </table>
                        <br>

                <p class="text-lg text-end w-full">Klik Next</p>
        
                                        </div>'));
                                    }),

                            ]),
                        // end of step 1

                        Step::make('KUESIONER KESEHATAN')
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">KUESIONER KESEHATAN</p>
                                                </div>')),
                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_sakit_serius_id')
                                            ->label('1. Apakah ananda pernah mengalami sakit yang cukup serius?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextArea::make('ps_kkes_sakit_serius_nama_penyakit')
                                            ->label('Jika iya, kapan dan penyakit apa?')
                                            ->required()
                                            //->default('asdad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_sakit_serius_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_terapi_id')
                                            ->label('2. Apakah ananda pernah atau sedang menjalani terapi kesehatan?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkes_terapi_nama_terapi')
                                            ->label('Jika iya, kapan dan terapi apa?')
                                            ->required()
                                            //->default('asdasd')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_terapi_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_kambuh_id')
                                            ->label('3. Apakah ananda memiliki penyakit yang dapat/sering kambuh?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkes_kambuh_nama_penyakit')
                                            ->label('Jika iya, penyakit apa?')
                                            ->required()
                                            //->default('asdad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_kambuh_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_alergi_id')
                                            ->label('4. Apakah ananda memiliki alergi terhadap perkara-perkara tertentu?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkes_alergi_nama_alergi')
                                            ->label('Jika iya, sebutkan!')
                                            ->required()
                                            //->default('asdadsd')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_alergi_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_pantangan_id')
                                            ->label('5. Apakah ananda mempunyai pantangan yang berkaitan dengan kesehatan?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkes_pantangan_nama')
                                            ->label('Jika iya, sebutkan dan jelaskan alasannya!')
                                            ->required()
                                            //->default('asdadssad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_pantangan_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_psikologis_id')
                                            ->label('6. Apakah ananda pernah mengalami gangguan psikologis (depresi dan gejala-gejalanya)?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkes_psikologis_kapan')
                                            ->label('Jika iya, kapan?')
                                            ->required()
                                            //->default('asdad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_psikologis_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkes_gangguan_id')
                                            ->label('7. Apakah ananda pernah mengalami gangguan jin?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextArea::make('ps_kkes_gangguan_kapan')
                                            ->label('Jika iya, kapan?')
                                            ->required()
                                            //->default('asdadsad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_gangguan_id') != 1
                                            ),

                                    ]),
                            ]),
                        // end of step 2

                        Step::make('KUESIONER KEMANDIRIAN')
                            ->hidden(function ($record) {

                                if ($record->qism_id == 1) {
                                    return false;
                                } elseif ($record->qism_id == 2 && $record->kelas_id == 1) {
                                    return false;
                                } elseif ($record->qism_id == 2 && $record->kelas_id == 2) {
                                    return false;
                                } elseif ($record->qism_id == 2 && $record->kelas_id == 3) {
                                    return false;
                                } elseif ($record->qism_id == 2 && $record->kelas_id == 4) {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">KUESIONER KEMANDIRIAN</p>
                                                </div>')),
                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkm_bak_id')
                                            ->label('1. Apakah ananda sudah bisa BAK sendiri?')
                                            ->required()
                                            ->required()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkm_bab_id')
                                            ->label('2. Apakah ananda sudah bisa BAB sendiri?')
                                            ->required()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkm_cebok_id')
                                            ->label('3. Apakah ananda sudah bisa cebok sendiri?')
                                            ->required()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkm_ngompol_id')
                                            ->label('4. Apakah ananda masih mengompol?')
                                            ->required()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkm_disuapin_id')
                                            ->label('5. Apakah makan ananda masih disuapi?')
                                            ->required()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),
                            ]),
                        // end of step 3

                        Step::make('KUESIONER KEMAMPUAN PEMBAYARAN ADMINISTRASI')
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div>
                                                    <p class="text-lg strong"><strong>KUESIONER KEMAMPUAN PEMBAYARAN ADMINISTRASI</strong></p>
                                                </div>')),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>RINCIAN BIAYA AWAL DAN SPP</strong></p>
                                                </div>')),

                                Placeholder::make('')
                                    ->content(function ($record) {
                                        if ($record->qism_id == 1) {
                                            return (new HtmlString(
                                                '<div class="grid grid-cols-1 justify-center">
                                                                            <div class="border rounded-xl p-4">
                                                                            <table>
                                                                                <!-- head -->
                                                                                <thead>
                                                                                    <tr class="border-b">
                                                                                        <th class="text-lg text-tsn-header" colspan="4">QISM TARBIYATUL AULAAD</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                            <!-- row 1 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Pendaftaran     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">50.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 2 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Gedung      </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">150.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 3 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Sarpras     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">100.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 4 -->
                                                                            <tr class="border-tsn-header">
                                                                                <th class="text-start">SPP*     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">75.000</td>
                                                                                <td class="text-end">(per bulan)</td>
                                                                            </tr>
                                                                            <tr class="border-t">
                                                                                <th>Total       </th>
                                                                                <td class="text-end"><strong>Rp.</strong></td>
                                                                                <td class="text-end"><strong>375.000</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk SPP bulan pertama</td>
                                                                            </tr>
                                                                            </tbody>
                                                                                </table>
                                                                            </div>
                                                                            </div>'
                                            ));
                                        } elseif ($record->qism_id == 2) {
                                            return (new HtmlString(
                                                '<div class="grid grid-cols-1 justify-center">
                                                                            <div class="border rounded-xl p-4">
                                                                            <table>
                                                                                <!-- head -->
                                                                                <thead>
                                                                                    <tr class="border-b">
                                                                                        <th class="text-lg text-tsn-header" colspan="4">QISM PRA TAHFIDZ-FULLDAY (tanpa makan)</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                            <!-- row 1 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Pendaftaran     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">100.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 2 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Gedung      </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">400.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 3 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Sarpras     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">300.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 4 -->
                                                                            <tr class="border-tsn-header">
                                                                                <th class="text-start">SPP*     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">200.000</td>
                                                                                <td class="text-end">(per bulan)</td>
                                                                            </tr>
                                                                            <tr class="border-t">
                                                                                <th>Total       </th>
                                                                                <td class="text-end"><strong>Rp.</strong></td>
                                                                                <td class="text-end"><strong>1.000.000</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk SPP bulan pertama</td>
                                                                            </tr>
                                                                            </tbody>
                                                                                </table>
                                                                            </div>
                                
                                                                            <br>
                                
                                                                            <div class="border rounded-xl p-4">
                                                                            <table>
                                                                                <!-- head -->
                                                                                <thead>
                                                                                    <tr class="border-b">
                                                                                        <th class="text-lg text-tsn-header" colspan="4">QISM PRA TAHFIDZ-FULLDAY (dengan makan)</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                            <!-- row 1 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Pendaftaran     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">100.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 2 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Gedung      </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">400.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 3 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Sarpras     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">300.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 4 -->
                                                                            <tr class="border-tsn-header">
                                                                                <th class="text-start">SPP*     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">300.000</td>
                                                                                <td class="text-end">(per bulan)</td>
                                                                            </tr>
                                                                            <tr class="border-t">
                                                                                <th>Total       </th>
                                                                                <td class="text-end"><strong>Rp.</strong></td>
                                                                                <td class="text-end"><strong>1.100.000</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk SPP bulan pertama</td>
                                                                            </tr>
                                                                            </tbody>
                                                                                </table>
                                                                            </div>
                                
                                                                            <br>
                                
                                                                            <div class="border rounded-xl p-4">
                                                                            <table>
                                                                                <!-- head -->
                                                                                <thead>
                                                                                    <tr class="border-b">
                                                                                        <th class="text-lg text-tsn-header" colspan="4">QISM PT (menginap)</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                            <!-- row 1 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Pendaftaran     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">100.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 2 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Gedung      </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">400.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 3 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Sarpras     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">300.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 4 -->
                                                                            <tr class="border-tsn-header">
                                                                                <th class="text-start">SPP*     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">550.000</td>
                                                                                <td class="text-end">(per bulan)</td>
                                                                            </tr>
                                                                            <tr class="border-t">
                                                                                <th>Total       </th>
                                                                                <td class="text-end"><strong>Rp.</strong></td>
                                                                                <td class="text-end"><strong>1.350.000</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk SPP bulan pertama</td>
                                                                            </tr>
                                                                            </tbody>
                                                                                </table>
                                                                            </div>
                                                                            </div>'
                                            ));
                                        } elseif ($record->qism_id != 1 || $record->qism_id != 2) {
                                            return (new HtmlString(
                                                '<div class="grid grid-cols-1 justify-center">
                                                                            
                                                                            <div class="border rounded-xl p-4">
                                                                            <table>
                                                                                <!-- head -->
                                                                                <thead>
                                                                                    <tr class="border-b">
                                                                                        <th class="text-lg text-tsn-header" colspan="4">QISM TQ, IDD, MTW, TN</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                            <!-- row 1 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Pendaftaran     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">100.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 2 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Gedung      </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">400.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 3 -->
                                                                            <tr>
                                                                                <th class="text-start">Uang Sarpras     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">300.000</td>
                                                                                <td class="text-end">(per tahun)</td>
                                                                            </tr>
                                                                            <!-- row 4 -->
                                                                            <tr class="border-tsn-header">
                                                                                <th class="text-start">SPP*     </th>
                                                                                <td class="text-end">Rp.</td>
                                                                                <td class="text-end">550.000</td>
                                                                                <td class="text-end">(per bulan)</td>
                                                                            </tr>
                                                                            <tr class="border-t">
                                                                                <th>Total       </th>
                                                                                <td class="text-end"><strong>Rp.</strong></td>
                                                                                <td class="text-end"><strong>1.350.000</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk SPP bulan pertama</td>
                                                                            </tr>
                                                                            </tbody>
                                                                                </table>
                                                                            </div>
                                                                            </div>'
                                            ));
                                        }
                                    }),


                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_status_id')
                                            ->label('Status anak didik terkait dengan administrasi?')
                                            ->required()
                                            ->live()
                                            ->options(StatusAdmPendaftar::whereIsActive(1)->pluck('status_adm_pendaftar', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        Placeholder::make('')
                                            ->content(new HtmlString('<div class="border-b">
                                                                        <p><strong>Bersedia memenuhi persyaratan sebagai berikut:</strong></p>
                                                                    </div>'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_surat_subsidi_id')
                                            ->label('1. Wali harus membuat surat permohonan subsidi/ keringanan biaya administrasi')
                                            ->required()
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_surat_kurang_mampu_id')
                                            ->label('2. Wali harus menyertakan surat keterangan kurang mampu')
                                            ->helperText(' Surat keterangan kurang mampu dari ustadz salafy setempat SERTA dari aparat pemerintah setempat, yang isinya menyatakan bahwa memang keluarga tersebut "perlu dibantu"')
                                            ->required()
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_atur_keuangan_id')
                                            ->label('3. Keuangan ananda akan dipegang dan diatur oleh Mahad')
                                            ->required()
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_penentuan_subsidi_id')
                                            ->label('4. Yang menentukan bentuk keringanan yang diberikan adalah Mahad')
                                            ->required()
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_hidup_sederhana_id')
                                            ->label('5. Ananda harus berpola hidup sederhana agar tidak menimbulkan pertanyaan pihak luar')
                                            ->required()
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kadm_kebijakan_subsidi_id')
                                            ->label('6. Kebijakan subsidi bisa berubah sewaktu waktu')
                                            ->required()
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status_id') != 2
                                            ),

                                    ]),


                                // end of step 6
                            ]),

                        // end of action steps
                    ]),

            ]);
    }
}
