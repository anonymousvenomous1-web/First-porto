window.initPasswordReveal = function (scope = document) {
  scope.querySelectorAll('input[type="password"]').forEach((input) => {
    if (input.closest('.password-field')) return;

    const wrapper = document.createElement('span');
    wrapper.className = 'password-field';
    input.parentNode.insertBefore(wrapper, input);
    wrapper.append(input);

    const button = document.createElement('button');
    button.className = 'password-reveal';
    button.type = 'button';
    button.setAttribute('aria-label', 'Tekan dan tahan untuk menampilkan kata sandi');
    button.title = 'Tekan dan tahan untuk menampilkan kata sandi';

    const icon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    icon.setAttribute('viewBox', '0 0 24 24');
    icon.setAttribute('aria-hidden', 'true');
    icon.setAttribute('focusable', 'false');

    const eye = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    eye.setAttribute('d', 'M2.1 12s3.6-7 9.9-7 9.9 7 9.9 7-3.6 7-9.9 7-9.9-7Z');
    icon.append(eye);

    const pupil = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
    pupil.setAttribute('cx', '12');
    pupil.setAttribute('cy', '12');
    pupil.setAttribute('r', '3');
    icon.append(pupil);
    button.append(icon);
    wrapper.append(button);

    let isRevealed = false;
    const showPassword = () => {
      if (isRevealed) return;
      input.type = 'text';
      isRevealed = true;
    };
    const hidePassword = () => {
      if (!isRevealed) return;
      input.type = 'password';
      isRevealed = false;
    };

    button.addEventListener('pointerdown', (event) => {
      if (event.button !== 0) return;
      event.preventDefault();
      showPassword();
      button.setPointerCapture(event.pointerId);
    });
    button.addEventListener('pointerup', hidePassword);
    button.addEventListener('pointercancel', hidePassword);
    button.addEventListener('lostpointercapture', hidePassword);
    button.addEventListener('keydown', (event) => {
      if ((event.key === ' ' || event.key === 'Enter') && !event.repeat) {
        event.preventDefault();
        showPassword();
      }
    });
    button.addEventListener('keyup', (event) => {
      if (event.key === ' ' || event.key === 'Enter') {
        event.preventDefault();
        hidePassword();
      }
    });
    button.addEventListener('blur', hidePassword);
  });
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => window.initPasswordReveal());
} else {
  window.initPasswordReveal();
}

