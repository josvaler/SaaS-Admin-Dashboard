const state = {
  users: {
    search: '',
    page: 1,
    perPage: 10,
    totalPages: 1,
  },
  operations: {
    userId: null,
    operationType: '',
    page: 1,
    perPage: 10,
    totalPages: 1,
  },
  selectedUser: null,
  selectedOperation: null,
};

const dom = {
  userSearchInput: document.getElementById('userSearchInput'),
  clearFiltersButton: document.getElementById('clearFiltersButton'),
  usersTableBody: document.getElementById('usersTableBody'),
  usersPerPage: document.getElementById('usersPerPage'),
  usersPagination: document.getElementById('usersPagination'),
  usersPaginationLabel: document.getElementById('usersPaginationLabel'),
  usersSummary: document.getElementById('usersSummary'),
  operationsPanel: document.getElementById('operationsPanel'),
  operationsTableBody: document.getElementById('operationsTableBody'),
  operationsPerPage: document.getElementById('operationsPerPage'),
  operationsPagination: document.getElementById('operationsPagination'),
  operationsPaginationLabel: document.getElementById('operationsPaginationLabel'),
  operationsSummary: document.getElementById('operationsSummary'),
  operationTypeInput: document.getElementById('operationTypeInput'),
  operationSubtotal: document.getElementById('operationSubtotal'),
  selectedUserName: document.getElementById('selectedUserName'),
  operationDetailCard: document.getElementById('operationDetailCard'),
  operationDetailTitle: document.getElementById('operationDetailTitle'),
  operationDetailType: document.getElementById('detailOperationType'),
  operationDetailStatus: document.getElementById('detailOperationStatus'),
  operationDetailSize: document.getElementById('detailOperationSize'),
  operationDetailDate: document.getElementById('detailOperationDate'),
  operationDetailCreated: document.getElementById('detailOperationCreated'),
};

const formatters = {
  date: new Intl.DateTimeFormat(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }),
  number: new Intl.NumberFormat(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
};

const debounce = (fn, delay = 220) => {
  let timer;
  return (...args) => {
    window.clearTimeout(timer);
    timer = window.setTimeout(() => fn(...args), delay);
  };
};

const fetchJson = async (url, controller) => {
  const response = await fetch(url, {
    signal: controller?.signal,
    headers: { 'Accept': 'application/json' },
  });
  if (!response.ok) {
    throw new Error(`Request failed (${response.status})`);
  }
  return response.json();
};

let usersAbortController;
const loadUsers = async () => {
  try {
    usersAbortController?.abort();
    usersAbortController = new AbortController();
    dom.usersTableBody.innerHTML = `
      <tr>
        <td colspan="4" class="py-6 text-center text-slate-400">Loading users…</td>
      </tr>`;

    const params = new URLSearchParams({
      q: state.users.search,
      page: String(state.users.page),
      per_page: String(state.users.perPage),
    });

    const payload = await fetchJson(`/api/users.php?${params.toString()}`, usersAbortController);
    if (!payload.success) {
      throw new Error(payload.error || 'Unable to load users');
    }

    const { items, meta } = payload.data;
    state.users.totalPages = meta.total_pages;
    renderUsers(items, meta);
  } catch (error) {
    if (error.name === 'AbortError') {
      return;
    }
    dom.usersTableBody.innerHTML = `
      <tr>
        <td colspan="4" class="py-6 text-center text-red-400">Failed to load users.</td>
      </tr>`;
    console.error(error);
  }
};

