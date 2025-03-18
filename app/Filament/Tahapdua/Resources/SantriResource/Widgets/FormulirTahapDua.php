<?php

namespace App\Filament\Tahapdua\Resources\SantriResource\Widgets;

use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelas;
use App\Models\Kelurahan;
use App\Models\Kodepos;
use App\Models\Pendaftar;
use App\Models\Provinsi;
use App\Models\Qism;
use App\Models\QismDetail;
use App\Models\QismDetailHasKelas;
use App\Models\Santri;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\TahunBerjalan;
use App\Models\Walisantri;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FormulirTahapDua extends BaseWidget
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
            ->heading('Status Upload Dokumen')
            ->description('Scroll/gulir ke kanan untuk melihat status dokumen')
            ->paginated(false)
            ->striped()
            ->query(

                Santri::where('walisantri_id', $walisantri->id)
                    ->where('jenis_pendaftar_id', 1)
                    ->where(function ($query) {
                        $query->where('tahap_pendaftaran_id', 2)
                            ->orWhere('tahap_pendaftaran_id', 3);
                    })
                    ->where('tahun_berjalan_id', $ts->id)
            )
            ->columns([
                TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->size(TextColumn\TextColumnSize::Large),

                TextColumn::make('file_kk')
                    ->label('1. Kartu Keluarga')
                    // ->description(fn(): string => 'Kartu Keluarga', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn(string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_kk !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_kk);
                        }
                    })
                    ->badge()
                    ->color('success')
                    ->openUrlInNewTab(),

                TextColumn::make('file_akte')
                    ->label('2. Akte')
                    // ->description(fn(): string => 'Kartu Keluarga', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn(string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_akte !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_akte);
                        }
                    })
                    ->badge()
                    ->color('success')
                    ->openUrlInNewTab(),

                TextColumn::make('file_srs')
                    ->label('3. Surat Rekomendasi')
                    // ->description(fn(): string => 'Kartu Keluarga', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn(string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_srs !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_srs);
                        }
                    })
                    ->badge()
                    ->color('success')
                    ->openUrlInNewTab(),

                TextColumn::make('file_ijz')
                    ->label('4. Ijazah')
                    // ->description(fn(): string => 'Kartu Keluarga', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn(string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_ijz !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_ijz);
                        }
                    })
                    ->badge()
                    ->color('success')
                    ->openUrlInNewTab(),

                TextColumn::make('file_skt')
                    ->label('5. Surat Keterangan Taklim')
                    // ->description(fn(): string => 'Kartu Keluarga', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn(string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_skt !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_skt);
                        }
                    })
                    ->badge()
                    ->color('success')
                    ->openUrlInNewTab(),

                TextColumn::make('file_skuasa')
                    ->label('6. Surat Kuasa')
                    // ->description(fn(): string => 'Kartu Keluarga', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn(string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_skuasa !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_skuasa);
                        }
                    })
                    ->badge()
                    ->color('success')
                    ->openUrlInNewTab(),

                TextColumn::make('file_spkm')
                    ->label('7. Surat Pernyataan Kesanggupan')
                    // ->description(fn(): string => 'Kartu Keluarga', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn(string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_spkm !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_spkm);
                        }
                    })
                    ->badge()
                    ->color('success')
                    ->openUrlInNewTab(),

                TextColumn::make('file_pka')
                    ->label('8. Surat Permohonan Keringanan Administrasi')
                    // ->description(fn(): string => 'Kartu Keluarga', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn(string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_pka !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_pka);
                        }
                    })
                    ->badge()
                    ->color('success')
                    ->openUrlInNewTab(),

                TextColumn::make('file_ktmu')
                    ->label('9. Surat Keterangan Tidak Mampu (U)')
                    // ->description(fn(): string => 'Kartu Keluarga', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn(string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_ktmu !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_ktmu);
                        }
                    })
                    ->badge()
                    ->color('success')
                    ->openUrlInNewTab(),

                TextColumn::make('file_ktmp')
                    ->label('10. Surat Keterangan Tidak Mampu (P)')
                    // ->description(fn(): string => 'Kartu Keluarga', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn(string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_ktmp !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_ktmp);
                        }
                    })
                    ->badge()
                    ->color('success')
                    ->openUrlInNewTab(),

                TextColumn::make('file_cvd')
                    ->label('11. Surat Keterangan Sehat dari RS/Puskesmas/Klinik')
                    // ->description(fn(): string => 'Kartu Keluarga', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn(string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_cvd !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_cvd);
                        }
                    })
                    ->badge()
                    ->color('success')
                    ->openUrlInNewTab(),

            ])
            ->defaultSort('nama_lengkap')
            ->actions([
                Tables\Actions\EditAction::make()
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
                            ->directory('psb2526/baru/01_file_kk')
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

                        FileUpload::make('file_akte')
                            ->label('2. Akte')
                            // ->helperText(new HtmlString('*Untuk <strong>santri baru</strong> dan <strong>santri lama</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/baru/02_file_akte')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('02 Akte-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                </div>')),

                        FileUpload::make('file_srs')
                            ->label('3. Surat Rekomendasi')
                            ->helperText(new HtmlString('Yang dimaksud surat rekomendasi dari mahad sebelumnya adalah <strong>surat keterangan berkelakuan baik</strong>, bukan surat keterangan status santri.'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/baru/03_file_srs')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('03 Surat Rekomendasi-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                </div>')),

                        FileUpload::make('file_ijz')
                            ->label('4. Ijazah atau Hasil Evaluasi Belajar atau Rapor')
                            ->helperText(new HtmlString('*Jika tidak ada, dapat dikosongkan'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/baru/04_file_ijz')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('04 Ijazah-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                </div>')),

                        FileUpload::make('file_skt')
                            ->label('5. Surat Keterangan Taklim Orang Tua')
                            // ->helperText(new HtmlString('*Untuk <strong>santri baru</strong> dan <strong>santri lama</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/baru/05_file_skt')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('05 Surat Keterangan Taklim-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                </div>')),

                        FileUpload::make('file_skuasa')
                            ->label('6. Surat Kuasa dari Orang Tua Kandung')
                            ->helperText(new HtmlString('*Hanya bagi yang mendaftarkan ananda bukan melalui Orang Tua Kandung'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/baru/06_file_skuasa')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('06 Surat Kuasa-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                </div>')),

                        FileUpload::make('file_spkm')
                            ->label('7. Surat Pernyataan Kesanggupan (Bermaterai (10000))')
                            // ->helperText(new HtmlString('*Untuk <strong>santri baru</strong> dan <strong>santri lama</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/baru/07_file_spkm')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('07 Surat Penyataan Kesanggupan-'),
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
                            ->label('8. Surat Permohonan Keringanan Administrasi (bagi yang mengajukan keringanan)')
                            ->helperText(new HtmlString('*Hanya bagi yang <strong>mengajukan keringanan</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/baru/08_file_pka')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('08 Surat Permohonan Keringanan Administrasi-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                </div>')),

                        FileUpload::make('file_ktmu')
                            ->label('9. Surat Keterangan Tidak Mampu dari Ustadz setempat (bagi yang mengajukan keringanan)')
                            ->helperText(new HtmlString('*Hanya bagi yang <strong>mengajukan keringanan</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/baru/09_file_ktmu')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('09 Surat Keterangan Tidak Mampu (U)-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                </div>')),

                        FileUpload::make('file_ktmp')
                            ->label('10. Surat Keterangan Tidak Mampu dari aparat pemerintah setempat (bagi yang mengajukan keringanan)')
                            ->helperText(new HtmlString('*Hanya bagi yang <strong>mengajukan keringanan</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/baru/10_file_ktmp')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('10 Surat Keterangan Tidak Mampu (P)-'),
                            ),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                                                </div>')),

                        FileUpload::make('file_cvd')
                            ->label('11. Surat Keterangan Sehat dari RS/Puskesmas/Klinik')
                            // ->helperText(new HtmlString('*Hanya bagi yang <strong>mengajukan keringanan</strong>'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->directory('psb2526/baru/10_file_cvd')
                            ->removeUploadedFileButtonPosition('right')
                            ->openable()
                            ->downloadable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file, $record): string => (string) str($record->nama_lengkap . "-" . $record->tahun_berjalan->tb . "." . $file->getClientOriginalExtension())
                                    ->prepend('11 Surat Keterangan Sehat-'),
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



            ], position: ActionsPosition::BeforeColumns);
    }
}
