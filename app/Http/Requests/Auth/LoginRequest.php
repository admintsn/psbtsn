<?php

namespace App\Http\Requests\Auth;

use App\Models\KelasSantri;
use App\Models\Santri;
use App\Models\User;
use App\Models\Walisantri;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // return [
        //     'email' => ['required', 'string', 'email'],
        //     'password' => ['required', 'string'],
        // ];

        return [
            // 'username' => ['required', 'string'],
            // 'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $kk = User::where('username', $this->kk)->count();
        $tahap1 = Santri::where('kartu_keluarga', $this->tahap1)
            ->where('jenispendaftar', '!=', null)
            ->count();



        $username = User::where('username', $this->username)->count();

        $naik = Santri::where('kartu_keluarga', $this->kk)->pluck('jenis_pendaftar_id')->toArray();


        $user = User::where('username', $this->kk)->first();

        $password = 'vnPgyLdEKcLdeqPjnXHfHicgEXd3kRujdnWjTAbxpUe9tbvVLEa7VwefU7cLYYaNfxokn9jw9fqyp97gbMtw9TakscwmqhFCanj4jLVHTNXowzJzvPH9LeMeJXmpTJAkqu47pap9daPCLezahf9n3mTAwnbAyYjqpnprMvhmaJxncNsswqwhhFqvpvpUafpmismJEjtEMo9HYATyWars9qR9mKEtfwaez3M9NmmJHLb97mHhTLzARRaLaehg3TM';
        $updatepassword = Hash::make($password);
        $adanaik = in_array(2, $naik);

        // dd($this);

        switch (true) {

            case ($this->tahap2 !== null):
                // dd($this->tahap2);

                $user = User::where('username', $this->tahap2)->first();

                $cekuser = User::where('username', $this->tahap2)
                    ->count();

                $cekpendaftar = Santri::where('kartu_keluarga', $this->tahap2)
                    ->where('jenis_pendaftar_id', '!=', null)
                    ->where(function ($query) {
                        $query->where('tahap_pendaftaran_id', 2)
                            ->orWhere('tahap_pendaftaran_id', 3);
                    })
                    ->count();

                if ($cekuser === 0) {
                    // dd('tahap2authfaield');

                    throw ValidationException::withMessages([
                        'tahap2' => trans('auth.failed'),
                    ]);
                } elseif ($cekpendaftar === 0) {
                    // dd('tahap2authfaield2');

                    throw ValidationException::withMessages([
                        'tahap2' => trans('auth.belumtahap2'),
                    ]);
                } elseif ($cekpendaftar !== 0) {
                    // dd('tahap2channel');

                    Auth::login($user, $this->boolean('remember'));
                    Session::put('channel', 'tahapdua');
                    RateLimiter::clear($this->throttleKey());
                }

                break;

            case ($this->kk !== null):
                // dd('form naik qism');
                if ($kk === 0) {
                    throw ValidationException::withMessages([
                        'naikqism' => trans('auth.failed'),
                    ]);
                    // Form Naik Qism, jika KK ada
                } elseif ($kk !== 0) {
                    // dd('aa');
                    $walisantri = Walisantri::where('user_id', $user->id)->first();

                    // Form Naik Qism, table santri jika naikqism = 'naik'
                    if ($adanaik === true) {

                        // dd('bb');

                        if (!$user || !Hash::check($this->password, $user->password)) {
                            User::where('username', $this->kk)
                                ->update(['password' => $updatepassword]);

                            Walisantri::where('user_id', $user->id)
                                ->update([
                                    'kartu_keluarga_santri' => $this->kk,
                                    'nama_kpl_kel_santri' => $walisantri->ak_nama_lengkap,
                                ]);
                        }
                        Auth::login($user, $this->boolean('remember'));
                        Session::put('channel', 'naikqism');
                        RateLimiter::clear($this->throttleKey());
                    } elseif ($adanaik === false) {
                        // dd('authpasswordsalah');
                        throw ValidationException::withMessages([
                            'naikqism' => trans('auth.password'),
                        ]);
                    }
                }

                break;

            case ($this->username !== null):
                // dd('form santri baru');

                $this->ensureIsNotRateLimited();
                $user = User::where('username', $this->username)->first();

                $password = 'vnPgyLdEKcLdeqPjnXHfHicgEXd3kRujdnWjTAbxpUe9tbvVLEa7VwefU7cLYYaNfxokn9jw9fqyp97gbMtw9TakscwmqhFCanj4jLVHTNXowzJzvPH9LeMeJXmpTJAkqu47pap9daPCLezahf9n3mTAwnbAyYjqpnprMvhmaJxncNsswqwhhFqvpvpUafpmismJEjtEMo9HYATyWars9qR9mKEtfwaez3M9NmmJHLb97mHhTLzARRaLaehg3TM';
                if ($user !== null) {
                    // dd('a');
                    // if (!$user || !Hash::check($password, $user->password)) {
                    $walisantri = Walisantri::where('user_id', $user->id)->first();
                    User::where('username', $this->username)
                        ->update(['password' => $updatepassword]);

                    Walisantri::where('user_id', $user->id)
                        ->update([
                            'kartu_keluarga_santri' => $this->username,
                            'nama_kpl_kel_santri' => $walisantri->ak_nama_lengkap,
                        ]);
                    // }
                    // dd('a');
                    Auth::login($user, $this->boolean('remember'));
                    Session::put('channel', 'psb');
                    RateLimiter::clear($this->throttleKey());
                } elseif ($user === null) {
                    // dd($user, 'b');

                    $user = User::create([
                        'name' => $this->name,
                        'username' => $this->username,
                        'password' => $password,
                        'panelrole' => 'psb',
                        'panelrole_id' => 4,
                        'channel' => 'psb',
                    ]);
                    Walisantri::create([
                        'user_id' => $user->id,
                        'kartu_keluarga_santri' => $this->username,
                        'nama_kpl_kel_santri' => $this->name,
                        'source' => 'psb',
                        'is_collapse' => '0',
                    ]);

                    // return $user;

                    event(new Registered($user));

                    Auth::login($user);
                    Session::put('channel', 'psb');

                    // return redirect('/psb');
                }

                break;
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')) . '|' . $this->ip());
    }
}
