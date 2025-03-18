<?php

namespace App\Filament\Resources\SantriResource\Widgets;

use App\Models\AnandaBerada;
use App\Models\BersediaTidak;
use App\Models\Cita;
use App\Models\Hafalan;
use App\Models\Hobi;
use App\Models\Jarakpp;
use App\Models\Jeniskelamin;
use App\Models\Kabupaten;
use App\Models\KebutuhanDisabilitas;
use App\Models\KebutuhanKhusus;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kewarganegaraan;
use App\Models\Kodepos;
use App\Models\MedsosAnanda;
use App\Models\MembiayaiSekolah;
use App\Models\MendaftarKeinginan;
use App\Models\MukimTidak;
use App\Models\Provinsi;
use App\Models\Qism;
use App\Models\QismDetail;
use App\Models\QismDetailHasKelas;
use App\Models\Santri;
use App\Models\Semester;
use App\Models\StatusAdmPendaftar;
use App\Models\StatusTempatTinggal;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranAktif;
use App\Models\TahunBerjalan;
use App\Models\Transpp;
use App\Models\Waktutempuh;
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
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Database\Eloquent\Model;
use Schmeits\FilamentCharacterCounter\Forms\Components\TextInput as ComponentsTextInput;

class TambahCalonSantri extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    // public static function canView(): bool
    // {
    //     // dd(Auth::user());

    //     $walisantri_id = Walisantri::where('kartu_keluarga_santri', Auth::user()->username)->first();
    //     // dd($walisantri_id->is_collapse);



    //     if ($walisantri_id->is_collapse == true) {
    //         return true;
    //     } elseif ($walisantri_id->is_collapse == false) {
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
            ->heading('2. Tambah Calon Santri')
            ->paginated(false)
            ->emptyStateHeading('Tambah Calon Santri')
            ->emptyStateDescription('Klik tombol "Tambah Calon Santri"')
            ->emptyStateIcon('heroicon-o-book-open')
            ->query(

                Santri::where('walisantri_id', $walisantri->id)
                    ->where('jenis_pendaftar_id', 1)
                    ->where('tahun_berjalan_id', $ts->id)

            )
            ->columns([
                Split::make([
                    TextColumn::make('index')
                        ->rowIndex(),
                    TextColumn::make('nama_lengkap')
                        ->description(fn($record): string => "Nama Calon Santri:", position: 'above'),
                    TextColumn::make('qism_detail.abbr_qism_detail')
                        ->description(fn($record): string => "Mendaftar ke qism:", position: 'above'),
                    TextColumn::make('kelas.kelas')
                        ->description(fn($record): string => "Kelas:", position: 'above'),

                    TextColumn::make('belum_nism')
                        ->label('Status')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'Terdaftar' => 'info',
                        })
                        ->default('Terdaftar')
                        ->description(fn($record): string => "Status:", position: 'above'),
 
                    TextColumn::make('s_emis4')
                        ->label('Status Data Santri')
                        ->default('Belum Lengkap')
                        ->size(TextColumn\TextColumnSize::Large)
                        ->weight(FontWeight::Bold)
                        ->description(fn($record): string => "Status Data Santri:", position: 'above')
                        ->formatStateUsing(function (Model $record) {
                            if ($record->s_emis4 == false) {
                                return ('Belum lengkap');
                            } elseif ($record->s_emis4 == true) {
                                return ('Lengkap');
                            }
                        })
                        ->badge()
                        ->color(function (Model $record) {
                            if ($record->s_emis4 == false) {
                                return ('danger');
                            } elseif ($record->s_emis4 == true) {
                                return ('success');
                            }
                        }),
                ])->from('md')

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Calon Santri')
                    ->modalHeading('Tambah Calon Santri')
                    ->modalCloseButton(false)
                    ->modalWidth('full')
                    ->closeModalByClickingAway(false)
                    ->closeModalByEscaping(false)
                    ->button()
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal'))
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
                    ->after(function ($record) {
                        Notification::make()
                            ->success()
                            ->title('Alhamdulillah data calon santri telah tersimpan')
                            ->body('Lanjutkan menambah calon santri, atau keluar jika telah selesai')
                            // ->persistent()
                            ->color('success')
                            ->send();

                        $santri = Santri::find($record->id);

                        $santri->nama_lengkap = Str::ucwords(strtolower($record->nama_lengkap));
                        $santri->nama_panggilan = Str::ucwords(strtolower($record->nama_panggilan));
                        $santri->tempat_lahir = Str::ucwords(strtolower($record->tempat_lahir));
                        $santri->nama_kpl_kel = Str::ucwords(strtolower($record->nama_kpl_kel));

                        $santri->save();
                    })
                    ->steps([

                        Step::make('1. DATA AWAL')
                            ->schema([
                                Hidden::make('tahap_pendaftaran_id')
                                    ->default(1),

                                Hidden::make('jenis_pendaftar_id')
                                    ->default(1),

                                Hidden::make('s_emis4')
                                    ->default(1),

                                Hidden::make('tahun_berjalan_id')
                                    ->default(
                                        function () {
                                            $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
                                            $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

                                            return $ts->id;
                                        }
                                    ),

                                Hidden::make('walisantri_id')
                                    ->default(function (Get $get, ?string $state, Set $set) {

                                        $walisantri_id = Walisantri::where('kartu_keluarga_santri', Auth::user()->username)->first();

                                        return ($walisantri_id->id);
                                    }),


                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">1. DATA AWAL</p>
                                                </div>')),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('qism_id')
                                            ->label('Qism yang dituju')
                                            ->placeholder('Pilih Qism yang dituju')
                                            ->options(Qism::whereIsActive(1)->pluck('qism', 'id'))
                                            ->live()
                                            ->required()
                                            ->native(false)
                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set) {

                                                // $qism = Qism::where('id', $get('qism_id'))->first();

                                                $taaktif = TahunAjaranAktif::where('is_active', true)->where('qism_id', $get('qism_id'))->first();

                                                $tasel = TahunAjaran::where('id', $taaktif->tahun_ajaran_id)->first();

                                                $set('tahun_ajaran_id', $tasel->tahun_ajaran_id);
                                                $set('qism_detail_id', null);
                                                $set('kelas_id', null);
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('qism_detail_id')
                                            ->label('Putra/Putri')
                                            ->inline()
                                            ->options(function (Get $get) {

                                                return (QismDetail::where('qism_id', $get('qism_id'))->pluck('jeniskelamin', 'id'));
                                            })
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set) {

                                                $jkqism = QismDetail::where('id', $state)->first();

                                                $set('jeniskelamin_id', $jkqism->jeniskelamin_id);
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('kelas_id')
                                            ->label('Kelas yang dituju')
                                            ->placeholder('Pilih Kelas')
                                            ->native(false)
                                            ->live()
                                            ->required()
                                            ->options(function (Get $get) {

                                                return (QismDetailHasKelas::where('qism_detail_id', $get('qism_detail_id'))->pluck('kelas', 'kelas_id'));
                                            })
                                            ->disabled(fn(Get $get) =>
                                            $get('qism_detail_id') == null),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('tahun_ajaran_id')
                                            ->label('Tahun Ajaran')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            ->options(TahunAjaran::all()->pluck('ta', 'id'))
                                            ->native(false),

                                    ]),


                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                    </div>')),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('kartu_keluarga_sama_id')
                                            ->label('Kartu Keluarga sama dengan')
                                            ->required()
                                            ->inline()
                                            ->live()
                                            ->options(function (Get $get) {

                                                $walisantri_id = $get('walisantri_id');

                                                $status = Walisantri::where('id', $walisantri_id)->first();
                                                // dd($status->ak_no_kk !== null);

                                                if ($status->ak_status_id == 1 && $status->ik_status_id == 1 && $status->w_status_id == 3) {

                                                    return ([
                                                        1 => 'KK sama dengan Ayah Kandung',
                                                        2 => 'KK sama dengan Ibu Kandung',
                                                        3 => 'KK sama dengan Wali',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id == 1 && $status->ik_status_id == 1 && $status->w_status_id != 3) {

                                                    return ([
                                                        1 => 'KK sama dengan Ayah Kandung',
                                                        2 => 'KK sama dengan Ibu Kandung',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id == 1 && $status->ik_status_id != 1 && $status->w_status_id != 3) {

                                                    return ([
                                                        1 => 'KK sama dengan Ayah Kandung',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id == 1 && $status->ik_status_id != 1 && $status->w_status_id == 3) {

                                                    return ([
                                                        1 => 'KK sama dengan Ayah Kandung',
                                                        3 => 'KK sama dengan Wali',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id != 1 && $status->ik_status_id == 1 && $status->w_status_id == 3) {

                                                    return ([
                                                        2 => 'KK sama dengan Ibu Kandung',
                                                        3 => 'KK sama dengan Wali',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id != 1 && $status->ik_status_id != 1 && $status->w_status_id == 3) {

                                                    return ([
                                                        3 => 'KK sama dengan Wali',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id != 1 && $status->ik_status_id == 1 && $status->w_status_id != 3) {

                                                    return ([
                                                        2 => 'KK sama dengan Ibu Kandung',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                }
                                            })
                                            ->afterStateUpdated(function (Get $get, Set $set) {

                                                $walisantri_id = $get('walisantri_id');

                                                $walisantri = Walisantri::where('id', $walisantri_id)->first();

                                                if ($get('kartu_keluarga_sama_id') == 1) {

                                                    $set('kartu_keluarga', $walisantri->ak_no_kk);
                                                    $set('nama_kpl_kel', $walisantri->ak_kep_kel_kk);
                                                } elseif ($get('kartu_keluarga_sama_id') == 2) {

                                                    $set('kartu_keluarga', $walisantri->ik_no_kk);
                                                    $set('nama_kpl_kel', $walisantri->ik_kep_kel_kk);
                                                } elseif ($get('kartu_keluarga_sama_id') == 3) {

                                                    $set('kartu_keluarga', $walisantri->w_no_kk);
                                                    $set('nama_kpl_kel', $walisantri->w_kep_kel_kk);
                                                } elseif ($get('kartu_keluarga_sama_id') == 4) {

                                                    $set('kartu_keluarga', null);
                                                    $set('nama_kpl_kel', null);
                                                }
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('kartu_keluarga')
                                            ->label('Nomor KK Calon Santri')
                                            ->length(16)
                                            ->required()
                                            // ->disabled(fn (Get $get) =>
                                            // $get('kartu_keluarga_sama') !== 'KK Sendiri')
                                            ->dehydrated(),

                                        TextInput::make('nama_kpl_kel')
                                            ->label('Nama Kepala Keluarga')
                                            ->required()
                                            // ->disabled(fn (Get $get) =>
                                            // $get('kartu_keluarga_sama') !== 'KK Sendiri')
                                            ->dehydrated(),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('kewarganegaraan_id')
                                            ->label('Kewarganegaraan')
                                            ->inline()
                                            ->options(Kewarganegaraan::whereIsActive(1)->pluck('kewarganegaraan', 'id'))
                                            ->default(1)
                                            ->live(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        ComponentsTextInput::make('nik')
                                            ->label('NIK')
                                            ->hint('Isi sesuai dengan KK')
                                            ->hintColor('danger')
                                            ->regex('/^[0-9]*$/')
                                            ->length(16)
                                            ->maxLength(16)
                                            ->required()
                                            ->unique(Santri::class, 'nik', ignoreRecord: true)
                                            //->default('3295131306822002')
                                            ->hidden(fn(Get $get) =>
                                            $get('kewarganegaraan_id') != 1),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('asal_negara')
                                            ->label('Asal Negara Calon Santri')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('kewarganegaraan_id') != 2),

                                        TextInput::make('kitas')
                                            ->label('KITAS Calon Santri')
                                            ->hint('Nomor Izin Tinggal (KITAS)')
                                            ->hintColor('danger')
                                            ->required()
                                            //->default('3295131306822002')
                                            ->unique(Santri::class, 'kitas')
                                            ->hidden(fn(Get $get) =>
                                            $get('kewarganegaraan_id') != 2),

                                    ]),

                            ]),
                        // end of step 1

                        Step::make('2. DATA SANTRI')
                            ->schema([
                                //SANTRI
                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                <p class="text-2xl">SANTRI</p>
                                            </div>')),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('nama_lengkap')
                                            ->label('Nama Lengkap')
                                            ->hint('Isi sesuai dengan KK')
                                            ->hintColor('danger')
                                            //->default('asfasdad')
                                            ->required(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('nama_panggilan')
                                            ->label('Nama Hijroh/Islami/Panggilan')
                                            //->default('asfasdad')
                                            ->required(),

                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                            </div>')),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('jeniskelamin_id')
                                            ->label('Jenis Kelamin')
                                            ->inline()
                                            ->options(Jeniskelamin::whereIsActive(1)->pluck('jeniskelamin', 'id'))
                                            ->required()
                                            ->disabled()
                                            ->dehydrated(),

                                    ]),

                                Grid::make(6)
                                    ->schema([

                                        TextInput::make('tempat_lahir')
                                            ->label('Tempat Lahir')
                                            ->hint('Isi sesuai dengan KK')
                                            ->hintColor('danger')
                                            //->default('asfasdad')
                                            ->required(),

                                        DatePicker::make('tanggal_lahir')
                                            ->label('Tanggal Lahir')
                                            ->hint('Isi sesuai dengan KK')
                                            ->hintColor('danger')
                                            //->default('20010101')
                                            ->required()
                                            ->displayFormat('d M Y')
                                            ->native(false)
                                            ->maxDate(now())
                                            ->live(onBlur: true)
                                            ->closeOnDateSelection()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $set('umur', Carbon::parse($state)->age);
                                            }),

                                        TextInput::make('umur')
                                            ->label('Umur')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required(),

                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('anak_ke')
                                            ->label('Anak ke-')
                                            ->required()
                                            ->numeric()
                                            ->disabled(fn(Get $get) =>
                                            $get('umur') == null)
                                            //->default('3')
                                            ->rules([
                                                fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                                                    $anakke = $get('anak_ke');
                                                    $psjumlahsaudara = $get('jumlah_saudara');
                                                    $jumlahsaudara = $psjumlahsaudara + 1;

                                                    if ($anakke > $jumlahsaudara) {
                                                        $fail("Anak ke tidak bisa lebih dari jumlah saudara + 1");
                                                    }
                                                },
                                            ]),

                                        TextInput::make('jumlah_saudara')
                                            ->label('Jumlah saudara')
                                            ->numeric()
                                            ->disabled(fn(Get $get) =>
                                            $get('umur') == null)
                                            //->default('5')
                                            ->required(),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('agama')
                                            ->label('Agama')
                                            ->default('Islam')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated(),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('cita_cita_id')
                                            ->label('Cita-cita')
                                            ->placeholder('Pilih Cita-cita')
                                            ->options(Cita::whereIsActive(1)->pluck('cita', 'id'))
                                            // ->searchable()
                                            ->required()
                                            ->live()
                                            ->native(false),

                                        TextInput::make('cita_cita_lainnya')
                                            ->label('Cita-cita Lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('cita_cita_id') != 10),
                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('hobi_id')
                                            ->label('Hobi')
                                            ->placeholder('Pilih Hobi')
                                            ->options(Hobi::whereIsActive(1)->pluck('hobi', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Lainnya')
                                            ->live()
                                            ->native(false),

                                        TextInput::make('hobi_lainnya')
                                            ->label('Hobi Lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('hobi_id') != 6),

                                    ]),


                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('keb_khus_id')
                                            ->label('Kebutuhan Khusus')
                                            ->placeholder('Pilih Kebutuhan Khusus')
                                            ->options(KebutuhanKhusus::whereIsActive(1)->pluck('kebutuhan_khusus', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Lainnya')
                                            ->live()
                                            ->native(false),

                                        TextInput::make('keb_khus_lainnya')
                                            ->label('Kebutuhan Khusus Lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('keb_khus_id') != 6),
                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('keb_dis_id')
                                            ->label('Kebutuhan Disabilitas')
                                            ->placeholder('Pilih Kebutuhan Disabilitas')
                                            ->options(KebutuhanDisabilitas::whereIsActive(1)->pluck('kebutuhan_disabilitas', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Lainnya')
                                            ->live()
                                            ->native(false),

                                        TextInput::make('keb_dis_lainnya')
                                            ->label('Kebutuhan Disabilitas Lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('keb_dis_id') != 8),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('tdk_hp_id')
                                            ->label('Apakah memiliki nomor handphone?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('nomor_handphone')
                                            ->label('No. Handphone')
                                            ->helperText('Contoh: 82187782223')
                                            // ->mask('82187782223')
                                            ->prefix('+62')
                                            ->tel()
                                            //->default('82187782223')
                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                            ->required()
                                            ->hidden(fn(Get $get) =>
                                            $get('tdk_hp_id') != 1),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('email')
                                            ->label('Email')
                                            //->default('mail@mail.com')
                                            ->email(),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_mendaftar_keinginan_id')
                                            ->label('Mendaftar atas kenginginan')
                                            ->inline()
                                            ->options(MendaftarKeinginan::whereIsActive(1)->pluck('mendaftar_keinginan', 'id'))
                                            ->live(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('ps_mendaftar_keinginan_lainnya')
                                            ->label('Lainnya')
                                            ->required()
                                            //->default('asdasf')
                                            ->hidden(fn(Get $get) =>
                                            $get('ps_mendaftar_keinginan_id') != 4),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Hidden::make('aktivitaspend_id')
                                    ->default(9),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('bya_sklh_id')
                                            ->label('Yang membiayai sekolah')
                                            ->inline()
                                            ->options(MembiayaiSekolah::whereIsActive(1)->pluck('membiayai_sekolah', 'id'))
                                            ->live(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('bya_sklh_lainnya')
                                            ->label('Yang membiayai sekolah lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('bya_sklh_id') != 4),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('belum_nisn_id')
                                            ->label('Apakah memiliki NISN?')
                                            ->helperText(new HtmlString('<strong>NISN</strong> adalah Nomor Induk Siswa Nasional'))
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                        TextInput::make('nisn')
                                            ->label('Nomor NISN')
                                            ->required()
                                            //->default('2421324')
                                            ->hidden(fn(Get $get) =>
                                            $get('belum_nisn_id') != 1),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('nomor_kip_memiliki_id')
                                            ->label('Apakah memiliki KIP?')
                                            ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                        TextInput::make('nomor_kip')
                                            ->label('Nomor KIP')
                                            ->required()
                                            //->default('32524324')
                                            ->hidden(fn(Get $get) =>
                                            $get('nomor_kip_memiliki_id') != 1),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(2)
                                    ->schema([

                                        Textarea::make('ps_peng_pend_agama')
                                            ->label('Pengalaman pendidikan agama')
                                            ->required(),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        Textarea::make('ps_peng_pend_formal')
                                            ->label('Pengalaman pendidikan formal')
                                            ->required(),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('hafalan_id')
                                            ->label('Hafalan')
                                            ->placeholder('Jumlah Hafalan dalam Hitungan Juz')
                                            ->options(Hafalan::whereIsActive(1)->pluck('hafalan', 'id'))
                                            ->required()
                                            ->suffix('juz')
                                            ->hidden(fn(Get $get) =>
                                            $get('qism_id') == 1)
                                            ->native(false),

                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                // ALAMAT SANTRI
                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                <p class="text-lg">TEMPAT TINGGAL DOMISILI</p>
                                                <p class="text-lg">SANTRI</p>
                                            </div>')),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('al_s_status_mukim_id')
                                            ->label('Apakah mukim di Pondok?')
                                            ->helperText(new HtmlString('Pilih <strong>Tidak Mukim</strong> khusus bagi pendaftar <strong>Tarbiyatul Aulaad</strong> dan <strong>Pra Tahfidz kelas 1-4</strong>'))
                                            ->live()
                                            ->inline()
                                            ->required()
                                            ->default(function (Get $get) {

                                                $qism = $get('qism_id');

                                                $kelas = $get('kelas_id');

                                                if ($qism == 1) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 1) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 1) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 2) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 3) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 4) {

                                                    return 2;
                                                } else {
                                                    return 1;
                                                }
                                            })
                                            ->options(function (Get $get) {

                                                $qism = $get('qism_id');

                                                $kelas = $get('kelas_id');

                                                if ($qism == 1) {

                                                    return ([
                                                        2 => 'Tidak Mukim'
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 1) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 1) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 2) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 3) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 4) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } else {
                                                    return ([

                                                        1 => 'Mukim',
                                                    ]);
                                                }
                                            })
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                if ($get('al_s_status_mukim_id') == 1) {

                                                    $set('al_s_stts_tptgl_id', 10);
                                                } elseif ($get('al_s_status_mukim_id') == 2) {

                                                    $set('al_s_stts_tptgl_id', null);
                                                }
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('al_s_stts_tptgl_id')
                                            ->label('Status tempat tinggal')
                                            ->placeholder('Status tempat tinggal')
                                            ->options(function (Get $get) {
                                                if ($get('al_s_status_mukim_id') == 2) {
                                                    return (StatusTempatTinggal::whereIsActive(1)->pluck('status_tempat_tinggal', 'id'));
                                                }
                                            })
                                            // ->searchable()
                                            ->required()
                                            //->default('Kontrak/Kost')
                                            ->hidden(fn(Get $get) =>
                                            $get('al_s_status_mukim_id') == 1)
                                            ->live()
                                            ->native(false)
                                            ->dehydrated(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('al_s_provinsi_id')
                                            ->label('Provinsi')
                                            ->placeholder('Pilih Provinsi')
                                            ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                            // ->searchable()
                                            //->default('35')
                                            ->required()
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            )
                                            ->afterStateUpdated(function (Set $set) {
                                                $set('al_s_kabupaten_id', null);
                                                $set('al_s_kecamatan_id', null);
                                                $set('al_s_kelurahan_id', null);
                                                $set('al_s_kodepos', null);
                                            }),

                                        Select::make('al_s_kabupaten_id')
                                            ->label('Kabupaten')
                                            ->placeholder('Pilih Kabupaten')
                                            ->options(fn(Get $get): Collection => Kabupaten::query()
                                                ->where('provinsi_id', $get('al_s_provinsi_id'))
                                                ->pluck('kabupaten', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('232')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('al_s_kecamatan_id')
                                            ->label('Kecamatan')
                                            ->placeholder('Pilih Kecamatan')
                                            ->options(fn(Get $get): Collection => Kecamatan::query()
                                                ->where('kabupaten_id', $get('al_s_kabupaten_id'))
                                                ->pluck('kecamatan', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('3617')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                        Select::make('al_s_kelurahan_id')
                                            ->label('Kelurahan')
                                            ->placeholder('Pilih Kelurahan')
                                            ->options(fn(Get $get): Collection => Kelurahan::query()
                                                ->where('kecamatan_id', $get('al_s_kecamatan_id'))
                                                ->pluck('kelurahan', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('45322')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            )
                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                $state = $kodepos;

                                                foreach ($state as $state) {
                                                    $set('al_s_kodepos', Str::substr($state, 12, 5));
                                                }
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('al_s_kodepos')
                                            ->label('Kodepos')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated()
                                            //->default('63264')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(4)
                                    ->schema([


                                        TextInput::make('al_s_rt')
                                            ->label('RT')
                                            ->helperText('Isi 0 jika tidak ada RT/RW')
                                            ->required()
                                            ->numeric()
                                            ->disabled(fn(Get $get) =>
                                            $get('al_s_kodepos') == null)
                                            //->default('2')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                        TextInput::make('al_s_rw')
                                            ->label('RW')
                                            ->helperText('Isi 0 jika tidak ada RT/RW')
                                            ->required()
                                            ->numeric()
                                            ->disabled(fn(Get $get) =>
                                            $get('al_s_kodepos') == null)
                                            //->default('2')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        Textarea::make('al_s_alamat')
                                            ->label('Alamat')
                                            ->required()
                                            ->disabled(fn(Get $get) =>
                                            $get('al_s_kodepos') == null)
                                            //->default('sdfsdasdada')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('al_s_jarak_id')
                                            ->label('Jarak tempat tinggal ke Pondok Pesantren')
                                            ->options(Jarakpp::whereIsActive(1)->pluck('jarak_kepp', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Kurang dari 5 km')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                        Select::make('al_s_transportasi_id')
                                            ->label('Transportasi ke Pondok Pesantren')
                                            ->options(Transpp::whereIsActive(1)->pluck('transportasi_kepp', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Ojek')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('al_s_waktu_tempuh_id')
                                            ->label('Waktu tempuh ke Pondok Pesantren')
                                            ->options(Waktutempuh::whereIsActive(1)->pluck('waktu_tempuh', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('10 - 19 menit')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                        TextInput::make('al_s_koordinat')
                                            ->label('Titik koordinat tempat tinggal')
                                            //->default('sfasdadasdads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),
                                    ]),
                            ]),

                        // end of step 2

                        Step::make('3. KUESIONER KEGIATAN HARIAN')
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">KUESIONER KEGIATAN HARIAN</p>
                                                </div>')),
                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_keberadaan_id')
                                            ->label('1. Di mana saat ini ananda berada?')
                                            ->live()
                                            ->inline()
                                            ->options(AnandaBerada::whereIsActive(1)->pluck('ananda_berada', 'id')),
                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_keberadaan_nama_mhd')
                                            ->label('Nama Mahad')
                                            ->required()
                                            //->default('sadads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_keberadaan_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_keberadaan_lokasi_mhd')
                                            ->label('Lokasi Mahad')
                                            ->required()
                                            //->default('sadads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_keberadaan_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_keberadaan_rumah_keg')
                                            ->label('Jika dirumah, apa kegiatan ananda selama waktu tersebut?')
                                            //->default('asfsadsa')
                                            ->required()
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_keberadaan_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_fasilitas_gawai_id')
                                            ->label('2. Apakah selama di rumah (baik bagi yg dirumah, atau bagi yang di Mahad ketika liburan), ananda difasilitasi HP atau laptop (baik dengan memiliki sendiri HP/ laptop dan yang sejenis atau dipinjami orang tua)?')
                                            // ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_fasilitas_gawai_medsos')
                                            ->label('Apakah ananda memiliki akun medsos (media sosial)?')
                                            ->required()
                                            //->default('Ya')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_fasilitas_gawai_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_fasilitas_gawai_medsos_daftar')
                                            ->label('Akun medsos apa saja yang ananda miliki?')
                                            ->required()
                                            //->default('asfdads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_fasilitas_gawai_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_fasilitas_gawai_medsos_aktif')
                                            ->label('Apakah akun tersebut masih aktif hingga sekarang?')
                                            ->required()
                                            //->default('asdafs')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_fasilitas_gawai_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_fasilitas_gawai_medsos_menutup_id')
                                            ->label('Apakah bersedia menutup akun tersebut selama menjadi santri/santriwati?')
                                            // ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_fasilitas_gawai_id') != 1
                                            ),
                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_medsos_sering_id')
                                            ->label('3. Dari medsos berikut, manakah yang sering digunakan ananda?')
                                            // ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            // ->live()
                                            ->multiple()
                                            ->options(MedsosAnanda::whereIsActive(1)->pluck('medsos_ananda', 'id'))
                                            ->required(),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_medsos_group_id')
                                            ->label('4. Apakah ananda tergabung dalam grup yang ada pada medsos tersebut?')
                                            // ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_medsos_group_nama')
                                            ->label('Mohon dijelaskan nama grup dan bidang kegiatannya')
                                            ->required()
                                            //->default('asdadasdads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_medsos_group_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_bacaan')
                                            ->label('5. Apa saja buku bacaan yang disukai atau sering dibaca ananda?')
                                            ->helperText('Mohon dijelaskan jenis bacaannya')
                                            //->default('asdads')
                                            ->required(),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_bacaan_cara_dapat')
                                            ->label('Bagaimana cara mendapatkan bacaan tersebut? (Via online atau membeli sendiri)')
                                            //->default('assad')
                                            ->required(),
                                    ]),


                            ]),
                        // end of step 3

                        Step::make('4. KUESIONER KESEHATAN')
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
                        // end of step 4

                        Step::make('5. KUESIONER KEMANDIRIAN')
                            ->hidden(function (Get $get) {
                                $qism = $get('qism_id');
                                $kelas = $get('kelas_id');

                                if ($qism == 1) {
                                    return false;
                                } elseif ($qism == 2 && $kelas == 1) {
                                    return false;
                                } elseif ($qism == 2 && $kelas == 2) {
                                    return false;
                                } elseif ($qism == 2 && $kelas == 3) {
                                    return false;
                                } elseif ($qism == 2 && $kelas == 4) {
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
                        // end of step 5

                        Step::make('6. KUESIONER KEMAMPUAN PEMBAYARAN ADMINISTRASI')
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
                                    ->content(function (Get $get) {
                                        if ($get('qism_id') == 1) {
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
                                        } elseif ($get('qism_id') == 2) {
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
                                        } elseif ($get('qism_id') != 1 || $get('qism_id') != 2) {
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
                                            ->label('Status anak didik terkait dengan administrasi')
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
            ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Data Calon Santri')
                    ->modalHeading('Edit Calon Santri')
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
                            ->title('Alhamdulillah data calon santri telah tersimpan')
                            ->body('Lanjutkan menambah calon santri, atau keluar jika telah selesai')
                            // ->persistent()
                            ->color('success')
                            ->send();
                    })
                    ->steps([

                        Step::make('1. DATA AWAL')
                            ->schema([
                                Hidden::make('tahap_pendaftaran_id')
                                    ->default(1),

                                Hidden::make('jenis_pendaftar_id')
                                    ->default(1),

                                Hidden::make('s_emis4')
                                    ->default(1),

                                Hidden::make('tahun_berjalan_id')
                                    ->default(
                                        function () {
                                            $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
                                            $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

                                            return $ts->id;
                                        }
                                    ),

                                Hidden::make('walisantri_id')
                                    ->default(function (Get $get, ?string $state, Set $set) {

                                        $walisantri_id = Walisantri::where('kartu_keluarga_santri', Auth::user()->username)->first();

                                        return ($walisantri_id->id);
                                    }),


                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">1. DATA AWAL</p>
                                                </div>')),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('qism_id')
                                            ->label('Qism yang dituju')
                                            ->placeholder('Pilih Qism yang dituju')
                                            ->options(Qism::whereIsActive(1)->pluck('qism', 'id'))
                                            ->live()
                                            ->required()
                                            ->native(false)
                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set) {

                                                // $qism = Qism::where('id', $get('qism_id'))->first();

                                                $taaktif = TahunAjaranAktif::where('is_active', true)->where('qism_id', $get('qism_id'))->first();

                                                $tasel = TahunAjaran::where('id', $taaktif->tahun_ajaran_id)->first();

                                                $set('tahun_ajaran_id', $tasel->tahun_ajaran_id);
                                                $set('qism_detail_id', null);
                                                $set('kelas_id', null);
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('qism_detail_id')
                                            ->label('Putra/Putri')
                                            ->inline()
                                            ->options(function (Get $get) {

                                                return (QismDetail::where('qism_id', $get('qism_id'))->pluck('jeniskelamin', 'id'));
                                            })
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set) {

                                                $jkqism = QismDetail::where('id', $state)->first();

                                                $set('jeniskelamin_id', $jkqism->jeniskelamin_id);
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('kelas_id')
                                            ->label('Kelas yang dituju')
                                            ->placeholder('Pilih Kelas')
                                            ->native(false)
                                            ->live()
                                            ->required()
                                            ->options(function (Get $get) {

                                                return (QismDetailHasKelas::where('qism_detail_id', $get('qism_detail_id'))->pluck('kelas', 'kelas_id'));
                                            })
                                            ->disabled(fn(Get $get) =>
                                            $get('qism_detail_id') == null),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('tahun_ajaran_id')
                                            ->label('Tahun Ajaran')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            ->options(TahunAjaran::all()->pluck('ta', 'id'))
                                            ->native(false),

                                    ]),


                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                    </div>')),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('kartu_keluarga_sama_id')
                                            ->label('Kartu Keluarga sama dengan')
                                            ->required()
                                            ->inline()
                                            ->live()
                                            ->options(function (Get $get) {

                                                $walisantri_id = $get('walisantri_id');

                                                $status = Walisantri::where('id', $walisantri_id)->first();
                                                // dd($status->ak_no_kk !== null);

                                                if ($status->ak_status_id == 1 && $status->ik_status_id == 1 && $status->w_status_id == 3) {

                                                    return ([
                                                        1 => 'KK sama dengan Ayah Kandung',
                                                        2 => 'KK sama dengan Ibu Kandung',
                                                        3 => 'KK sama dengan Wali',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id == 1 && $status->ik_status_id == 1 && $status->w_status_id != 3) {

                                                    return ([
                                                        1 => 'KK sama dengan Ayah Kandung',
                                                        2 => 'KK sama dengan Ibu Kandung',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id == 1 && $status->ik_status_id != 1 && $status->w_status_id != 3) {

                                                    return ([
                                                        1 => 'KK sama dengan Ayah Kandung',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id == 1 && $status->ik_status_id != 1 && $status->w_status_id == 3) {

                                                    return ([
                                                        1 => 'KK sama dengan Ayah Kandung',
                                                        3 => 'KK sama dengan Wali',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id != 1 && $status->ik_status_id == 1 && $status->w_status_id == 3) {

                                                    return ([
                                                        2 => 'KK sama dengan Ibu Kandung',
                                                        3 => 'KK sama dengan Wali',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id != 1 && $status->ik_status_id != 1 && $status->w_status_id == 3) {

                                                    return ([
                                                        3 => 'KK sama dengan Wali',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id != 1 && $status->ik_status_id == 1 && $status->w_status_id != 3) {

                                                    return ([
                                                        2 => 'KK sama dengan Ibu Kandung',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                }
                                            })
                                            ->afterStateUpdated(function (Get $get, Set $set) {

                                                $walisantri_id = $get('walisantri_id');

                                                $walisantri = Walisantri::where('id', $walisantri_id)->first();

                                                if ($get('kartu_keluarga_sama_id') == 1) {

                                                    $set('kartu_keluarga', $walisantri->ak_no_kk);
                                                    $set('nama_kpl_kel', $walisantri->ak_kep_kel_kk);
                                                } elseif ($get('kartu_keluarga_sama_id') == 2) {

                                                    $set('kartu_keluarga', $walisantri->ik_no_kk);
                                                    $set('nama_kpl_kel', $walisantri->ik_kep_kel_kk);
                                                } elseif ($get('kartu_keluarga_sama_id') == 3) {

                                                    $set('kartu_keluarga', $walisantri->w_no_kk);
                                                    $set('nama_kpl_kel', $walisantri->w_kep_kel_kk);
                                                } elseif ($get('kartu_keluarga_sama_id') == 4) {

                                                    $set('kartu_keluarga', null);
                                                    $set('nama_kpl_kel', null);
                                                }
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('kartu_keluarga')
                                            ->label('Nomor KK Calon Santri')
                                            ->length(16)
                                            ->required()
                                            // ->disabled(fn (Get $get) =>
                                            // $get('kartu_keluarga_sama') !== 'KK Sendiri')
                                            ->dehydrated(),

                                        TextInput::make('nama_kpl_kel')
                                            ->label('Nama Kepala Keluarga')
                                            ->required()
                                            // ->disabled(fn (Get $get) =>
                                            // $get('kartu_keluarga_sama') !== 'KK Sendiri')
                                            ->dehydrated(),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('kewarganegaraan_id')
                                            ->label('Kewarganegaraan')
                                            ->inline()
                                            ->options(Kewarganegaraan::whereIsActive(1)->pluck('kewarganegaraan', 'id'))
                                            ->default(1)
                                            ->live(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        ComponentsTextInput::make('nik')
                                            ->label('NIK')
                                            ->hint('Isi sesuai dengan KK')
                                            ->hintColor('danger')
                                            ->regex('/^[0-9]*$/')
                                            ->length(16)
                                            ->maxLength(16)
                                            ->required()
                                            ->unique(Santri::class, 'nik', ignoreRecord: true)
                                            //->default('3295131306822002')
                                            ->hidden(fn(Get $get) =>
                                            $get('kewarganegaraan_id') != 1),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('asal_negara')
                                            ->label('Asal Negara Calon Santri')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('kewarganegaraan_id') != 2),

                                        TextInput::make('kitas')
                                            ->label('KITAS Calon Santri')
                                            ->hint('Nomor Izin Tinggal (KITAS)')
                                            ->hintColor('danger')
                                            ->required()
                                            //->default('3295131306822002')
                                            ->unique(Santri::class, 'kitas')
                                            ->hidden(fn(Get $get) =>
                                            $get('kewarganegaraan_id') != 2),

                                    ]),

                            ]),
                        // end of step 1

                        Step::make('2. DATA SANTRI')
                            ->schema([
                                //SANTRI
                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                <p class="text-2xl">SANTRI</p>
                                            </div>')),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('nama_lengkap')
                                            ->label('Nama Lengkap')
                                            ->hint('Isi sesuai dengan KK')
                                            ->hintColor('danger')
                                            //->default('asfasdad')
                                            ->required(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('nama_panggilan')
                                            ->label('Nama Hijroh/Islami/Panggilan')
                                            //->default('asfasdad')
                                            ->required(),

                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                            </div>')),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('jeniskelamin_id')
                                            ->label('Jenis Kelamin')
                                            ->inline()
                                            ->options(Jeniskelamin::whereIsActive(1)->pluck('jeniskelamin', 'id'))
                                            ->required()
                                            ->disabled()
                                            ->dehydrated(),

                                    ]),

                                Grid::make(6)
                                    ->schema([

                                        TextInput::make('tempat_lahir')
                                            ->label('Tempat Lahir')
                                            ->hint('Isi sesuai dengan KK')
                                            ->hintColor('danger')
                                            //->default('asfasdad')
                                            ->required(),

                                        DatePicker::make('tanggal_lahir')
                                            ->label('Tanggal Lahir')
                                            ->hint('Isi sesuai dengan KK')
                                            ->hintColor('danger')
                                            //->default('20010101')
                                            ->required()
                                            ->displayFormat('d M Y')
                                            ->native(false)
                                            ->maxDate(now())
                                            ->live(onBlur: true)
                                            ->closeOnDateSelection()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $set('umur', Carbon::parse($state)->age);
                                            }),

                                        TextInput::make('umur')
                                            ->label('Umur')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required(),

                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('anak_ke')
                                            ->label('Anak ke-')
                                            ->required()
                                            ->numeric()
                                            ->disabled(fn(Get $get) =>
                                            $get('umur') == null)
                                            //->default('3')
                                            ->rules([
                                                fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                                                    $anakke = $get('anak_ke');
                                                    $psjumlahsaudara = $get('jumlah_saudara');
                                                    $jumlahsaudara = $psjumlahsaudara + 1;

                                                    if ($anakke > $jumlahsaudara) {
                                                        $fail("Anak ke tidak bisa lebih dari jumlah saudara + 1");
                                                    }
                                                },
                                            ]),

                                        TextInput::make('jumlah_saudara')
                                            ->label('Jumlah saudara')
                                            ->numeric()
                                            ->disabled(fn(Get $get) =>
                                            $get('umur') == null)
                                            //->default('5')
                                            ->required(),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('agama')
                                            ->label('Agama')
                                            ->default('Islam')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated(),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('cita_cita_id')
                                            ->label('Cita-cita')
                                            ->placeholder('Pilih Cita-cita')
                                            ->options(Cita::whereIsActive(1)->pluck('cita', 'id'))
                                            // ->searchable()
                                            ->required()
                                            ->live()
                                            ->native(false),

                                        TextInput::make('cita_cita_lainnya')
                                            ->label('Cita-cita Lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('cita_cita_id') != 10),
                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('hobi_id')
                                            ->label('Hobi')
                                            ->placeholder('Pilih Hobi')
                                            ->options(Hobi::whereIsActive(1)->pluck('hobi', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Lainnya')
                                            ->live()
                                            ->native(false),

                                        TextInput::make('hobi_lainnya')
                                            ->label('Hobi Lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('hobi_id') != 6),

                                    ]),


                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('keb_khus_id')
                                            ->label('Kebutuhan Khusus')
                                            ->placeholder('Pilih Kebutuhan Khusus')
                                            ->options(KebutuhanKhusus::whereIsActive(1)->pluck('kebutuhan_khusus', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Lainnya')
                                            ->live()
                                            ->native(false),

                                        TextInput::make('keb_khus_lainnya')
                                            ->label('Kebutuhan Khusus Lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('keb_khus_id') != 6),
                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('keb_dis_id')
                                            ->label('Kebutuhan Disabilitas')
                                            ->placeholder('Pilih Kebutuhan Disabilitas')
                                            ->options(KebutuhanDisabilitas::whereIsActive(1)->pluck('kebutuhan_disabilitas', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Lainnya')
                                            ->live()
                                            ->native(false),

                                        TextInput::make('keb_dis_lainnya')
                                            ->label('Kebutuhan Disabilitas Lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('keb_dis_id') != 8),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('tdk_hp_id')
                                            ->label('Apakah memiliki nomor handphone?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('nomor_handphone')
                                            ->label('No. Handphone')
                                            ->helperText('Contoh: 82187782223')
                                            // ->mask('82187782223')
                                            ->prefix('+62')
                                            ->tel()
                                            //->default('82187782223')
                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                            ->required()
                                            ->hidden(fn(Get $get) =>
                                            $get('tdk_hp_id') != 1),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('email')
                                            ->label('Email')
                                            //->default('mail@mail.com')
                                            ->email(),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_mendaftar_keinginan_id')
                                            ->label('Mendaftar atas kenginginan')
                                            ->inline()
                                            ->options(MendaftarKeinginan::whereIsActive(1)->pluck('mendaftar_keinginan', 'id'))
                                            ->live(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('ps_mendaftar_keinginan_lainnya')
                                            ->label('Lainnya')
                                            ->required()
                                            //->default('asdasf')
                                            ->hidden(fn(Get $get) =>
                                            $get('ps_mendaftar_keinginan_id') != 4),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Hidden::make('aktivitaspend_id')
                                    ->default(9),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('bya_sklh_id')
                                            ->label('Yang membiayai sekolah')
                                            ->inline()
                                            ->options(MembiayaiSekolah::whereIsActive(1)->pluck('membiayai_sekolah', 'id'))
                                            ->live(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('bya_sklh_lainnya')
                                            ->label('Yang membiayai sekolah lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('bya_sklh_id') != 4),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('belum_nisn_id')
                                            ->label('Apakah memiliki NISN?')
                                            ->helperText(new HtmlString('<strong>NISN</strong> adalah Nomor Induk Siswa Nasional'))
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                        TextInput::make('nisn')
                                            ->label('Nomor NISN')
                                            ->required()
                                            //->default('2421324')
                                            ->hidden(fn(Get $get) =>
                                            $get('belum_nisn_id') != 1),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('nomor_kip_memiliki_id')
                                            ->label('Apakah memiliki KIP?')
                                            ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                        TextInput::make('nomor_kip')
                                            ->label('Nomor KIP')
                                            ->required()
                                            //->default('32524324')
                                            ->hidden(fn(Get $get) =>
                                            $get('nomor_kip_memiliki_id') != 1),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(2)
                                    ->schema([

                                        Textarea::make('ps_peng_pend_agama')
                                            ->label('Pengalaman pendidikan agama')
                                            ->required(),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        Textarea::make('ps_peng_pend_formal')
                                            ->label('Pengalaman pendidikan formal')
                                            ->required(),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('hafalan_id')
                                            ->label('Hafalan')
                                            ->placeholder('Jumlah Hafalan dalam Hitungan Juz')
                                            ->options(Hafalan::whereIsActive(1)->pluck('hafalan', 'id'))
                                            ->required()
                                            ->suffix('juz')
                                            ->hidden(fn(Get $get) =>
                                            $get('qism_id') == 1)
                                            ->native(false),

                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                // ALAMAT SANTRI
                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                <p class="text-lg">TEMPAT TINGGAL DOMISILI</p>
                                                <p class="text-lg">SANTRI</p>
                                            </div>')),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('al_s_status_mukim_id')
                                            ->label('Apakah mukim di Pondok?')
                                            ->helperText(new HtmlString('Pilih <strong>Tidak Mukim</strong> khusus bagi pendaftar <strong>Tarbiyatul Aulaad</strong> dan <strong>Pra Tahfidz kelas 1-4</strong>'))
                                            ->live()
                                            ->inline()
                                            ->required()
                                            ->default(function (Get $get) {

                                                $qism = $get('qism_id');

                                                $kelas = $get('kelas_id');

                                                if ($qism == 1) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 1) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 1) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 2) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 3) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 4) {

                                                    return 2;
                                                } else {
                                                    return 1;
                                                }
                                            })
                                            ->options(function (Get $get) {

                                                $qism = $get('qism_id');

                                                $kelas = $get('kelas_id');

                                                if ($qism == 1) {

                                                    return ([
                                                        2 => 'Tidak Mukim'
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 1) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 1) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 2) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 3) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 4) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } else {
                                                    return ([

                                                        1 => 'Mukim',
                                                    ]);
                                                }
                                            })
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                if ($get('al_s_status_mukim_id') == 1) {

                                                    $set('al_s_stts_tptgl_id', 10);
                                                } elseif ($get('al_s_status_mukim_id') == 2) {

                                                    $set('al_s_stts_tptgl_id', null);
                                                }
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('al_s_stts_tptgl_id')
                                            ->label('Status tempat tinggal')
                                            ->placeholder('Status tempat tinggal')
                                            ->options(function (Get $get) {
                                                if ($get('al_s_status_mukim_id') == 2) {
                                                    return (StatusTempatTinggal::whereIsActive(1)->pluck('status_tempat_tinggal', 'id'));
                                                }
                                            })
                                            // ->searchable()
                                            ->required()
                                            //->default('Kontrak/Kost')
                                            ->hidden(fn(Get $get) =>
                                            $get('al_s_status_mukim_id') == 1)
                                            ->live()
                                            ->native(false)
                                            ->dehydrated(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('al_s_provinsi_id')
                                            ->label('Provinsi')
                                            ->placeholder('Pilih Provinsi')
                                            ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                            // ->searchable()
                                            //->default('35')
                                            ->required()
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            )
                                            ->afterStateUpdated(function (Set $set) {
                                                $set('al_s_kabupaten_id', null);
                                                $set('al_s_kecamatan_id', null);
                                                $set('al_s_kelurahan_id', null);
                                                $set('al_s_kodepos', null);
                                            }),

                                        Select::make('al_s_kabupaten_id')
                                            ->label('Kabupaten')
                                            ->placeholder('Pilih Kabupaten')
                                            ->options(fn(Get $get): Collection => Kabupaten::query()
                                                ->where('provinsi_id', $get('al_s_provinsi_id'))
                                                ->pluck('kabupaten', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('232')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('al_s_kecamatan_id')
                                            ->label('Kecamatan')
                                            ->placeholder('Pilih Kecamatan')
                                            ->options(fn(Get $get): Collection => Kecamatan::query()
                                                ->where('kabupaten_id', $get('al_s_kabupaten_id'))
                                                ->pluck('kecamatan', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('3617')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                        Select::make('al_s_kelurahan_id')
                                            ->label('Kelurahan')
                                            ->placeholder('Pilih Kelurahan')
                                            ->options(fn(Get $get): Collection => Kelurahan::query()
                                                ->where('kecamatan_id', $get('al_s_kecamatan_id'))
                                                ->pluck('kelurahan', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('45322')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            )
                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                $state = $kodepos;

                                                foreach ($state as $state) {
                                                    $set('al_s_kodepos', Str::substr($state, 12, 5));
                                                }
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('al_s_kodepos')
                                            ->label('Kodepos')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated()
                                            //->default('63264')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(4)
                                    ->schema([


                                        TextInput::make('al_s_rt')
                                            ->label('RT')
                                            ->helperText('Isi 0 jika tidak ada RT/RW')
                                            ->required()
                                            ->numeric()
                                            ->disabled(fn(Get $get) =>
                                            $get('al_s_kodepos') == null)
                                            //->default('2')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                        TextInput::make('al_s_rw')
                                            ->label('RW')
                                            ->helperText('Isi 0 jika tidak ada RT/RW')
                                            ->required()
                                            ->numeric()
                                            ->disabled(fn(Get $get) =>
                                            $get('al_s_kodepos') == null)
                                            //->default('2')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        Textarea::make('al_s_alamat')
                                            ->label('Alamat')
                                            ->required()
                                            ->disabled(fn(Get $get) =>
                                            $get('al_s_kodepos') == null)
                                            //->default('sdfsdasdada')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('al_s_jarak_id')
                                            ->label('Jarak tempat tinggal ke Pondok Pesantren')
                                            ->options(Jarakpp::whereIsActive(1)->pluck('jarak_kepp', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Kurang dari 5 km')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                        Select::make('al_s_transportasi_id')
                                            ->label('Transportasi ke Pondok Pesantren')
                                            ->options(Transpp::whereIsActive(1)->pluck('transportasi_kepp', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Ojek')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('al_s_waktu_tempuh_id')
                                            ->label('Waktu tempuh ke Pondok Pesantren')
                                            ->options(Waktutempuh::whereIsActive(1)->pluck('waktu_tempuh', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('10 - 19 menit')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                        TextInput::make('al_s_koordinat')
                                            ->label('Titik koordinat tempat tinggal')
                                            //->default('sfasdadasdads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),
                                    ]),
                            ]),

                        // end of step 2

                        Step::make('3. KUESIONER KEGIATAN HARIAN')
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">KUESIONER KEGIATAN HARIAN</p>
                                                </div>')),
                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_keberadaan_id')
                                            ->label('1. Di mana saat ini ananda berada?')
                                            ->live()
                                            ->inline()
                                            ->options(AnandaBerada::whereIsActive(1)->pluck('ananda_berada', 'id')),
                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_keberadaan_nama_mhd')
                                            ->label('Nama Mahad')
                                            ->required()
                                            //->default('sadads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_keberadaan_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_keberadaan_lokasi_mhd')
                                            ->label('Lokasi Mahad')
                                            ->required()
                                            //->default('sadads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_keberadaan_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_keberadaan_rumah_keg')
                                            ->label('Jika dirumah, apa kegiatan ananda selama waktu tersebut?')
                                            //->default('asfsadsa')
                                            ->required()
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_keberadaan_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_fasilitas_gawai_id')
                                            ->label('2. Apakah selama di rumah (baik bagi yg dirumah, atau bagi yang di Mahad ketika liburan), ananda difasilitasi HP atau laptop (baik dengan memiliki sendiri HP/ laptop dan yang sejenis atau dipinjami orang tua)?')
                                            // ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_fasilitas_gawai_medsos')
                                            ->label('Apakah ananda memiliki akun medsos (media sosial)?')
                                            ->required()
                                            //->default('Ya')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_fasilitas_gawai_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_fasilitas_gawai_medsos_daftar')
                                            ->label('Akun medsos apa saja yang ananda miliki?')
                                            ->required()
                                            //->default('asfdads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_fasilitas_gawai_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_fasilitas_gawai_medsos_aktif')
                                            ->label('Apakah akun tersebut masih aktif hingga sekarang?')
                                            ->required()
                                            //->default('asdafs')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_fasilitas_gawai_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_fasilitas_gawai_medsos_menutup_id')
                                            ->label('Apakah bersedia menutup akun tersebut selama menjadi santri/santriwati?')
                                            // ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_fasilitas_gawai_id') != 1
                                            ),
                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_medsos_sering_id')
                                            ->label('3. Dari medsos berikut, manakah yang sering digunakan ananda?')
                                            // ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            // ->live()
                                            ->multiple()
                                            ->options(MedsosAnanda::whereIsActive(1)->pluck('medsos_ananda', 'id'))
                                            ->required(),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_medsos_group_id')
                                            ->label('4. Apakah ananda tergabung dalam grup yang ada pada medsos tersebut?')
                                            // ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_medsos_group_nama')
                                            ->label('Mohon dijelaskan nama grup dan bidang kegiatannya')
                                            ->required()
                                            //->default('asdadasdads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_medsos_group_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_bacaan')
                                            ->label('5. Apa saja buku bacaan yang disukai atau sering dibaca ananda?')
                                            ->helperText('Mohon dijelaskan jenis bacaannya')
                                            //->default('asdads')
                                            ->required(),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_bacaan_cara_dapat')
                                            ->label('Bagaimana cara mendapatkan bacaan tersebut? (Via online atau membeli sendiri)')
                                            //->default('assad')
                                            ->required(),
                                    ]),


                            ]),
                        // end of step 3

                        Step::make('4. KUESIONER KESEHATAN')
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
                        // end of step 4

                        Step::make('5. KUESIONER KEMANDIRIAN')
                            ->hidden(function (Get $get) {
                                $qism = $get('qism_id');
                                $kelas = $get('kelas_id');

                                if ($qism == 1) {
                                    return false;
                                } elseif ($qism == 2 && $kelas == 1) {
                                    return false;
                                } elseif ($qism == 2 && $kelas == 2) {
                                    return false;
                                } elseif ($qism == 2 && $kelas == 3) {
                                    return false;
                                } elseif ($qism == 2 && $kelas == 4) {
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
                        // end of step 5

                        Step::make('6. KUESIONER KEMAMPUAN PEMBAYARAN ADMINISTRASI')
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
                                    ->content(function (Get $get) {
                                        if ($get('qism_id') == 1) {
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
                                        } elseif ($get('qism_id') == 2) {
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
                                        } elseif ($get('qism_id') != 1 || $get('qism_id') != 2) {
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
                                            ->label('Status anak didik terkait dengan administrasi')
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
                    ->label('Lihat Data Calon Santri')
                    ->modalHeading('Lihat Calon Santri')
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
                    ->closeModalByEscaping(false)
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Tutup'))
                    ->form([

                        Section::make('1. DATA AWAL')
                            ->schema([
                                Hidden::make('tahap_pendaftaran_id')
                                    ->default(1),

                                Hidden::make('jenis_pendaftar_id')
                                    ->default(1),

                                Hidden::make('s_emis4')
                                    ->default(1),

                                Hidden::make('tahun_berjalan_id')
                                    ->default(
                                        function () {
                                            $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
                                            $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

                                            return $ts->id;
                                        }
                                    ),

                                Hidden::make('walisantri_id')
                                    ->default(function (Get $get, ?string $state, Set $set) {

                                        $walisantri_id = Walisantri::where('kartu_keluarga_santri', Auth::user()->username)->first();

                                        return ($walisantri_id->id);
                                    }),


                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">1. DATA AWAL</p>
                                                </div>')),

                                Group::make()
                                    ->relationship('statussantri')
                                    ->schema([
                                        Hidden::make('stat_santri_id')
                                            ->default(1),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('qism_id')
                                            ->label('Qism yang dituju')
                                            ->placeholder('Pilih Qism yang dituju')
                                            ->options(Qism::whereIsActive(1)->pluck('qism', 'id'))
                                            ->live()
                                            ->required()
                                            ->native(false)
                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set) {

                                                // $qism = Qism::where('id', $get('qism_id'))->first();

                                                $taaktif = TahunAjaranAktif::where('is_active', true)->where('qism_id', $get('qism_id'))->first();

                                                $tasel = TahunAjaran::where('id', $taaktif->tahun_ajaran_id)->first();

                                                $set('tahun_ajaran_id', $tasel->tahun_ajaran_id);
                                                $set('qism_detail_id', null);
                                                $set('kelas_id', null);
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('qism_detail_id')
                                            ->label('Putra/Putri')
                                            ->inline()
                                            ->options(function (Get $get) {

                                                return (QismDetail::where('qism_id', $get('qism_id'))->pluck('jeniskelamin', 'id'));
                                            })
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set) {

                                                $jkqism = QismDetail::where('id', $state)->first();

                                                $set('jeniskelamin_id', $jkqism->jeniskelamin_id);
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('kelas_id')
                                            ->label('Kelas yang dituju')
                                            ->placeholder('Pilih Kelas')
                                            ->native(false)
                                            ->live()
                                            ->required()
                                            ->options(function (Get $get) {

                                                return (QismDetailHasKelas::where('qism_detail_id', $get('qism_detail_id'))->pluck('kelas', 'kelas_id'));
                                            })
                                            ->disabled(fn(Get $get) =>
                                            $get('qism_detail_id') == null),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('tahun_ajaran_id')
                                            ->label('Tahun Ajaran')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            ->options(TahunAjaran::all()->pluck('ta', 'id'))
                                            ->native(false),

                                    ]),


                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                    </div>')),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('kartu_keluarga_sama_id')
                                            ->label('Kartu Keluarga sama dengan')
                                            ->required()
                                            ->inline()
                                            ->live()
                                            ->options(function (Get $get) {

                                                $walisantri_id = $get('walisantri_id');

                                                $status = Walisantri::where('id', $walisantri_id)->first();
                                                // dd($status->ak_no_kk !== null);

                                                if ($status->ak_status_id == 1 && $status->ik_status_id == 1 && $status->w_status_id == 3) {

                                                    return ([
                                                        1 => 'KK sama dengan Ayah Kandung',
                                                        2 => 'KK sama dengan Ibu Kandung',
                                                        3 => 'KK sama dengan Wali',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id == 1 && $status->ik_status_id == 1 && $status->w_status_id != 3) {

                                                    return ([
                                                        1 => 'KK sama dengan Ayah Kandung',
                                                        2 => 'KK sama dengan Ibu Kandung',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id == 1 && $status->ik_status_id != 1 && $status->w_status_id != 3) {

                                                    return ([
                                                        1 => 'KK sama dengan Ayah Kandung',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id == 1 && $status->ik_status_id != 1 && $status->w_status_id == 3) {

                                                    return ([
                                                        1 => 'KK sama dengan Ayah Kandung',
                                                        3 => 'KK sama dengan Wali',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id != 1 && $status->ik_status_id == 1 && $status->w_status_id == 3) {

                                                    return ([
                                                        2 => 'KK sama dengan Ibu Kandung',
                                                        3 => 'KK sama dengan Wali',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id != 1 && $status->ik_status_id != 1 && $status->w_status_id == 3) {

                                                    return ([
                                                        3 => 'KK sama dengan Wali',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                } elseif ($status->ak_status_id != 1 && $status->ik_status_id == 1 && $status->w_status_id != 3) {

                                                    return ([
                                                        2 => 'KK sama dengan Ibu Kandung',
                                                        4 => 'KK sendiri',
                                                    ]);
                                                }
                                            })
                                            ->afterStateUpdated(function (Get $get, Set $set) {

                                                $walisantri_id = $get('walisantri_id');

                                                $walisantri = Walisantri::where('id', $walisantri_id)->first();

                                                if ($get('kartu_keluarga_sama_id') == 1) {

                                                    $set('kartu_keluarga', $walisantri->ak_no_kk);
                                                    $set('nama_kpl_kel', $walisantri->ak_kep_kel_kk);
                                                } elseif ($get('kartu_keluarga_sama_id') == 2) {

                                                    $set('kartu_keluarga', $walisantri->ik_no_kk);
                                                    $set('nama_kpl_kel', $walisantri->ik_kep_kel_kk);
                                                } elseif ($get('kartu_keluarga_sama_id') == 3) {

                                                    $set('kartu_keluarga', $walisantri->w_no_kk);
                                                    $set('nama_kpl_kel', $walisantri->w_kep_kel_kk);
                                                } elseif ($get('kartu_keluarga_sama_id') == 4) {

                                                    $set('kartu_keluarga', null);
                                                    $set('nama_kpl_kel', null);
                                                }
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('kartu_keluarga')
                                            ->label('Nomor KK Calon Santri')
                                            ->length(16)
                                            ->required()
                                            // ->disabled(fn (Get $get) =>
                                            // $get('kartu_keluarga_sama') !== 'KK Sendiri')
                                            ->dehydrated(),

                                        TextInput::make('nama_kpl_kel')
                                            ->label('Nama Kepala Keluarga')
                                            ->required()
                                            // ->disabled(fn (Get $get) =>
                                            // $get('kartu_keluarga_sama') !== 'KK Sendiri')
                                            ->dehydrated(),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('kewarganegaraan_id')
                                            ->label('Kewarganegaraan')
                                            ->inline()
                                            ->options(Kewarganegaraan::whereIsActive(1)->pluck('kewarganegaraan', 'id'))
                                            ->default(1)
                                            ->live(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        ComponentsTextInput::make('nik')
                                            ->label('NIK')
                                            ->hint('Isi sesuai dengan KK')
                                            ->hintColor('danger')
                                            ->regex('/^[0-9]*$/')
                                            ->length(16)
                                            ->maxLength(16)
                                            ->required()
                                            ->unique(Santri::class, 'nik', ignoreRecord: true)
                                            //->default('3295131306822002')
                                            ->hidden(fn(Get $get) =>
                                            $get('kewarganegaraan_id') != 1),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('asal_negara')
                                            ->label('Asal Negara Calon Santri')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('kewarganegaraan_id') != 2),

                                        TextInput::make('kitas')
                                            ->label('KITAS Calon Santri')
                                            ->hint('Nomor Izin Tinggal (KITAS)')
                                            ->hintColor('danger')
                                            ->required()
                                            //->default('3295131306822002')
                                            ->unique(Santri::class, 'kitas')
                                            ->hidden(fn(Get $get) =>
                                            $get('kewarganegaraan_id') != 2),

                                    ]),

                            ]),
                        // end of Section 1

                        Section::make('2. DATA SANTRI')
                            ->schema([
                                //SANTRI
                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                <p class="text-2xl">SANTRI</p>
                                            </div>')),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('nama_lengkap')
                                            ->label('Nama Lengkap')
                                            ->hint('Isi sesuai dengan KK')
                                            ->hintColor('danger')
                                            //->default('asfasdad')
                                            ->required(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('nama_panggilan')
                                            ->label('Nama Hijroh/Islami/Panggilan')
                                            //->default('asfasdad')
                                            ->required(),

                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                            </div>')),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('jeniskelamin_id')
                                            ->label('Jenis Kelamin')
                                            ->inline()
                                            ->options(Jeniskelamin::whereIsActive(1)->pluck('jeniskelamin', 'id'))
                                            ->required()
                                            ->disabled()
                                            ->dehydrated(),

                                    ]),

                                Grid::make(6)
                                    ->schema([

                                        TextInput::make('tempat_lahir')
                                            ->label('Tempat Lahir')
                                            ->hint('Isi sesuai dengan KK')
                                            ->hintColor('danger')
                                            //->default('asfasdad')
                                            ->required(),

                                        DatePicker::make('tanggal_lahir')
                                            ->label('Tanggal Lahir')
                                            ->hint('Isi sesuai dengan KK')
                                            ->hintColor('danger')
                                            //->default('20010101')
                                            ->required()
                                            ->displayFormat('d M Y')
                                            ->native(false)
                                            ->maxDate(now())
                                            ->live(onBlur: true)
                                            ->closeOnDateSelection()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $set('umur', Carbon::parse($state)->age);
                                            }),

                                        TextInput::make('umur')
                                            ->label('Umur')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required(),

                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('anak_ke')
                                            ->label('Anak ke-')
                                            ->required()
                                            ->numeric()
                                            ->disabled(fn(Get $get) =>
                                            $get('umur') == null)
                                            //->default('3')
                                            ->rules([
                                                fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                                                    $anakke = $get('anak_ke');
                                                    $psjumlahsaudara = $get('jumlah_saudara');
                                                    $jumlahsaudara = $psjumlahsaudara + 1;

                                                    if ($anakke > $jumlahsaudara) {
                                                        $fail("Anak ke tidak bisa lebih dari jumlah saudara + 1");
                                                    }
                                                },
                                            ]),

                                        TextInput::make('jumlah_saudara')
                                            ->label('Jumlah saudara')
                                            ->numeric()
                                            ->disabled(fn(Get $get) =>
                                            $get('umur') == null)
                                            //->default('5')
                                            ->required(),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('agama')
                                            ->label('Agama')
                                            ->default('Islam')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated(),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('cita_cita_id')
                                            ->label('Cita-cita')
                                            ->placeholder('Pilih Cita-cita')
                                            ->options(Cita::whereIsActive(1)->pluck('cita', 'id'))
                                            // ->searchable()
                                            ->required()
                                            ->live()
                                            ->native(false),

                                        TextInput::make('cita_cita_lainnya')
                                            ->label('Cita-cita Lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('cita_cita_id') != 10),
                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('hobi_id')
                                            ->label('Hobi')
                                            ->placeholder('Pilih Hobi')
                                            ->options(Hobi::whereIsActive(1)->pluck('hobi', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Lainnya')
                                            ->live()
                                            ->native(false),

                                        TextInput::make('hobi_lainnya')
                                            ->label('Hobi Lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('hobi_id') != 6),

                                    ]),


                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('keb_khus_id')
                                            ->label('Kebutuhan Khusus')
                                            ->placeholder('Pilih Kebutuhan Khusus')
                                            ->options(KebutuhanKhusus::whereIsActive(1)->pluck('kebutuhan_khusus', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Lainnya')
                                            ->live()
                                            ->native(false),

                                        TextInput::make('keb_khus_lainnya')
                                            ->label('Kebutuhan Khusus Lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('keb_khus_id') != 6),
                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('keb_dis_id')
                                            ->label('Kebutuhan Disabilitas')
                                            ->placeholder('Pilih Kebutuhan Disabilitas')
                                            ->options(KebutuhanDisabilitas::whereIsActive(1)->pluck('kebutuhan_disabilitas', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Lainnya')
                                            ->live()
                                            ->native(false),

                                        TextInput::make('keb_dis_lainnya')
                                            ->label('Kebutuhan Disabilitas Lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('keb_dis_id') != 8),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('tdk_hp_id')
                                            ->label('Apakah memiliki nomor handphone?')
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('nomor_handphone')
                                            ->label('No. Handphone')
                                            ->helperText('Contoh: 82187782223')
                                            // ->mask('82187782223')
                                            ->prefix('+62')
                                            ->tel()
                                            //->default('82187782223')
                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                            ->required()
                                            ->hidden(fn(Get $get) =>
                                            $get('tdk_hp_id') != 1),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('email')
                                            ->label('Email')
                                            //->default('mail@mail.com')
                                            ->email(),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_mendaftar_keinginan_id')
                                            ->label('Mendaftar atas kenginginan')
                                            ->inline()
                                            ->options(MendaftarKeinginan::whereIsActive(1)->pluck('mendaftar_keinginan', 'id'))
                                            ->live(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('ps_mendaftar_keinginan_lainnya')
                                            ->label('Lainnya')
                                            ->required()
                                            //->default('asdasf')
                                            ->hidden(fn(Get $get) =>
                                            $get('ps_mendaftar_keinginan_id') != 4),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Hidden::make('aktivitaspend_id')
                                    ->default(9),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('bya_sklh_id')
                                            ->label('Yang membiayai sekolah')
                                            ->inline()
                                            ->options(MembiayaiSekolah::whereIsActive(1)->pluck('membiayai_sekolah', 'id'))
                                            ->live(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('bya_sklh_lainnya')
                                            ->label('Yang membiayai sekolah lainnya')
                                            ->required()
                                            //->default('asfasdad')
                                            ->hidden(fn(Get $get) =>
                                            $get('bya_sklh_id') != 4),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('belum_nisn_id')
                                            ->label('Apakah memiliki NISN?')
                                            ->helperText(new HtmlString('<strong>NISN</strong> adalah Nomor Induk Siswa Nasional'))
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                        TextInput::make('nisn')
                                            ->label('Nomor NISN')
                                            ->required()
                                            //->default('2421324')
                                            ->hidden(fn(Get $get) =>
                                            $get('belum_nisn_id') != 1),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        ToggleButtons::make('nomor_kip_memiliki_id')
                                            ->label('Apakah memiliki KIP?')
                                            ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                        TextInput::make('nomor_kip')
                                            ->label('Nomor KIP')
                                            ->required()
                                            //->default('32524324')
                                            ->hidden(fn(Get $get) =>
                                            $get('nomor_kip_memiliki_id') != 1),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                Grid::make(2)
                                    ->schema([

                                        Textarea::make('ps_peng_pend_agama')
                                            ->label('Pengalaman pendidikan agama')
                                            ->required(),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        Textarea::make('ps_peng_pend_formal')
                                            ->label('Pengalaman pendidikan formal')
                                            ->required(),
                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('hafalan_id')
                                            ->label('Hafalan')
                                            ->placeholder('Jumlah Hafalan dalam Hitungan Juz')
                                            ->options(Hafalan::whereIsActive(1)->pluck('hafalan', 'id'))
                                            ->required()
                                            ->suffix('juz')
                                            ->hidden(fn(Get $get) =>
                                            $get('qism_id') == 1)
                                            ->native(false),

                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                // ALAMAT SANTRI
                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                <p class="text-lg">TEMPAT TINGGAL DOMISILI</p>
                                                <p class="text-lg">SANTRI</p>
                                            </div>')),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('al_s_status_mukim_id')
                                            ->label('Apakah mukim di Pondok?')
                                            ->helperText(new HtmlString('Pilih <strong>Tidak Mukim</strong> khusus bagi pendaftar <strong>Tarbiyatul Aulaad</strong> dan <strong>Pra Tahfidz kelas 1-4</strong>'))
                                            ->live()
                                            ->inline()
                                            ->required()
                                            ->default(function (Get $get) {

                                                $qism = $get('qism_id');

                                                $kelas = $get('kelas_id');

                                                if ($qism == 1) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 1) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 1) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 2) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 3) {

                                                    return 2;
                                                } elseif ($qism == 2 && $kelas == 4) {

                                                    return 2;
                                                } else {
                                                    return 1;
                                                }
                                            })
                                            ->options(function (Get $get) {

                                                $qism = $get('qism_id');

                                                $kelas = $get('kelas_id');

                                                if ($qism == 1) {

                                                    return ([
                                                        2 => 'Tidak Mukim'
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 1) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 1) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 2) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 3) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } elseif ($qism == 2 && $kelas == 4) {

                                                    return ([
                                                        2 => 'Tidak Mukim',
                                                    ]);
                                                } else {
                                                    return ([

                                                        1 => 'Mukim',
                                                    ]);
                                                }
                                            })
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                if ($get('al_s_status_mukim_id') == 1) {

                                                    $set('al_s_stts_tptgl_id', 10);
                                                } elseif ($get('al_s_status_mukim_id') == 2) {

                                                    $set('al_s_stts_tptgl_id', null);
                                                }
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('al_s_stts_tptgl_id')
                                            ->label('Status tempat tinggal')
                                            ->placeholder('Status tempat tinggal')
                                            ->options(function (Get $get) {
                                                if ($get('al_s_status_mukim_id') == 2) {
                                                    return (StatusTempatTinggal::whereIsActive(1)->pluck('status_tempat_tinggal', 'id'));
                                                }
                                            })
                                            // ->searchable()
                                            ->required()
                                            //->default('Kontrak/Kost')
                                            ->hidden(fn(Get $get) =>
                                            $get('al_s_status_mukim_id') == 1)
                                            ->live()
                                            ->native(false)
                                            ->dehydrated(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('al_s_provinsi_id')
                                            ->label('Provinsi')
                                            ->placeholder('Pilih Provinsi')
                                            ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                            // ->searchable()
                                            //->default('35')
                                            ->required()
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            )
                                            ->afterStateUpdated(function (Set $set) {
                                                $set('al_s_kabupaten_id', null);
                                                $set('al_s_kecamatan_id', null);
                                                $set('al_s_kelurahan_id', null);
                                                $set('al_s_kodepos', null);
                                            }),

                                        Select::make('al_s_kabupaten_id')
                                            ->label('Kabupaten')
                                            ->placeholder('Pilih Kabupaten')
                                            ->options(fn(Get $get): Collection => Kabupaten::query()
                                                ->where('provinsi_id', $get('al_s_provinsi_id'))
                                                ->pluck('kabupaten', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('232')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('al_s_kecamatan_id')
                                            ->label('Kecamatan')
                                            ->placeholder('Pilih Kecamatan')
                                            ->options(fn(Get $get): Collection => Kecamatan::query()
                                                ->where('kabupaten_id', $get('al_s_kabupaten_id'))
                                                ->pluck('kecamatan', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('3617')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                        Select::make('al_s_kelurahan_id')
                                            ->label('Kelurahan')
                                            ->placeholder('Pilih Kelurahan')
                                            ->options(fn(Get $get): Collection => Kelurahan::query()
                                                ->where('kecamatan_id', $get('al_s_kecamatan_id'))
                                                ->pluck('kelurahan', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('45322')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            )
                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                $state = $kodepos;

                                                foreach ($state as $state) {
                                                    $set('al_s_kodepos', Str::substr($state, 12, 5));
                                                }
                                            }),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('al_s_kodepos')
                                            ->label('Kodepos')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated()
                                            //->default('63264')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(4)
                                    ->schema([


                                        TextInput::make('al_s_rt')
                                            ->label('RT')
                                            ->helperText('Isi 0 jika tidak ada RT/RW')
                                            ->required()
                                            ->numeric()
                                            ->disabled(fn(Get $get) =>
                                            $get('al_s_kodepos') == null)
                                            //->default('2')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                        TextInput::make('al_s_rw')
                                            ->label('RW')
                                            ->helperText('Isi 0 jika tidak ada RT/RW')
                                            ->required()
                                            ->numeric()
                                            ->disabled(fn(Get $get) =>
                                            $get('al_s_kodepos') == null)
                                            //->default('2')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        Textarea::make('al_s_alamat')
                                            ->label('Alamat')
                                            ->required()
                                            ->disabled(fn(Get $get) =>
                                            $get('al_s_kodepos') == null)
                                            //->default('sdfsdasdada')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == 1 ||
                                                    $get('al_s_stts_tptgl_id') == 2 ||
                                                    $get('al_s_stts_tptgl_id') == 3 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('al_s_jarak_id')
                                            ->label('Jarak tempat tinggal ke Pondok Pesantren')
                                            ->options(Jarakpp::whereIsActive(1)->pluck('jarak_kepp', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Kurang dari 5 km')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                        Select::make('al_s_transportasi_id')
                                            ->label('Transportasi ke Pondok Pesantren')
                                            ->options(Transpp::whereIsActive(1)->pluck('transportasi_kepp', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('Ojek')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('al_s_waktu_tempuh_id')
                                            ->label('Waktu tempuh ke Pondok Pesantren')
                                            ->options(Waktutempuh::whereIsActive(1)->pluck('waktu_tempuh', 'id'))
                                            // ->searchable()
                                            ->required()
                                            //->default('10 - 19 menit')
                                            ->live()
                                            ->native(false)
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),

                                        TextInput::make('al_s_koordinat')
                                            ->label('Titik koordinat tempat tinggal')
                                            //->default('sfasdadasdads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('al_s_status_mukim_id') != 2 ||
                                                    $get('al_s_stts_tptgl_id') == null
                                            ),
                                    ]),
                            ]),

                        // end of Section 2

                        Section::make('3. KUESIONER KEGIATAN HARIAN')
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">KUESIONER KEGIATAN HARIAN</p>
                                                </div>')),
                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_keberadaan_id')
                                            ->label('1. Di mana saat ini ananda berada?')
                                            ->live()
                                            ->inline()
                                            ->options(AnandaBerada::whereIsActive(1)->pluck('ananda_berada', 'id')),
                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_keberadaan_nama_mhd')
                                            ->label('Nama Mahad')
                                            ->required()
                                            //->default('sadads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_keberadaan_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_keberadaan_lokasi_mhd')
                                            ->label('Lokasi Mahad')
                                            ->required()
                                            //->default('sadads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_keberadaan_id') != 2
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_keberadaan_rumah_keg')
                                            ->label('Jika dirumah, apa kegiatan ananda selama waktu tersebut?')
                                            //->default('asfsadsa')
                                            ->required()
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_keberadaan_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_fasilitas_gawai_id')
                                            ->label('2. Apakah selama di rumah (baik bagi yg dirumah, atau bagi yang di Mahad ketika liburan), ananda difasilitasi HP atau laptop (baik dengan memiliki sendiri HP/ laptop dan yang sejenis atau dipinjami orang tua)?')
                                            // ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_fasilitas_gawai_medsos')
                                            ->label('Apakah ananda memiliki akun medsos (media sosial)?')
                                            ->required()
                                            //->default('Ya')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_fasilitas_gawai_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_fasilitas_gawai_medsos_daftar')
                                            ->label('Akun medsos apa saja yang ananda miliki?')
                                            ->required()
                                            //->default('asfdads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_fasilitas_gawai_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_fasilitas_gawai_medsos_aktif')
                                            ->label('Apakah akun tersebut masih aktif hingga sekarang?')
                                            ->required()
                                            //->default('asdafs')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_fasilitas_gawai_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_fasilitas_gawai_medsos_menutup_id')
                                            ->label('Apakah bersedia menutup akun tersebut selama menjadi santri/santriwati?')
                                            // ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            ->inline()
                                            ->options(BersediaTidak::whereIsActive(1)->pluck('bersedia_tidak', 'id'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_fasilitas_gawai_id') != 1
                                            ),
                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_medsos_sering_id')
                                            ->label('3. Dari medsos berikut, manakah yang sering digunakan ananda?')
                                            // ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            // ->live()
                                            ->multiple()
                                            ->options(MedsosAnanda::whereIsActive(1)->pluck('medsos_ananda', 'id'))
                                            ->required(),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        ToggleButtons::make('ps_kkh_medsos_group_id')
                                            ->label('4. Apakah ananda tergabung dalam grup yang ada pada medsos tersebut?')
                                            // ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                            ->live()
                                            ->inline()
                                            ->grouped()
                                            ->boolean()
                                            ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id')),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_medsos_group_nama')
                                            ->label('Mohon dijelaskan nama grup dan bidang kegiatannya')
                                            ->required()
                                            //->default('asdadasdads')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkh_medsos_group_id') != 1
                                            ),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_bacaan')
                                            ->label('5. Apa saja buku bacaan yang disukai atau sering dibaca ananda?')
                                            ->helperText('Mohon dijelaskan jenis bacaannya')
                                            //->default('asdads')
                                            ->required(),

                                    ]),

                                Grid::make(2)
                                    ->schema([

                                        TextArea::make('ps_kkh_bacaan_cara_dapat')
                                            ->label('Bagaimana cara mendapatkan bacaan tersebut? (Via online atau membeli sendiri)')
                                            //->default('assad')
                                            ->required(),
                                    ]),


                            ]),
                        // end of Section 3

                        Section::make('4. KUESIONER KESEHATAN')
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
                        // end of Section 4

                        Section::make('5. KUESIONER KEMANDIRIAN')
                            ->hidden(function (Get $get) {
                                $qism = $get('qism_id');
                                $kelas = $get('kelas_id');

                                if ($qism == 1) {
                                    return false;
                                } elseif ($qism == 2 && $kelas == 1) {
                                    return false;
                                } elseif ($qism == 2 && $kelas == 2) {
                                    return false;
                                } elseif ($qism == 2 && $kelas == 3) {
                                    return false;
                                } elseif ($qism == 2 && $kelas == 4) {
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
                        // end of Section 5

                        Section::make('6. KUESIONER KEMAMPUAN PEMBAYARAN ADMINISTRASI')
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
                                    ->content(function (Get $get) {
                                        if ($get('qism_id') == 1) {
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
                                        } elseif ($get('qism_id') == 2) {
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
                                        } elseif ($get('qism_id') != 1 || $get('qism_id') != 2) {
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
                                            ->label('Status anak didik terkait dengan administrasi')
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
