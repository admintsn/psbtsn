<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-BZJHVHE7EZ"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-BZJHVHE7EZ');
    </script>
    <meta charset="utf-8">

    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#274043">

    <title>@yield('title', 'PSB TSN')</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


    <!-- Scripts -->
    @filamentStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
</head>

<body class="font-raleway antialiased bg-tsn-bg no-scrollbar">
    <input type="text" autofocus="autofocus" style="display:none" />
    {{-- header --}}
    <div class="flex sticky top-0 h-25 z-50 bg-tsn-header border-b-4 border-tsn-accent justify-between">
        <div class="w-fit px-2">
            <a href="https://psb.tsn.ponpes.id/">
                <x-application-logo />
            </a>
        </div>
        <div class="w-fit"></div>
        <div class="w-fit mt-4 me-4">
            <a href="https://tsn.ponpes.id/">
                <figure><img src="\LogoTSN.png" alt="Album" class="w-16" /></figure>
            </a>
        </div>
    </div>

    @if(session()->has('message'))
    <div class="flex w-full justify-center bg-transparent py-4">
        <!-- The Modal -->
        <div id="myModal" class="modal-show bg-transparet w-full flex justify-center">
            <!-- Modal content -->
            <div
                class="bg-white w-fit p-2 flex flex-col rounded-xl shadow-xl border-2 border-tsn-header justify-center">
                <div class="flex justify-center">
                    <div class="flex">
                        @svg('heroicon-o-check-circle', 'w-10 h-10', ['style'
                        => 'color: #274043'])
                    </div>
                </div>
                <div class="flex">
                    <br>
                    <p class="text-center sm:text-xs lg:text-md">{{ session()->get('message') }}</p>
                    <br>
                </div>
                <div class="flex justify-center">
                    <a href="/tn/daftar" role="button" class="btn bg-tsn-accent focus:bg-tsn-bg">Tutup</a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="flex w-full pt-8 justify-center flex-col">
        <div>
            <p class="lg:text-3xl sm:text-2xl text-tsn-header font-raleway text-center"><strong>PENERIMAAN SANTRI
                    BARU</strong>
            </p>
        </div>
        <div>
            <p class="lg:text-3xl sm:text-2xl text-tsn-header font-raleway text-center"><strong>MA'HAD
                    TA'DZIMUSSUNNAH</strong>
            </p>
        </div>
        <div>
            <p class="lg:text-3xl sm:text-2xl text-tsn-header font-raleway text-center"><strong>SINE NGAWI</strong>
            </p>
            <br>
        </div>
        {{-- <div>
            <p class="lg:text-3xl sm:text-2xl text-tsn-header font-raleway text-center"><strong>PENDAFTARAN DIBUKA
                    TANGGAL 7 JANUARI 2025</strong>
            </p>
            <br>
        </div> --}}

        <div class="justify-center flex text-sm">
            <ul class="timeline timeline-snap-icon timeline-compact timeline-vertical">
                <li>
                    <div class="timeline-middle">
                        @svg('heroicon-s-check-circle', 'w-7 h-7', ['style'=> 'color: #274043'])
                    </div>
                    <div class="timeline-start md:text-start">
                        <div class="w-full justify-center flex text-start">
                            <div class="flex px-3 py-3 w-fit justify-center justify-self-center">
                                <div class="grid grid-cols-1 card lg:card-side bg-base-100 shadow-xl px-4 py-4">
                                    <div class="card-body">
                                        <h2 class="card-title text-start text-tsn-header">Mulai
                                            Pendaftaran
                                            Santri Lama
                                        </h2>
                                        <div class="text-lg text-start"><strong>Pengisian Formulir dan Upload
                                                Dokumen</strong>
                                            <br>
                                            10 Februari 2025
                                        </div>
                                    </div>
                                    <div>
                                        <!--Session Status -->
                                        <x-auth-session-status class="mb-4" :status="session('status')" />

                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf

                                            <!--Username -->
                                            <div class="pt-4">
                                                <x-input-label for="kk" :value="__('Masukkan nomor KARTU KELUARGA')" />
                                                <x-text-input id="kk" class="block mt-1 w-full" type="text" name="kk"
                                                    minlength="16" maxlength="16" :value="old('kk')" required
                                                    autocomplete="kk" />
                                                <x-input-error :messages="$errors->get('naikqism')" class="mt-2" />
                                            </div>


                                            <div class="flex items-center justify-center mt-4">
                                                <x-primary-button class="ms-3">
                                                    {{ __('Mulai') }}
                                                </x-primary-button>


                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <hr class="bg-tsn-header" />
                </li>
                <li>
                    <hr class="bg-tsn-header" />
                    <div class="timeline-middle">
                        @svg('heroicon-s-arrow-right-circle', 'w-7 h-7', ['style'=> 'color: #9e5d4b'])
                    </div>
                    @livewire('statusnaikqism')

                    <hr class="bg-tsn-header" />
                </li>
                {{-- <li>
                    <div class="timeline-middle">
                        @svg('heroicon-s-check-circle', 'w-7 h-7', ['style'=> 'color: #274043'])
                    </div>
                    <div class="timeline-start md:text-start">
                        <div class="w-full justify-center flex text-start">
                            <div class="flex px-3 py-3 w-fit justify-center justify-self-center">
                                <div class="grid grid-cols-1 card lg:card-side bg-base-100 shadow-xl px-4 py-4">
                                    <div class="card-body">
                                        <h2 class="card-title text-start text-tsn-header">Mulai
                                            Pendaftaran
                                            Santri Baru
                                        </h2>
                                        <div class="text-lg text-start"><strong>Pengisian Formulir Tahap 1:</strong>
                                            <br>
                                            7 Januari 2025 s/d 13 Januari 2025
                                        </div>
                                    </div>
                                    <div>
                                        <!--Session Status -->
                                        <x-auth-session-status class="mb-4" :status="session('status')" />

                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            <!--Username -->
                                            <div class="pt-4">
                                                <x-input-label for="username"
                                                    :value="__('Masukkan nomor KARTU KELUARGA')" />
                                                <x-text-input id="username" class="block mt-1 w-full" type="text"
                                                    name="username" minlength="16" maxlength="16"
                                                    :value="old('username')" required autocomplete="username" />
                                            </div>

                                            <div class="pt-4">
                                                <x-input-label for="name"
                                                    :value="__('Masukkan Nama KEPALA KELUARGA')" />
                                                <x-text-input id="name" class="block mt-1 w-full" type="text"
                                                    name="name" :value="old('name')" required autocomplete="name" />
                                                <div class="label">
                                                    <span class="label-text-alt">Harus sesuai KK</span>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-center mt-4">

                                                <x-primary-button class="ms-3">
                                                    {{ __('Mulai') }}
                                                </x-primary-button>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <hr class="bg-tsn-header" />
                </li> --}}
                {{-- <li>
                    <div class="timeline-middle">
                        @svg('heroicon-s-arrow-right-circle', 'w-7 h-7', ['style'=> 'color: #9e5d4b'])
                    </div>
                    <div class="timeline-start md:text-start">
                        <div class="w-full justify-center flex text-start">
                            <div class="flex px-3 py-3 w-fit justify-center justify-self-center">
                                <div class="grid grid-cols-1 card lg:card-side bg-base-100 shadow-xl px-4 py-4">
                                    <div class="card-body">
                                        <h2 class="card-title text-start text-tsn-header">Saat ini sedang
                                            berlangsung proses:
                                        </h2>
                                        <div class="text-lg text-start"><strong>Penyeleksian Data oleh Penanggung
                                                Jawab Qism</strong>
                                            <br>
                                            13 Januari 2025 s/d 15 Januari 2025
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="bg-tsn-accent" />
                </li> --}}
                {{-- <li>
                    <hr class="bg-tsn-header" />
                    <div class="timeline-middle">
                        @svg('heroicon-s-arrow-right-circle', 'w-7 h-7', ['style'=> 'color: #9e5d4b'])
                    </div>
                    @livewire('statuspendaftaran')

                    <hr class="bg-tsn-header" />
                </li>--}}
                {{-- <li>

                    <hr class="bg-tsn-header" />
                    <div class="timeline-middle">
                        @svg('heroicon-s-arrow-right-circle', 'w-7 h-7', ['style'=> 'color: #9e5d4b'])
                    </div>
                    <div class="timeline-start md:text-start">
                        <div class="w-full justify-center flex text-start">
                            <div class="flex px-3 py-3 w-fit justify-center justify-self-center">
                                <div class="grid grid-cols-1 card lg:card-side bg-base-100 shadow-xl px-4 py-4">
                                    <div class="card-body">
                                        <div class="text-lg text-start"><strong>Pengisian Formulir Tahap 2:</strong>
                                            <br>
                                            15 Januari 2025 s/d 29 Januari 2025
                                        </div>
                                    </div>
                                    <div>
                                        <!--Session Status -->
                                        <x-auth-session-status class="mb-4" :status="session('status')" />

                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf

                                            <!--Username -->
                                            <div class="pt-4">
                                                <x-input-label for="tahap2"
                                                    :value="__('Masukkan nomor KARTU KELUARGA')" />
                                                <x-text-input id="tahap2" class="block mt-1 w-full" type="text"
                                                    name="tahap2" minlength="16" maxlength="16" :value="old('tahap2')"
                                                    required autocomplete="tahap2" />
                                                <x-input-error :messages="$errors->get('tahap2')" class="mt-2" />
                                            </div>


                                            <div class="flex items-center justify-center mt-4">
                                                <x-primary-button class="ms-3">
                                                    {{ __('Mulai') }}
                                                </x-primary-button>


                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <hr class="bg-tsn-header" />
                </li> --}}
                {{-- <li>
                    <hr class="bg-tsn-header" />
                    <div class="timeline-middle">
                        @svg('heroicon-s-arrow-right-circle', 'w-7 h-7', ['style'=> 'color: #9e5d4b'])
                    </div>
                    @livewire('statusbaru')

                    <hr class="bg-tsn-header" />
                </li> --}}

                <div class="timeline-middle">
                    @svg('heroicon-s-book-open', 'w-7 h-7', ['style'=> 'color: #274043'])
                </div>
                {{-- <li>

                    <hr class="bg-tsn-accent" />
                    <hr />
                    <div class="timeline-middle">
                        @svg('heroicon-o-stop', 'w-7 h-7', ['style'=> 'color: #274043'])
                    </div>
                    <div class="timeline-end md:text-start">
                        <div class="text-lg timeline-box"><strong>Pengisian formulir tahap 3 dan 4:</strong>
                            <br>
                            21 Februari 2024 s/d 29 Februari 2024
                        </div>
                    </div>
                </li> --}}
            </ul>
        </div>
    </div>

    {{-- Start Rincian Program --}}
    <div class="grid sm:grid-cols-1 lg:grid-cols-2 w-full h-fit justify-items-center p-4">

        {{-- TA --}}
        <div class="px-3 py-3 w-fit justify-center justify-self-center">
            <div class="card lg:card-side bg-base-100 shadow-xl px-4 py-4">
                <figure><img src="logoqism\Tarbiyatul Aulaad.png" alt="Album" class="w-32" /></figure>
                <div class="card-body">
                    <h2 class="card-title self-center text-center text-tsn-header">Pendaftaran</h2>
                    <h2 class="card-title self-center text-center text-tsn-header">Qism Tarbiyatul Aulaad</h2>
                    <h4 class="card-title self-center text-center text-tsn-header text-md">(Setingkat TK)</h4>
                    <p class="self-center">Putra minimal umur 4,5 tahun</p>
                    <p class="self-center">Putri minimal umur 5 tahun</p>
                    <br>
                    <table class="table">
                        <!-- head -->
                        <thead>
                            <tr class="border-tsn-header">
                                <th class="text-tsn-header" colspan="2">Informasi hubungi:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- row 1 -->
                            <tr>
                                <th><a href="https://wa.me/6285233745522">@svg('heroicon-o-phone', 'w-4 h-4', ['style'
                                        => 'color: #274043'])</a></th>
                                <td class="text-tsn-header"><a href="https://wa.me/6285233745522">Ustadz Abu Tsabit
                                        (Putra)</a></td>
                            </tr>
                            <!-- row 2 -->
                            <tr>
                                <th><a href="https://wa.me/6282328485257">@svg('heroicon-o-phone', 'w-4 h-4', ['style'
                                        => 'color: #274043'])</a></th>
                                <td class="text-tsn-header"><a href="https://wa.me/6282328485257">Kontak person
                                        putri</a></td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <div class="card-actions justify-start">
                        <button class="btn bg-tsn-accent focus:bg-tsn-bg"
                            onclick="rincian_program_ta.showModal()">Rincian
                            Program</button>
                        <dialog id="rincian_program_ta" class="modal">
                            <div class="modal-box">

                                <br>

                                {{-- Tabel Rincian Program TA --}}
                                <div class="bg-tsn-header w-full border-b-4 border-tsn-accent">

                                    <h3 class="font-bold text-lg text-white text-center">Rincian Program Qism Tarbiyatul
                                        Aulaad</h3>

                                    <br>
                                </div>

                                <div>
                                    <table class="table">
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-tsn-header">
                                                <th class="text-lg text-tsn-header" colspan="2">TARGET PENDIDIKAN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- row 1 -->
                                            <tr>
                                                <th>1</th>
                                                <td>Memahamkan dan membiasakan anak didik untuk mempelajari serta
                                                    mengamalkan ilmu agama sejak kecil</td>
                                            </tr>
                                            <!-- row 2 -->
                                            <tr>
                                                <th>2</th>
                                                <td>Menanamkan pada diri anak didik kecintaan dan rasa butuh terhadap
                                                    ilmu agama</td>
                                            </tr>
                                            <!-- row 3 -->
                                            <tr>
                                                <th>3</th>
                                                <td>Anak didik mampu membaca Al Qur’an dan aksara latin dengan baik
                                                </td>
                                            </tr>
                                            <!-- row 4 -->
                                            <tr>
                                                <th>4</th>
                                                <td>Anak didik mampu menulis arab dan latin dengan baik</td>
                                            </tr>
                                            <!-- row 5 -->
                                            <tr>
                                                <th>5</th>
                                                <td>Anak didik mampu menerapkan ilmu yang dipelajari dalam kehidupan
                                                    sehari-hari</td>
                                            </tr>
                                            <!-- row 6 -->
                                            <tr>
                                                <th>6</th>
                                                <td>Anak didik memiliki hafalan Al Qur’an ( Juz Amma ) dan doa
                                                    sehari-hari
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <br>

                                <div>
                                    <table class="table">
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-tsn-header">
                                                <th class="text-lg text-tsn-header" colspan="2">MATERI PENDIDIKAN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- row 1 -->
                                            <tr>
                                                <th>1</th>
                                                <td>Aqidah</td>
                                            </tr>
                                            <!-- row 2 -->
                                            <tr>
                                                <th>2</th>
                                                <td>Akhlak</td>
                                            </tr>
                                            <!-- row 3 -->
                                            <tr>
                                                <th>3</th>
                                                <td>Fiqh</td>
                                            </tr>
                                            <!-- row 4 -->
                                            <tr>
                                                <th>4</th>
                                                <td>Tarikh/Sejarah Islam</td>
                                            </tr>
                                            <!-- row 5 -->
                                            <tr>
                                                <th>5</th>
                                                <td>Baca tulis arab</td>
                                            </tr>
                                            <!-- row 6 -->
                                            <tr>
                                                <th>6</th>
                                                <td>Baca tulis latin</td>
                                            </tr>
                                            <!-- row 7 -->
                                            <tr>
                                                <th>7</th>
                                                <td>Berhitung</td>
                                            </tr>
                                            <!-- row 8 -->
                                            <tr>
                                                <th>8</th>
                                                <td>Seni</td>
                                            </tr>
                                            <!-- row 8 -->
                                            <tr>
                                                <th></th>
                                                <td>Dll</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <br>

                                <div>
                                    <table class="table">
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-tsn-header">
                                                <th class="text-lg text-tsn-header" colspan="2">SYARAT PENDAFTARAN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- row 1 -->
                                            <tr>
                                                <th>1</th>
                                                <td>Putra berusia minimal 4,5 tahun
                                                    <br>Putri berusia minimal 5 tahun
                                                </td>
                                            </tr>
                                            <!-- row 2 -->
                                            <tr>
                                                <th>2</th>
                                                <td>Sehat jasmani dan Rohani</td>
                                            </tr>
                                            <!-- row 3 -->
                                            <tr>
                                                <th>3</th>
                                                <td>Fotokopi akte kelahiran</td>
                                            </tr>
                                            <!-- row 4 -->
                                            <tr>
                                                <th>4</th>
                                                <td>Fotokopi Kartu Keluarga</td>
                                            </tr>
                                            <!-- row 5 -->
                                            <tr>
                                                <th>5</th>
                                                <td>Mengisi formular pendaftaran</td>
                                            </tr>
                                            <!-- row 6 -->
                                            <tr>
                                                <th>6</th>
                                                <td>Orang tua/wali sanggup mengikuti peraturan yang berlaku</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div>
                                    <table class="table">
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-tsn-header">
                                                <th class="text-lg text-tsn-header" colspan="2">DOKUMEN-DOKUMEN
                                                    PERSYARATAN</th>
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
                                                <th>8</th>
                                                <td>Surat pernyataan kesanggupan bermaterai (Format Surat dapat diunduh
                                                    dari formulir)</td>
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

                                <br>

                                <div>
                                    <table>
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-b">
                                                <th class="text-lg text-tsn-header" colspan="4">RINCIAN BIAYA AWAL DAN
                                                    SPP</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- row 1 -->
                                            <tr>
                                                <th class="text-start">Uang Pendaftaran </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">50.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 2 -->
                                            <tr>
                                                <th class="text-start">Uang Gedung </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">150.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 3 -->
                                            <tr>
                                                <th class="text-start">Uang Sarpras </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">100.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 4 -->
                                            <tr class="border-tsn-header">
                                                <th class="text-start">SPP* </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">75.000</td>
                                                <td class="text-end">(per bulan)</td>
                                            </tr>
                                            <tr class="border-t">
                                                <th>Total </th>
                                                <td class="text-end"><strong>Rp.</strong></td>
                                                <td class="text-end"><strong>375.000</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk
                                                    SPP bulan pertama</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>


                                <br>
                                <div class="flex sticky bottom-0 bg-transparent w-full justify-between rounded-xl">
                                    {{-- <button class="btn bg-tsn-accent focus:bg-tsn-bg"
                                        onclick="daftar_pt.showModal()">Daftar</button>
                                    <dialog id="daftar_pt" class="modal">
                                        <div class="modal-box">
                                            <div class="sticky top-0 right-0">
                                                <form method="dialog">
                                                    <button
                                                        class="btn btn-sm btn-circle btn-ghost absolute right-0 top-0">✕</button>
                                                </form>
                                            </div>
                                            <div>
                                                <p class="text-center">Tahap pendaftaran belum dimulai</p>
                                            </div>
                                        </div>
                                    </dialog> --}}
                                    <form method="dialog">
                                        <button class="btn bg-tsn-accent focus:bg-tsn-bg">Tutup</button>
                                    </form>
                                </div>
                            </div>
                        </dialog>
                        {{-- <button class="btn bg-tsn-accent focus:bg-tsn-bg"
                            onclick="daftar_ta.showModal()">Daftar</button>
                        <dialog id="daftar_ta" class="modal">
                            <div class="modal-box">
                                <div class="sticky top-0 right-0">
                                    <form method="dialog">
                                        <button
                                            class="btn btn-sm btn-circle btn-ghost absolute right-0 top-0">✕</button>
                                    </form>
                                </div>
                                <div>
                                    <p class="text-center">Tahap pendaftaran belum dimulai</p>
                                </div>
                            </div>
                        </dialog> --}}
                    </div>
                </div>

            </div>
        </div>

        {{-- PT --}}
        <div class="px-3 py-3 w-fit justify-center justify-self-center">
            <div class="card lg:card-side bg-base-100 shadow-xl px-4 py-4">
                <figure><img src="logoqism\Pra tahfidz.png" alt="Album" class="w-32" /></figure>
                <div class="card-body">
                    <h2 class="card-title self-center text-center text-tsn-header">Pendaftaran</h2>
                    <h2 class="card-title self-center text-center text-tsn-header">Qism Pra Tahfidz</h2>
                    <h4 class="card-title self-center text-center text-tsn-header text-md">(Setingkat SD)</h4>
                    <p class="self-center">Putra/Putri (umur mulai 6 tahun)</p>
                    <br>
                    <table class="table">
                        <!-- head -->
                        <thead>
                            <tr class="border-tsn-header">
                                <th class="text-tsn-header" colspan="2">Informasi hubungi:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- row 1 -->
                            <tr>
                                <th><a href="https://wa.me/6285235636182">@svg('heroicon-o-phone', 'w-4 h-4', ['style'
                                        => 'color: #274043'])</a></th>
                                <td class="text-tsn-header"><a href="https://wa.me/6285235636182">Ustadz Abu Ruwaifi'
                                        (Putra)</a></td>
                            </tr>
                            <!-- row 2 -->
                            <tr>
                                <th><a href="https://wa.me/6285234772629">@svg('heroicon-o-phone', 'w-4 h-4', ['style'
                                        => 'color: #274043'])</a></th>
                                <td class="text-tsn-header"><a href="https://wa.me/6285234772629">Kontak person
                                        putri</a></td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <div class="card-actions justify-start">
                        <button class="btn bg-tsn-accent focus:bg-tsn-bg"
                            onclick="rincian_program_pt.showModal()">Rincian
                            Program</button>
                        <dialog id="rincian_program_pt" class="modal">
                            <div class="modal-box">

                                <br>
                                <div class="bg-tsn-header w-full border-b-4 border-tsn-accent">

                                    <h3 class="font-bold text-lg text-white text-center">Rincian Program Qism Pra
                                        Tahfidz</h3>

                                    <br>
                                </div>

                                <div>
                                    <table class="table">
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-tsn-header">
                                                <th class="text-lg text-tsn-header" colspan="2">TUJUAN PENDIDIKAN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- row 1 -->
                                            <tr>
                                                <th>1</th>
                                                <td>Memahamkan dan membiasakan anak didik untuk mempelajari dan
                                                    mengamalkan ilmu agama sejak dini</td>
                                            </tr>
                                            <!-- row 2 -->
                                            <tr>
                                                <th>2</th>
                                                <td>Membekali dengan pendidikan dasar yang disesuaikan dengan kebutuhan
                                                    agar anak tidak tertinggal jauh dalam hal akademis dengan anak-anak
                                                    seusianya yang mengeyam pendidikan dasar umum</td>
                                            </tr>
                                            <!-- row 3 -->
                                            <tr>
                                                <th>3</th>
                                                <td>Menanamkan kecintaan dan rasa butuh terhadap ilmu
                                                </td>
                                            </tr>
                                            <!-- row 4 -->
                                            <tr>
                                                <th>4</th>
                                                <td>Menanamkan kesadaran untuk belajar</td>
                                            </tr>
                                            <!-- row 5 -->
                                            <tr>
                                                <th>5</th>
                                                <td>Melatih kemandirian belajar di ma’had secara bertahap</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <br>

                                <div>
                                    <table class="table">
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-tsn-header">
                                                <th class="text-lg text-tsn-header" colspan="2">MATERI PELAJARAN</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr class="border-tsn-header">
                                                <th class="text-tsn-header">1</th>
                                                <td class="text-tsn-header"><strong>MATERI POKOK DINIYAH</strong></td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Aqidah</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Akhlak dan adab</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Hafalan Qur’an</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Fiqh</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Siroh</td>
                                            </tr>

                                            <tr class="border-tsn-header">
                                                <th class="text-tsn-header">2</th>
                                                <td class="text-tsn-header"><strong>MATERI ALAT</strong></td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Qiroah</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Bahasa arab</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Khot</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Jarlis</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Tajwid</td>
                                            </tr>

                                            <tr class="border-tsn-header">
                                                <th class="text-tsn-header">3</th>
                                                <td class="text-tsn-header"><strong>MATERI PENGETAHUAN UMUM
                                                        DASAR</strong></td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Matematika</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>IPS</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Bahasa Indonesia</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>Kesehatan dasar</td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>IPA</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>

                                <br>

                                <div>
                                    <table class="table">
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-tsn-header">
                                                <th class="text-lg text-tsn-header" colspan="2">SISTEM PENDIDIKAN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- row 1 -->
                                            <tr>
                                                <th>1</th>
                                                <td>Membagi anak dalam kelompok kelas berdasarkan kemampuan dasar dan
                                                    umurnya</td>
                                            </tr>
                                            <!-- row 2 -->
                                            <tr>
                                                <th>2</th>
                                                <td>Evaluasi belajar diberikan tiap semester dalam bentuk rapor</td>
                                            </tr>
                                            <!-- row 3 -->
                                            <tr>
                                                <th>3</th>
                                                <td>Adanya program full day bagi anak kelas 1 s.d 4, sehingga diharapkan
                                                    anak istirahat / tidur siang di ma’had, dengan membawa bekal makan
                                                    siang dari rumah</td>
                                            </tr>
                                            <!-- row 4 -->
                                            <tr>
                                                <th>4</th>
                                                <td>Adanya program menginap terkhusus anak kelas 5 dan 6, yang dibedakan
                                                    sesuai dengan tingkat kesiapan anak pada tiap tingkatan kelasnya
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>


                                <br>

                                <div>
                                    <table class="table">
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-tsn-header">
                                                <th class="text-lg text-tsn-header" colspan="2">SYARAT PENDAFTARAN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>1</th>
                                                <td>Putra usia 6 s/d 12 tahun</td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>Sehat jasmani dan rohani</td>
                                            </tr>
                                            <tr>
                                                <th>3</th>
                                                <td>Dapat membaca dan menulis latin dan arab</td>
                                            </tr>
                                            <tr>
                                                <th>4</th>
                                                <td>Orang tua / wali sanggup mematuhi peraturan yang berlaku di ma’had
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>5</th>
                                                <td>Membawa perlengkapan menginap</td>
                                            </tr>
                                            <tr>
                                                <th>6</th>
                                                <td>Mengisi formulir pendaftaran</td>
                                            </tr>
                                            <tr>
                                                <th>7</th>
                                                <td>Memenuhi dokumen-dokumen pendaftaran</td>
                                            </tr>
                                            <tr>
                                                <th>8</th>
                                                <td>Bagi santri pindahan diwajibkan membawa surat keterangan baik dari
                                                    ma’had sebelumnya</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>

                                <div>
                                    <table class="table">
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-tsn-header">
                                                <th class="text-lg text-tsn-header" colspan="2">DOKUMEN-DOKUMEN
                                                    PERSYARATAN</th>
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
                                                <th>8</th>
                                                <td>Surat pernyataan kesanggupan bermaterai (Format Surat dapat diunduh
                                                    dari formulir)</td>
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

                                <br>

                                <div>
                                    <table>
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-b">
                                                <th class="text-lg text-tsn-header" colspan="4">RINCIAN BIAYA AWAL DAN
                                                    SPP</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- row 1 -->
                                            <tr>
                                                <th class="text-start">Uang Pendaftaran </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">100.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 2 -->
                                            <tr>
                                                <th class="text-start">Uang Gedung </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">400.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 3 -->
                                            <tr>
                                                <th class="text-start">Uang Sarpras </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">300.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 4 -->
                                            <tr class="border-tsn-header">
                                                <th class="text-start">SPP* </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">550.000</td>
                                                <td class="text-end">(per bulan)</td>
                                            </tr>
                                            <tr class="border-t">
                                                <th>Total </th>
                                                <td class="text-end"><strong>Rp.</strong></td>
                                                <td class="text-end"><strong>1.350.000</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk
                                                    SPP bulan pertama</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <br>

                                <div>
                                    <table>
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-b">
                                                <th class="text-lg text-tsn-header" colspan="4">RINCIAN BIAYA AWAL DAN
                                                    SPP (fullday tanpa makan)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- row 1 -->
                                            <tr>
                                                <th class="text-start">Uang Pendaftaran </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">100.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 2 -->
                                            <tr>
                                                <th class="text-start">Uang Gedung </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">400.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 3 -->
                                            <tr>
                                                <th class="text-start">Uang Sarpras </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">300.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 4 -->
                                            <tr class="border-tsn-header">
                                                <th class="text-start">SPP* </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">300.000</td>
                                                <td class="text-end">(per bulan)</td>
                                            </tr>
                                            <tr class="border-t">
                                                <th>Total </th>
                                                <td class="text-end"><strong>Rp.</strong></td>
                                                <td class="text-end"><strong>1.000.000</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk
                                                    SPP bulan pertama</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <br>

                                <div>
                                    <table>
                                        <!-- head -->
                                        <thead>
                                            <tr class="border-b">
                                                <th class="text-lg text-tsn-header" colspan="4">RINCIAN BIAYA AWAL DAN
                                                    SPP (fullday dengan makan)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- row 1 -->
                                            <tr>
                                                <th class="text-start">Uang Pendaftaran </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">100.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 2 -->
                                            <tr>
                                                <th class="text-start">Uang Gedung </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">400.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 3 -->
                                            <tr>
                                                <th class="text-start">Uang Sarpras </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">300.000</td>
                                                <td class="text-end">(per tahun)</td>
                                            </tr>
                                            <!-- row 4 -->
                                            <tr class="border-tsn-header">
                                                <th class="text-start">SPP* </th>
                                                <td class="text-end">Rp.</td>
                                                <td class="text-end">400.000</td>
                                                <td class="text-end">(per bulan)</td>
                                            </tr>
                                            <tr class="border-t">
                                                <th>Total </th>
                                                <td class="text-end"><strong>Rp.</strong></td>
                                                <td class="text-end"><strong>1.100.000</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm" colspan="4">*Pembayaran administrasi awal termasuk
                                                    SPP bulan pertama</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <br>

                                <div class="flex sticky bottom-0 bg-transparent w-full justify-between rounded-xl">
                                    {{-- <button class="btn bg-tsn-accent focus:bg-tsn-bg"
                                        onclick="daftar_pt.showModal()">Daftar</button>
                                    <dialog id="daftar_pt" class="modal">
                                        <div class="modal-box">
                                            <div class="sticky top-0 right-0">
                                                <form method="dialog">
                                                    <button
                                                        class="btn btn-sm btn-circle btn-ghost absolute right-0 top-0">✕</button>
                                                </form>
                                            </div>
                                            <div>
                                                <p class="text-center">Tahap pendaftaran belum dimulai</p>
                                            </div>
                                        </div>
                                    </dialog> --}}
                                    <form method="dialog">
                                        <button class="btn bg-tsn-accent focus:bg-tsn-bg">Tutup</button>
                                    </form>
                                </div>
                            </div>
                        </dialog>
                        {{-- <button class="btn bg-tsn-accent focus:bg-tsn-bg"
                            onclick="daftar_pt.showModal()">Daftar</button>
                        <dialog id="daftar_pt" class="modal">
                            <div class="modal-box">
                                <div class="sticky top-0 right-0">
                                    <form method="dialog">
                                        <button
                                            class="btn btn-sm btn-circle btn-ghost absolute right-0 top-0">✕</button>
                                    </form>
                                </div>
                                <div>
                                    <p class="text-center">Tahap pendaftaran belum dimulai</p>
                                </div>
                            </div>
                        </dialog> --}}
                    </div>
                </div>

            </div>
        </div>

    </div>
    {{-- End Rincian Program --}}

    <div class="px-3 py-3 w-fit justify-center justify-self-center">
        {{-- <div class="card lg:card-side bg-base-100 shadow-xl px-4 py-4"> --}}

            <div class="w-auto h-auto p-4 shadow-xl bg-base-100 rounded-xl">
                <h2 class="text-2xl text-tsn-header text-center"><strong>Gallery</strong></h2>
                <br>

                <div class="carousel rounded-xl w-full h-full shadow-xl">
                    <div id="slide1" class="carousel-item relative w-full">
                        <img src="tsngallery25/01.jpg" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Masjid Pondok Pesantren Ta'dzimussunnah Sine Ngawi</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide13" class="btn btn-circle">❮</a>
                            <a href="#slide2" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                    <div id="slide2" class="carousel-item relative w-full">
                        <img src="tsngallery25/02.png" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Pondok Pesantren tampak gerbang utama</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide1" class="btn btn-circle">❮</a>
                            <a href="#slide3" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                    <div id="slide3" class="carousel-item relative w-full">
                        <img src="tsngallery25/03.png" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Tampak Gedung Baru dari Utara</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide2" class="btn btn-circle">❮</a>
                            <a href="#slide4" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                    <div id="slide4" class="carousel-item relative w-full">
                        <img src="tsngallery25/04.jpg" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Pondok Tampak dari Selatan</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide3" class="btn btn-circle">❮</a>
                            <a href="#slide5" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                    <div id="slide5" class="carousel-item relative w-full">
                        <img src="tsngallery25/05.jpg" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Gedung Qism Pra Tahfidz Putra</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide4" class="btn btn-circle">❮</a>
                            <a href="#slide6" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                    <div id="slide6" class="carousel-item relative w-full">
                        <img src="tsngallery25/06.png" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Ruang KBM Tarbiyatul Aulaad Putra</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide5" class="btn btn-circle">❮</a>
                            <a href="#slide7" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                    <div id="slide7" class="carousel-item relative w-full">
                        <img src="tsngallery25/07.png" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Syirkah Putra</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide6" class="btn btn-circle">❮</a>
                            <a href="#slide8" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                    <div id="slide8" class="carousel-item relative w-full">
                        <img src="tsngallery25/08.png" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Kegiatan Rihlah di Akhir Tahun Ajaran</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide7" class="btn btn-circle">❮</a>
                            <a href="#slide9" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                    <div id="slide9" class="carousel-item relative w-full">
                        <img src="tsngallery25/09.png" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Tampak area halaman dan lapangan Pondok Pesantren</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide8" class="btn btn-circle">❮</a>
                            <a href="#slide10" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                    <div id="slide10" class="carousel-item relative w-full">
                        <img src="tsngallery25/10.jpg" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Pintu Masuk Ruang KBM Tarbiyatul Aulaad Putri</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide9" class="btn btn-circle">❮</a>
                            <a href="#slide11" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                    <div id="slide11" class="carousel-item relative w-full">
                        <img src="tsngallery25/11.png" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Masjid Lin Nisaa' Pondok Pesantren Ta'dzimussunnah Sine Ngawi</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide10" class="btn btn-circle">❮</a>
                            <a href="#slide12" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                    <div id="slide12" class="carousel-item relative w-full">
                        <img src="tsngallery25/12.png" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Ruang Aula Putri</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide11" class="btn btn-circle">❮</a>
                            <a href="#slide13" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                    <div id="slide13" class="carousel-item relative w-full">
                        <img src="tsngallery25/13.png" class="w-full" />
                        <div
                            class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                            <p>Syirkah Putri</p>
                        </div>
                        <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <a href="#slide12" class="btn btn-circle">❮</a>
                            <a href="#slide1" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer footer-center p-10 bg-tsn-header text-primary-content border-t-4 border-tsn-accent">
            <aside>
                <figure><img src="\LogoTSN.png" alt="Album" class="w-16" /></figure>
                <p class="font-raleway">
                    Ma'had Ta'dzimussunnah <br />Sine Ngawi
                </p>
                <p class="font-raleway">Nomor Statistik Pesantren: 510035210133</p>
                <p class="font-raleway">Dusun Krajan RT 02/RW 02 Desa Ketanggung Kec. Sine Kab. Ngawi 63264</p>
                <p class="font-raleway text-center"><a href="https://maps.app.goo.gl/UP1YyR7V6J3tV3bH6">Link Google
                        Maps</a>
                </p>
                <p class="font-raleway text-center"><a
                        href="https://maps.app.goo.gl/UP1YyR7V6J3tV3bH6">@svg('heroicon-o-map-pin', 'w-4 h-4', ['style'
                        => 'color: #d3c281'])</a></p>
            </aside>
        </footer>

        <footer class="footer footer-center bottom-0 bg-tsn-header text-primary-content">
            <p class="text-cente font-raleway">Copyright © 1446 H/2025 M - All right reserved</p>
        </footer>

        @filamentScripts
        @vite('resources/js/app.js')

</body>

</html>