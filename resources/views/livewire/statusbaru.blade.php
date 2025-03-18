<div class="timeline-start md:text-start">
    <div class="w-full justify-center flex text-start">
        <div class="flex px-3 py-3 w-fit justify-center justify-self-center">
            <div class="grid grid-cols-1 card lg:card-side bg-base-100 shadow-xl px-4 py-4">
                <div class="card-body">
                    <div class="text-lg text-start"><strong>Cek Status Penerimaan Santri Baru</strong>
                        <br>
                        1 Februari 2025
                    </div>
                </div>
                <div>
                    <div>
                        <form wire:submit="cektahaptiga">
                            <!--Username -->
                            <div class="pt-4">
                                <x-input-label for="tahap3" :value="__('Masukkan nomor KARTU KELUARGA')" />
                                <x-text-input id="tahap3" class="block mt-1 w-full" type="text" name="tahap3"
                                    minlength="16" maxlength="16" :value="old('tahap3')" required autocomplete="tahap3"
                                    wire:model="tahap3" />
                                <x-input-error :messages="$errors->get('tahap3')" class="mt-2" />
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
                    <p>Jika ada yang ingin ditanyakan, silakan menghubungi Asatidzah penanggung jawab Qism</p>
                    <p><br></p>
                    @endif
                    <div>
                        <div>
                            {{ $this->table }}
                        </div>

                        @if($cekditerima === 0)

                        @elseif($cekditerima !== 0)

                        @endif
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>