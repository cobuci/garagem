<div class="min-h-screen flex flex-col md:flex-row bg-gray-50 dark:bg-gray-950 font-sans antialiased text-gray-900 dark:text-gray-100 transition-colors duration-300">
    {{-- Left Hero Panel: Command Depot Workstation Presence --}}
    <div class="hidden md:flex md:w-1/2 bg-gray-900 dark:bg-gray-900 relative overflow-hidden flex-col justify-between p-12 lg:p-16 text-white border-r border-gray-800">
        {{-- Subtle architectural grid backdrop --}}
        <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px); background-size: 24px 24px;"></div>

        {{-- Controlled, restrained glow anchors --}}
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-sky-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Header / Brand Lockup --}}
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-sky-600 rounded-xl flex items-center justify-center shadow-sm shadow-sky-500/20 ring-1 ring-white/10 shrink-0">
                    <x-icon name="bolt" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <span class="text-2xl font-bold tracking-tight text-white leading-none block">{{ config('app.name') }}</span>
                    <span class="text-xs font-semibold uppercase tracking-wider text-sky-400 mt-1 block">{{ __('login.hero_badge') }}</span>
                </div>
            </div>
        </div>

        {{-- Core Content & Value Proofs --}}
        <div class="relative z-10 max-w-lg space-y-8 my-auto py-12">
            <div class="space-y-3">
                <h1 class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight">
                    {{ __('login.hero_title') }}
                </h1>
                <p class="text-gray-400 text-base leading-relaxed">
                    {{ __('login.access_account') }}
                </p>
            </div>

            {{-- Operational Pillars --}}
            <div class="space-y-4 pt-2">
                <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-gray-800/60 border border-gray-800 backdrop-blur-sm">
                    <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center shrink-0 mt-0.5">
                        <x-icon name="chart-bar-square" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-200">{{ __('login.pillar_sales_title') }}</h2>
                        <p class="text-xs text-gray-400 leading-normal mt-0.5">{{ __('login.pillar_sales_desc') }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-gray-800/60 border border-gray-800 backdrop-blur-sm">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                        <x-icon name="device-phone-mobile" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-200">{{ __('login.pillar_offline_title') }}</h2>
                        <p class="text-xs text-gray-400 leading-normal mt-0.5">{{ __('login.pillar_offline_desc') }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-gray-800/60 border border-gray-800 backdrop-blur-sm">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center shrink-0 mt-0.5">
                        <x-icon name="key" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-200">{{ __('login.pillar_security_title') }}</h2>
                        <p class="text-xs text-gray-400 leading-normal mt-0.5">{{ __('login.pillar_security_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Trust Badge --}}
        <div class="relative z-10 flex items-center justify-between text-xs text-gray-500 border-t border-gray-800/80 pt-4">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>{{ __('login.secure_environment') }}</span>
            </div>
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
        </div>
    </div>

    {{-- Right Form Stage --}}
    <div class="w-full md:w-1/2 flex flex-col justify-between p-6 sm:p-10 md:p-12 lg:p-16 relative">
        {{-- Top Right Utility Bar: Theme Toggle --}}
        <div class="flex justify-end w-full">
            <button
                type="button"
                x-on:click="darkMode = !darkMode"
                aria-label="{{ __('login.toggle_theme') }}"
                class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500"
            >

                <x-icon x-show="!darkMode" name="moon" class="w-5 h-5" x-cloak />
                <x-icon x-show="darkMode" name="sun" class="w-5 h-5" x-cloak />
            </button>
        </div>

        {{-- Centered Auth Form Container --}}
        <div class="w-full max-w-md mx-auto my-auto py-8">
            {{-- Mobile Brand Header (< md) --}}
            <div class="md:hidden flex flex-col items-center gap-2 mb-8">
                <div class="w-11 h-11 bg-sky-600 rounded-xl flex items-center justify-center shadow-sm">
                    <x-icon name="bolt" class="w-6 h-6 text-white" />
                </div>
                <span class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ config('app.name') }}</span>
            </div>

            {{-- Step Titles --}}
            <div class="space-y-2 text-center mb-8">
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ $step == 1 ? __('login.welcome_back') : __('login.verify_email') }}
                </h2>

                @if($step == 1)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('login.access_account') }}
                    </p>
                @else
                    <div class="flex items-center justify-center flex-wrap gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                        <span>{{ __('login.otp_sent_to') }}</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $email }}</span>
                        <span>&bull;</span>
                        <button
                            type="button"
                            wire:click="backToEmail"
                            class="font-medium text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300 underline underline-offset-2 transition-colors cursor-pointer"
                        >
                            {{ __('login.change_email') }}
                        </button>
                    </div>
                @endif

            </div>

            {{-- Forms Container --}}
            <div>
                @if($step == 1)
                    <form wire:submit.prevent="sendOtp" class="space-y-6">
                        <x-input
                            wire:model="email"
                            :label="__('login.email_label')"
                            :placeholder="__('login.email_placeholder')"
                            icon="envelope"
                            type="email"
                            autocomplete="email"
                            autofocus
                            required
                        />

                        <div class="flex items-center justify-between">
                            <x-checkbox
                                wire:model="rememberMe"
                                :label="__('login.remember_me')"
                            />
                        </div>

                        <x-button
                            type="submit"
                            primary
                            xl
                            class="w-full font-semibold rounded-lg shadow-sm hover:bg-sky-700 active:bg-sky-800 transition-colors"
                            wire:loading.attr="disabled"
                            wire:target="sendOtp"
                        >
                            <x-icon wire:loading wire:target="sendOtp" name="arrow-path" class="w-5 h-5 animate-spin mr-2" />
                            <span wire:loading wire:target="sendOtp">{{ __('login.sending') }}</span>
                            <span wire:loading.remove wire:target="sendOtp">{{ __('login.send_code') }}</span>
                        </x-button>
                    </form>
                @else
                    <form wire:submit.prevent="verifyOtp" class="space-y-6">
                        <div
                            x-data="{
                                otp: @entangle('otp'),
                                length: 6,
                                init() {
                                    this.$nextTick(() => {
                                        if (this.$refs.otp0) {
                                            this.$refs.otp0.focus();
                                        }
                                    });
                                },
                                handleInput(e, index) {
                                    let val = e.target.value.replace(/\D/g, '');
                                    if (val.length > 1) {
                                        val = val.substring(val.length - 1);
                                    }
                                    e.target.value = val;

                                    this.updateOtp();

                                    if (val.length === 1 && index < this.length - 1) {
                                        let next = this.$refs['otp' + (index + 1)];
                                        if (next) {
                                            next.focus();
                                            next.select();
                                        }
                                    }
                                },
                                handleKeyDown(e, index) {
                                    if (e.key === 'Backspace') {
                                        if (e.target.value === '' && index > 0) {
                                            let prev = this.$refs['otp' + (index - 1)];
                                            if (prev) {
                                                prev.focus();
                                            }
                                        }
                                    } else if (e.key === 'ArrowLeft' && index > 0) {
                                        e.preventDefault();
                                        let prev = this.$refs['otp' + (index - 1)];
                                        if (prev) {
                                            prev.focus();
                                            prev.select();
                                        }
                                    } else if (e.key === 'ArrowRight' && index < this.length - 1) {
                                        e.preventDefault();
                                        let next = this.$refs['otp' + (index + 1)];
                                        if (next) {
                                            next.focus();
                                            next.select();
                                        }
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
                                    $wire.otp = code;
                                    if (code.length === this.length) {
                                        $wire.verifyOtp(code);
                                    }
                                }

                            }"
                            class="flex flex-col items-center space-y-4"
                        >
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('login.otp_label') }}
                            </label>

                            <div class="flex gap-2 sm:gap-3" @paste="handlePaste">
                                @for($i = 0; $i < 6; $i++)
                                    <input
                                        x-ref="otp{{ $i }}"
                                        type="text"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        autocomplete="one-time-code"
                                        maxlength="1"
                                        @focus="$event.target.select()"
                                        @input="handleInput($event, {{ $i }})"
                                        @keydown="handleKeyDown($event, {{ $i }})"
                                        class="w-11 h-14 sm:w-13 sm:h-16 text-center text-2xl font-bold font-mono tabular-nums rounded-xl border bg-white dark:bg-gray-800/80 text-gray-900 dark:text-white transition-all outline-none @error('otp') border-red-500 dark:border-red-500 ring-2 ring-red-500/20 text-red-600 dark:text-red-400 @else border-gray-200 dark:border-gray-700 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 @enderror"
                                    />
                                @endfor
                            </div>

                            @error('otp')
                                <span class="text-xs font-semibold text-red-600 dark:text-red-400 flex items-center gap-1 mt-1">
                                    <x-icon name="exclamation-circle" class="w-4 h-4 shrink-0" />
                                    <span>{{ $message }}</span>
                                </span>
                            @enderror

                            {{-- Resend Code Action --}}
                            <div class="flex items-center justify-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 pt-3">
                                <span>{{ __('login.didnt_receive') }}</span>
                                <button
                                    type="button"
                                    wire:click="sendOtp"
                                    wire:loading.attr="disabled"
                                    wire:target="sendOtp"
                                    class="font-semibold text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300 transition-colors cursor-pointer inline-flex items-center gap-1"
                                >
                                    <x-icon wire:loading wire:target="sendOtp" name="arrow-path" class="w-3.5 h-3.5 animate-spin" />
                                    <span>{{ __('login.resend_code') }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <x-button
                                secondary
                                xl
                                flat
                                icon="arrow-left"
                                :label="__('login.back')"
                                wire:click="backToEmail"
                                wire:loading.attr="disabled"
                                class="rounded-lg"
                            />
                            <x-button
                                type="submit"
                                primary
                                xl
                                class="font-semibold rounded-lg shadow-sm hover:bg-sky-700 transition-colors"
                                wire:loading.attr="disabled"
                                wire:target="verifyOtp"
                            >
                                <x-icon wire:loading wire:target="verifyOtp" name="arrow-path" class="w-5 h-5 animate-spin mr-2" />
                                <span wire:loading wire:target="verifyOtp">{{ __('login.validating') }}</span>
                                <span wire:loading.remove wire:target="verifyOtp">{{ __('login.verify') }}</span>
                            </x-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- Mobile Footer (< md) --}}
        <div class="md:hidden text-center text-xs text-gray-400 dark:text-gray-500 py-4">
            &copy; {{ date('Y') }} {{ config('app.name') }} &bull; {{ __('login.secure_environment') }}
        </div>
    </div>
</div>

