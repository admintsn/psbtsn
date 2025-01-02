<div class="w-full justify-center flex text-start">
    <div class="flex px-3 py-3 w-fit justify-center justify-self-center">

        <div class="grid grid-cols-1 card lg:card-side bg-base-100 shadow-xl px-4 py-4">
            <div class="card-body">
                <h2 class="card-title self-center text-center text-tsn-header">Cek Status Tahap 1</h2>
            </div>
            <div>
                <form wire:submit="cek">
                    <!--Username -->
                    <div class="pt-4">
                        <x-input-label for="tahap1" :value="__('Masukkan nomor KARTU KELUARGA')" />
                        <x-text-input id="tahap1" class="block mt-1 w-full" type="text" name="tahap1" minlength="16"
                            maxlength="16" :value="old('tahap1')" required autocomplete="tahap1" wire:model="tahap1" />
                        <x-input-error :messages="$errors->get('tahap1')" class="mt-2" />
                        {{--
                        <x-input-error :messages="$errors->get('username')" class="mt-2" /> --}}
                    </div>


                    <div class="flex items-center justify-center mt-4">
                        <x-primary-button class="ms-3">
                            {{ __('Cek Status Tahap 1') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            @if($data === null)

            @endif
            @if($data !== null)
            <p><br></p>
            @if($data->status_tahap === null)
            <p>Jika informasi status "Lolos"/"Tidak Lolos" belum ada, berarti masih dalam proses seleksi</p>
            <p><br></p>
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
                    <h2 class="self-center text-center text-tsn-header"><br>Dokumen dalam bentuk file foto atau pdf
                        ukuran maksimal 5 mb</h2>

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
                                    <th>7</th>
                                    <td>Sertifikat Vaksin Covid-19 (vaksin terakhir)</td>
                                </tr>
                                <tr>
                                    <th class="align-text-top">8</th>
                                    <td>Surat pernyataan kesanggupan bermaterai
                                        <br>
                                        <br>
                                        Unduh format dokumen surat pernyataan kesanggupan:<br><br>
                                        <a href="/contohsurat/TSN-TA-PSB-Surat Kesanggupan Orang Tua.pdf" role="button"
                                            class="btn border-tsn-accent bg-white w-20 m-1" target="_blank">TA</a>
                                        <a href="/contohsurat/TSN-PT-PSB-Surat Kesanggupan Orang Tua (Menginap).pdf"
                                            role="button" class="btn border-tsn-accent bg-white w-20 m-1"
                                            target="_blank">PT</a>
                                        <a href="/contohsurat/TSN-PT-PSB-Surat Kesanggupan Orang Tua (Fullday dengan makan).pdf"
                                            role="button" class="btn border-tsn-accent bg-white w-20 m-1"
                                            target="_blank">PT
                                            (mkn)</a>
                                        <a href="/contohsurat/TSN-PT-PSB-Surat Kesanggupan Orang Tua (Fullday tanpa makan).pdf"
                                            role="button" class="btn border-tsn-accent bg-white w-20 m-1"
                                            target="_blank">PT
                                            (tnp mkn)</a>
                                        <a href="/contohsurat/TSN-TQ-PSB-Surat Kesanggupan Orang Tua.pdf" role="button"
                                            class="btn border-tsn-accent bg-white w-20 m-1" target="_blank">TQ</a>
                                        <a href="/contohsurat/TSN-IDD-PSB-Surat Kesanggupan Orang Tua.pdf" role="button"
                                            class="btn border-tsn-accent bg-white w-20 m-1" target="_blank">IDD</a>
                                        <a href="/contohsurat/TSN-TNMTW-PSB-Surat Kesanggupan Orang Tua.pdf"
                                            role="button" class="btn border-tsn-accent bg-white w-20 m-1"
                                            target="_blank">MTW</a>
                                        <a href="/contohsurat/TSN-TNMTW-PSB-Surat Kesanggupan Orang Tua.pdf"
                                            role="button" class="btn border-tsn-accent bg-white w-20 m-1"
                                            target="_blank">TN</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>9</th>
                                    <td>Surat Permohonan Keringanan Administrasi (bagi yang mengajukan
                                        keringanan)</td>
                                </tr>
                                <tr>
                                    <th>10</th>
                                    <td>Surat Keterangan tidak mampu dari Ustadz setempat (bagi yang
                                        mengajukan keringanan)</td>
                                </tr>
                                <tr>
                                    <th>11</th>
                                    <td>Surat Keterangan tidak mampu dari aparat pemerintah setempat (bagi
                                        yang mengajukan keringanan)</td>
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
