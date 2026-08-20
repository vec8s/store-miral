<div x-data="{
  code: ['', '', '', ''],
  timer: 60,
  timerInterval: null,

  init() {
    this.startTimer();
    this.$watch('code', (val) => {
      if (val.every(c => c !== '')) {
        this.submitCode();
      }
    });
  },

  startTimer() {
    this.timer = 60;
    clearInterval(this.timerInterval);
    this.timerInterval = setInterval(() => {
      if (this.timer > 0) this.timer--;
      else clearInterval(this.timerInterval);
    }, 1000);
  },

  handleInput(e, index) {
    const val = e.target.value;
    if (val.length >= 1) {
      this.code[index] = val.slice(-1);
      if (index < 3) {
        this.$refs['otp_' + (index + 1)].focus();
      }
    }
  },

  handleKeyDown(e, index) {
    if (e.key === 'Backspace' && !this.code[index] && index > 0) {
      this.$refs['otp_' + (index - 1)].focus();
    }
  },

  submitCode() {
    const fullCode = this.code.join('');
    this.$dispatch('verify-otp-submitted', { code: fullCode });
  },

  resendOtp() {
    if (this.timer === 0) {
      this.code = ['', '', '', ''];
      this.startTimer();
      this.$dispatch('resend-otp-requested');
    }
  }
}">
  <div class="card-awesomic p-6 bg-slate-900 text-white rounded-[24px]">
    <div class="text-center mb-6">
      <div class="text-4xl mb-2">📩</div>
      <h4 class="font-bold text-base text-white">أدخل كود التحقق المرسل بريدياً</h4>
      <p class="text-xs text-slate-400 mt-1">تم إرسال كود من 4 أرقام لعنوان البريد الإلكتروني المدخل</p>
    </div>

    <!-- OTP Input Boxes -->
    <div class="flex justify-center gap-3 dir-ltr mb-6">
      <template x-for="(item, index) in 4" :key="index">
        <input type="text"
               maxlength="1"
               x-model="code[index]"
               :x-ref="'otp_' + index"
               @input="handleInput($event, index)"
               @keydown="handleKeyDown($event, index)"
               class="w-12 h-14 text-center text-xl font-extrabold text-slate-900 bg-white border border-slate-700 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-500 outline-none">
      </template>
    </div>

    <div class="flex items-center justify-between text-xs text-slate-400 pt-4 border-t border-slate-800">
      <button type="button" @click="resendOtp()" :disabled="timer > 0" class="hover:text-white disabled:opacity-50 font-bold py-2 min-h-[44px]">
        إعادة إرسال الكود <span x-show="timer > 0" x-text="'(' + timer + 'ث)'"></span>
      </button>
      <button type="button" @click="$dispatch('change-email')" class="text-orange-400 hover:underline py-2 min-h-[44px]">
        تعديل البريد
      </button>
    </div>
  </div>
</div>
