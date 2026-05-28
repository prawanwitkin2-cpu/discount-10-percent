(function () {
  const form = document.querySelector('[data-stepper-form]');
  if (!form) return;

  const steps = Array.from(form.querySelectorAll('[data-step]'));
  const indicators = Array.from(form.querySelectorAll('[data-step-indicator]'));
  const prev = form.querySelector('[data-prev]');
  const next = form.querySelector('[data-next]');
  const submit = form.querySelector('[data-submit]');
  const actions = form.querySelector('.actions');
  let current = 0;
  let activePopup = null;

  function showMiniPopup(message, focusEl) {
    if (activePopup) {
      activePopup.remove();
      activePopup = null;
    }

    const overlay = document.createElement('div');
    overlay.className = 'mini-popup-overlay';
    overlay.innerHTML = [
      '<div class="mini-popup" role="dialog" aria-modal="true" aria-label="แจ้งเตือน">',
      '  <div class="mini-popup-icon" aria-hidden="true">☕</div>',
      '  <p class="mini-popup-title">กรอกข้อมูลไม่ครบ</p>',
      `  <p class="mini-popup-text">${message}</p>`,
      '  <button type="button" class="mini-popup-btn">ตกลง</button>',
      '</div>'
    ].join('');

    function closePopup() {
      overlay.classList.remove('show');
      window.setTimeout(function () {
        overlay.remove();
        activePopup = null;
        if (focusEl) focusEl.focus();
      }, 160);
    }

    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) {
        closePopup();
      }
    });

    const button = overlay.querySelector('.mini-popup-btn');
    button.addEventListener('click', closePopup);

    document.body.appendChild(overlay);
    requestAnimationFrame(function () {
      overlay.classList.add('show');
      button.focus();
    });

    activePopup = overlay;
  }

  function showSaveSuccessModal() {
    if (activePopup) {
      activePopup.remove();
      activePopup = null;
    }

    const overlay = document.createElement('div');
    overlay.className = 'mini-popup-overlay show';
    overlay.innerHTML = [
      '<div class="mini-popup mini-popup-success" role="dialog" aria-modal="true" aria-label="บันทึกสำเร็จ">',
      '  <div class="mini-popup-icon success-icon" aria-hidden="true">✓</div>',
      '  <p class="mini-popup-title">บันทึกข้อมูลสำเร็จ</p>',
      '  <p class="mini-popup-text">ระบบบันทึกข้อมูลเรียบร้อยแล้ว</p>',
      '  <button type="button" class="mini-popup-btn">ตกลง</button>',
      '</div>'
    ].join('');

    let closed = false;
    function closeSuccess() {
      if (closed) return;
      closed = true;
      overlay.classList.remove('show');
      window.setTimeout(function () {
        overlay.remove();
        activePopup = null;
        form.reset();
        showStep(0);
      }, 160);
    }

    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) {
        closeSuccess();
      }
    });

    const button = overlay.querySelector('.mini-popup-btn');
    button.addEventListener('click', closeSuccess);

    document.body.appendChild(overlay);
    button.focus();
    activePopup = overlay;
    window.setTimeout(closeSuccess, 2000);
  }

  function validateCurrentStep() {
    if (current === 0) {
      const pdpa = form.querySelector('input[name="pdpa_accepted"]');
      if (!pdpa || !pdpa.checked) {
        showMiniPopup('กรุณายอมรับ PDPA ก่อนดำเนินการต่อ', pdpa);
        return false;
      }
    }

    if (current === 1) {
      const nickname = form.querySelector('input[name="nickname"]');
      const department = form.querySelector('input[name="department_name"]');

      if (!nickname || nickname.value.trim() === '') {
        showMiniPopup('กรุณากรอกชื่อเล่น', nickname);
        return false;
      }

      if (!department || !department.value) {
        showMiniPopup('กรุณาเลือกสำนัก', department);
        return false;
      }
    }

    return true;
  }

  function validateFinalSubmit() {
    const pdpa = form.querySelector('input[name="pdpa_accepted"]');
    const nickname = form.querySelector('input[name="nickname"]');
    const department = form.querySelector('input[name="department_name"]');
    const branch = form.querySelector('select[name="branch_name"]');
    const hot = form.querySelector('input[name="hot_cups"]');
    const cold = form.querySelector('input[name="cold_cups"]');

    if (!pdpa || !pdpa.checked) {
      showMiniPopup('กรุณายอมรับ PDPA ก่อนบันทึกข้อมูล', pdpa);
      return false;
    }

    if (!nickname || nickname.value.trim() === '') {
      showMiniPopup('กรุณากรอกชื่อเล่น', nickname);
      return false;
    }

    if (!department || !department.value) {
      showMiniPopup('กรุณาเลือกสำนัก', department);
      return false;
    }

    if (!branch || !branch.value) {
      showMiniPopup('กรุณาเลือกสาขา', branch);
      return false;
    }

    const hotValue = parseInt(hot && hot.value ? hot.value : '0', 10) || 0;
    const coldValue = parseInt(cold && cold.value ? cold.value : '0', 10) || 0;
    if ((hotValue + coldValue) <= 0) {
      showMiniPopup('กรุณาใส่จำนวนแก้วอย่างน้อย 1 รายการ', hot || cold);
      return false;
    }

    return true;
  }

  function showStep(index) {
    const nextIndex = Math.max(0, Math.min(index, steps.length - 1));
    // ไม่มี early-return เพื่อให้ finishShowStep ทำงานทุกครั้ง (รวมถึง step 0)

    const currentStep = steps[current];
    const nextStep = steps[nextIndex];

    if (currentStep && currentStep.classList.contains('active') && current !== nextIndex) {
      currentStep.classList.remove('active', 'fade-in');
      currentStep.classList.add('fade-out');

      setTimeout(() => {
        currentStep.classList.remove('fade-out');
        finishShowStep(nextIndex, nextStep);
      }, 250);
    } else {
      finishShowStep(nextIndex, nextStep);
    }
  }

  function finishShowStep(nextIndex, nextStep) {
    current = nextIndex;
    steps.forEach((step, idx) => {
      step.classList.toggle('active', idx === current);
      if (idx === current) {
        step.classList.add('fade-in');
      } else {
        step.classList.remove('fade-in');
      }
    });

    indicators.forEach((item, idx) => {
      item.classList.toggle('active', idx === current);
      item.classList.toggle('completed', idx < current);
    });

    // สั่งเซ็ตเฉพาะถ้า element นั้นมีอยู่ (guard null)
    if (prev) prev.hidden = current === 0;
    if (next) next.hidden = current === steps.length - 1;
    if (submit) submit.hidden = current !== steps.length - 1;

    // ซ่อนปุ่มทั้งหมดบนหน้า PDPA (step 0), แสดงเมื่อ step >= 1
    if (actions) {
      actions.hidden = current === 0;
      if (steps[current]) {
        steps[current].appendChild(actions);
      }
    }
  }

  if (prev) {
    prev.addEventListener('click', function () {
      showStep(current - 1);
    });
  }

  // ติ๊กถูก PDPA แล้วข้ามหน้าอัตโนมัติ
  const pdpaCheckbox = form.querySelector('input[name="pdpa_accepted"]');
  if (pdpaCheckbox) {
    pdpaCheckbox.addEventListener('change', function () {
      if (this.checked && current === 0) {
        showStep(1);
      }
    });
  }

  form.addEventListener('submit', function (e) {
    if (!validateFinalSubmit()) {
      e.preventDefault();
    }
  });

  ['hot_cups', 'cold_cups'].forEach(function (name) {
    const input = form.querySelector('input[name="' + name + '"]');
    if (!input) return;
    input.addEventListener('input', function () {
      this.value = String(this.value || '').replace(/\D+/g, '');
    });
    input.addEventListener('keydown', function (e) {
      if (e.key === 'e' || e.key === 'E' || e.key === '+' || e.key === '-') {
        e.preventDefault();
      }
    });
  });

  const startStep = parseInt(form.dataset.startStep || '0', 10);
  showStep(startStep);

  const successFlag = document.getElementById('save-success-flag');
  if (successFlag && successFlag.dataset.saveSuccess === '1') {
    showSaveSuccessModal();
  }
})();
