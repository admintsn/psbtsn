<div class="timeline-start md:text-start">
    <div class="w-full justify-center flex text-start">
        <div class="flex px-3 py-3 w-fit justify-center justify-self-center">
            <div class="grid grid-cols-1 card lg:card-side bg-base-100 shadow-xl px-4 py-4">
                <div class="card-body">
                    <div class="text-lg text-start"><strong>Pengumuman Seleksi Tahap 1:</strong>
                        <br>
                        15 Januari 2025
                    </div>
                </div>
                <div>
                    <form wire:submit="cek">
                        <!--Username -->
                        <div class="pt-4">
                            <x-input-label for="tahap1" :value="__('Masukkan nomor KARTU KELUARGA')" />
                            <x-text-input id="tahap1" class="block mt-1 w-full" type="text" name="tahap1" minlength="16"
                                maxlength="16" :value="old('tahap1')" required autocomplete="tahap1"
                                wire:model="tahap1" />
                            <x-input-error :messages="$errors->get('tahap1')" class="mt-2" />
                            {{--
                            <x-input-error :messages="$errors->get('username')" class="mt-2" /> --}}
                        </div>


                        <div class="flex items-center justify-center mt-4">
                            <x-primary-button class="ms-3">
                                {{ __('Cek') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>




                @if($data === null)

                @endif
                @if($data !== null)
                <p><br></p>
                @if($data->status_tahap === null)
                @endif
                <div>
                    <div>
                        {{ $this->table }}
                    </div>
                    @if($tahap2 === 0)

                    @elseif($tahap2 !== 0)
                    <p><br></p>
                    <div>
                        <h2 class="self-center text-center text-tsn-header"><br>Persiapkan dokumen-dokumen berikut untuk
                            diupload di
                            formulir Tahap 2</h2>
                        <h2 class="self-center text-center text-tsn-header"><br>Dokumen dalam bentuk file PDF
                            ukuran maksimal 2 mb</h2>
                        <h2 class="self-center text-center text-tsn-header"><br>Disarankan dokumen discan menggunakan
                            aplikasi "Office Lens" -> <a
                                href="https://play.google.com/store/apps/details?id=com.microsoft.office.officelens"
                                class="link" target="_blank">Unduh di Playstore</a></h2>

                        <div class="w-fit">
                            <table class="table w-auto">
                                <!-- head -->
                                <thead>
                                    <tr class="border-tsn-header">
                                        <th class="text-lg text-tsn-header" colspan="2">DOKUMEN-DOKUMEN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>1</th>
                                        <td>Kartu Keluarga</td>
                                    </tr>
                                    <tr>
                                        <th>2</th>
                                        <td>Akte Kelahiran</td>
                                    </tr>
                                    <tr>
                                        <th>3</th>
                                        <td>Surat Rekomendasi dari sekolah sebelumnya</td>
                                    </tr>
                                    <tr>
                                        <th>4</th>
                                        <td>Ijazah atau Laporan Hasil Evaluasi Belajar dari sekolah sebelumnya
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>5</th>
                                        <td>Surat Keterangan Ta'lim untuk walisantri</td>
                                    </tr>
                                    <tr>
                                        <th>6</th>
                                        <td>Surat Kuasa dari Orangtua Kandung (Jika yang mendaftarkan adalah
                                            wali)</td>
                                    </tr>
                                    <tr>
                                        <th class="align-text-top">7</th>
                                        <td>Surat pernyataan kesanggupan bermaterai
                                            <br>
                                            <br>
                                            Unduh format dokumen surat pernyataan kesanggupan:<br><br>
                                            <table class="table max-w-fit bg-white">
                                                <!-- head -->
                                                <thead>
                                                    <tr class="border-tsn-header">
                                                        <th class="text-tsn-header">Qism</th>
                                                        <th class="text-tsn-header">Unduh</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-tsn-header">Tarbiyatul Aulaad</td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-TA Surat Kesanggupan Orang Tua.pdf" target="_blank">@svg('heroicon-o-cloud-arrow-down',
                                                                'w-7 h-7', ['style'
                                                                => 'color: #274043'])</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-tsn-header">Pra Tahfidz (Kelas 4 s/d Kelas 6)
                                                        </td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-PT Surat Kesanggupan Orang Tua (menginap).pdf" target="_blank">@svg('heroicon-o-cloud-arrow-down',
                                                                'w-7 h-7', ['style'
                                                                => 'color: #274043'])</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-tsn-header">Pra Tahfidz (Fullday tanpa makan)
                                                        </td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-PT Surat Kesanggupan Orang Tua (fullday tanpa makan).pdf" target="_blank">@svg('heroicon-o-cloud-arrow-down',
                                                                'w-7 h-7', ['style'
                                                                => 'color: #274043'])</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-tsn-header">Pra Tahfidz (Fullday dengan makan)
                                                        </td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-PT Surat Kesanggupan Orang Tua (fullday dengan makan).pdf" target="_blank">@svg('heroicon-o-cloud-arrow-down',
                                                                'w-7 h-7', ['style'
                                                                => 'color: #274043'])</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-tsn-header">Tahfidzul Qur'an</td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-TQ Surat Kesanggupan Orang Tua.pdf" target="_blank">@svg('heroicon-o-cloud-arrow-down',
                                                                'w-7 h-7', ['style'
                                                                => 'color: #274043'])</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-tsn-header">I'dad Lughoh</td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-ID Surat Kesanggupan Orang Tua.pdf" target="_blank">@svg('heroicon-o-cloud-arrow-down',
                                                                'w-7 h-7', ['style'
                                                                => 'color: #274043'])</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-tsn-header">Mutawasithoh</td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-TNMTW Surat Kesanggupan Orang Tua.pdf" target="_blank">@svg('heroicon-o-cloud-arrow-down',
                                                                'w-7 h-7', ['style'
                                                                => 'color: #274043'])</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-tsn-header">Tarbiyatunnisaa</td>
                                                        <td><a
                                                                href="/contohsurat/PSB-TSN-2526-TNMTW Surat Kesanggupan Orang Tua.pdf" target="_blank">@svg('heroicon-o-cloud-arrow-down',
                                                                'w-7 h-7', ['style'
                                                                => 'color: #274043'])</a></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </td>
                                    </tr>
                                    <tr>
                                        <th>8</th>
                                        <td>Surat Permohonan Keringanan Administrasi (bagi yang mengajukan
                                            keringanan)</td>
                                    </tr>
                                    <tr>
                                        <th>9</th>
                                        <td>Surat Keterangan tidak mampu dari Ustadz setempat (bagi yang
                                            mengajukan keringanan)</td>
                                    </tr>
                                    <tr>
                                        <th>10</th>
                                        <td>Surat Keterangan tidak mampu dari aparat pemerintah setempat (bagi
                                            yang mengajukan keringanan)</td>
                                    </tr>
                                    <tr>
                                        <th>11</th>
                                        <td>Surat Keterangan Sehat dari RS/Puskesmas/Klinik</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>