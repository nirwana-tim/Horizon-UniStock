<x-app-layout>

    <x-page-header title="Pengaturan SMTP" subtitle="Konfigurasi mailer yang digunakan untuk mengirim aplikasi (OTP, notifikasi, laporan).">
        <x-slot name="actions">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </x-slot>
    </x-page-header>

    <div class="py-6" x-data="smtpForm()">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alert berbasis state Alpine --}}
            <div x-show="message" x-transition>
                <div class="flex items-start gap-3 rounded-lg border p-4"
                     :class="messageType === 'error' ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50'">
                    <svg x-show="messageType === 'error'" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <svg x-show="messageType === 'success'" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium flex-1" :class="messageType === 'error' ? 'text-red-800' : 'text-green-800'" x-text="message"></p>
                    <button type="button" @click="message = ''" class="text-gray-400 hover:text-gray-600 flex-shrink-0">✕</button>
                </div>
            </div>

            {{-- Status mailer aktif --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-primary-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500">Mailer yang Sedang Aktif</p>
                            <p class="text-sm font-semibold text-gray-800">
                                <span x-text="active.mailer.toUpperCase()"></span>
                                <template x-if="active.mailer === 'smtp' && active.host">
                                    <span class="text-gray-500 font-normal"> · <span x-text="active.host + ':' + active.port"></span></span>
                                </template>
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="active.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700'"
                          x-text="active.is_active ? 'Aktif' : 'Tidak aktif'"></span>
                </div>
                <p x-show="active.from_address" class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-500">
                    Pengirim:
                    <span class="text-gray-700 font-medium" x-text="(active.from_name ? active.from_name + ' <' : '') + active.from_address + (active.from_name ? '>' : '')"></span>
                </p>
            </div>

            {{-- ① Konfigurasi Mailer --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary-700 text-white text-sm font-semibold">1</span>
                    <h3 class="text-lg font-semibold text-gray-800">Konfigurasi Mailer</h3>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <x-input-label for="mailer" value="{{ __('Mailer Driver') }}" />
                        <select id="mailer" name="mailer" x-model="mailer"
                            class="mt-1 block w-full bg-gray-100 border border-gray-200 rounded-lg text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-colors">
                            @foreach($mailers as $key => $label)
                                <option value="{{ $key }}" {{ $settings->mailer === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('mailer')" class="mt-2" />
                    </div>

                    {{-- SMTP --}}
                    <div x-show="mailer === 'smtp'" x-transition class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="smtp_scheme" value="{{ __('Encryption') }}" />
                                <select id="smtp_scheme" name="smtp_scheme"
                                    class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-colors">
                                    <option value="tls" {{ old('smtp_scheme', $settings->scheme) === 'tls' ? 'selected' : '' }}>TLS (STARTTLS)</option>
                                    <option value="ssl" {{ old('smtp_scheme', $settings->scheme) === 'ssl' ? 'selected' : '' }}>SSL / SMTPS</option>
                                </select>
                                <x-input-error :messages="$errors->get('smtp_scheme')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="smtp_port" value="{{ __('Port') }}" />
                                <x-text-input id="smtp_port" name="smtp_port" type="number"
                                    class="mt-1 block w-full" :value="old('smtp_port', $settings->port)"
                                    placeholder="587" />
                                <x-input-error :messages="$errors->get('smtp_port')" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="smtp_host" value="{{ __('SMTP Host') }}" />
                            <x-text-input id="smtp_host" name="smtp_host" type="text"
                                class="mt-1 block w-full" :value="old('smtp_host', $settings->host)"
                                placeholder="smtp.example.com" />
                            <x-input-error :messages="$errors->get('smtp_host')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="smtp_username" value="{{ __('Username (SMTP Auth)') }}" />
                            <x-text-input id="smtp_username" name="smtp_username" type="text"
                                class="mt-1 block w-full" :value="old('smtp_username', $settings->username)"
                                placeholder="user@example.com" />
                            <x-input-error :messages="$errors->get('smtp_username')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="smtp_password" value="{{ __('Password') }}" />
                            <x-text-input id="smtp_password" name="smtp_password" type="password"
                                class="mt-1 block w-full"
                                :value="old('smtp_password')"
                                placeholder="{{ $settings->password ? '•••••••• (biarkan kosong untuk mempertahankan)' : 'Masukkan password' }}" />
                            <x-input-error :messages="$errors->get('smtp_password')" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-1">Disimpan terenkripsi di database. Kosongkan untuk mempertahankan nilai yang sudah tersimpan.</p>
                        </div>
                        <div class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4">
                            <input type="checkbox" id="smtp_verify_peer" name="smtp_verify_peer" value="1"
                                @checked(old('smtp_verify_peer', $settings->verify_peer ?? false))
                                class="mt-0.5 rounded border-gray-300 text-primary-700 focus:ring-primary-500">
                            <div>
                                <label for="smtp_verify_peer" class="text-sm font-medium text-amber-900 cursor-pointer">
                                    {{ __('Nonaktifkan Verifikasi SSL') }}
                                </label>
                                <p class="text-xs text-amber-700 mt-1">Aktifkan hanya jika SMTP host gagal koneksi dengan error certificate mismatch (contoh: <code>smtp-relay.brevo.com</code>). Mematikan verifikasi SSL berarti koneksi tidak memvalidasi identitas server.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Sendmail --}}
                    <div x-show="mailer === 'sendmail'" x-transition>
                        <x-input-label for="sendmail_path" value="{{ __('Sendmail Path') }}" />
                        <x-text-input id="sendmail_path" name="sendmail_path" type="text"
                            class="mt-1 block w-full" :value="old('sendmail_path', $settings->host)"
                            placeholder="/usr/sbin/sendmail -bs -i" />
                        <x-input-error :messages="$errors->get('sendmail_path')" class="mt-2" />
                    </div>

                    {{-- Log --}}
                    <div x-show="mailer === 'log'" x-transition>
                        <x-input-label for="log_channel" value="{{ __('Log Channel') }}" />
                        <x-text-input id="log_channel" name="log_channel" type="text"
                            class="mt-1 block w-full" :value="old('log_channel', $settings->username)"
                            placeholder="mail" />
                        <x-input-error :messages="$errors->get('log_channel')" class="mt-2" />
                    </div>

                    {{-- API drivers --}}
                    <div x-show="apiDrivers.includes(mailer)" x-transition>
                        <x-input-label for="api_key" value="{{ __('API Key') }}" />
                        <x-text-input id="api_key" name="api_key" type="password"
                            class="mt-1 block w-full"
                            :value="old('api_key')"
                            placeholder="{{ $settings->api_key ? '•••••••• (biarkan kosong untuk mempertahankan)' : 'Masukkan API key' }}" />
                        <x-input-error :messages="$errors->get('api_key')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">Disimpan terenkripsi di database. Kosongkan untuk mempertahankan nilai yang sudah tersimpan.</p>
                    </div>

                    {{-- From --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="from_address" value="{{ __('From Email') }}" />
                            <x-text-input id="from_address" name="from_address" type="email"
                                class="mt-1 block w-full" :value="old('from_address', $settings->from_address)" required />
                            <x-input-error :messages="$errors->get('from_address')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="from_name" value="{{ __('From Name') }}" />
                            <x-text-input id="from_name" name="from_name" type="text"
                                class="mt-1 block w-full" :value="old('from_name', $settings->from_name)"
                                placeholder="UniStock" />
                            <x-input-error :messages="$errors->get('from_name')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ② Verifikasi & Aktifkan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary-700 text-white text-sm font-semibold">2</span>
                    <h2 class="text-lg font-semibold text-gray-800">Verifikasi &amp; Aktifkan</h2>
                </div>

                <div class="p-5">
                    <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                        <p class="text-sm text-gray-600">Sebelum disimpan, uji kirim kode OTP dan verifikasi dulu.</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="hasPending ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'"
                              x-text="hasPending ? 'Terverifikasi' : 'Belum diverifikasi'"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" @click="openTestModal()"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 h-10 text-sm font-medium rounded-lg border border-primary-500 text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/>
                            </svg>
                            Uji Koneksi
                        </button>
                        <button type="button" @click="saveConfig()" :disabled="saving"
                            class="inline-flex items-center justify-center gap-2 flex-1 sm:flex-none sm:min-w-[180px] px-4 py-2 h-10 text-sm font-medium rounded-lg bg-primary-700 text-white hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <svg x-show="saving" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="saving ? 'Menyimpan...' : 'Simpan &amp; Aktifkan'"></span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-3">Konfigurasi baru tidak menggantikan mailer aktif otomatis — aktifkan lewat tombol di atas setelah verifikasi sukses.</p>
                </div>
            </div>

        </div>

        {{-- Modal Uji Koneksi --}}
        <x-modal name="test-mail-modal" :show="false" maxWidth="md">
            <div class="p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-gray-800">Uji Koneksi SMTP</h3>
                    <button type="button" @click="$dispatch('close-modal','test-mail-modal')"
                        class="text-gray-400 hover:text-gray-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Langkah 1: Kirim OTP --}}
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary-700 text-white text-xs font-semibold">1</span>
                        <label class="text-xs font-medium text-gray-700">Email Target</label>
                    </div>
                    <div class="flex gap-2">
                        <input type="email" x-model="toEmail" placeholder="email@contoh.com"
                            class="flex-1 px-3 py-2 h-10 text-sm bg-gray-100 border border-gray-200 rounded-lg focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-colors">
                        <button type="button" @click="sendOtp()" :disabled="sending || !toEmail"
                            class="inline-flex items-center gap-1.5 px-4 h-10 text-sm font-medium rounded-lg bg-primary-700 text-white hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="sending" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="sending ? 'Mengirim...' : 'Kirim'"></span>
                        </button>
                    </div>
                </div>

                {{-- Langkah 2: Verifikasi OTP (tampil setelah kirim) --}}
                <div x-show="otpSent || otpVerified || modalError" x-transition class="mt-5 pt-5 border-t border-gray-100 space-y-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary-700 text-white text-xs font-semibold">2</span>
                            <label class="text-xs font-medium text-gray-700">Verifikasi OTP</label>
                        </div>

                        <div class="flex gap-2 justify-between items-end">
                            <div class="flex gap-2">
                                <template x-for="(box, i) in [0,1,2,3]" :key="i">
                                    <input type="text" inputmode="numeric" maxlength="1" class="otp-input w-12 h-14 text-center text-lg font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-colors"
                                        x-model="otp[i]"
                                        @keydown.backspace="if(!otp[i] && i>0) focusOtp(i-1)"
                                        @input="otp[i]=otp[i].replace(/[^0-9]/g,''); if(otp[i] && i<3) focusOtp(i+1)"
                                        @keydown.enter.prevent="i===3 ? verifyOtp() : null">
                                </template>
                            </div>
                            <button type="button" @click="verifyOtp()" :disabled="verifying || otp.join('').length !== 4"
                                class="inline-flex items-center gap-1.5 px-4 h-10 text-sm font-medium rounded-lg border border-primary-500 text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="verifying" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span x-text="verifying ? 'Memverifikasi...' : 'Verifikasi'"></span>
                            </button>
                        </div>
                        <p x-show="modalError" class="text-xs text-red-600 mt-2" x-text="modalError"></p>
                        <p x-show="!modalError" class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Kode OTP dikirim ke <span x-text="toEmail || 'email Anda'"></span> — berlaku 5 menit.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-100">
                    <button type="button" @click="$dispatch('close-modal','test-mail-modal')"
                        class="text-sm text-gray-500 hover:text-gray-700">Batal</button>
                    <span x-show="otpVerified" class="inline-flex items-center gap-1.5 text-sm font-medium text-green-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        OTP benar — tutup lalu klik "Simpan &amp; Aktifkan"
                    </span>
                </div>
            </div>
        </x-modal>
    </div>

    @push('scripts')
    <script>
        function smtpForm() {
            return {
                apiDrivers: @json($apiDrivers),
                mailer: '{{ old("mailer", $settings->mailer) }}',
                toEmail: '{{ Auth::user()->email ?? "" }}',
                otp: ['', '', '', ''],
                otpSent: @json($hasOtp),
                otpVerified: @json($hasPending),
                hasPending: @json($hasPending),
                sending: false,
                verifying: false,
                saving: false,
                message: '',
                messageType: 'success',
                modalError: '',
                active: {
                    mailer: '{{ $settings->mailer }}',
                    host: '{{ $settings->host ?? "" }}',
                    port: {{ $settings->port ?: 'null' }},
                    from_address: '{{ $settings->from_address ?? "" }}',
                    from_name: '{{ $settings->from_name ?? "" }}',
                    is_active: {{ $settings->is_active ? 'true' : 'false' }},
                },
                collectConfig() {
                    const val = id => document.getElementById(id)?.value || '';
                    const checked = id => document.getElementById(id)?.checked || false;
                    return {
                        mailer: this.mailer,
                        smtp_scheme: val('smtp_scheme'),
                        smtp_host: val('smtp_host'),
                        smtp_port: val('smtp_port'),
                        smtp_username: val('smtp_username'),
                        smtp_password: val('smtp_password'),
                        smtp_verify_peer: checked('smtp_verify_peer'),
                        sendmail_path: val('sendmail_path'),
                        log_channel: val('log_channel'),
                        api_key: val('api_key'),
                        from_address: val('from_address'),
                        from_name: val('from_name'),
                    };
                },
                showMessage(msg, type = 'success') {
                    this.message = msg;
                    this.messageType = type;
                    clearTimeout(this._msgTimer);
                    this._msgTimer = setTimeout(() => this.message = '', 6000);
                },
                focusOtp(i) {
                    const el = this.$el.querySelectorAll('.otp-input')[i];
                    if (el) el.focus();
                },
                openTestModal() {
                    this.modalError = '';
                    this.$dispatch('open-modal', 'test-mail-modal');
                },
                sendOtp() {
                    if (!this.toEmail || this.sending) return;
                    this.sending = true;
                    this.modalError = '';
                    axios.post('{{ route('system.smtp.test') }}', { ...this.collectConfig(), to_email: this.toEmail })
                        .then(r => {
                            this.sending = false;
                            this.otpSent = true;
                            this.otp = ['', '', '', ''];
                            this.modalError = '';
                            this.showMessage(r.data.message || 'Kode OTP terkirim.', 'success');
                        })
                        .catch(e => {
                            this.sending = false;
                            this.modalError = e.response?.data?.message || e.response?.data?.errors?.otp?.[0] || 'Terjadi kesalahan saat mengirim OTP.';
                        });
                },
                verifyOtp() {
                    const code = this.otp.join('');
                    if (code.length !== 4 || this.verifying) return;
                    this.verifying = true;
                    this.modalError = '';
                    axios.post('{{ route('system.smtp.verify') }}', { otp_code: code })
                        .then(r => {
                            this.verifying = false;
                            this.otpVerified = true;
                            this.hasPending = true;
                            this.showMessage(r.data.message || 'OTP benar.', 'success');
                        })
                        .catch(e => {
                            this.verifying = false;
                            this.modalError = e.response?.data?.message || e.response?.data?.errors?.otp?.[0] || 'Kode OTP salah.';
                        });
                },
                saveConfig() {
                    if (this.saving) return;
                    this.saving = true;
                    axios.put('{{ route('system.smtp.store') }}', { ...this.collectConfig() })
                        .then(r => {
                            this.saving = false;
                            const c = r.data.config;
                            if (c) {
                                this.active.mailer = c.mailer || this.active.mailer;
                                this.active.host = c.host || '';
                                this.active.port = c.port || null;
                                this.active.from_address = c.from_address || '';
                                this.active.from_name = c.from_name || '';
                                this.active.is_active = c.is_active ?? true;
                            }
                            this.hasPending = false;
                            this.otpVerified = false;
                            this.otpSent = false;
                            this.showMessage(r.data.message || 'Konfigurasi SMTP berhasil diaktifkan.', 'success');
                        })
                        .catch(e => {
                            this.saving = false;
                            this.showMessage(e.response?.data?.message || 'Gagal menyimpan konfigurasi.', 'error');
                        });
                },
            };
        }
    </script>
    @endpush
</x-app-layout>