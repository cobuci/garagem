<div class="min-h-screen flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950 transition-colors duration-500">
    <div class="hidden md:flex md:w-1/2 bg-slate-900 dark:bg-slate-900 relative overflow-hidden flex-col justify-center items-center p-12 text-white">
        <div class="absolute inset-0 opacity-20 dark:opacity-30">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-500 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 -right-24 w-80 h-80 bg-blue-600 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 left-1/4 w-64 h-64 bg-indigo-500 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 text-center">
            <div class="flex flex-col items-center gap-4">
                <h1 class="text-6xl font-black tracking-tighter italic">{{ config('app.name') }}</h1>
                <p class="text-slate-400 max-w-sm">{{ __('login.access_account') }}</p>
            </div>
        </div>
    </div>

    <div class="w-full md:w-1/2 flex items-center justify-center p-8 sm:p-12 md:p-16 lg:p-24 relative">
        <div class="absolute top-4 right-4">
            <x-button
                x-on:click="darkMode = !darkMode"
                flat
                secondary
                icon="sun"
                class="dark:hidden"
            />
            <x-button
                x-on:click="darkMode = !darkMode"
                flat
                secondary
                icon="moon"
                class="hidden dark:inline-flex"
            />
        </div>

        <div class="w-full max-w-md space-y-8">
            <div class="md:hidden flex flex-col items-center mb-8">
                <h2 class="text-4xl font-black text-slate-900 dark:text-white italic tracking-tighter">{{ config('app.name') }}</h2>
            </div>

            <div class="space-y-2 text-center">
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight leading-tight">
                    {{ $step == 1 ? __('login.welcome_back') : __('login.verify_email') }}
                </h2>
                <p class="text-slate-500 dark:text-slate-400 font-medium">
                    {{ $step == 1 ? __('login.access_account') : __('login.otp_sent', ['email' => $email]) }}
                </p>
            </div>

            <div class="mt-8">
                @if($step == 1)
                    <form wire:submit.prevent="sendOtp" class="space-y-6">
                        <x-input
                            wire:model="email"
                            :label="__('login.email_label')"
                            :placeholder="__('login.email_placeholder')"
                            icon="envelope"
                            type="email"
                            autocomplete="email"
                            class="dark:bg-slate-900/50 dark:border-slate-800 dark:text-slate-200 dark:placeholder-slate-500"
                            required
                        />

                        <x-checkbox
                            wire:model="rememberMe"
                            :label="__('login.remember_me')"
                            class="text-slate-600 dark:text-slate-400"
                        />

                        <x-button
                            type="submit"
                            primary
                            xl
                            class="w-full font-bold shadow-xl shadow-primary-500/20 hover:shadow-primary-500/40 transition-all duration-300"
                            wire:loading.attr="disabled"
                            wire:target="sendOtp"
                        >
                            <x-icon wire:loading wire:target="sendOtp" name="arrow-path" class="w-5 h-5 animate-spin" />
                            <span wire:loading wire:target="sendOtp">{{ __('login.sending') }}</span>
                            <span wire:loading.remove wire:target="sendOtp">{{ __('login.send_code') }}</span>
                        </x-button>
                    </form>
                @else
                    <form wire:submit.prevent="verifyOtp" class="space-y-6">
                        <div x-data="{
                            otp: @entangle('otp'),
                            length: 6,
                            handleInput(e, index) {
                                let val = e.target.value;
                                if (val.length > 1) {
                                    val = val.substring(0, 1);
                                    e.target.value = val;
                                }

                                this.updateOtp();

                                if (val.length === 1 && index < this.length - 1) {
                                    this.$refs['otp' + (index + 1)].focus();
                                }
                            },
                            handleKeyDown(e, index) {
                                if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                                    this.$refs['otp' + (index - 1)].focus();
                                }
                            },
                            handlePaste(e) {
                                let paste = (e.clipboardData || window.clipboardData).getData('text');
                                paste = paste.replace(/\D/g, '').substring(0, this.length);
                                if (paste) {
                                    for (let i = 0; i < paste.length; i++) {
                                        if (this.$refs['otp' + i]) {
                                            this.$refs['otp' + i].value = paste[i];
                                        }
                                    }
                                    this.updateOtp();
                                    let nextIndex = Math.min(paste.length, this.length - 1);
                                    if (this.$refs['otp' + nextIndex]) {
                                        this.$refs['otp' + nextIndex].focus();
                                    }
                                }
                            },
                            updateOtp() {
                                let code = '';
                                for (let i = 0; i < this.length; i++) {
                                    if (this.$refs['otp' + i]) {
                                        code += this.$refs['otp' + i].value;
                                    }
                                }
                                this.otp = code;
                            }
                        }" class="flex flex-col items-center space-y-4">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                {{ __('login.otp_label') }}
                            </label>

                            <div class="flex gap-2 sm:gap-4" @paste="handlePaste">
                                @for($i = 0; $i < 6; $i++)
                                    <input
                                        x-ref="otp{{ $i }}"
                                        type="text"
                                        inputmode="numeric"
                                        maxlength="1"
                                        class="w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-bold border-2 rounded-xl focus:border-primary-500 focus:ring-primary-500 bg-white dark:bg-slate-900/50 border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white transition-all duration-200 outline-none"
                                        @input="handleInput($event, {{ $i }})"
                                        @keydown="handleKeyDown($event, {{ $i }})"
                                        {{ $i === 0 ? 'autofocus' : '' }}
                                    />
                                @endfor
                            </div>

                            @error('otp')
                                <span class="text-sm text-negative-600 dark:text-negative-500 font-medium italic">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <x-button
                                secondary
                                xl
                                flat
                                :label="__('login.back')"
                                wire:click="backToEmail"
                                wire:loading.attr="disabled"
                                class="dark:text-slate-400 dark:hover:bg-slate-800"
                            />
                            <x-button
                                type="submit"
                                primary
                                xl
                                class="font-bold shadow-xl shadow-primary-500/20 hover:shadow-primary-500/40 transition-all duration-300"
                                wire:loading.attr="disabled"
                                wire:target="verifyOtp"
                            >
                                <x-icon wire:loading wire:target="verifyOtp" name="arrow-path" class="w-5 h-5 animate-spin" />
                                <span wire:loading wire:target="verifyOtp">{{ __('login.validating') }}</span>
                                <span wire:loading.remove wire:target="verifyOtp">{{ __('login.verify') }}</span>
                            </x-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
