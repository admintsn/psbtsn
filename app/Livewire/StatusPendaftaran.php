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
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\Attributes\On;
use PhpParser\Node\Stmt\Label;
use Illuminate\Validation\ValidationException;

class StatusPendaftaran extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;


    public $tahap1 = '';

    public function cek()
    {

        $cekuser = User::where('username', $this->tahap1)
            ->count();

        $cekpendaftar = Santri::where('kartu_keluarga', $this->tahap1)
            ->where('jenispendaftar', '!=', null)
            ->count();

        if ($cekuser === 0) {
            throw ValidationException::withMessages([
                'tahap1' => trans('auth.failed'),
            ]);
            // Form Naik Qism, jika tahap1 ada
        } elseif ($cekpendaftar === 0) {
            throw ValidationException::withMessages([
                'tahap1' => trans('auth.bukanpendaftar'),
            ]);
            // Form Naik Qism, jika tahap1 ada
        }

        Santri::where('kartu_keluarga', $this->tahap1)
            ->where('jenispendaftar', '!=', null);

        $data = Santri::where('kartu_keluarga', $this->tahap1)
            ->where('jenispendaftar', '!=', null);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Santri::where('kartu_keluarga', $this->tahap1)
                ->where('jenispendaftar', '!=', null))
            ->heading('Status Pendaftaran')
            ->columns([
                Stack::make([
                    TextColumn::make('No.')
                        ->rowIndex()
                        ->grow(false)
                        ->description(fn ($record): string => "No.", position: 'above'),

                    TextColumn::make('nama_lengkap')
                        ->label('Nama')
                        ->grow(false)
                        ->description(fn ($record): string => "Nama:", position: 'above'),

                    TextColumn::make('qism_detail')
                        ->label('Qism')
                        ->grow(false)
                        ->description(fn ($record): string => "Mendaftar ke Qism", position: 'above'),

                    TextColumn::make('kelas')
                        ->label('Kelas')
                        ->grow(false),

                    TextColumn::make('tahap')
                        ->label('Tahap')
                        ->grow(false)
                        ->description(fn ($record): string => "Tahap saat ini:", position: 'above'),

                    TextColumn::make('status_tahap')
                        ->label('Status Tahap 1')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Lolos' => 'success',
                            'Tidak Lolos' => 'danger',
                        })
                        ->grow(false)
                        ->description(fn ($record): string => "Status:", position: 'above'),

                    TextColumn::make('deskripsitahap')
                        ->label('Pengumuman')
                        ->grow(false),



                    TextColumn::make('jenispendaftar')
                        ->label('Jenis')
                        ->grow(false)
                        ->description(fn ($record): string => "Jenis:", position: 'above'),


                ])

            ])
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
            ->emptyStateHeading('Klik Tombol CEK STATUS');
    }

    public function render(): View
    {
        // $data = Santri::where('kartu_keluarga', $this->tahap1)
        // ->where('jenispendaftar', '!=', null)->first();

        $data = Santri::where('kartu_keluarga', $this->tahap1)
            ->where('jenispendaftar', '!=', null)->first();

        $tahap2 = Santri::where('kartu_keluarga', $this->tahap1)
            ->where('jenispendaftar', '!=', null)
            ->where('tahap', 'Tahap 2')->count();

        return view('livewire.statuspendaftaran', [
            'data' => $data,
            'tahap2' => $tahap2,
        ]);
    }
}
