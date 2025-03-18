<?php

namespace App\Livewire;

use App\Models\Pendaftar;
use App\Models\Santri;
use App\Models\Shop\Product;
use App\Models\User;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Livewire\Attributes\On;
use PhpParser\Node\Stmt\Label;
use Illuminate\Validation\ValidationException;

class StatusNaikQism extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;


    public $ceknaikqism = '';

    public function cekqism()
    {

        $cekuser = User::where('username', $this->ceknaikqism)
            ->count();

        $cekpendaftar = Santri::where('kartu_keluarga', $this->ceknaikqism)
            ->where('jenis_pendaftar_id', 2)
            ->count();

        if ($cekuser === 0) {
            throw ValidationException::withMessages([
                'naikqismmsg' => trans('auth.failed'),
            ]);
            // Form Naik Qism, jika tahap1 ada
        } elseif ($cekpendaftar === 0) {
            throw ValidationException::withMessages([
                'naikqismmsg' => trans('auth.bukanpendaftarlama'),
            ]);
            // Form Naik Qism, jika tahap1 ada
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Santri::where('kartu_keluarga', $this->ceknaikqism)
                ->where('jenis_pendaftar_id', 2))
            // ->where(function ($query) {
            //     $query->where('tahap_pendaftaran_id', 1)
            //         ->orWhere('tahap_pendaftaran_id', 2);
            // }))
            ->heading('Status Pendaftaran')
            ->columns([
                Stack::make([
                    TextColumn::make('No.')
                        ->rowIndex()
                        ->grow(false)
                        ->description(fn($record): string => "No.", position: 'above'),

                    TextColumn::make('nama_lengkap')
                        ->label('Nama')
                        ->grow(false)
                        ->size(TextColumn\TextColumnSize::Large)
                        ->weight(FontWeight::Bold)
                        ->description(fn($record): string => "Nama:", position: 'above'),

                    TextColumn::make('qism_detail.qism_detail')
                        ->label('Qism')
                        ->grow(false)
                        ->description(fn($record): string => "Mendaftar ke Qism", position: 'above'),

                    TextColumn::make('kelas.kelas')
                        ->label('Kelas')
                        ->grow(false),

                    TextColumn::make('statusPendaftaran.status_pendaftaran')
                        ->label('Status Tahap 1')
                        ->badge()
                        ->default(function ($record) {
                            if ($record->daftarnaikqism == 'Mendaftar') {
                                return ('Proses Seleksi');
                            } elseif ($record->daftarnaikqism == 'Belum Mendaftar') {
                                return ('Belum Mendaftar');
                            }
                        })
                        ->color(fn(string $state): string => match ($state) {
                            'Lolos' => 'success',
                            'Tidak Lolos' => 'danger',
                            'Diterima' => 'success',
                            'Tidak Diterima' => 'info',
                            'Proses Seleksi' => 'info',
                            'Belum Mendaftar' => 'danger',
                        })
                        ->formatStateUsing(function ($record, $state) {
                            if ($state == 'Tidak Diterima') {
                                return 'Lolos';
                            } elseif ($state == 'Lolos') {
                                return 'Lolos';
                            } elseif ($state == 'Tidak Lolos') {
                                return 'Tidak Lolos';
                            } elseif ($state == 'Diterima') {
                                return 'Diterima';
                            } elseif ($state == 'Proses Seleksi') {
                                return 'Proses Seleksi';
                            } elseif ($state == 'Belum Mendaftar') {
                                return 'Belum Mendaftar';
                            }
                        })
                        ->grow(false)
                        ->size(TextColumn\TextColumnSize::Large)
                        ->weight(FontWeight::Bold)
                        ->description(fn($record): string => "Status:", position: 'above'),

                    // TextColumn::make('tahapPendaftaran.tahap_pendaftaran')
                    //     ->label('Tahap')
                    //     ->grow(false)
                    //     ->description(fn($record): string => "Tahap saat ini:", position: 'above'),

                    // TextColumn::make('deskripsitahap')
                    //     ->label('Pengumuman')
                    //     ->grow(false),

                    TextColumn::make('jenisPendaftaran.jenis_pendaftaran')
                        ->label('Jenis')
                        ->grow(false)
                        ->description(fn($record): string => "Jenis:", position: 'above'),


                ])

            ])
            ->defaultSort('nama_lengkap')
            ->actions([
                // Action::make('Login')
                // ->url('//siakad.tsn.ponpes.id')
                // ->button()
                // ->openUrlInNewTab()
                // ->hidden(fn ($record) => $record->tahap !== 'Tahap 2')
                // ->extraAttributes([
                //     'class' => 'bg-tsn-accent text-black focus:bg-tsn-bg',
                // ])
            ])
            ->paginated(false)
            ->emptyStateHeading('Klik Tombol CEK');
    }

    public function render(): View
    {
        // $data = Santri::where('kartu_keluarga', $this->tahap1)
        // ->where('jenispendaftar', '!=', null)->first();

        $datanaikqism = Santri::where('kartu_keluarga', $this->ceknaikqism)
            ->where('jenis_pendaftar_id', 2)->first();

        $cekditerimanaikqism = Santri::where('kartu_keluarga', $this->ceknaikqism)
            ->where('jenis_pendaftar_id', 2)->first();

        return view('livewire.statusnaikqism', [
            'datanaikqism' => $datanaikqism,
            'cekditerimanaikqism' => $cekditerimanaikqism,
        ]);
    }
}
