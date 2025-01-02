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
            <figure><img src="\LogoTSN.png" alt="Album" class="w-16" /></figure>
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

        <div class="px-3 py-3 w-fit justify-center justify-self-center">
            {{-- <div class="card lg:card-side bg-base-100 shadow-xl px-4 py-4"> --}}

                <div class="w-auto h-auto p-4 shadow-xl bg-base-100 rounded-xl">
                    <h2 class="text-2xl text-tsn-header text-center"><strong>Gallery</strong></h2>
                    <br>
                    <div class="carousel rounded-xl w-full h-full shadow-xl">
                        <div id="slide1" class="carousel-item relative w-full">
                            <img src="tsngallery/Selatan menghadap utara.png" class="lg:w-100 w-full" />
                            <div
                                class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                                <p>Tampak dari selatan</p>
                            </div>
                        </div>
                        <div id="slide2" class="carousel-item relative w-full">
                            <img src="tsngallery/Depan menghadap selatan.png" class="w-full" />
                            <div
                                class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                                <p>Pondok Pesantren tampak gedung baru</p>
                            </div>
                        </div>
                        <div id="slide3" class="carousel-item relative w-full">
                            <img src="tsngallery/Depan menghadap utara.png" class="w-full" />
                            <div
                                class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                                <p>Pondok Pesantren tampak Area Masjid</p>
                            </div>
                        </div>
                        <div id="slide4" class="carousel-item relative w-full">
                            <img src="tsngallery/Masjid.png" class="w-full" />
                            <div
                                class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                                <p>Masjid Pondok Pesantren Ta'dzimussunnah Sine Ngawi</p>
                            </div>
                        </div>
                        <div id="slide5" class="carousel-item relative w-full">
                            <img src="tsngallery/Lapangan.png" class="w-full" />
                            <div
                                class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                                <p>Area Halaman Masjid</p>
                            </div>
                        </div>
                        <div id="slide6" class="carousel-item relative w-full">
                            <img src="tsngallery/Sakan PT.png" class="w-full" />
                            <div
                                class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                                <p>Asrama dan Sakan PT Putra</p>
                            </div>
                        </div>
                        <div id="slide7" class="carousel-item relative w-full">
                            <img src="tsngallery/Kelas TA.png" class="w-full" />
                            <div
                                class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                                <p>Ruang KBM TA Putra</p>
                            </div>
                        </div>
                        <div id="slide8" class="carousel-item relative w-full">
                            <img src="tsngallery/Syirkah Putra.png" class="w-full" />
                            <div
                                class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                                <p>Syirkah/Toko Putra</p>
                            </div>
                        </div>
                        <div id="slide9" class="carousel-item relative w-full">
                            <img src="tsngallery/Masjid Putri.png" class="w-full" />
                            <div
                                class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                                <p>Masjid Putri</p>
                            </div>
                        </div>
                        <div id="slide10" class="carousel-item relative w-full">
                            <img src="tsngallery/Aula Putri.png" class="w-full" />
                            <div
                                class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                                <p>Aula Putri</p>
                            </div>
                        </div>
                        <div id="slide11" class="carousel-item relative w-full">
                            <img src="tsngallery/Syirkah Putri.png" class="w-full" />
                            <div
                                class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                                <p>Syirkah Putri</p>
                            </div>
                        </div>
                        <div id="slide12" class="carousel-item relative w-full">
                            <img src="tsngallery/Kegiatan Rihlah.png" class="w-full" />
                            <div
                                class="bg-base-100 absolute flex justify-between transform left-2 -translate-y-1/2 5 bottom-0">
                                <p>Salah satu kegiatan rihlah</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- </div> --}}

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
            <p class="text-cente font-raleway">Copyright © 1445 H/2024 M - All right reserved</p>
        </footer>

        @filamentScripts
        @vite('resources/js/app.js')

</body>

</html>