<div class="w-full justify-center flex text-start">
    <div class="flex px-3 py-3 w-fit justify-center justify-self-center">

        <div class="grid grid-cols-1 card lg:card-side bg-base-100 shadow-xl px-4 py-4">
            <div class="card-body">
                <h2 class="card-title self-center text-center text-tsn-header">Cek Status Penerimaan Naik Qism</h2>
            </div>
            <div>
                <form wire:submit="cekqism">
                    <!--Username -->
                    <div class="pt-4">
                        <x-input-label for="ceknaikqism" :value="__('Masukkan nomor KARTU KELUARGA')" />
                        <x-text-input id="ceknaikqism" class="block mt-1 w-full" type="text" name="ceknaikqism" minlength="16"
                            maxlength="16" :value="old('ceknaikqism')" required autocomplete="ceknaikqism" wire:model="ceknaikqism" />
                        <x-input-error :messages="$errors->get('naikqismmsg')" class="mt-2" />
                        {{--
                        <x-input-error :messages="$errors->get('username')" class="mt-2" /> --}}
                    </div>


                    <div class="flex items-center justify-center mt-4">
                        <x-primary-button class="ms-3">
                            {{ __('Cek Status') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>



            @if($datanaikqism === null)

            @endif

            @if($datanaikqism !== null)
            <p><br></p>
            @if($datanaikqism->status_tahap === null)
            <p>Jika informasi status "Lolos"/"Tidak Lolos" belum ada, berarti masih dalam proses seleksi</p>
            <p><br></p>
            @endif
            <div>
                <div>
                    {{ $this->table }}
                </div>

                @if($cekditerimanaikqism === 0)

                @elseif($cekditerimanaikqism !== 0)

                @endif
            </div>
            @endif

        </div>
    </div>
</div>
