<?php

namespace App\Filament\Resources\PendaftaranResource\Widgets;

use App\Filament\Resources\PendaftaranResource;
use App\Filament\Resources\PendaftaranResource\RelationManagers\SantrisRelationManager;
use App\Filament\Walisantri\Resources\WalisantriResource;
use App\Models\HubunganWali;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kewarganegaraan;
use App\Models\Kodepos;
use App\Models\PekerjaanUtamaWalisantri;
use App\Models\PendidikanTerakhirWalisantri;
use App\Models\PenghasilanWalisantri;
use App\Models\Provinsi;
use App\Models\Statuskepemilikanrumah;
use App\Models\StatusWalisantri;
use App\Models\Walisantri;
use App\Models\YaTidak;
use Egulias\EmailValidator\Parser\Comment;
use Filament\Actions\StaticAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Schmeits\FilamentCharacterCounter\Forms\Components\TextInput as ComponentsTextInput;

class PendaftaranSantriBaruTahapPertama extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->heading('1. Lengkapi Data Walisantri')
            ->paginated(false)
            ->query(
                Walisantri::where('user_id', Auth::user()->id)
            )
            ->columns([
                Split::make([
                    TextColumn::make('nama_kpl_kel_santri')
                        ->description(fn($record): string => "Nama Walisantri:", position: 'above'),

                    TextColumn::make('is_collapse')
                        ->label('Status Data Walisantri')
                        ->default('Belum Lengkap')
                        ->size(TextColumn\TextColumnSize::Large)
                        ->weight(FontWeight::Bold)
                        ->description(fn($record): string => "Status Data Walisantri:", position: 'above')
                        ->formatStateUsing(function (Model $record) {
                            $iscollapse = Walisantri::where('id', $record->id)->first();
                            // dd($pendaftar->ps_kadm_status);
                            if ($iscollapse->is_collapse == false) {
                                return ('Belum lengkap');
                            } elseif ($iscollapse->is_collapse == true) {
                                return ('Lengkap');
                            }
                        })
                        ->badge()
                        ->color(function (Model $record) {
                            $iscollapse = Walisantri::where('id', $record->id)->first();
                            // dd($pendaftar->ps_kadm_status);
                            if ($iscollapse->is_collapse == false) {
                                return ('danger');
                            } elseif ($iscollapse->is_collapse == true) {
                                return ('success');
                            }
                        }),
                ])
            ])
            ->actions(
                [
                    Tables\Actions\EditAction::make()
                        ->label('Edit Data Walisantri')
                        ->modalHeading(' ')
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
                                ->title('Data Walisantri telah tersimpan')
                                ->body('Lanjutkan dengan menambah data calon santri')
                                ->persistent()
                                ->color('Success')
                                ->send();

                            // dd($record);

                            $walisantri = Walisantri::find($record->id);
                            $walisantri->is_collapse = 1;
                            $walisantri->ws_emis4 = 1;
                            $walisantri->save();
                        })
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

                        ->form([

                            Section::make('Informasi Pendaftar')
                                ->schema([
                                    Grid::make(4)
                                        ->schema([
                                            TextInput::make('kartu_keluarga_santri')
                                                ->label('Nomor Kartu Keluarga')
                                                ->disabled(),

                                            TextInput::make('nama_kpl_kel_santri')
                                                ->label('Nama Kepala Keluarga')
                                                ->required(),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            TextInput::make('hp_komunikasi')
                                                ->label('No Handphone walisantri untuk komunikasi')
                                                ->helperText('Contoh: 82187782223')
                                                // ->mask('82187782223')
                                                ->prefix('+62')
                                                ->tel()
                                                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                ->required(),

                                        ]),

                                ])->compact(),

                            //AYAH KANDUNG
                            Section::make('A. AYAH KANDUNG')
                                ->schema([

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ak_nama_lengkap_sama_id')
                                                ->label('Apakah Nama sama dengan Nama Kepala Keluarga?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                // ->hidden(fn (Get $get) =>
                                                // $get('ak_status_id') != 1)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ak_nama_lengkap_sama_id') == 1) {
                                                        $set('ak_nama_lengkap', $get('nama_kpl_kel_santri'));
                                                        $set('ik_nama_lengkap_sama_id_id', 2);
                                                        $set('ik_nama_lengkap', null);
                                                        $set('w_nama_lengkap_sama_id_id', 2);
                                                        $set('w_nama_lengkap', null);
                                                    } else {
                                                        $set('ak_nama_lengkap', null);
                                                    }
                                                })->columnSpanFull(),

                                            TextInput::make('ak_nama_lengkap')
                                                ->label('Nama Lengkap')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ak_nama_lengkap_sama') == 1)
                                                ->dehydrated(),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">A.01 STATUS AYAH KANDUNG</p>
                                                </div>')),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ak_status_id')
                                                ->label('Status')
                                                // ->placeholder('Pilih Status')
                                                ->options(StatusWalisantri::whereIsActive(1)->pluck('status_walisantri', 'id'))
                                                ->required()
                                                ->inline()
                                                ->live()
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ak_status_id') == 1) {
                                                        $set('ak_kewarganegaraan_id', 1);
                                                    }
                                                }),
                                            // ->native(false),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            TextInput::make('ak_nama_kunyah')
                                                ->label('Nama Hijroh/Islami/Panggilan')
                                                ->required(),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            ToggleButtons::make('ak_kewarganegaraan_id')
                                                ->label('Kewarganegaraan')
                                                // ->placeholder('Pilih Kewarganegaraan')
                                                ->inline()
                                                ->default(1)
                                                ->options(Kewarganegaraan::whereIsActive(1)->pluck('kewarganegaraan', 'id'))
                                                ->required()
                                                ->live(),
                                            // ->native(false),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_kewarganegaraan_id') != 1 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([

                                            ComponentsTextInput::make('ak_nik')
                                                ->label('NIK')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->regex('/^[0-9]*$/')
                                                ->length(16)
                                                ->maxLength(16)
                                                ->required(),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_kewarganegaraan_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([

                                            TextInput::make('ak_asal_negara')
                                                ->label('Asal Negara')
                                                ->required(),


                                            TextInput::make('ak_kitas')
                                                ->label('KITAS')
                                                ->hint('Nomor Izin Tinggal (KITAS)')
                                                ->hintColor('danger')
                                                ->required(),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            TextInput::make('ak_tempat_lahir')
                                                ->label('Tempat Lahir')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required(),


                                            DatePicker::make('ak_tanggal_lahir')
                                                ->label('Tanggal Lahir')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->format('dd/mm/yyyy')
                                                ->displayFormat('d M Y')
                                                ->maxDate(now())
                                                ->native(false)
                                                ->closeOnDateSelection(),
                                        ]),

                                    Grid::make(6)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            Select::make('ak_pend_terakhir_id')
                                                ->label('Pendidikan Terakhir')
                                                ->placeholder('Pilih Pendidikan Terakhir')
                                                ->options(PendidikanTerakhirWalisantri::whereIsActive(1)->pluck('pendidikan_terakhir_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),

                                            Select::make('ak_pekerjaan_utama_id')
                                                ->label('Pekerjaan Utama')
                                                ->placeholder('Pilih Pekerjaan Utama')
                                                ->options(PekerjaanUtamaWalisantri::whereIsActive(1)->pluck('pekerjaan_utama_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),

                                            Select::make('ak_pghsln_rt_id')
                                                ->label('Penghasilan Rata-Rata')
                                                ->placeholder('Pilih Penghasilan Rata-Rata')
                                                ->options(PenghasilanWalisantri::whereIsActive(1)->pluck('penghasilan_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            ToggleButtons::make('ak_tdk_hp_id')
                                                ->label('Apakah memiliki nomor handphone?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ak_tdk_hp_id') == 2) {
                                                        $set('ak_nomor_handphone_sama_id', null);
                                                        $set('ak_nomor_handphone', null);
                                                    }
                                                }),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_tdk_hp_id') != 1 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([


                                            ToggleButtons::make('ak_nomor_handphone_sama_id')
                                                ->label('Apakah nomor handphone sama dengan Pendaftar?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ak_nomor_handphone_sama_id') == 1) {
                                                        $set('ak_nomor_handphone', $get('hp_komunikasi'));
                                                        $set('ik_nomor_handphone_sama_id', 2);
                                                        $set('ik_nomor_handphone', null);
                                                        $set('w_nomor_handphone_sama_id', 2);
                                                        $set('w_nomor_handphone', null);
                                                    } else {
                                                        $set('ak_nomor_handphone', null);
                                                    }
                                                })->columnSpanFull(),

                                            TextInput::make('ak_nomor_handphone')
                                                ->label('No. Handphone')
                                                ->helperText('Contoh: 82187782223')
                                                ->prefix('+62')
                                                ->tel()
                                                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ak_nomor_handphone_sama_id') == 1)
                                                ->dehydrated(),
                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                         <p class="text-lg">Kajian yang diikuti</p>
                                     </div>')),
                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            Textarea::make('ak_ustadz_kajian')
                                                ->label('Ustadz yang mengisi kajian')
                                                ->required(),

                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            TextArea::make('ak_tempat_kajian')
                                                ->label('Tempat kajian yang diikuti')
                                                ->required(),

                                        ]),

                                    // KARTU KELUARGA AYAH KANDUNG
                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                    <p class="text-lg">A.02 KARTU KELUARGA</p>
                                    <p class="text-lg">AYAH KANDUNG</p>
                                       </div>')),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            ToggleButtons::make('ak_kk_sama_pendaftar_id')
                                                ->label('Apakah KK dan Nama Kepala Keluarga sama dengan Pendaftar?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ak_kk_sama_pendaftar_id') == 1) {
                                                        $set('ak_no_kk', $get('kartu_keluarga_santri'));
                                                        $set('ak_kep_kel_kk', $get('nama_kpl_kel_santri'));
                                                        $set('ik_kk_sama_pendaftar_id', 2);
                                                        $set('ik_no_kk', null);
                                                        $set('ik_kep_kel_kk', null);
                                                        $set('w_kk_sama_pendaftar_id', 2);
                                                        $set('w_no_kk', null);
                                                        $set('w_kep_kel_kk', null);
                                                    } else {
                                                        $set('ak_no_kk', null);
                                                        $set('ak_kep_kel_kk', null);
                                                    }
                                                })->columnSpanFull(),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            ComponentsTextInput::make('ak_no_kk')
                                                ->label('No. KK Ayah Kandung')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->length(16)
                                                ->maxLength(16)
                                                ->required()
                                                ->regex('/^[0-9]*$/')
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ak_kk_sama_pendaftar_id') == 1)
                                                ->dehydrated(),

                                            TextInput::make('ak_kep_kel_kk')
                                                ->label('Nama Kepala Keluarga')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ak_kk_sama_pendaftar_id') == 1)
                                                ->dehydrated(),
                                        ]),

                                    // ALAMAT AYAH KANDUNG
                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">A.03 TEMPAT TINGGAL DOMISILI</p>
                                                    <p class="text-lg">AYAH KANDUNG</p>
                                                </div>')),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            ToggleButtons::make('al_ak_tgldi_ln_id')
                                                ->label('Apakah tinggal di luar negeri?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 1)
                                        ->schema([

                                            Textarea::make('al_ak_almt_ln')
                                                ->label('Alamat Luar Negeri')
                                                ->required(),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([

                                            Select::make('al_ak_stts_rmh_id')
                                                ->label('Status Kepemilikan Rumah')
                                                ->placeholder('Pilih Status Kepemilikan Rumah')
                                                ->options(Statuskepemilikanrumah::whereIsActive(1)->pluck('status_kepemilikan_rumah', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([

                                            Select::make('al_ak_provinsi_id')
                                                ->label('Provinsi')
                                                ->placeholder('Pilih Provinsi')
                                                ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->afterStateUpdated(function (Set $set) {
                                                    $set('al_ak_kabupaten_id', null);
                                                    $set('al_ak_kecamatan_id', null);
                                                    $set('al_ak_kelurahan_id', null);
                                                    $set('al_ak_kodepos', null);
                                                }),

                                            Select::make('al_ak_kabupaten_id')
                                                ->label('Kabupaten')
                                                ->placeholder('Pilih Kabupaten')
                                                ->options(fn(Get $get): Collection => Kabupaten::query()
                                                    ->where('provinsi_id', $get('al_ak_provinsi_id'))
                                                    ->pluck('kabupaten', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([

                                            Select::make('al_ak_kecamatan_id')
                                                ->label('Kecamatan')
                                                ->placeholder('Pilih Kecamatan')
                                                ->options(fn(Get $get): Collection => Kecamatan::query()
                                                    ->where('kabupaten_id', $get('al_ak_kabupaten_id'))
                                                    ->pluck('kecamatan', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false),

                                            Select::make('al_ak_kelurahan_id')
                                                ->label('Kelurahan')
                                                ->placeholder('Pilih Kelurahan')
                                                ->options(fn(Get $get): Collection => Kelurahan::query()
                                                    ->where('kecamatan_id', $get('al_ak_kecamatan_id'))
                                                    ->pluck('kelurahan', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                    if (($get('al_ak_kodepos') ?? '') !== Str::slug($old)) {
                                                        return;
                                                    }

                                                    $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                    $state = $kodepos;

                                                    foreach ($state as $state) {
                                                        $set('al_ak_kodepos', Str::substr($state, 12, 5));
                                                    }
                                                }),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([

                                            TextInput::make('al_ak_kodepos')
                                                ->label('Kodepos')
                                                ->disabled()
                                                ->required()
                                                ->dehydrated(),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([


                                            TextInput::make('al_ak_rt')
                                                ->label('RT')
                                                ->helperText('Isi 0 jika tidak ada RT/RW')
                                                ->required()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_ak_kodepos') == null)
                                                ->numeric(),

                                            TextInput::make('al_ak_rw')
                                                ->label('RW')
                                                ->helperText('Isi 0 jika tidak ada RT/RW')
                                                ->required()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_ak_kodepos') == null)
                                                ->numeric(),
                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([
                                            Textarea::make('al_ak_alamat')
                                                ->label('Alamat')
                                                ->disabled(fn(Get $get) =>
                                                $get('al_ak_kodepos') == null)
                                                ->required(),
                                        ]),

                                ])->compact(),


                            // //IBU KANDUNG
                            Section::make('B. IBU KANDUNG')
                                ->schema([

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ik_nama_lengkap_sama_id')
                                                ->label('Apakah Nama sama dengan Nama Kepala Keluarga?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('ak_nama_lengkap_sama_id') != 2)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ik_nama_lengkap_sama_id') == 1) {
                                                        $set('ik_nama_lengkap', $get('nama_kpl_kel_santri'));
                                                        $set('w_nama_lengkap_sama_id', 2);
                                                        $set('w_nama_lengkap', null);
                                                    } else {
                                                        $set('ik_nama_lengkap', null);
                                                    }
                                                })->columnSpanFull(),

                                            TextInput::make('ik_nama_lengkap')
                                                ->label('Nama Lengkap')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ik_nama_lengkap_sama_id') == 1)
                                                ->dehydrated(),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">B.01 STATUS IBU KANDUNG</p>
                                                </div>')),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ik_status_id')
                                                ->label('Status')
                                                // ->placeholder('Pilih Status')
                                                ->options(StatusWalisantri::whereIsActive(1)->pluck('status_walisantri', 'id'))
                                                ->required()
                                                ->inline()
                                                ->live()
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ik_status_id') == 1) {
                                                        $set('ik_kewarganegaraan_id', 1);
                                                    }
                                                }),
                                            // ->native(false),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            TextInput::make('ik_nama_kunyah')
                                                ->label('Nama Hijroh/Islami/Panggilan')
                                                ->required(),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            ToggleButtons::make('ik_kewarganegaraan_id')
                                                ->label('Kewarganegaraan')
                                                // ->placeholder('Pilih Kewarganegaraan')
                                                ->inline()
                                                ->options(Kewarganegaraan::whereIsActive(1)->pluck('kewarganegaraan', 'id'))
                                                ->default(1),
                                            // ->native(false)

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_kewarganegaraan_id') != 1 ||
                                            $get('ik_status_id') != 1)
                                        ->schema([

                                            ComponentsTextInput::make('ik_nik')
                                                ->label('NIK')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->regex('/^[0-9]*$/')
                                                ->length(16)
                                                ->maxLength(16)
                                                ->required(),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_kewarganegaraan_id') != 2 ||
                                            $get('ik_status_id') != 1)
                                        ->schema([

                                            TextInput::make('ik_asal_negara')
                                                ->label('Asal Negara')
                                                ->required(),

                                            TextInput::make('ik_kitas')
                                                ->label('KITAS')
                                                ->hint('Nomor Izin Tinggal (KITAS)')
                                                ->hintColor('danger')
                                                ->required(),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            TextInput::make('ik_tempat_lahir')
                                                ->label('Tempat Lahir')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required(),

                                            DatePicker::make('ik_tanggal_lahir')
                                                ->label('Tanggal Lahir')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->format('dd/mm/yyyy')
                                                ->displayFormat('d M Y')
                                                ->maxDate(now())
                                                ->native(false)
                                                ->closeOnDateSelection(),
                                        ]),

                                    Grid::make(6)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            Select::make('ik_pend_terakhir_id')
                                                ->label('Pendidikan Terakhir')
                                                ->placeholder('Pilih Pendidikan Terakhir')
                                                ->options(PendidikanTerakhirWalisantri::whereIsActive(1)->pluck('pendidikan_terakhir_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),

                                            Select::make('ik_pekerjaan_utama_id')
                                                ->label('Pekerjaan Utama')
                                                ->placeholder('Pilih Pekerjaan Utama')
                                                ->options(PekerjaanUtamaWalisantri::whereIsActive(1)->pluck('pekerjaan_utama_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),

                                            Select::make('ik_pghsln_rt_id')
                                                ->label('Penghasilan Rata-Rata')
                                                ->placeholder('Pilih Penghasilan Rata-Rata')
                                                ->options(PenghasilanWalisantri::whereIsActive(1)->pluck('penghasilan_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            ToggleButtons::make('ik_tdk_hp_id')
                                                ->label('Apakah memiliki nomor handphone?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ik_nomor_handphone_sama_id')
                                                ->label('Apakah nomor handphone sama dengan Pendaftar?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))

                                                ->hidden(fn(Get $get) =>
                                                $get('ik_tdk_hp_id') != 1 ||
                                                    $get('ak_nomor_handphone_sama_id') != 2 ||
                                                    $get('ik_status_id') != 1)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ik_nomor_handphone_sama_id') == 1) {
                                                        $set('ik_nomor_handphone', $get('hp_komunikasi'));
                                                        $set('w_nomor_handphone', null);
                                                    } else {
                                                        $set('ik_nomor_handphone', null);
                                                    }
                                                })->columnSpanFull(),

                                            TextInput::make('ik_nomor_handphone')
                                                ->label('No. Handphone')
                                                ->helperText('Contoh: 82187782223')
                                                ->prefix('+62')
                                                ->tel()
                                                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ik_nomor_handphone_sama_id') == 1)
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_tdk_hp_id') != 1 ||
                                                    $get('ik_status_id') != 1),
                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                         <p class="text-lg">Kajian yang diikuti</p>
                                     </div>')),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ik_kajian_sama_ak_id')
                                                ->label('Apakah kajian yang diikuti sama dengan Ayah?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_status_id') != 1)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ik_kajian_sama_ak_id') == 1) {
                                                        $set('ik_ustadz_kajian', $get('ak_ustadz_kajian'));
                                                        $set('ik_tempat_kajian', $get('ak_tempat_kajian'));
                                                    } else {
                                                        $set('ik_ustadz_kajian', null);
                                                        $set('ik_tempat_kajian', null);
                                                    }
                                                })->columnSpanFull(),

                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            Textarea::make('ik_ustadz_kajian')
                                                ->label('Ustadz yang mengisi kajian')
                                                ->required(),

                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            TextArea::make('ik_tempat_kajian')
                                                ->label('Tempat kajian yang diikuti')
                                                ->required(),

                                        ]),

                                    // KARTU KELUARGA IBU KANDUNG
                                    Grid::make(2)
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                        <p class="text-lg">B.02 KARTU KELUARGA</p>
                                        <p class="text-lg">IBU KANDUNG</p>
                                        </div>')),

                                        ])

                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ik_kk_sama_ak_id')
                                                ->label('Apakah KK Ibu Kandung sama dengan KK Ayah Kandung?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(function (Get $get) {

                                                    if ($get('ak_status_id') != 1) {

                                                        return ([
                                                            2 => 'Tidak',
                                                        ]);
                                                    } else {
                                                        return ([
                                                            1 => 'Ya',
                                                            2 => 'Tidak',
                                                        ]);
                                                    }
                                                })
                                                ->afterStateUpdated(function (Get $get, Set $set) {
                                                    $sama = $get('ik_kk_sama_ak_id');
                                                    $set('al_ik_sama_ak_id', $sama);

                                                    if ($get('ik_kk_sama_ak_id') == 1) {
                                                        $set('al_ik_sama_ak_id', 1);
                                                    }
                                                })
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_status_id') != 1),

                                            ToggleButtons::make('al_ik_sama_ak_id')
                                                ->label('Alamat sama dengan Ayah Kandung')
                                                ->helperText('Untuk mengubah alamat, silakan mengubah status KK Ibu kandung')
                                                ->disabled()
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_status_id') != 1),

                                            ToggleButtons::make('ik_kk_sama_pendaftar_id')
                                                ->label('Apakah KK dan Nama Kepala Keluarga sama dengan Pendaftar?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('ak_kk_sama_pendaftar_id') != 2 ||
                                                    $get('ik_status_id') != 1)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ik_kk_sama_pendaftar_id') == 1) {
                                                        $set('ik_no_kk', $get('kartu_keluarga_santri'));
                                                        $set('ik_kep_kel_kk', $get('nama_kpl_kel_santri'));
                                                        $set('w_kk_sama_pendaftar_id', 2);
                                                        $set('w_no_kk', null);
                                                        $set('w_kep_kel_kk', null);
                                                    } else {
                                                        $set('ik_no_kk', null);
                                                        $set('ik_kep_kel_kk', null);
                                                    }
                                                })->columnSpanFull(),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_kk_sama_ak_id') != 2 ||
                                            $get('ik_status_id') != 1)
                                        ->schema([

                                            ComponentsTextInput::make('ik_no_kk')
                                                ->label('No. KK Ibu Kandung')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->length(16)
                                                ->maxLength(16)
                                                ->regex('/^[0-9]*$/')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ik_kk_sama_pendaftar_id') == 1)
                                                ->dehydrated(),

                                            TextInput::make('ik_kep_kel_kk')
                                                ->label('Nama Kepala Keluarga')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ik_kk_sama_pendaftar_id') == 1)
                                                ->dehydrated(),

                                        ]),


                                    // ALAMAT IBU KANDUNG
                                    Grid::make(2)
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">B.03 TEMPAT TINGGAL DOMISILI</p>
                                                    <p class="text-lg">IBU KANDUNG</p>
                                                </div>')),
                                        ])->hidden(fn(Get $get) =>
                                        $get('ik_kk_sama_ak_id') == null ||
                                            $get('ik_kk_sama_ak_id') != 2 ||
                                            $get('ik_status_id') != 1),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('al_ik_tgldi_ln_id')
                                                ->label('Apakah tinggal di luar negeri?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Textarea::make('al_ik_almt_ln')
                                                ->label('Alamat Luar Negeri')
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 1 ||
                                                    $get('ik_status_id') != 1),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('al_ik_stts_rmh_id')
                                                ->label('Status Kepemilikan Rumah')
                                                ->placeholder('Pilih Status Kepemilikan Rumah')
                                                ->options(Statuskepemilikanrumah::whereIsActive(1)->pluck('status_kepemilikan_rumah', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('al_ik_provinsi_id')
                                                ->label('Provinsi')
                                                ->placeholder('Pilih Provinsi')
                                                ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1)
                                                ->afterStateUpdated(function (Set $set) {
                                                    $set('al_ik_kabupaten_id', null);
                                                    $set('al_ik_kecamatan_id', null);
                                                    $set('al_ik_kelurahan_id', null);
                                                    $set('al_ik_kodepos', null);
                                                }),

                                            Select::make('al_ik_kabupaten_id')
                                                ->label('Kabupaten')
                                                ->placeholder('Pilih Kabupaten')
                                                ->options(fn(Get $get): Collection => Kabupaten::query()
                                                    ->where('provinsi_id', $get('al_ik_provinsi_id'))
                                                    ->pluck('kabupaten', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('al_ik_kecamatan_id')
                                                ->label('Kecamatan')
                                                ->placeholder('Pilih Kecamatan')
                                                ->options(fn(Get $get): Collection => Kecamatan::query()
                                                    ->where('kabupaten_id', $get('al_ik_kabupaten_id'))
                                                    ->pluck('kecamatan', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                            Select::make('al_ik_kelurahan_id')
                                                ->label('Kelurahan')
                                                ->placeholder('Pilih Kelurahan')
                                                ->options(fn(Get $get): Collection => Kelurahan::query()
                                                    ->where('kecamatan_id', $get('al_ik_kecamatan_id'))
                                                    ->pluck('kelurahan', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1)
                                                ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                    if (($get('al_ik_kodepos') ?? '') !== Str::slug($old)) {
                                                        return;
                                                    }

                                                    $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                    $state = $kodepos;

                                                    foreach ($state as $state) {
                                                        $set('al_ik_kodepos', Str::substr($state, 12, 5));
                                                    }
                                                }),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            TextInput::make('al_ik_kodepos')
                                                ->label('Kodepos')
                                                ->disabled()
                                                ->required()
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),
                                        ]),

                                    Grid::make(4)
                                        ->schema([


                                            TextInput::make('al_ik_rt')
                                                ->label('RT')
                                                ->helperText('Isi 0 jika tidak ada RT/RW')
                                                ->required()
                                                ->numeric()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_ik_kodepos') == null)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                            TextInput::make('al_ik_rw')
                                                ->label('RW')
                                                ->helperText('Isi 0 jika tidak ada RT/RW')
                                                ->required()
                                                ->numeric()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_ik_kodepos') == null)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Textarea::make('al_ik_alamat')
                                                ->label('Alamat')
                                                ->required()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_ik_kodepos') == null)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                        ]),

                                ])->compact(),

                            // WALI

                            Section::make('C. WALI')
                                ->schema([

                                    Grid::make(2)
                                        ->schema([

                                            ToggleButtons::make('w_status_id')
                                                ->label('Status')
                                                // ->placeholder('Pilih Status')
                                                ->inline()
                                                ->options(function (Get $get) {

                                                    if (($get('ak_status_id') == 1 && $get('ik_status_id') == 1)) {
                                                        return ([
                                                            1 => 'Sama dengan ayah kandung',
                                                            2 => 'Sama dengan ibu kandung',
                                                            3 => 'Lainnya'
                                                        ]);
                                                    } elseif (($get('ak_status_id') == 1 && $get('ik_status_id') !== 1)) {
                                                        return ([
                                                            1 => 'Sama dengan ayah kandung',
                                                            3 => 'Lainnya'
                                                        ]);
                                                    } elseif (($get('ak_status_id') !== 1 && $get('ik_status_id') == 1)) {
                                                        return ([
                                                            2 => 'Sama dengan ibu kandung',
                                                            3 => 'Lainnya'
                                                        ]);
                                                    } elseif (($get('ak_status_id') !== 1 && $get('ik_status_id') !== 1)) {
                                                        return ([
                                                            3 => 'Lainnya'
                                                        ]);
                                                    }
                                                })
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('w_status_id') == 3) {
                                                        $set('w_kewarganegaraan_id', 1);
                                                    }
                                                }),
                                            // ->native(false),

                                        ]),

                                    Grid::make(2)

                                        ->hidden(fn(Get $get) =>
                                        $get('w_status_id') != 3)
                                        ->schema([

                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">C.01 STATUS WALI</p>
                                                </div>')),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('w_hubungan_id')
                                                ->label('Hubungan wali dengan calon santri')
                                                ->placeholder('Pilih Hubungan')
                                                ->options(HubunganWali::whereIsActive(1)->pluck('hubungan_wali', 'id'))
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('w_nama_lengkap_sama_id')
                                                ->label('Apakah Nama sama dengan Nama Kepala Keluarga?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3 ||
                                                    $get('ak_nama_lengkap_sama_id') != 2 ||
                                                    $get('ik_nama_lengkap_sama_id') != 2)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('w_nama_lengkap_sama_id') == 1) {
                                                        $set('w_nama_lengkap', $get('nama_kpl_kel_santri'));
                                                    } else {
                                                        $set('w_nama_lengkap', null);
                                                    }
                                                })->columnSpanFull(),

                                            TextInput::make('w_nama_lengkap')
                                                ->label('Nama Lengkap')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('w_nama_lengkap_sama_id') == 1)
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            TextInput::make('w_nama_kunyah')
                                                ->label('Nama Hijroh/Islami/Panggilan')
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('w_kewarganegaraan_id')
                                                ->label('Kewarganegaraan')
                                                // ->placeholder('Pilih Kewarganegaraan')
                                                ->inline()
                                                ->options(Kewarganegaraan::whereIsActive(1)->pluck('kewarganegaraan', 'id'))
                                                ->default(1)
                                                ->live()
                                                // ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ComponentsTextInput::make('w_nik')
                                                ->label('NIK')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->regex('/^[0-9]*$/')
                                                ->length(16)
                                                ->maxLength(16)
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_kewarganegaraan_id') != 1 ||
                                                    $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            TextInput::make('w_asal_negara')
                                                ->label('Asal Negara')
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_kewarganegaraan_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                            TextInput::make('w_kitas')
                                                ->label('KITAS')
                                                ->hint('Nomor Izin Tinggal (KITAS)')
                                                ->hintColor('danger')
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_kewarganegaraan_id') != 2 ||
                                                    $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            TextInput::make('w_tempat_lahir')
                                                ->label('Tempat Lahir')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                            DatePicker::make('w_tanggal_lahir')
                                                ->label('Tanggal Lahir')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->maxDate(now())
                                                ->required()
                                                // ->format('dd/mm/yyyy')
                                                ->displayFormat('d M Y')
                                                ->native(false)
                                                ->closeOnDateSelection()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(6)
                                        ->schema([

                                            Select::make('w_pend_terakhir_id')
                                                ->label('Pendidikan Terakhir')
                                                ->placeholder('Pilih Pendidikan Terakhir')
                                                ->options(PendidikanTerakhirWalisantri::whereIsActive(1)->pluck('pendidikan_terakhir_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                            Select::make('w_pekerjaan_utama_id')
                                                ->label('Pekerjaan Utama')
                                                ->placeholder('Pilih Pekerjaan Utama')
                                                ->options(PekerjaanUtamaWalisantri::whereIsActive(1)->pluck('pekerjaan_utama_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                            Select::make('w_pghsln_rt_id')
                                                ->label('Penghasilan Rata-Rata')
                                                ->placeholder('Pilih Penghasilan Rata-Rata')
                                                ->options(PenghasilanWalisantri::whereIsActive(1)->pluck('penghasilan_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('w_tdk_hp_id')
                                                ->label('Apakah memiliki nomor handphone?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('w_nomor_handphone_sama_id')
                                                ->label('Apakah nomor handphone sama dengan Pendaftar?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_tdk_hp_id') != 1 ||
                                                    $get('ak_nomor_handphone_sama_id') != 2 ||
                                                    $get('ik_nomor_handphone_sama_id') != 2 ||
                                                    $get('w_status_id') != 3)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('w_nomor_handphone_sama_id') == 1) {
                                                        $set('w_nomor_handphone', $get('hp_komunikasi'));
                                                    } else {
                                                        $set('w_nomor_handphone', null);
                                                    }
                                                })->columnSpanFull(),

                                            TextInput::make('w_nomor_handphone')
                                                ->label('No. Handphone')
                                                ->helperText('Contoh: 82187782223')
                                                // ->mask('82187782223')
                                                ->prefix('+62')
                                                ->tel()
                                                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('w_nomor_handphone_sama_id') == 1)
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_tdk_hp_id') != 1 ||
                                                    $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                         <p class="text-lg">Kajian yang diikuti</p>
                                     </div>'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Textarea::make('w_ustadz_kajian')
                                                ->label('Ustadz yang mengisi kajian')
                                                ->required()
                                                // ->default('4232')
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            TextArea::make('w_tempat_kajian')
                                                ->label('Tempat kajian yang diikuti')
                                                ->required()
                                                // ->default('4232')
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    // KARTU KELUARGA WALI
                                    Grid::make(2)
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                    <p class="text-lg">C.02 KARTU KELUARGA</p>
                                    <p class="text-lg">WALI</p>
                                 </div>'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('w_kk_sama_pendaftar_id')
                                                ->label('Apakah KK dan Nama Kepala Keluarga sama dengan Pendaftar?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('ak_kk_sama_pendaftar_id') != 2 ||
                                                    $get('ik_kk_sama_pendaftar_id') != 2 ||
                                                    $get('w_status_id') != 3)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('w_kk_sama_pendaftar_id') == 1) {
                                                        $set('w_no_kk', $get('kartu_keluarga_santri'));
                                                        $set('w_kep_kel_kk', $get('nama_kpl_kel_santri'));
                                                    } else {
                                                        $set('w_no_kk', null);
                                                        $set('w_kep_kel_kk', null);
                                                    }
                                                })->columnSpanFull(),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ComponentsTextInput::make('w_no_kk')
                                                ->label('No. KK Wali')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->length(16)
                                                ->maxLength(16)
                                                ->required()
                                                ->regex('/^[0-9]*$/')
                                                // ->disabled(fn (Get $get) =>
                                                // $get('w_kk_sama_pendaftar_id') == 1)
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                            TextInput::make('w_kep_kel_kk')
                                                ->label('Nama Kepala Keluarga')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('w_kk_sama_pendaftar_id') == 1)
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),
                                        ]),


                                    // ALAMAT WALI
                                    Grid::make(2)
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">C.03 TEMPAT TINGGAL DOMISILI</p>
                                                    <p class="text-lg">WALI</p>
                                                </div>'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('al_w_tgldi_ln_id')
                                                ->label('Apakah tinggal di luar negeri?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Textarea::make('al_w_almt_ln')
                                                ->label('Alamat Luar Negeri')
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 1),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('al_w_stts_rmh_id')
                                                ->label('Status Kepemilikan Rumah')
                                                ->placeholder('Pilih Status Kepemilikan Rumah')
                                                ->options(Statuskepemilikanrumah::whereIsActive(1)->pluck('status_kepemilikan_rumah', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('al_w_provinsi_id')
                                                ->label('Provinsi')
                                                ->placeholder('Pilih Provinsi')
                                                ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3)
                                                ->afterStateUpdated(function (Set $set) {
                                                    $set('al_w_kabupaten_id', null);
                                                    $set('al_w_kecamatan_id', null);
                                                    $set('al_w_kelurahan_id', null);
                                                    $set('al_w_kodepos', null);
                                                }),

                                            Select::make('al_w_kabupaten_id')
                                                ->label('Kabupaten')
                                                ->placeholder('Pilih Kabupaten')
                                                ->options(fn(Get $get): Collection => Kabupaten::query()
                                                    ->where('provinsi_id', $get('al_w_provinsi_id'))
                                                    ->pluck('kabupaten', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('al_w_kecamatan_id')
                                                ->label('Kecamatan')
                                                ->placeholder('Pilih Kecamatan')
                                                ->options(fn(Get $get): Collection => Kecamatan::query()
                                                    ->where('kabupaten_id', $get('al_w_kabupaten_id'))
                                                    ->pluck('kecamatan', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                            Select::make('al_w_kelurahan_id')
                                                ->label('Kelurahan')
                                                ->placeholder('Pilih Kelurahan')
                                                ->options(fn(Get $get): Collection => Kelurahan::query()
                                                    ->where('kecamatan_id', $get('al_w_kecamatan_id'))
                                                    ->pluck('kelurahan', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3)
                                                ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                    if (($get('al_w_kodepos') ?? '') !== Str::slug($old)) {
                                                        return;
                                                    }

                                                    $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                    $state = $kodepos;

                                                    foreach ($state as $state) {
                                                        $set('al_w_kodepos', Str::substr($state, 12, 5));
                                                    }
                                                }),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            TextInput::make('al_w_kodepos')
                                                ->label('Kodepos')
                                                ->disabled()
                                                ->required()
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(4)
                                        ->schema([


                                            TextInput::make('al_w_rt')
                                                ->label('RT')
                                                ->helperText('Isi 0 jika tidak ada RT/RW')
                                                ->required()
                                                ->numeric()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_w_kodepos') == null)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                            TextInput::make('al_w_rw')
                                                ->label('RW')
                                                ->helperText('Isi 0 jika tidak ada RT/RW')
                                                ->required()
                                                ->numeric()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_w_kodepos') == null)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Textarea::make('al_w_alamat')
                                                ->label('Alamat')
                                                ->required()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_w_kodepos') == null)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                        ]),



                                ])->compact()
                            // ->collapsed(fn (Get $get): bool => $get('is_collapse')),

                            // end of action steps
                        ]),


                    Tables\Actions\ViewAction::make()
                        ->label('Lihat Data Walisantri')
                        ->modalHeading('Lihat Data Walisantri')
                        ->modalWidth('full')
                        ->closeModalByClickingAway(false)
                        ->form([

                            Section::make('Informasi Pendaftar')
                                ->schema([
                                    Grid::make(4)
                                        ->schema([
                                            TextInput::make('kartu_keluarga_santri')
                                                ->label('Nomor Kartu Keluarga')
                                                ->disabled(),

                                            TextInput::make('nama_kpl_kel_santri')
                                                ->label('Nama Kepala Keluarga')
                                                ->required(),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            TextInput::make('hp_komunikasi')
                                                ->label('No Handphone walisantri untuk komunikasi')
                                                ->helperText('Contoh: 82187782223')
                                                // ->mask('82187782223')
                                                ->prefix('+62')
                                                ->tel()
                                                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                ->required(),

                                        ]),

                                ])->compact(),

                            //AYAH KANDUNG
                            Section::make('A. AYAH KANDUNG')
                                ->schema([

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ak_nama_lengkap_sama_id')
                                                ->label('Apakah Nama sama dengan Nama Kepala Keluarga?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                // ->hidden(fn (Get $get) =>
                                                // $get('ak_status_id') != 1)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ak_nama_lengkap_sama_id') == 1) {
                                                        $set('ak_nama_lengkap', $get('nama_kpl_kel_santri'));
                                                        $set('ik_nama_lengkap_sama_id_id', 2);
                                                        $set('ik_nama_lengkap', null);
                                                        $set('w_nama_lengkap_sama_id_id', 2);
                                                        $set('w_nama_lengkap', null);
                                                    } else {
                                                        $set('ak_nama_lengkap', null);
                                                    }
                                                })->columnSpanFull(),

                                            TextInput::make('ak_nama_lengkap')
                                                ->label('Nama Lengkap')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ak_nama_lengkap_sama') == 1)
                                                ->dehydrated(),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">A.01 STATUS AYAH KANDUNG</p>
                                                </div>')),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ak_status_id')
                                                ->label('Status')
                                                // ->placeholder('Pilih Status')
                                                ->options(StatusWalisantri::whereIsActive(1)->pluck('status_walisantri', 'id'))
                                                ->required()
                                                ->inline()
                                                ->live()
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ak_status_id') == 1) {
                                                        $set('ak_kewarganegaraan_id', 1);
                                                    }
                                                }),
                                            // ->native(false),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            TextInput::make('ak_nama_kunyah')
                                                ->label('Nama Hijroh/Islami/Panggilan')
                                                ->required(),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            ToggleButtons::make('ak_kewarganegaraan_id')
                                                ->label('Kewarganegaraan')
                                                // ->placeholder('Pilih Kewarganegaraan')
                                                ->inline()
                                                ->default(1)
                                                ->options(Kewarganegaraan::whereIsActive(1)->pluck('kewarganegaraan', 'id'))
                                                ->required()
                                                ->live(),
                                            // ->native(false),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_kewarganegaraan_id') != 1 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([

                                            ComponentsTextInput::make('ak_nik')
                                                ->label('NIK')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->regex('/^[0-9]*$/')
                                                ->length(16)
                                                ->maxLength(16)
                                                ->required(),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_kewarganegaraan_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([

                                            TextInput::make('ak_asal_negara')
                                                ->label('Asal Negara')
                                                ->required(),


                                            TextInput::make('ak_kitas')
                                                ->label('KITAS')
                                                ->hint('Nomor Izin Tinggal (KITAS)')
                                                ->hintColor('danger')
                                                ->required(),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            TextInput::make('ak_tempat_lahir')
                                                ->label('Tempat Lahir')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required(),


                                            DatePicker::make('ak_tanggal_lahir')
                                                ->label('Tanggal Lahir')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->format('dd/mm/yyyy')
                                                ->displayFormat('d M Y')
                                                ->maxDate(now())
                                                ->native(false)
                                                ->closeOnDateSelection(),
                                        ]),

                                    Grid::make(6)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            Select::make('ak_pend_terakhir_id')
                                                ->label('Pendidikan Terakhir')
                                                ->placeholder('Pilih Pendidikan Terakhir')
                                                ->options(PendidikanTerakhirWalisantri::whereIsActive(1)->pluck('pendidikan_terakhir_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),

                                            Select::make('ak_pekerjaan_utama_id')
                                                ->label('Pekerjaan Utama')
                                                ->placeholder('Pilih Pekerjaan Utama')
                                                ->options(PekerjaanUtamaWalisantri::whereIsActive(1)->pluck('pekerjaan_utama_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),

                                            Select::make('ak_pghsln_rt_id')
                                                ->label('Penghasilan Rata-Rata')
                                                ->placeholder('Pilih Penghasilan Rata-Rata')
                                                ->options(PenghasilanWalisantri::whereIsActive(1)->pluck('penghasilan_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            ToggleButtons::make('ak_tdk_hp_id')
                                                ->label('Apakah memiliki nomor handphone?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ak_tdk_hp_id') == 2) {
                                                        $set('ak_nomor_handphone_sama_id', null);
                                                        $set('ak_nomor_handphone', null);
                                                    }
                                                }),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_tdk_hp_id') != 1 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([


                                            ToggleButtons::make('ak_nomor_handphone_sama_id')
                                                ->label('Apakah nomor handphone sama dengan Pendaftar?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ak_nomor_handphone_sama_id') == 1) {
                                                        $set('ak_nomor_handphone', $get('hp_komunikasi'));
                                                        $set('ik_nomor_handphone_sama_id', 2);
                                                        $set('ik_nomor_handphone', null);
                                                        $set('w_nomor_handphone_sama_id', 2);
                                                        $set('w_nomor_handphone', null);
                                                    } else {
                                                        $set('ak_nomor_handphone', null);
                                                    }
                                                })->columnSpanFull(),

                                            TextInput::make('ak_nomor_handphone')
                                                ->label('No. Handphone')
                                                ->helperText('Contoh: 82187782223')
                                                ->prefix('+62')
                                                ->tel()
                                                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ak_nomor_handphone_sama_id') == 1)
                                                ->dehydrated(),
                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                         <p class="text-lg">Kajian yang diikuti</p>
                                     </div>')),
                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            Textarea::make('ak_ustadz_kajian')
                                                ->label('Ustadz yang mengisi kajian')
                                                ->required(),

                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            TextArea::make('ak_tempat_kajian')
                                                ->label('Tempat kajian yang diikuti')
                                                ->required(),

                                        ]),

                                    // KARTU KELUARGA AYAH KANDUNG
                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                    <p class="text-lg">A.02 KARTU KELUARGA</p>
                                    <p class="text-lg">AYAH KANDUNG</p>
                                       </div>')),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            ToggleButtons::make('ak_kk_sama_pendaftar_id')
                                                ->label('Apakah KK dan Nama Kepala Keluarga sama dengan Pendaftar?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ak_kk_sama_pendaftar_id') == 1) {
                                                        $set('ak_no_kk', $get('kartu_keluarga_santri'));
                                                        $set('ak_kep_kel_kk', $get('nama_kpl_kel_santri'));
                                                        $set('ik_kk_sama_pendaftar_id', 2);
                                                        $set('ik_no_kk', null);
                                                        $set('ik_kep_kel_kk', null);
                                                        $set('w_kk_sama_pendaftar_id', 2);
                                                        $set('w_no_kk', null);
                                                        $set('w_kep_kel_kk', null);
                                                    } else {
                                                        $set('ak_no_kk', null);
                                                        $set('ak_kep_kel_kk', null);
                                                    }
                                                })->columnSpanFull(),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            ComponentsTextInput::make('ak_no_kk')
                                                ->label('No. KK Ayah Kandung')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->length(16)
                                                ->maxLength(16)
                                                ->required()
                                                ->regex('/^[0-9]*$/')
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ak_kk_sama_pendaftar_id') == 1)
                                                ->dehydrated(),

                                            TextInput::make('ak_kep_kel_kk')
                                                ->label('Nama Kepala Keluarga')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ak_kk_sama_pendaftar_id') == 1)
                                                ->dehydrated(),
                                        ]),

                                    // ALAMAT AYAH KANDUNG
                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">A.03 TEMPAT TINGGAL DOMISILI</p>
                                                    <p class="text-lg">AYAH KANDUNG</p>
                                                </div>')),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ak_status_id') != 1)
                                        ->schema([

                                            ToggleButtons::make('al_ak_tgldi_ln_id')
                                                ->label('Apakah tinggal di luar negeri?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 1)
                                        ->schema([

                                            Textarea::make('al_ak_almt_ln')
                                                ->label('Alamat Luar Negeri')
                                                ->required(),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([

                                            Select::make('al_ak_stts_rmh_id')
                                                ->label('Status Kepemilikan Rumah')
                                                ->placeholder('Pilih Status Kepemilikan Rumah')
                                                ->options(Statuskepemilikanrumah::whereIsActive(1)->pluck('status_kepemilikan_rumah', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([

                                            Select::make('al_ak_provinsi_id')
                                                ->label('Provinsi')
                                                ->placeholder('Pilih Provinsi')
                                                ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->afterStateUpdated(function (Set $set) {
                                                    $set('al_ak_kabupaten_id', null);
                                                    $set('al_ak_kecamatan_id', null);
                                                    $set('al_ak_kelurahan_id', null);
                                                    $set('al_ak_kodepos', null);
                                                }),

                                            Select::make('al_ak_kabupaten_id')
                                                ->label('Kabupaten')
                                                ->placeholder('Pilih Kabupaten')
                                                ->options(fn(Get $get): Collection => Kabupaten::query()
                                                    ->where('provinsi_id', $get('al_ak_provinsi_id'))
                                                    ->pluck('kabupaten', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([

                                            Select::make('al_ak_kecamatan_id')
                                                ->label('Kecamatan')
                                                ->placeholder('Pilih Kecamatan')
                                                ->options(fn(Get $get): Collection => Kecamatan::query()
                                                    ->where('kabupaten_id', $get('al_ak_kabupaten_id'))
                                                    ->pluck('kecamatan', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false),

                                            Select::make('al_ak_kelurahan_id')
                                                ->label('Kelurahan')
                                                ->placeholder('Pilih Kelurahan')
                                                ->options(fn(Get $get): Collection => Kelurahan::query()
                                                    ->where('kecamatan_id', $get('al_ak_kecamatan_id'))
                                                    ->pluck('kelurahan', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                    if (($get('al_ak_kodepos') ?? '') !== Str::slug($old)) {
                                                        return;
                                                    }

                                                    $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                    $state = $kodepos;

                                                    foreach ($state as $state) {
                                                        $set('al_ak_kodepos', Str::substr($state, 12, 5));
                                                    }
                                                }),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([

                                            TextInput::make('al_ak_kodepos')
                                                ->label('Kodepos')
                                                ->disabled()
                                                ->required()
                                                ->dehydrated(),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([


                                            TextInput::make('al_ak_rt')
                                                ->label('RT')
                                                ->helperText('Isi 0 jika tidak ada RT/RW')
                                                ->required()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_ak_kodepos') == null)
                                                ->numeric(),

                                            TextInput::make('al_ak_rw')
                                                ->label('RW')
                                                ->helperText('Isi 0 jika tidak ada RT/RW')
                                                ->required()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_ak_kodepos') == null)
                                                ->numeric(),
                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('al_ak_tgldi_ln_id') != 2 ||
                                            $get('ak_status_id') != 1)
                                        ->schema([
                                            Textarea::make('al_ak_alamat')
                                                ->label('Alamat')
                                                ->disabled(fn(Get $get) =>
                                                $get('al_ak_kodepos') == null)
                                                ->required(),
                                        ]),

                                ])->compact(),


                            // //IBU KANDUNG
                            Section::make('B. IBU KANDUNG')
                                ->schema([

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ik_nama_lengkap_sama_id')
                                                ->label('Apakah Nama sama dengan Nama Kepala Keluarga?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('ak_nama_lengkap_sama_id') != 2)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ik_nama_lengkap_sama_id') == 1) {
                                                        $set('ik_nama_lengkap', $get('nama_kpl_kel_santri'));
                                                        $set('w_nama_lengkap_sama_id', 2);
                                                        $set('w_nama_lengkap', null);
                                                    } else {
                                                        $set('ik_nama_lengkap', null);
                                                    }
                                                })->columnSpanFull(),

                                            TextInput::make('ik_nama_lengkap')
                                                ->label('Nama Lengkap')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ik_nama_lengkap_sama_id') == 1)
                                                ->dehydrated(),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">B.01 STATUS IBU KANDUNG</p>
                                                </div>')),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ik_status_id')
                                                ->label('Status')
                                                // ->placeholder('Pilih Status')
                                                ->options(StatusWalisantri::whereIsActive(1)->pluck('status_walisantri', 'id'))
                                                ->required()
                                                ->inline()
                                                ->live()
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ik_status_id') == 1) {
                                                        $set('ik_kewarganegaraan_id', 1);
                                                    }
                                                }),
                                            // ->native(false),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            TextInput::make('ik_nama_kunyah')
                                                ->label('Nama Hijroh/Islami/Panggilan')
                                                ->required(),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            ToggleButtons::make('ik_kewarganegaraan_id')
                                                ->label('Kewarganegaraan')
                                                // ->placeholder('Pilih Kewarganegaraan')
                                                ->inline()
                                                ->options(Kewarganegaraan::whereIsActive(1)->pluck('kewarganegaraan', 'id'))
                                                ->default(1),
                                            // ->native(false)

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_kewarganegaraan_id') != 1 ||
                                            $get('ik_status_id') != 1)
                                        ->schema([

                                            ComponentsTextInput::make('ik_nik')
                                                ->label('NIK')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->regex('/^[0-9]*$/')
                                                ->length(16)
                                                ->maxLength(16)
                                                ->required(),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_kewarganegaraan_id') != 2 ||
                                            $get('ik_status_id') != 1)
                                        ->schema([

                                            TextInput::make('ik_asal_negara')
                                                ->label('Asal Negara')
                                                ->required(),

                                            TextInput::make('ik_kitas')
                                                ->label('KITAS')
                                                ->hint('Nomor Izin Tinggal (KITAS)')
                                                ->hintColor('danger')
                                                ->required(),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            TextInput::make('ik_tempat_lahir')
                                                ->label('Tempat Lahir')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required(),

                                            DatePicker::make('ik_tanggal_lahir')
                                                ->label('Tanggal Lahir')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->format('dd/mm/yyyy')
                                                ->displayFormat('d M Y')
                                                ->maxDate(now())
                                                ->native(false)
                                                ->closeOnDateSelection(),
                                        ]),

                                    Grid::make(6)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            Select::make('ik_pend_terakhir_id')
                                                ->label('Pendidikan Terakhir')
                                                ->placeholder('Pilih Pendidikan Terakhir')
                                                ->options(PendidikanTerakhirWalisantri::whereIsActive(1)->pluck('pendidikan_terakhir_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),

                                            Select::make('ik_pekerjaan_utama_id')
                                                ->label('Pekerjaan Utama')
                                                ->placeholder('Pilih Pekerjaan Utama')
                                                ->options(PekerjaanUtamaWalisantri::whereIsActive(1)->pluck('pekerjaan_utama_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),

                                            Select::make('ik_pghsln_rt_id')
                                                ->label('Penghasilan Rata-Rata')
                                                ->placeholder('Pilih Penghasilan Rata-Rata')
                                                ->options(PenghasilanWalisantri::whereIsActive(1)->pluck('penghasilan_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false),
                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            ToggleButtons::make('ik_tdk_hp_id')
                                                ->label('Apakah memiliki nomor handphone?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ik_nomor_handphone_sama_id')
                                                ->label('Apakah nomor handphone sama dengan Pendaftar?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))

                                                ->hidden(fn(Get $get) =>
                                                $get('ik_tdk_hp_id') != 1 ||
                                                    $get('ak_nomor_handphone_sama_id') != 2 ||
                                                    $get('ik_status_id') != 1)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ik_nomor_handphone_sama_id') == 1) {
                                                        $set('ik_nomor_handphone', $get('hp_komunikasi'));
                                                        $set('w_nomor_handphone', null);
                                                    } else {
                                                        $set('ik_nomor_handphone', null);
                                                    }
                                                })->columnSpanFull(),

                                            TextInput::make('ik_nomor_handphone')
                                                ->label('No. Handphone')
                                                ->helperText('Contoh: 82187782223')
                                                ->prefix('+62')
                                                ->tel()
                                                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ik_nomor_handphone_sama_id') == 1)
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_tdk_hp_id') != 1 ||
                                                    $get('ik_status_id') != 1),
                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                         <p class="text-lg">Kajian yang diikuti</p>
                                     </div>')),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ik_kajian_sama_ak_id')
                                                ->label('Apakah kajian yang diikuti sama dengan Ayah?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_status_id') != 1)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ik_kajian_sama_ak_id') == 1) {
                                                        $set('ik_ustadz_kajian', $get('ak_ustadz_kajian'));
                                                        $set('ik_tempat_kajian', $get('ak_tempat_kajian'));
                                                    } else {
                                                        $set('ik_ustadz_kajian', null);
                                                        $set('ik_tempat_kajian', null);
                                                    }
                                                })->columnSpanFull(),

                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            Textarea::make('ik_ustadz_kajian')
                                                ->label('Ustadz yang mengisi kajian')
                                                ->required(),

                                        ]),

                                    Grid::make(2)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1)
                                        ->schema([

                                            TextArea::make('ik_tempat_kajian')
                                                ->label('Tempat kajian yang diikuti')
                                                ->required(),

                                        ]),

                                    // KARTU KELUARGA IBU KANDUNG
                                    Grid::make(2)
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                        <p class="text-lg">B.02 KARTU KELUARGA</p>
                                        <p class="text-lg">IBU KANDUNG</p>
                                        </div>')),

                                        ])

                                        ->hidden(fn(Get $get) =>
                                        $get('ik_status_id') != 1),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('ik_kk_sama_ak_id')
                                                ->label('Apakah KK Ibu Kandung sama dengan KK Ayah Kandung?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(function (Get $get) {

                                                    if ($get('ak_status_id') != 1) {

                                                        return ([
                                                            2 => 'Tidak',
                                                        ]);
                                                    } else {
                                                        return ([
                                                            1 => 'Ya',
                                                            2 => 'Tidak',
                                                        ]);
                                                    }
                                                })
                                                ->afterStateUpdated(function (Get $get, Set $set) {
                                                    $sama = $get('ik_kk_sama_ak_id');
                                                    $set('al_ik_sama_ak_id', $sama);

                                                    if ($get('ik_kk_sama_ak_id') == 1) {
                                                        $set('al_ik_sama_ak_id', 1);
                                                    }
                                                })
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_status_id') != 1),

                                            ToggleButtons::make('al_ik_sama_ak_id')
                                                ->label('Alamat sama dengan Ayah Kandung')
                                                ->helperText('Untuk mengubah alamat, silakan mengubah status KK Ibu kandung')
                                                ->disabled()
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_status_id') != 1),

                                            ToggleButtons::make('ik_kk_sama_pendaftar_id')
                                                ->label('Apakah KK dan Nama Kepala Keluarga sama dengan Pendaftar?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('ak_kk_sama_pendaftar_id') != 2 ||
                                                    $get('ik_status_id') != 1)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('ik_kk_sama_pendaftar_id') == 1) {
                                                        $set('ik_no_kk', $get('kartu_keluarga_santri'));
                                                        $set('ik_kep_kel_kk', $get('nama_kpl_kel_santri'));
                                                        $set('w_kk_sama_pendaftar_id', 2);
                                                        $set('w_no_kk', null);
                                                        $set('w_kep_kel_kk', null);
                                                    } else {
                                                        $set('ik_no_kk', null);
                                                        $set('ik_kep_kel_kk', null);
                                                    }
                                                })->columnSpanFull(),

                                        ]),

                                    Grid::make(4)
                                        ->hidden(fn(Get $get) =>
                                        $get('ik_kk_sama_ak_id') != 2 ||
                                            $get('ik_status_id') != 1)
                                        ->schema([

                                            ComponentsTextInput::make('ik_no_kk')
                                                ->label('No. KK Ibu Kandung')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->length(16)
                                                ->maxLength(16)
                                                ->regex('/^[0-9]*$/')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ik_kk_sama_pendaftar_id') == 1)
                                                ->dehydrated(),

                                            TextInput::make('ik_kep_kel_kk')
                                                ->label('Nama Kepala Keluarga')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('ik_kk_sama_pendaftar_id') == 1)
                                                ->dehydrated(),

                                        ]),


                                    // ALAMAT IBU KANDUNG
                                    Grid::make(2)
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">B.03 TEMPAT TINGGAL DOMISILI</p>
                                                    <p class="text-lg">IBU KANDUNG</p>
                                                </div>')),
                                        ])->hidden(fn(Get $get) =>
                                        $get('ik_kk_sama_ak_id') == null ||
                                            $get('ik_kk_sama_ak_id') != 2 ||
                                            $get('ik_status_id') != 1),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('al_ik_tgldi_ln_id')
                                                ->label('Apakah tinggal di luar negeri?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Textarea::make('al_ik_almt_ln')
                                                ->label('Alamat Luar Negeri')
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 1 ||
                                                    $get('ik_status_id') != 1),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('al_ik_stts_rmh_id')
                                                ->label('Status Kepemilikan Rumah')
                                                ->placeholder('Pilih Status Kepemilikan Rumah')
                                                ->options(Statuskepemilikanrumah::whereIsActive(1)->pluck('status_kepemilikan_rumah', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('al_ik_provinsi_id')
                                                ->label('Provinsi')
                                                ->placeholder('Pilih Provinsi')
                                                ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1)
                                                ->afterStateUpdated(function (Set $set) {
                                                    $set('al_ik_kabupaten_id', null);
                                                    $set('al_ik_kecamatan_id', null);
                                                    $set('al_ik_kelurahan_id', null);
                                                    $set('al_ik_kodepos', null);
                                                }),

                                            Select::make('al_ik_kabupaten_id')
                                                ->label('Kabupaten')
                                                ->placeholder('Pilih Kabupaten')
                                                ->options(fn(Get $get): Collection => Kabupaten::query()
                                                    ->where('provinsi_id', $get('al_ik_provinsi_id'))
                                                    ->pluck('kabupaten', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('al_ik_kecamatan_id')
                                                ->label('Kecamatan')
                                                ->placeholder('Pilih Kecamatan')
                                                ->options(fn(Get $get): Collection => Kecamatan::query()
                                                    ->where('kabupaten_id', $get('al_ik_kabupaten_id'))
                                                    ->pluck('kecamatan', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                            Select::make('al_ik_kelurahan_id')
                                                ->label('Kelurahan')
                                                ->placeholder('Pilih Kelurahan')
                                                ->options(fn(Get $get): Collection => Kelurahan::query()
                                                    ->where('kecamatan_id', $get('al_ik_kecamatan_id'))
                                                    ->pluck('kelurahan', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1)
                                                ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                    if (($get('al_ik_kodepos') ?? '') !== Str::slug($old)) {
                                                        return;
                                                    }

                                                    $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                    $state = $kodepos;

                                                    foreach ($state as $state) {
                                                        $set('al_ik_kodepos', Str::substr($state, 12, 5));
                                                    }
                                                }),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            TextInput::make('al_ik_kodepos')
                                                ->label('Kodepos')
                                                ->disabled()
                                                ->required()
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),
                                        ]),

                                    Grid::make(4)
                                        ->schema([


                                            TextInput::make('al_ik_rt')
                                                ->label('RT')
                                                ->helperText('Isi 0 jika tidak ada RT/RW')
                                                ->required()
                                                ->numeric()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_ik_kodepos') == null)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                            TextInput::make('al_ik_rw')
                                                ->label('RW')
                                                ->helperText('Isi 0 jika tidak ada RT/RW')
                                                ->required()
                                                ->numeric()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_ik_kodepos') == null)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Textarea::make('al_ik_alamat')
                                                ->label('Alamat')
                                                ->required()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_ik_kodepos') == null)
                                                ->hidden(fn(Get $get) =>
                                                $get('ik_kk_sama_ak_id') != 2 ||
                                                    $get('al_ik_tgldi_ln_id') != 2 ||
                                                    $get('ik_status_id') != 1),

                                        ]),

                                ])->compact(),

                            // WALI

                            Section::make('C. WALI')
                                ->schema([

                                    Grid::make(2)
                                        ->schema([

                                            ToggleButtons::make('w_status_id')
                                                ->label('Status')
                                                // ->placeholder('Pilih Status')
                                                ->inline()
                                                ->options(function (Get $get) {

                                                    if (($get('ak_status_id') == 1 && $get('ik_status_id') == 1)) {
                                                        return ([
                                                            1 => 'Sama dengan ayah kandung',
                                                            2 => 'Sama dengan ibu kandung',
                                                            3 => 'Lainnya'
                                                        ]);
                                                    } elseif (($get('ak_status_id') == 1 && $get('ik_status_id') !== 1)) {
                                                        return ([
                                                            1 => 'Sama dengan ayah kandung',
                                                            3 => 'Lainnya'
                                                        ]);
                                                    } elseif (($get('ak_status_id') !== 1 && $get('ik_status_id') == 1)) {
                                                        return ([
                                                            2 => 'Sama dengan ibu kandung',
                                                            3 => 'Lainnya'
                                                        ]);
                                                    } elseif (($get('ak_status_id') !== 1 && $get('ik_status_id') !== 1)) {
                                                        return ([
                                                            3 => 'Lainnya'
                                                        ]);
                                                    }
                                                })
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('w_status_id') == 3) {
                                                        $set('w_kewarganegaraan_id', 1);
                                                    }
                                                }),
                                            // ->native(false),

                                        ]),

                                    Grid::make(2)

                                        ->hidden(fn(Get $get) =>
                                        $get('w_status_id') != 3)
                                        ->schema([

                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">C.01 STATUS WALI</p>
                                                </div>')),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('w_hubungan_id')
                                                ->label('Hubungan wali dengan calon santri')
                                                ->placeholder('Pilih Hubungan')
                                                ->options(HubunganWali::whereIsActive(1)->pluck('hubungan_wali', 'id'))
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('w_nama_lengkap_sama_id')
                                                ->label('Apakah Nama sama dengan Nama Kepala Keluarga?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3 ||
                                                    $get('ak_nama_lengkap_sama_id') != 2 ||
                                                    $get('ik_nama_lengkap_sama_id') != 2)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('w_nama_lengkap_sama_id') == 1) {
                                                        $set('w_nama_lengkap', $get('nama_kpl_kel_santri'));
                                                    } else {
                                                        $set('w_nama_lengkap', null);
                                                    }
                                                })->columnSpanFull(),

                                            TextInput::make('w_nama_lengkap')
                                                ->label('Nama Lengkap')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('w_nama_lengkap_sama_id') == 1)
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            TextInput::make('w_nama_kunyah')
                                                ->label('Nama Hijroh/Islami/Panggilan')
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('w_kewarganegaraan_id')
                                                ->label('Kewarganegaraan')
                                                // ->placeholder('Pilih Kewarganegaraan')
                                                ->inline()
                                                ->options(Kewarganegaraan::whereIsActive(1)->pluck('kewarganegaraan', 'id'))
                                                ->default(1)
                                                ->live()
                                                // ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ComponentsTextInput::make('w_nik')
                                                ->label('NIK')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->regex('/^[0-9]*$/')
                                                ->length(16)
                                                ->maxLength(16)
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_kewarganegaraan_id') != 1 ||
                                                    $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            TextInput::make('w_asal_negara')
                                                ->label('Asal Negara')
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_kewarganegaraan_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                            TextInput::make('w_kitas')
                                                ->label('KITAS')
                                                ->hint('Nomor Izin Tinggal (KITAS)')
                                                ->hintColor('danger')
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_kewarganegaraan_id') != 2 ||
                                                    $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            TextInput::make('w_tempat_lahir')
                                                ->label('Tempat Lahir')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                            DatePicker::make('w_tanggal_lahir')
                                                ->label('Tanggal Lahir')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->maxDate(now())
                                                ->required()
                                                // ->format('dd/mm/yyyy')
                                                ->displayFormat('d M Y')
                                                ->native(false)
                                                ->closeOnDateSelection()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(6)
                                        ->schema([

                                            Select::make('w_pend_terakhir_id')
                                                ->label('Pendidikan Terakhir')
                                                ->placeholder('Pilih Pendidikan Terakhir')
                                                ->options(PendidikanTerakhirWalisantri::whereIsActive(1)->pluck('pendidikan_terakhir_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                            Select::make('w_pekerjaan_utama_id')
                                                ->label('Pekerjaan Utama')
                                                ->placeholder('Pilih Pekerjaan Utama')
                                                ->options(PekerjaanUtamaWalisantri::whereIsActive(1)->pluck('pekerjaan_utama_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                            Select::make('w_pghsln_rt_id')
                                                ->label('Penghasilan Rata-Rata')
                                                ->placeholder('Pilih Penghasilan Rata-Rata')
                                                ->options(PenghasilanWalisantri::whereIsActive(1)->pluck('penghasilan_walisantri', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('w_tdk_hp_id')
                                                ->label('Apakah memiliki nomor handphone?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('w_nomor_handphone_sama_id')
                                                ->label('Apakah nomor handphone sama dengan Pendaftar?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_tdk_hp_id') != 1 ||
                                                    $get('ak_nomor_handphone_sama_id') != 2 ||
                                                    $get('ik_nomor_handphone_sama_id') != 2 ||
                                                    $get('w_status_id') != 3)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('w_nomor_handphone_sama_id') == 1) {
                                                        $set('w_nomor_handphone', $get('hp_komunikasi'));
                                                    } else {
                                                        $set('w_nomor_handphone', null);
                                                    }
                                                })->columnSpanFull(),

                                            TextInput::make('w_nomor_handphone')
                                                ->label('No. Handphone')
                                                ->helperText('Contoh: 82187782223')
                                                // ->mask('82187782223')
                                                ->prefix('+62')
                                                ->tel()
                                                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('w_nomor_handphone_sama_id') == 1)
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_tdk_hp_id') != 1 ||
                                                    $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                         <p class="text-lg">Kajian yang diikuti</p>
                                     </div>'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Textarea::make('w_ustadz_kajian')
                                                ->label('Ustadz yang mengisi kajian')
                                                ->required()
                                                // ->default('4232')
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            TextArea::make('w_tempat_kajian')
                                                ->label('Tempat kajian yang diikuti')
                                                ->required()
                                                // ->default('4232')
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    // KARTU KELUARGA WALI
                                    Grid::make(2)
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                    <p class="text-lg">C.02 KARTU KELUARGA</p>
                                    <p class="text-lg">WALI</p>
                                 </div>'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('w_kk_sama_pendaftar_id')
                                                ->label('Apakah KK dan Nama Kepala Keluarga sama dengan Pendaftar?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('ak_kk_sama_pendaftar_id') != 2 ||
                                                    $get('ik_kk_sama_pendaftar_id') != 2 ||
                                                    $get('w_status_id') != 3)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    if ($get('w_kk_sama_pendaftar_id') == 1) {
                                                        $set('w_no_kk', $get('kartu_keluarga_santri'));
                                                        $set('w_kep_kel_kk', $get('nama_kpl_kel_santri'));
                                                    } else {
                                                        $set('w_no_kk', null);
                                                        $set('w_kep_kel_kk', null);
                                                    }
                                                })->columnSpanFull(),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ComponentsTextInput::make('w_no_kk')
                                                ->label('No. KK Wali')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->length(16)
                                                ->maxLength(16)
                                                ->required()
                                                ->regex('/^[0-9]*$/')
                                                // ->disabled(fn (Get $get) =>
                                                // $get('w_kk_sama_pendaftar_id') == 1)
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                            TextInput::make('w_kep_kel_kk')
                                                ->label('Nama Kepala Keluarga')
                                                ->hint('Isi sesuai dengan KK')
                                                ->hintColor('danger')
                                                ->required()
                                                // ->disabled(fn (Get $get) =>
                                                // $get('w_kk_sama_pendaftar_id') == 1)
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),
                                        ]),


                                    // ALAMAT WALI
                                    Grid::make(2)
                                        ->schema([
                                            Placeholder::make('')
                                                ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">C.03 TEMPAT TINGGAL DOMISILI</p>
                                                    <p class="text-lg">WALI</p>
                                                </div>'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            ToggleButtons::make('al_w_tgldi_ln_id')
                                                ->label('Apakah tinggal di luar negeri?')
                                                ->live()
                                                ->inline()
                                                ->grouped()
                                                ->boolean()
                                                ->options(YaTidak::whereIsActive(1)->pluck('ya_tidak', 'id'))
                                                ->hidden(fn(Get $get) =>
                                                $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Textarea::make('al_w_almt_ln')
                                                ->label('Alamat Luar Negeri')
                                                ->required()
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 1),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('al_w_stts_rmh_id')
                                                ->label('Status Kepemilikan Rumah')
                                                ->placeholder('Pilih Status Kepemilikan Rumah')
                                                ->options(Statuskepemilikanrumah::whereIsActive(1)->pluck('status_kepemilikan_rumah', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('al_w_provinsi_id')
                                                ->label('Provinsi')
                                                ->placeholder('Pilih Provinsi')
                                                ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3)
                                                ->afterStateUpdated(function (Set $set) {
                                                    $set('al_w_kabupaten_id', null);
                                                    $set('al_w_kecamatan_id', null);
                                                    $set('al_w_kelurahan_id', null);
                                                    $set('al_w_kodepos', null);
                                                }),

                                            Select::make('al_w_kabupaten_id')
                                                ->label('Kabupaten')
                                                ->placeholder('Pilih Kabupaten')
                                                ->options(fn(Get $get): Collection => Kabupaten::query()
                                                    ->where('provinsi_id', $get('al_w_provinsi_id'))
                                                    ->pluck('kabupaten', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            Select::make('al_w_kecamatan_id')
                                                ->label('Kecamatan')
                                                ->placeholder('Pilih Kecamatan')
                                                ->options(fn(Get $get): Collection => Kecamatan::query()
                                                    ->where('kabupaten_id', $get('al_w_kabupaten_id'))
                                                    ->pluck('kecamatan', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                            Select::make('al_w_kelurahan_id')
                                                ->label('Kelurahan')
                                                ->placeholder('Pilih Kelurahan')
                                                ->options(fn(Get $get): Collection => Kelurahan::query()
                                                    ->where('kecamatan_id', $get('al_w_kecamatan_id'))
                                                    ->pluck('kelurahan', 'id'))
                                                // ->searchable()
                                                ->required()
                                                ->live()
                                                ->native(false)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3)
                                                ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                    if (($get('al_w_kodepos') ?? '') !== Str::slug($old)) {
                                                        return;
                                                    }

                                                    $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                    $state = $kodepos;

                                                    foreach ($state as $state) {
                                                        $set('al_w_kodepos', Str::substr($state, 12, 5));
                                                    }
                                                }),

                                        ]),

                                    Grid::make(4)
                                        ->schema([

                                            TextInput::make('al_w_kodepos')
                                                ->label('Kodepos')
                                                ->disabled()
                                                ->required()
                                                ->dehydrated()
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),
                                        ]),

                                    Grid::make(4)
                                        ->schema([


                                            TextInput::make('al_w_rt')
                                                ->label('RT')
                                                ->helperText('Isi 0 jika tidak ada RT/RW')
                                                ->required()
                                                ->numeric()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_w_kodepos') == null)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                            TextInput::make('al_w_rw')
                                                ->label('RW')
                                                ->helperText('Isi 0 jika tidak ada RT/RW')
                                                ->required()
                                                ->numeric()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_w_kodepos') == null)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            Textarea::make('al_w_alamat')
                                                ->label('Alamat')
                                                ->required()
                                                ->disabled(fn(Get $get) =>
                                                $get('al_w_kodepos') == null)
                                                ->hidden(fn(Get $get) =>
                                                $get('al_w_tgldi_ln_id') != 2 ||
                                                    $get('w_status_id') != 3),

                                        ]),



                                ])->compact()
                            // ->collapsed(fn (Get $get): bool => $get('is_collapse')),

                            // end of action steps
                        ])
                        ->closeModalByEscaping(false)
                        ->button()
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Tutup'))
                        ->hidden(function (Walisantri $record) {
                            // dd($record->is_collapse);
                            if ($record->is_collapse == false) {
                                return true;
                            } elseif ($record->is_collapse == true) {
                                return false;
                            }
                        })->closeModalByEscaping(false)
                    // ->button()
                ]
            );
    }
}
