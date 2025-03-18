<?php

namespace App\Filament\Naikqism\Resources\SantriResource\Widgets;

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

class FormulirUploadNaikQism extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '1s';

    // public static function canView(): bool
    // {
    //     // dd(Auth::user());

    //     $walisantri_id = Walisantri::where('kartu_keluarga_santri', Auth::user()->username)->first();
    //     // dd($walisantri_id->is_collapse);

    //     $walisantri = Walisantri::where('user_id', Auth::user()->id)->first();

    //     $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
    //     $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

    //     $mendaftar = Santri::where('walisantri_id', $walisantri->id)
    //         ->where('jenis_pendaftar_id', 2)
    //         // ->where('daftarnaikqism', 'Mendaftar')
    //         ->where('tahun_berjalan_id', $ts->id)->first();



    //     if ($mendaftar->daftarnaikqism == 'Mendaftar') {
    //         return true;
    //     } else {
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
            ->description('Scroll/gulir ke kanan untuk melihat status upload dokumen')
            ->paginated(false)
            ->striped()
            ->poll('1s')
            ->query(

                Santri::where('walisantri_id', $walisantri->id)
                    ->where('jenis_pendaftar_id', 2)
                    // ->where('daftarnaikqism', 'Mendaftar')
                    ->where('tahun_berjalan_id', $ts->id)
            )
            ->columns([
                TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->size(TextColumn\TextColumnSize::Large),

                TextColumn::make('file_kk')
                    ->label('1. Kartu Keluarga')
                    // ->description(fn(): string => "1. Kartu Keluarga", position: 'above')
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

                TextColumn::make('file_skt')
                    ->label('2. Surat Keterangan Taklim')
                    // ->description(fn(): string => '2. Surat Keterangan Taklim', position: 'above')
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

                TextColumn::make('file_spkm')
                    ->label('3. Surat Pernyataan Kesanggupan')
                    // ->description(fn(): string => '3. Surat Pernyataan Kesanggupan', position: 'above')
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
                    ->label('4. Surat Permohonan Keringanan Administrasi')
                    // ->description(fn(): string => '4. Surat Permohonan Keringanan Administrasi', position: 'above')
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
                    ->label('5. Surat Keterangan Tidak Mampu (U)')
                    // ->description(fn(): string => '5. Surat Keterangan Tidak Mampu (U)', position: 'above')
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
                    ->label('6. Surat Keterangan Tidak Mampu (P)')
                    // ->description(fn(): string => '6. Surat Keterangan Tidak Mampu (P)', position: 'above')
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
                    ->label('7. Surat Keterangan Sehat dari RS/Puskesmas/Klinik')
                    // ->description(fn(): string => '7. Surat Keterangan Sehat dari RS/Puskesmas/Klinik', position: 'above')
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
            ->defaultSort('nama_lengkap');
    }
}
