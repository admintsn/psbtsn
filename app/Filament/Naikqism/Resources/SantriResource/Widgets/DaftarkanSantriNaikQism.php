<?php

namespace App\Filament\Naikqism\Resources\SantriResource\Widgets;

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
use Filament\Tables\Columns\Layout\Stack;

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

        return $table
            ->emptyStateHeading('Belum ada data ananda')
            ->emptyStateDescription('Belum ada data ananda yang bisa naik qism')
            ->emptyStateIcon('heroicon-o-book-open')
            ->query(

                Santri::where('walisantri_id', $walisantri->id)->where('jenis_pendaftar_id', 2)->whereHas('statussantri', function ($query) {
                    $query->where('stat_santri_id', 3);
                })
            )
            ->columns([
                Stack::make([
                    TextColumn::make('index')
                        ->description(fn($record): string => "Nomor", position: 'above')
                        ->rowIndex(),

                    TextColumn::make('nama_lengkap')
                        ->description(fn($record): string => "Nama Calon Santri:", position: 'above'),

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

                    TextColumn::make('daftarnaikqism')
                        ->description(fn($record): string => "Status:", position: 'above'),

                    TextColumn::make('qism.qism')
                        ->description(fn($record): string => "ke Qism:", position: 'above'),

                    TextColumn::make('qism_detail.qism_detail')
                        ->description(fn($record): string => "", position: 'above'),
                ])

            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make()
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
                    ->modalWidth('full')
                    // ->stickyModalHeader()
                    ->button()
                    ->closeModalByClickingAway(false)
                    ->closeModalByEscaping(false)
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal'))
                    ->steps([

                        Step::make('1. DAFTAR NAIK QISM')
                            ->schema([

                                Hidden::make('tahap_pendaftaran_id')
                                    ->default(1),

                                Group::make()
                                    ->relationship('statussantri')
                                    ->schema([
                                        Hidden::make('ket_status')
                                            ->default('NaikQism'),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">SANTRI</p>
                                                </div>')),

                                TextInput::make('nama_lengkap')
                                    ->label('Nama Lengkap')
                                    ->disabled()
                                    ->required(),

                                TextInput::make('nik')
                                    ->label('NIK Santri')
                                    ->length(16)
                                    ->disabled()
                                    ->required(),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg">QISM SAAT INI</p>
                                                </div>')),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('qism_tujuan')
                                            ->label('Qism saat ini')
                                            ->disabled()
                                            ->dehydrated()
                                            ->native(false)
                                            ->options(function ($record) {
                                                $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();

                                                $qism = KelasSantri::where('santri_id', $record->id)->where('tahun_berjalan_id', $tahunberjalanaktif->id)->first();

                                                return Qism::where('id', $qism->qism_id)->pluck('qism', 'id');
                                            })
                                            ->required(),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        Select::make('qism_detail_tujuan')
                                            ->label('Kelas saat ini')
                                            ->disabled()
                                            ->native(false)
                                            ->options(function ($record) {
                                                $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();

                                                $qismdetail = KelasSantri::where('santri_id', $record->id)->where('tahun_berjalan_id', $tahunberjalanaktif->id)->first();

                                                return [1 => $qismdetail->qism_detail->qism_detail];
                                            })
                                            ->required(),
                                    ]),
                                Grid::make(4)
                                    ->schema([

                                        Select::make('kelas_tujuan')
                                            ->label('Kelas saat ini')
                                            ->disabled()
                                            ->native(false)
                                            ->options(function ($record) {
                                                $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();

                                                $kelas = KelasSantri::where('santri_id', $record->id)->where('tahun_berjalan_id', $tahunberjalanaktif->id)->first();

                                                return [1 => $kelas->kelas->kelas];
                                            })
                                            ->required(),
                                    ]),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg"><strong>DIDAFTARKAN UNTUK NAIK KE QISM</strong></p>
                                                </div>')),

                                TextInput::make('qism')
                                    ->label('Qism tujuan')
                                    ->disabled()
                                    ->required()
                                    ->live(),

                                TextInput::make('qism_detail')
                                    ->label('')
                                    ->disabled()
                                    ->required()
                                    ->live(),
                            ]),
                        // end of step 1

                        Step::make('2. KUESIONER KESEHATAN')
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>KUESIONER KESEHATAN</strong></p>
                                                </div>')),
                                Group::make()
                                    ->relationship('pendaftar')
                                    ->schema([
                                        Radio::make('ps_kkes_sakit_serius')
                                            ->label('1. Apakah ananda pernah mengalami sakit yang cukup serius?')
                                            ->required()
                                            ->default('Ya')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ])
                                            ->live(),

                                        TextArea::make('ps_kkes_sakit_serius_nama_penyakit')
                                            ->label('Jika iya, kapan dan penyakit apa?')
                                            ->required()
                                            ->default('asdad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_sakit_serius') !== 'Ya'
                                            ),

                                        Radio::make('ps_kkes_terapi')
                                            ->label('2. Apakah ananda pernah atau sedang menjalani terapi kesehatan?')
                                            ->required()
                                            ->default('Ya')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ])
                                            ->live(),

                                        TextArea::make('ps_kkes_terapi_nama_terapi')
                                            ->label('Jika iya, kapan dan terapi apa?')
                                            ->required()
                                            ->default('asdasd')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_terapi') !== 'Ya'
                                            ),

                                        Radio::make('ps_kkes_kambuh')
                                            ->label('3. Apakah ananda memiliki penyakit yang dapat/sering kambuh?')
                                            ->required()
                                            ->default('Ya')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ])
                                            ->live(),

                                        TextArea::make('ps_kkes_kambuh_nama_penyakit')
                                            ->label('Jika iya, penyakit apa?')
                                            ->required()
                                            ->default('asdad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_kambuh') !== 'Ya'
                                            ),

                                        Radio::make('ps_kkes_alergi')
                                            ->label('4. Apakah ananda memiliki alergi terhadap perkara-perkara tertentu?')
                                            ->required()
                                            ->default('Ya')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ])
                                            ->live(),

                                        TextArea::make('ps_kkes_alergi_nama_alergi')
                                            ->label('Jika iya, sebutkan!')
                                            ->required()
                                            ->default('asdadsd')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_alergi') !== 'Ya'
                                            ),

                                        Radio::make('ps_kkes_pantangan')
                                            ->label('5. Apakah ananda mempunyai pantangan yang berkaitan dengan kesehatan?')
                                            ->required()
                                            ->default('Ya')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ])
                                            ->live(),

                                        TextArea::make('ps_kkes_pantangan_nama')
                                            ->label('Jika iya, sebutkan dan jelaskan alasannya!')
                                            ->required()
                                            ->default('asdadssad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_pantangan') !== 'Ya'
                                            ),

                                        Radio::make('ps_kkes_psikologis')
                                            ->label('6. Apakah ananda pernah mengalami gangguan psikologis (depresi dan gejala-gejalanya)?')
                                            ->required()
                                            ->default('Ya')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ])
                                            ->live(),

                                        TextArea::make('ps_kkes_psikologis_kapan')
                                            ->label('Jika iya, kapan?')
                                            ->required()
                                            ->default('asdad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_psikologis') !== 'Ya'
                                            ),

                                        Radio::make('ps_kkes_gangguan')
                                            ->label('7. Apakah ananda pernah mengalami gangguan jin?')
                                            ->required()
                                            ->default('Ya')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ])
                                            ->live(),

                                        TextArea::make('ps_kkes_gangguan_kapan')
                                            ->label('Jika iya, kapan?')
                                            ->required()
                                            ->default('asdadsad')
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kkes_gangguan') !== 'Ya'
                                            ),

                                    ]),
                            ]),
                        // end of step 2

                        Step::make('3. KUESIONER KEMANDIRIAN')
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg"><strong>KUESIONER KEMANDIRIAN</strong></p>
                                                    <br>
                                                    <p class="text-sm"><strong>Kuesioner ini khusus untuk calon santri Pra Tahfidz kelas 1-4</strong></p>
                                                </div>')),
                                Group::make()
                                    ->relationship('pendaftar')
                                    ->schema([
                                        Radio::make('ps_kkm_bak')
                                            ->label('1. Apakah ananda sudah bisa BAK sendiri?')
                                            // ->required()
                                            ->default('Ya')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ]),

                                        Radio::make('ps_kkm_bab')
                                            ->label('2. Apakah ananda sudah bisa BAB sendiri?')
                                            // ->required()
                                            ->default('Ya')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ]),

                                        Radio::make('ps_kkm_cebok')
                                            ->label('3. Apakah ananda sudah bisa cebok sendiri?')
                                            // ->required()
                                            ->default('Ya')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ]),

                                        Radio::make('ps_kkm_ngompol')
                                            ->label('4. Apakah ananda masih mengompol?')
                                            // ->required()
                                            ->default('Ya')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ]),

                                        Radio::make('ps_kkm_disuapin')
                                            ->label('5. Apakah makan ananda masih disuapi?')
                                            // ->required()
                                            ->default('Ya')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ]),

                                    ]),
                            ]),
                        // end of step 3

                        Step::make('4. KUESIONER KEMAMPUAN PEMBAYARAN ADMINISTRASI')
                            ->schema([

                                Placeholder::make('')
                                    ->content(new HtmlString('<div>
                                                    <p class="text-lg strong"><strong>KUESIONER KEMAMPUAN PEMBAYARAN ADMINISTRASI</strong></p>
                                                </div>')),

                                Placeholder::make('')
                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>RINCIAN BIAYA AWAL DAN SPP</strong></p>
                                                </div>')),
                                Group::make()
                                    ->relationship('pendaftar')
                                    ->schema([
                                        Placeholder::make('')
                                            ->content(new HtmlString(
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
                                                <td class="text-end">300.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 3 -->
                                            <tr>
                                                <th class="text-start">Uang Sarpras     </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">200.000</td>
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
                                                <td class="text-end"><strong>800.000</strong></td>
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
                                                <td class="text-end">300.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 3 -->
                                            <tr>
                                                <th class="text-start">Uang Sarpras     </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">200.000</td>
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
                                                <td class="text-end"><strong>900.000</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk SPP bulan pertama</td>
                                            </tr>
                                            </tbody>
                                                </table>
                                            </div>
                                            </div>

                                            <br>

                                            <div class="border rounded-xl p-4">
                                            <table>
                                                <!-- head -->
                                                <thead>
                                                    <tr class="border-b">
                                                        <th class="text-lg text-tsn-header" colspan="4">QISM PT (menginap), TQ, IDD, MTW, TN</th>
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
                                                <td class="text-end">300.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 3 -->
                                            <tr>
                                                <th class="text-start">Uang Sarpras     </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">200.000</td>
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
                                                <td class="text-end"><strong>1.150.000</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk SPP bulan pertama</td>
                                            </tr>
                                            </tbody>
                                                </table>
                                            </div>'
                                            )),

                                        Radio::make('ps_kadm_status')
                                            ->label('Status anak didik terkait dengan administrasi')
                                            ->required()
                                            ->default('Santri/Santriwati tidak mampu')
                                            ->options([
                                                'Santri/Santriwati mampu (tidak ada permasalahan biaya)' => 'Santri/Santriwati mampu (tidak ada permasalahan biaya)',
                                                'Santri/Santriwati tidak mampu' => 'Santri/Santriwati tidak mampu',
                                            ])
                                            ->live(),

                                        Placeholder::make('')
                                            ->content(new HtmlString('<div class="border-b">
                                                                        <p><strong>Bersedia memenuhi persyaratan sebagai berikut:</strong></p>
                                                                    </div>'))
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status') !== 'Santri/Santriwati tidak mampu'
                                            ),

                                        Radio::make('ps_kadm_surat_subsidi')
                                            ->label('1. Wali harus membuat surat permohonan subsidi/ keringanan biaya administrasi')
                                            ->required()
                                            ->default('Bersedia')
                                            ->options([
                                                'Bersedia' => 'Bersedia',
                                                'Tidak bersedia' => 'Tidak bersedia',
                                            ])
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status') !== 'Santri/Santriwati tidak mampu'
                                            ),

                                        Radio::make('ps_kadm_surat_kurang_mampu')
                                            ->label('2. Wali harus menyertakan surat keterangan kurang mampu dari ustadz salafy setempat SERTA dari aparat pemerintah setempat, yang isinya menyatakan bhw mmg kluarga tersebut "perlu dibantu"')
                                            ->required()
                                            ->default('Bersedia')
                                            ->options([
                                                'Bersedia' => 'Bersedia',
                                                'Tidak bersedia' => 'Tidak bersedia',
                                            ])
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status') !== 'Santri/Santriwati tidak mampu'
                                            ),

                                        Radio::make('ps_kadm_atur_keuangan')
                                            ->label('3. Keuangan ananda akan dipegang dan diatur oleh Mahad')
                                            ->required()
                                            ->default('Bersedia')
                                            ->options([
                                                'Bersedia' => 'Bersedia',
                                                'Tidak bersedia' => 'Tidak bersedia',
                                            ])
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status') !== 'Santri/Santriwati tidak mampu'
                                            ),

                                        Radio::make('ps_kadm_penentuan_subsidi')
                                            ->label('4. Yang menentukan bentuk keringanan yang diberikan adalah Mahad')
                                            ->required()
                                            ->default('Bersedia')
                                            ->options([
                                                'Bersedia' => 'Bersedia',
                                                'Tidak bersedia' => 'Tidak bersedia',
                                            ])
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status') !== 'Santri/Santriwati tidak mampu'
                                            ),

                                        Radio::make('ps_kadm_hidup_sederhana')
                                            ->label('5. Ananda harus berpola hidup sederhana agar tidak menimbulkan pertanyaan pihak luar')
                                            ->required()
                                            ->default('Bersedia')
                                            ->options([
                                                'Bersedia' => 'Bersedia',
                                                'Tidak bersedia' => 'Tidak bersedia',
                                            ])
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status') !== 'Santri/Santriwati tidak mampu'
                                            ),

                                        Radio::make('ps_kadm_kebijakan_subsidi')
                                            ->label('6. Kebijakan subsidi bisa berubah sewaktu waktu')
                                            ->required()
                                            ->default('Bersedia')
                                            ->options([
                                                'Bersedia' => 'Bersedia',
                                                'Tidak bersedia' => 'Tidak bersedia',
                                            ])
                                            ->hidden(
                                                fn(Get $get) =>
                                                $get('ps_kadm_status') !== 'Santri/Santriwati tidak mampu'
                                            ),
                                    ]),
                            ]),
                        // end of step 4
                    ])
                    ->after(function ($record) {
                        Notification::make()
                            ->success()
                            ->title('Alhamdulillah ananda telah didaftarkan untuk naik qism')
                            ->body('Keluar jika telah selesai')
                            ->persistent()
                            ->color('success')
                            ->send();

                        $walisantri = Santri::find($record->id);
                        $walisantri->daftarnaikqism = 'Mendaftar';
                        $walisantri->qism_tujuan = $record->qism;
                        $walisantri->qism_detail_tujuan = $record->qism_detail;
                        $walisantri->save();
                    })
                    ->hidden(function (Santri $record) {
                        //     // dd($record->is_collapse);
                        if ($record->daftarnaikqism == 'Mendaftar') {
                            return true;
                        } elseif ($record->daftarnaikqism != 'Mendaftar') {
                            return false;
                        }
                    }),

                Tables\Actions\ViewAction::make()
                    ->label('Lihat Data Santri')
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
            ]);
    }
}