const renderUsers = (users, meta) => {
  if (users.length === 0) {
    dom.usersTableBody.innerHTML = `
      <tr>
        <td colspan="4" class="py-6 text-center text-slate-400">
          No users found. Try adjusting your search.
        </td>
      </tr>`;
  } else {
    dom.usersTableBody.innerHTML = users
      .map(
        (user) => `
        <tr class="cursor-pointer transition hover:bg-white/5"
            data-user-id="${user.id}"
            data-user-name="${user.name}"
            data-user-email="${user.email}">
          <td class="font-mono text-xs text-slate-400">#${user.id}</td>
          <td class="font-medium text-white">${user.name}</td>
          <td class="text-slate-400">${user.email}</td>
          <td class="text-slate-400">${formatters.date.format(new Date(user.created_at))}</td>
        </tr>`,
      )
      .join('');
  }

  dom.usersSummary.textContent = `${meta.total} result${meta.total === 1 ? '' : 's'}`;
  dom.usersPaginationLabel.textContent = `Page ${meta.page} of ${meta.total_pages}`;
  dom.usersPagination.querySelector('[data-action="prev"]').disabled = !meta.has_prev;
  dom.usersPagination.querySelector('[data-action="next"]').disabled = !meta.has_next;
};

let operationsAbortController;
const loadOperations = async () => {
  if (!state.operations.userId) {
    dom.operationsTableBody.innerHTML = `
      <tr>
        <td colspan="6" class="py-6 text-center text-slate-400">
          Select a user to view their operations.
        </td>
      </tr>`;
    dom.operationsSummary.textContent = '—';
    dom.operationSubtotal.textContent = '—';
    state.selectedOperation = null;
    highlightOperationRow(null);
    updateOperationDetailCard(null);
    return;
  }

  try {
    operationsAbortController?.abort();
    operationsAbortController = new AbortController();

    dom.operationsTableBody.innerHTML = `
      <tr>
        <td colspan="6" class="py-6 text-center text-slate-400">Loading operations…</td>
      </tr>`;

    const params = new URLSearchParams({
      user_id: String(state.operations.userId),
      operation_type: state.operations.operationType,
      page: String(state.operations.page),
      per_page: String(state.operations.perPage),
    });

    const payload = await fetchJson(`/api/operations.php?${params.toString()}`, operationsAbortController);
    if (!payload.success) {
      throw new Error(payload.error || 'Unable to load operations');
    }

    const { user, operations, meta } = payload.data;
    state.selectedUser = user;
    state.operations.totalPages = meta.total_pages;
    state.selectedOperation = null;
    updateOperationDetailCard(null);
    renderOperations(operations, meta);
  } catch (error) {
    if (error.name === 'AbortError') {
      return;
    }
    dom.operationsTableBody.innerHTML = `
      <tr>
        <td colspan="6" class="py-6 text-center text-red-400">Failed to load operations.</td>
      </tr>`;
    dom.operationsSummary.textContent = '—';
    dom.operationSubtotal.textContent = '—';
    state.selectedOperation = null;
    highlightOperationRow(null);
    updateOperationDetailCard(null);
    console.error(error);
  }
};

