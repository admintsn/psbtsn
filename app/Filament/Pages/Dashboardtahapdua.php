<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class DashboardTahapDua extends Dashboard
{

    protected static ?string $title = 'PSB TSN-TAHAP 2';

    protected static string $routePath = 'tahapdua';

    // public function getTitle(): string | Htmlable
    // {
    //     return __('PSB TSN');
    // }

    protected ?string $heading = "Ma'had Ta'dzimussunnah Sine Ngawi";
    protected ?string $subheading = "Penerimaan Santri Baru-Formulir Tahap 2";

    // protected static ?string $navigationLabel = '';

    use HasFiltersForm;
    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="">
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
                <td class="text-xs"><a href="https://wa.me/6282210862400">WA Admin Putra (Abu Hammaam)</a></td>
            </tr>
            <tr>
                <th><a href="https://wa.me/6285236459012"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                </svg>
                </a></th>
                <td class="text-xs"><a href="https://wa.me/6285236459012">WA Admin Putra (Abu Fathimah Hendi)</a></td>
            </tr>
            <tr>
                <th><a href="https://wa.me/6281333838691"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                </svg>
                </a></th>
                <td class="text-xs"><a href="https://wa.me/6281333838691">WA Admin Putra (Akh Irfan)</a></td>
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

                        </div>')),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                           </div>')),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="">
                            <p class="text-lg">Klik tombol "Mulai Upload" untuk mulai</p>
                           </div>')),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="">
                            <p class="text-md">Jika telah selesai, klik tombol pojok kanan atas, kemudian pilih "Keluar"</p>
                           </div>')),

                        Placeholder::make('')
                            ->content(new HtmlString('<div class="border-b">
                           </div>')),

                        Placeholder::make('')
                            ->content(new HtmlString('<div>
                            <p class="text-lg">Link download Dokumen Surat Pernyataan Kesanggupan:</p>
                           </div>')),

                        Actions::make([
                            Action::make('TA')
                                ->label('TA')
                                ->icon('heroicon-m-cloud-arrow-down')
                                ->color('primary')
                                ->outlined()
                                ->url('/contohsurat/TSN-TA-PSB-Surat Kesanggupan Orang Tua.pdf')
                                ->openUrlInNewTab(),

                            Action::make('PT')
                                ->label('PT')
                                ->icon('heroicon-m-cloud-arrow-down')
                                ->color('primary')
                                ->outlined()
                                ->url('/contohsurat/TSN-PT-PSB-Surat Kesanggupan Orang Tua (Menginap).pdf')
                                ->openUrlInNewTab(),

                            Action::make('PTFDM')
                                ->label('PT (fd-mkn)')
                                ->icon('heroicon-m-cloud-arrow-down')
                                ->color('primary')
                                ->outlined()
                                ->url('/contohsurat/TSN-PT-PSB-Surat Kesanggupan Orang Tua (Fullday dengan makan).pdf')
                                ->openUrlInNewTab(),

                            Action::make('PTFDTM')
                                ->label('PT (fd-tnpmkn)')
                                ->icon('heroicon-m-cloud-arrow-down')
                                ->color('primary')
                                ->outlined()
                                ->url('/contohsurat/TSN-PT-PSB-Surat Kesanggupan Orang Tua (Fullday tanpa makan).pdf')
                                ->openUrlInNewTab(),

                            Action::make('TQ')
                                ->label('TQ')
                                ->icon('heroicon-m-cloud-arrow-down')
                                ->color('primary')
                                ->outlined()
                                ->url('/contohsurat/TSN-TQ-PSB-Surat Kesanggupan Orang Tua.pdf')
                                ->openUrlInNewTab(),

                                Action::make('IDD')
                                ->label('IDD')
                                ->icon('heroicon-m-cloud-arrow-down')
                                ->color('primary')
                                ->outlined()
                                ->url('/contohsurat/TSN-IDD-PSB-Surat Kesanggupan Orang Tua.pdf')
                                ->openUrlInNewTab(),

                            Action::make('MTW')
                                ->label('MTW')
                                ->icon('heroicon-m-cloud-arrow-down')
                                ->color('primary')
                                ->outlined()
                                ->url('/contohsurat/TSN-TNMTW-PSB-Surat Kesanggupan Orang Tua.pdf')
                                ->openUrlInNewTab(),

                            Action::make('TN')
                                ->label('TN')
                                ->icon('heroicon-m-cloud-arrow-down')
                                ->color('primary')
                                ->outlined()
                                ->url('/contohsurat/TSN-TNMTW-PSB-Surat Kesanggupan Orang Tua.pdf')
                                ->openUrlInNewTab(),
                        ])






                    ])
            ]);
    }
}
