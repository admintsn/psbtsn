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

class StatusBaru extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;


    public $tahap3 = '';

    public function cektahaptiga()
    {

        $cekuser = User::where('username', $this->tahap3)
            ->count();

        $cekpendaftar = Santri::where('kartu_keluarga', $this->tahap3)
            ->where('jenispendaftar', 'Baru')
            ->where('status_tahap', 'Diterima')
            ->count();

        if ($cekuser === 0) {
            throw ValidationException::withMessages([
                'tahap3' => trans('auth.failed'),
            ]);
            // Form Naik Qism, jika tahap1 ada
        } elseif ($cekpendaftar < 1) {
            throw ValidationException::withMessages([
                'tahap3' => trans('auth.belumtahap3'),
            ]);
            // Form Naik Qism, jika tahap1 ada
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Santri::where('kartu_keluarga', $this->tahap3)
                ->where('jenispendaftar', 'Baru')
                ->where(function ($query) {
                    $query->where('status_tahap', 'Diterima');
                        // ->orWhere('status_tahap', 'Tidak Diterima');
                }))
            ->heading('Status Penerimaan Santri Baru')
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

                    TextColumn::make('status_tahap')
                        ->label('Status Tahap 1')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Lolos' => 'success',
                            'Tidak Lolos' => 'danger',
                            'Diterima' => 'success',
                            'Tidak Diterima' => 'danger',
                        })
                        ->grow(false)
                        ->size(TextColumnSize::Large)
                        ->description(fn ($record): string => "Status:", position: 'above'),

                        TextColumn::make('pengumuman')
                        ->label('Rekomendasi')
                        ->default(new HtmlString('<br>Tahap berikutnya adalah <strong>TAHAP PENGIRIMAN DOKUMEN</strong> dan <strong>TAHAP PENGUMPULAN PEMBAYARAN ADMINISTRASI AWAL</strong>
                        <br>
                        <br>
                        Rincian tahap tersebut akan diumumkan oleh masing-masing penanggung jawab qism'))
                        ->grow(false),




                ])

            ])
            ->actions([])
            ->paginated(false)
            ->emptyStateHeading('Klik Tombol CEK STATUS');
    }

    public function render(): View
    {
        // $data = Santri::where('kartu_keluarga', $this->tahap1)
        // ->where('jenispendaftar', '!=', null)->first();

        $data = Santri::where('kartu_keluarga', $this->tahap3)
            ->where('jenispendaftar', 'Baru')
            ->where('tahap', 'Tahap 3')->first();

        $cekditerima = Santri::where('kartu_keluarga', $this->tahap3)
            ->where('jenispendaftar', 'Baru')
            ->where('status_tahap', 'Diterima')->count();

        return view('livewire.statusbaru', [
            'data' => $data,
            'cekditerima' => $cekditerima,
        ]);
    }
}