const escapeAttr = (value) => String(value ?? '')
  .replace(/&/g, '&amp;')
  .replace(/"/g, '&quot;')
  .replace(/'/g, '&#39;');

const highlightOperationRow = (operationId) => {
  if (!dom.operationsTableBody) {
    return;
  }

  dom.operationsTableBody.querySelectorAll('tr[data-op-id]').forEach((tr) => {
    tr.classList.toggle('bg-white/10', operationId && tr.dataset.opId === operationId);
  });
};

const updateOperationDetailCard = (detail) => {
  if (!dom.operationDetailCard) {
    return;
  }

  if (!detail) {
    dom.operationDetailCard.classList.add('hidden');
    dom.operationDetailTitle.textContent = 'Select an operation';
    dom.operationDetailType.textContent = '—';
    dom.operationDetailStatus.textContent = '—';
    dom.operationDetailSize.textContent = '—';
    dom.operationDetailDate.textContent = '—';
    dom.operationDetailCreated.textContent = '—';
    return;
  }

  dom.operationDetailTitle.textContent = `Operation #${detail.id ?? '—'}`;
  dom.operationDetailType.textContent = detail.type ?? '—';
  dom.operationDetailStatus.textContent = (detail.status || '—').toString().toUpperCase();

  dom.operationDetailSize.textContent = Number.isFinite(detail.fileSize)
    ? `${formatters.number.format(detail.fileSize)} KB`
    : '—';

  dom.operationDetailDate.textContent = detail.date
    ? formatters.date.format(new Date(detail.date))
    : '—';

  dom.operationDetailCreated.textContent = detail.created
    ? formatters.date.format(new Date(detail.created))
    : '—';

  dom.operationDetailCard.classList.remove('hidden');
};

const renderOperations = (operations, meta) => {
  dom.selectedUserName.textContent = state.selectedUser ? state.selectedUser.name : 'no user selected';

  if (operations.length === 0) {
    dom.operationsTableBody.innerHTML = `
      <tr>
        <td colspan="6" class="py-6 text-center text-slate-400">
          No operations matched the current filters.
        </td>
      </tr>`;
    updateOperationDetailCard(null);
    highlightOperationRow(null);
  } else {
    dom.operationsTableBody.innerHTML = operations
      .map((op) => {
        const sizeValue = op.file_size !== null ? Number(op.file_size) : null;
        const status = (op.status || '').toUpperCase();
        const statusClass = status === 'SUCCESS'
          ? 'status-pill success'
          : status === 'FAILED'
            ? 'status-pill danger'
            : 'status-pill warning';
        return `
          <tr class="transition hover:bg-white/5 cursor-pointer"
              data-op-id="${escapeAttr(op.id)}"
              data-op-type="${escapeAttr(op.operation_type)}"
              data-op-status="${escapeAttr(op.status)}"
              data-op-size="${escapeAttr(op.file_size)}"
              data-op-date="${escapeAttr(op.date)}"
              data-op-created="${escapeAttr(op.created_at)}">
            <td class="font-mono text-xs text-slate-400">#${op.id}</td>
            <td class="text-white font-medium">${op.operation_type ?? '—'}</td>
            <td class="text-slate-200">${sizeValue !== null ? `${formatters.number.format(sizeValue)} KB` : '—'}</td>
            <td>
              <span class="${statusClass}">
                <span class="w-2 h-2 rounded-full bg-current/70"></span>
                ${status || '—'}
              </span>
            </td>
            <td class="text-slate-400">${op.date ? formatters.date.format(new Date(op.date)) : '—'}</td>
            <td class="text-slate-500 text-xs italic">Tap row</td>
          </tr>`;
      })
      .join('');

    if (state.selectedOperation) {
      highlightOperationRow(state.selectedOperation.id);
      updateOperationDetailCard(state.selectedOperation);
    } else {
      highlightOperationRow(null);
      updateOperationDetailCard(null);
    }
  }

  dom.operationsSummary.textContent = `${meta.total} operation${meta.total === 1 ? '' : 's'}`;
  dom.operationsPaginationLabel.textContent = `Page ${meta.page} of ${meta.total_pages}`;
  dom.operationsPagination.querySelector('[data-action="prev"]').disabled = !meta.has_prev;
  dom.operationsPagination.querySelector('[data-action="next"]').disabled = !meta.has_next;

  const pageTotal = operations.reduce((acc, item) => acc + (Number(item.file_size) || 0), 0);
  dom.operationSubtotal.textContent = `Page total: ${formatters.number.format(pageTotal)} KB`;
};

const resetOperationsState = () => {
  state.operations.page = 1;
  state.operations.operationType = '';
  dom.operationTypeInput.value = '';
  dom.operationsSummary.textContent = '—';
  dom.operationSubtotal.textContent = '—';
  dom.selectedUserName.textContent = 'no user selected';
   state.selectedOperation = null;
  dom.operationsTableBody.innerHTML = `
    <tr>
      <td colspan="6" class="py-6 text-center text-slate-400">
        Select a user to view their operations.
      </td>
    </tr>`;
  highlightOperationRow(null);
  updateOperationDetailCard(null);
};

const handleUserRowClick = (event) => {
  const row = event.target.closest('tr[data-user-id]');
  if (!row) {
    return;
  }

  const userId = Number(row.dataset.userId);
  const userName = row.dataset.userName;
  const userEmail = row.dataset.userEmail;

  state.operations.userId = userId;
  state.operations.page = 1;
  dom.selectedUserName.textContent = `${userName} (${userEmail})`;
  loadOperations();

  dom.usersTableBody.querySelectorAll('tr[data-user-id]').forEach((tr) => {
    tr.classList.toggle('bg-white/10', tr === row);
  });
};

const handleOperationRowClick = (event) => {
  const row = event.target.closest('tr[data-op-id]');
  if (!row) {
    return;
  }

  const detail = {
    id: row.dataset.opId ?? '—',
    type: row.dataset.opType ?? '—',
    status: row.dataset.opStatus ?? '—',
    fileSize: row.dataset.opSize ? Number(row.dataset.opSize) : null,
    date: row.dataset.opDate ?? '',
    created: row.dataset.opCreated ?? '',
  };

  state.selectedOperation = detail;
  highlightOperationRow(detail.id);
  updateOperationDetailCard(detail);
};

const initPagination = () => {
  dom.usersPagination.querySelector('[data-action="prev"]').addEventListener('click', () => {
    if (state.users.page > 1) {
      state.users.page -= 1;
      loadUsers();
    }
  });
  dom.usersPagination.querySelector('[data-action="next"]').addEventListener('click', () => {
    if (state.users.page < state.users.totalPages) {
      state.users.page += 1;
      loadUsers();
    }
  });

  dom.operationsPagination.querySelector('[data-action="prev"]').addEventListener('click', () => {
    if (state.operations.page > 1) {
      state.operations.page -= 1;
      loadOperations();
    }
  });
  dom.operationsPagination.querySelector('[data-action="next"]').addEventListener('click', () => {
    if (state.operations.page < state.operations.totalPages) {
      state.operations.page += 1;
      loadOperations();
    }
  });
};

const initEventListeners = () => {
  dom.userSearchInput.addEventListener(
    'input',
    debounce((event) => {
      state.users.search = event.target.value.trim();
      state.users.page = 1;
      loadUsers();
    }),
  );

  dom.usersPerPage.addEventListener('change', (event) => {
    state.users.perPage = Math.min(Number(event.target.value), 10);
    dom.usersPerPage.value = String(state.users.perPage);
    state.users.page = 1;
    loadUsers();
  });

  dom.operationsPerPage.addEventListener('change', (event) => {
    state.operations.perPage = Math.min(Number(event.target.value), 20);
    dom.operationsPerPage.value = String(state.operations.perPage);
    state.operations.page = 1;
    loadOperations();
  });

  dom.operationTypeInput.addEventListener(
    'input',
    debounce((event) => {
      state.operations.operationType = event.target.value.trim();
      state.operations.page = 1;
      loadOperations();
    }),
  );

  dom.clearFiltersButton.addEventListener('click', () => {
    state.users.search = '';
    state.users.page = 1;
    state.users.perPage = Number(dom.usersPerPage.value = '10');
    dom.userSearchInput.value = '';
    state.operations.userId = null;
    state.operations.operationType = '';
    state.operations.page = 1;
    state.operations.perPage = Number(dom.operationsPerPage.value = '10');
    dom.operationTypeInput.value = '';
    loadUsers();
    resetOperationsState();
    dom.usersTableBody.querySelectorAll('tr[data-user-id]').forEach((tr) => {
      tr.classList.remove('bg-white/10');
    });
  });

  dom.usersTableBody.addEventListener('click', handleUserRowClick);
  dom.operationsTableBody.addEventListener('click', handleOperationRowClick);
};

const initialize = () => {
  initPagination();
  initEventListeners();
  loadUsers();
};

initialize();

