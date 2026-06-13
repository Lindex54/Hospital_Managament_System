document.addEventListener('DOMContentLoaded', () => {
  const openModal = (modal) => {
    modal.hidden = false;
    document.body.classList.add('overflow-hidden');
  };

  const closeModal = (modal) => {
    modal.hidden = true;

    if (!document.querySelector('.modal-shell:not([hidden])')) {
      document.body.classList.remove('overflow-hidden');
    }
  };

  document.addEventListener('click', (event) => {
    const target = event.target;

    if (!(target instanceof Element)) {
      return;
    }

    const openTrigger = target.closest('[data-modal-open]');

    if (openTrigger instanceof HTMLElement) {
      const modal = document.querySelector(`[data-modal="${openTrigger.dataset.modalOpen}"]`);

      if (modal) {
        openModal(modal);
      }

      return;
    }

    const closeTrigger = target.closest('[data-modal-close]');

    if (closeTrigger instanceof HTMLElement) {
      const modal = closeTrigger.closest('[data-modal]');

      if (modal) {
        closeModal(modal);
      }
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
      return;
    }

    document.querySelectorAll('[data-modal]:not([hidden])').forEach((modal) => {
      closeModal(modal);
    });
  });

  const dobInput = document.querySelector('[data-patient-dob]');
  const ageOutput = document.querySelector('[data-patient-age]');

  if (dobInput && ageOutput) {
    const updateAge = () => {
      if (!dobInput.value) {
        ageOutput.textContent = 'Age will appear here';
        return;
      }

      const dob = new Date(dobInput.value);
      const today = new Date();
      let age = today.getFullYear() - dob.getFullYear();
      const monthDiff = today.getMonth() - dob.getMonth();

      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age -= 1;
      }

      ageOutput.textContent = Number.isFinite(age) ? `${age} years old` : 'Age will appear here';
    };

    dobInput.addEventListener('input', updateAge);
    updateAge();
  }

  document.querySelectorAll('[data-room-filter]').forEach((roomSelect) => {
    const wardSelect = document.querySelector('[data-ward-filter]');

    if (!wardSelect) {
      return;
    }

    const filterRooms = () => {
      const selectedWard = wardSelect.value;

      Array.from(roomSelect.options).forEach((option, index) => {
        if (index === 0) {
          option.hidden = false;
          return;
        }

        option.hidden = selectedWard !== '' && option.dataset.wardId !== selectedWard;
      });

      if (roomSelect.selectedOptions[0]?.hidden) {
        roomSelect.value = '';
      }
    };

    wardSelect.addEventListener('change', filterRooms);
    filterRooms();
  });

  document.querySelectorAll('[data-bed-filter]').forEach((bedSelect) => {
    const roomSelect = document.querySelector('[data-room-filter]');

    if (!roomSelect) {
      return;
    }

    const filterBeds = () => {
      const selectedRoom = roomSelect.value;

      Array.from(bedSelect.options).forEach((option, index) => {
        if (index === 0) {
          option.hidden = false;
          return;
        }

        option.hidden = selectedRoom !== '' && option.dataset.roomId !== selectedRoom;
      });

      if (bedSelect.selectedOptions[0]?.hidden) {
        bedSelect.value = '';
      }
    };

    roomSelect.addEventListener('change', filterBeds);
    filterBeds();
  });

  document.querySelectorAll('[data-conditional-target]').forEach((field) => {
    const controllingSelect = document.querySelector(`[data-conditional-source="${field.dataset.conditionalTarget}"]`);

    if (!controllingSelect) {
      return;
    }

    const updateVisibility = () => {
      const shouldShow = controllingSelect.value === field.dataset.conditionalValue;
      field.hidden = !shouldShow;
    };

    controllingSelect.addEventListener('change', updateVisibility);
    updateVisibility();
  });

  document.querySelectorAll('textarea.form-input').forEach((textarea) => {
    const resize = () => {
      textarea.style.height = 'auto';
      textarea.style.height = `${textarea.scrollHeight}px`;
    };

    textarea.addEventListener('input', resize);
    resize();
  });
});
