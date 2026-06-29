document.addEventListener('DOMContentLoaded', () => {
  const escapeHtml = (value) => String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;');

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
    const scope = roomSelect.closest('form') ?? document;
    const wardSelect = scope.querySelector('[data-ward-filter]');

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
    const scope = bedSelect.closest('form') ?? document;
    const roomSelect = scope.querySelector('[data-room-filter]');

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

  document.querySelectorAll('form').forEach((form) => {
    const roomList = form.querySelector('[data-room-list]');
    const roomTemplate = form.querySelector('[data-room-template]');
    const addRoomButton = form.querySelector('[data-add-room]');
    const capacityInput = form.querySelector('[data-capacity-total]');

    if (!roomList || !roomTemplate || !addRoomButton || !capacityInput) {
      return;
    }

    let roomIndex = roomList.querySelectorAll('[data-ward-room-item]').length;

    const updateCapacity = () => {
      const totalBeds = Array.from(roomList.querySelectorAll('[data-bed-count]')).reduce((sum, input) => {
        const value = Number.parseInt(input.value || '0', 10);
        return sum + (Number.isFinite(value) ? value : 0);
      }, 0);

      capacityInput.value = totalBeds > 0 ? String(totalBeds) : '';
    };

    const updateRemoveButtons = () => {
      const roomItems = roomList.querySelectorAll('[data-ward-room-item]');

      roomItems.forEach((item, index) => {
        const removeButton = item.querySelector('[data-remove-room]');

        if (!removeButton) {
          return;
        }

        removeButton.hidden = roomItems.length === 1 && index === 0;
      });
    };

    addRoomButton.addEventListener('click', () => {
      const html = roomTemplate.innerHTML.replaceAll('__INDEX__', String(roomIndex));
      roomIndex += 1;
      roomList.insertAdjacentHTML('beforeend', html);
      updateRemoveButtons();
      updateCapacity();
    });

    roomList.addEventListener('click', (event) => {
      const target = event.target;

      if (!(target instanceof Element)) {
        return;
      }

      const removeButton = target.closest('[data-remove-room]');

      if (!removeButton) {
        return;
      }

      const roomItem = removeButton.closest('[data-ward-room-item]');

      if (roomItem && roomList.querySelectorAll('[data-ward-room-item]').length > 1) {
        roomItem.remove();
      }

      updateRemoveButtons();
      updateCapacity();
    });

    roomList.addEventListener('input', (event) => {
      const target = event.target;

      if (target instanceof Element && target.matches('[data-bed-count]')) {
        updateCapacity();
      }
    });

    form.addEventListener('reset', () => {
      window.setTimeout(() => {
        const roomItems = roomList.querySelectorAll('[data-ward-room-item]');

        roomItems.forEach((item, index) => {
          if (index > 0) {
            item.remove();
          }
        });

        updateRemoveButtons();
        updateCapacity();
      }, 0);
    });

    updateRemoveButtons();
    updateCapacity();
  });

  document.querySelectorAll('[data-patient-search]').forEach((searchShell) => {
    const searchInput = searchShell.querySelector('[data-patient-search-input]');
    const hiddenInput = searchShell.querySelector('input[name="patient_id"]');
    const resultsPanel = searchShell.querySelector('[data-patient-search-results]');
    const searchUrl = searchShell.dataset.searchUrl;

    if (!(searchInput instanceof HTMLInputElement) || !(hiddenInput instanceof HTMLInputElement) || !(resultsPanel instanceof HTMLElement) || !searchUrl) {
      return;
    }

    let currentAbortController = null;
    let debounceTimer = null;

    const closeResults = () => {
      resultsPanel.innerHTML = '';
      resultsPanel.classList.add('hidden');
    };

    const openResults = () => {
      resultsPanel.classList.remove('hidden');
    };

    const renderMessage = (message) => {
      resultsPanel.innerHTML = `<p class="px-4 py-3 text-sm text-hospital-secondary">${escapeHtml(message)}</p>`;
      openResults();
    };

    const renderResults = (results) => {
      if (!Array.isArray(results) || results.length === 0) {
        renderMessage('No patients matched that search.');
        return;
      }

      resultsPanel.innerHTML = results.map((patient) => {
        const name = escapeHtml(patient.name || 'Unknown Patient');
        const patientNumber = escapeHtml(patient.patient_number || '');
        const phone = escapeHtml(patient.phone || 'No phone');

        return `
          <button
            class="flex w-full items-start justify-between gap-3 border-b border-hospital-borderSoft px-4 py-3 text-left transition last:border-b-0 hover:bg-hospital-primary/5"
            type="button"
            data-patient-result
            data-patient-id="${escapeHtml(patient.id || '')}"
            data-patient-label="${patientNumber} - ${name}"
          >
            <span>
              <span class="block text-sm font-bold text-hospital-ink">${patientNumber}</span>
              <span class="mt-1 block text-sm text-hospital-secondary">${name}</span>
            </span>
            <span class="whitespace-nowrap text-xs font-medium text-hospital-muted">${phone}</span>
          </button>
        `;
      }).join('');
      openResults();
    };

    const runSearch = async (query) => {
      if (currentAbortController) {
        currentAbortController.abort();
      }

      currentAbortController = new AbortController();
      renderMessage(query.trim() === '' ? 'Loading recent patients...' : 'Searching patients...');

      try {
        const url = new URL(searchUrl, window.location.origin);
        url.searchParams.set('q', query);

        const response = await fetch(url.toString(), {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
          },
          signal: currentAbortController.signal,
        });

        if (!response.ok) {
          throw new Error('Search failed');
        }

        const payload = await response.json();
        renderResults(payload.results || []);
      } catch (error) {
        if (error.name === 'AbortError') {
          return;
        }

        renderMessage('Unable to search patients right now.');
      }
    };

    const scheduleSearch = (query) => {
      window.clearTimeout(debounceTimer);
      debounceTimer = window.setTimeout(() => {
        runSearch(query);
      }, 220);
    };

    searchInput.addEventListener('focus', () => {
      scheduleSearch(searchInput.value.trim());
    });

    searchInput.addEventListener('input', () => {
      hiddenInput.value = '';
      searchInput.setCustomValidity('');
      scheduleSearch(searchInput.value.trim());
    });

    searchShell.addEventListener('click', (event) => {
      const target = event.target;

      if (!(target instanceof Element)) {
        return;
      }

      const resultButton = target.closest('[data-patient-result]');

      if (!(resultButton instanceof HTMLElement)) {
        return;
      }

      hiddenInput.value = resultButton.dataset.patientId || '';
      searchInput.value = resultButton.dataset.patientLabel || '';
      searchInput.setCustomValidity('');
      closeResults();
    });

    document.addEventListener('click', (event) => {
      const target = event.target;

      if (!(target instanceof Node) || searchShell.contains(target)) {
        return;
      }

      closeResults();
    });

    const form = searchShell.closest('form');
    if (form instanceof HTMLFormElement) {
      form.addEventListener('submit', (event) => {
        if (hiddenInput.value.trim() !== '') {
          searchInput.setCustomValidity('');
          return;
        }

        event.preventDefault();
        searchInput.setCustomValidity('Select a patient from the search results.');
        searchInput.reportValidity();
      });

      form.addEventListener('reset', () => {
        window.setTimeout(() => {
          hiddenInput.value = '';
          searchInput.value = '';
          searchInput.setCustomValidity('');
          closeResults();
        }, 0);
      });
    }
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

  document.querySelectorAll('[data-future-datetime]').forEach((input) => {
    if (!(input instanceof HTMLInputElement)) {
      return;
    }

    const pad = (value) => String(value).padStart(2, '0');
    const buildMinValue = () => {
      const now = new Date();
      return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
    };

    const applyMin = () => {
      input.min = buildMinValue();
    };

    const validateValue = () => {
      if (!input.value) {
        input.setCustomValidity('');
        return;
      }

      applyMin();
      if (input.value < input.min) {
        input.setCustomValidity('Choose a future appointment date and time.');
      } else {
        input.setCustomValidity('');
      }
    };

    input.addEventListener('focus', applyMin);
    input.addEventListener('input', validateValue);
    input.addEventListener('change', validateValue);
    applyMin();
  });

  document.querySelectorAll('[data-appointment-calendar]').forEach((calendarShell) => {
    const monthButtons = Array.from(calendarShell.querySelectorAll('[data-calendar-month-button]'));
    const monthPanels = Array.from(calendarShell.querySelectorAll('[data-calendar-month-panel]'));
    const overviewSection = calendarShell.firstElementChild;
    const defaultMonth = calendarShell.dataset.defaultMonth || monthButtons[0]?.dataset.monthKey || '';

    if (!(overviewSection instanceof HTMLElement) || monthButtons.length === 0 || monthPanels.length === 0) {
      return;
    }

    const activeButtonClasses = ['border-hospital-primary', 'bg-hospital-primary/5', 'shadow-[0_16px_30px_rgba(37,99,235,0.12)]'];
    const inactiveButtonClasses = ['border-hospital-borderSoft', 'bg-slate-50/60'];
    const activeTextClass = 'text-hospital-primary';
    const inactiveTextClass = 'text-hospital-ink';
    const modalBody = calendarShell.closest('.modal-body');

    const scrollToTop = () => {
      if (modalBody instanceof HTMLElement) {
        modalBody.scrollTo({ top: 0, behavior: 'smooth' });
        return;
      }

      calendarShell.scrollIntoView({ block: 'start', behavior: 'smooth' });
    };

    const ensureBackButton = (panel) => {
      let backButton = panel.querySelector('[data-calendar-back]');

      if (backButton instanceof HTMLButtonElement) {
        return backButton;
      }

      const panelHeader = panel.querySelector('.rounded-xl');
      if (!(panelHeader instanceof HTMLElement)) {
        return null;
      }

      const actionRow = document.createElement('div');
      actionRow.className = 'mb-4 flex justify-end';
      actionRow.innerHTML = `
        <button
          class="inline-flex items-center gap-2 rounded-2xl border border-hospital-primary bg-hospital-primary px-5 py-3 text-sm font-bold text-white shadow-[0_14px_30px_rgba(37,99,235,0.22)] transition duration-200 hover:-translate-y-0.5 hover:bg-[#0f5bd3] hover:shadow-[0_18px_36px_rgba(37,99,235,0.3)] focus:outline-none focus:ring-2 focus:ring-hospital-primary/30"
          type="button"
          data-calendar-back
        >
          <span aria-hidden="true">←</span>
          Back To Months
        </button>
      `;

      panelHeader.insertBefore(actionRow, panelHeader.firstElementChild);
      backButton = actionRow.querySelector('[data-calendar-back]');
      return backButton instanceof HTMLButtonElement ? backButton : null;
    };

    const showOverview = () => {
      overviewSection.hidden = false;

      monthButtons.forEach((button) => {
        const isActive = button.dataset.monthKey === defaultMonth;
        button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        button.classList.toggle(activeButtonClasses[0], isActive);
        button.classList.toggle(activeButtonClasses[1], isActive);
        button.classList.toggle(activeButtonClasses[2], isActive);
        button.classList.toggle(inactiveButtonClasses[0], !isActive);
        button.classList.toggle(inactiveButtonClasses[1], !isActive);

        const monthLabel = button.querySelector('p');
        if (monthLabel) {
          monthLabel.classList.toggle(activeTextClass, isActive);
          monthLabel.classList.toggle(inactiveTextClass, !isActive);
        }
      });

      monthPanels.forEach((panel) => {
        panel.hidden = true;
      });

      scrollToTop();
    };

    const showMonth = (monthKey) => {
      overviewSection.hidden = true;

      monthButtons.forEach((button) => {
        const isActive = button.dataset.monthKey === monthKey;
        button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        button.classList.toggle(activeButtonClasses[0], isActive);
        button.classList.toggle(activeButtonClasses[1], isActive);
        button.classList.toggle(activeButtonClasses[2], isActive);
        button.classList.toggle(inactiveButtonClasses[0], !isActive);
        button.classList.toggle(inactiveButtonClasses[1], !isActive);

        const monthLabel = button.querySelector('p');
        if (monthLabel) {
          monthLabel.classList.toggle(activeTextClass, isActive);
          monthLabel.classList.toggle(inactiveTextClass, !isActive);
        }
      });

      monthPanels.forEach((panel) => {
        const isActive = panel.dataset.monthKey === monthKey;
        panel.hidden = !isActive;

        if (isActive) {
          ensureBackButton(panel);
        }
      });

      scrollToTop();
    };

    calendarShell.addEventListener('click', (event) => {
      const target = event.target;

      if (!(target instanceof Element)) {
        return;
      }

      const backButton = target.closest('[data-calendar-back]');
      if (backButton instanceof HTMLElement) {
        showOverview();
        return;
      }

      const monthButton = target.closest('[data-calendar-month-button]');

      if (!(monthButton instanceof HTMLElement)) {
        return;
      }

      showMonth(monthButton.dataset.monthKey || defaultMonth);
    });

    showOverview();
  });
});
