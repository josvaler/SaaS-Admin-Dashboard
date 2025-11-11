<section class="w-100 flex flex-col gap-8">
  <header class="card-surface px-6 py-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div class="text-center w-full">
      <p class="text-sm uppercase tracking-[0.4em] text-slate-400 mb-1">Database Visualization</p>
      <h1 class="text-2xl font-semibold text-white">Users &amp; Operations Explorer</h1>
      <p class="text-sm text-slate-400">Browse users, inspect their operations, and filter data in real-time.</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
      <div class="relative flex-1">
        <label for="userSearchInput" class="sr-only">Search users</label>
        <input
          id="userSearchInput"
          type="search"
          placeholder="Search users by name or email"
          class="input-dark w-full pl-11 pr-4 py-2.5 text-sm focus:outline-none"
          autocomplete="off">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
          <i class="bi bi-search"></i>
        </span>
      </div>
      <div class="flex items-center gap-2">
        <span class="badge-accent hidden sm:inline-flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          Live filters enabled
        </span>
        <button id="clearFiltersButton" type="button" class="btn-accent flex items-center gap-2">
          <i class="bi bi-arrow-counterclockwise"></i>
          Reset
        </button>
      </div>
    </div>
  </header>

  <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
    <section id="users-section" class="xl:col-span-5 card-surface">
      <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-white/5 pb-4 text-center">
        <div class="w-full">
          <h2 class="text-lg font-semibold text-white">Users</h2>
          <p class="text-sm text-slate-400">Select a user to inspect recent operations.</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-400 uppercase tracking-[0.2em]" id="usersSummary">
          Loading...
        </div>
      </header>

      <div class="mt-4 overflow-hidden rounded-xl border border-white/5">
        <div class="overflow-x-auto table-scroll table-scroll-users">
          <table class="table-dark min-w-full" aria-label="Users table">
            <thead>
              <tr>
                <th scope="col" class="w-16">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col" class="w-36">Joined</th>
              </tr>
            </thead>
            <tbody id="usersTableBody" class="text-sm">
              <tr>
                <td colspan="4" class="py-6 text-center text-slate-400">Loading users…</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <footer class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between text-sm text-slate-400">
        <div class="flex items-center gap-2">
          <span>Rows per page:</span>
          <select id="usersPerPage" class="input-dark text-sm w-24 py-1">
            <option value="5">5</option>
            <option value="10" selected>10</option>
          </select>
        </div>
        <div class="flex items-center gap-3" id="usersPagination">
          <button type="button" class="btn-accent disabled:opacity-40 disabled:pointer-events-none px-3 py-2 text-xs" data-action="prev">
            <i class="bi bi-chevron-left"></i>
          </button>
          <span id="usersPaginationLabel">Page 1 of 1</span>
          <button type="button" class="btn-accent disabled:opacity-40 disabled:pointer-events-none px-3 py-2 text-xs" data-action="next">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </footer>
    </section>

    <section id="operations-section" class="xl:col-span-7 card-surface">
      <header class="flex flex-col gap-3 border-b border-white/5 pb-4 text-center">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <h2 class="text-lg font-semibold text-white">Operations</h2>
            <p class="text-sm text-slate-400">
              Showing operations for <span id="selectedUserName" class="text-white font-medium">no user selected</span>.
            </p>
          </div>
          <div class="flex items-center gap-2 text-xs text-slate-400 uppercase tracking-[0.2em]" id="operationsSummary">
            —
          </div>
        </div>
        <div class="grid gap-3 md:grid-cols-2">
          <div class="relative">
            <label for="operationTypeInput" class="text-xs uppercase tracking-[0.3em] text-slate-500 block mb-1">Operation type</label>
            <input
              id="operationTypeInput"
              type="search"
              placeholder="Filter by operation type"
              class="input-dark w-full pl-10 pr-4 py-2.5 text-sm">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 translate-y-3">
              <i class="bi bi-funnel"></i>
            </span>
          </div>
          <div class="relative">
            <label for="operationsPerPage" class="text-xs uppercase tracking-[0.3em] text-slate-500 block mb-1">Rows per page</label>
            <select id="operationsPerPage" class="input-dark w-full text-sm py-2.5">
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="15">15</option>
              <option value="20">20</option>
            </select>
          </div>
        </div>
      </header>

      <div class="mt-4 overflow-hidden rounded-xl border border-white/5" id="operationsPanel">
        <div class="overflow-x-auto table-scroll table-scroll-operations">
            <table class="table-dark min-w-full" aria-label="Operations table">
              <thead>
                <tr>
                  <th scope="col" class="w-16">ID</th>
                  <th scope="col">Type</th>
                  <th scope="col" class="w-28">File Size</th>
                  <th scope="col" class="w-32">Status</th>
                  <th scope="col" class="w-40">Date</th>
                  <th scope="col" class="w-32">Details</th>
                </tr>
              </thead>
              <tbody id="operationsTableBody" class="text-sm">
                <tr>
                  <td colspan="6" class="py-6 text-center text-slate-400">
                    Select a user to view their operations.
                  </td>
                </tr>
              </tbody>
          </table>
        </div>
      </div>

      <div id="operationDetailCard" class="mt-4 hidden rounded-xl border border-white/5 overflow-hidden">
        <div class="px-5 py-4 border-b border-white/5">
          <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Selected operation</p>
          <h3 id="operationDetailTitle" class="text-lg font-semibold text-white">Select an operation</h3>
        </div>
        <div class="px-5 py-4 text-sm text-slate-300">
          <dl class="grid gap-4 md:grid-cols-2">
            <div>
              <dt class="text-xs uppercase tracking-[0.3em] text-slate-500">Type</dt>
              <dd id="detailOperationType" class="text-base text-white">—</dd>
            </div>
            <div>
              <dt class="text-xs uppercase tracking-[0.3em] text-slate-500">Status</dt>
              <dd id="detailOperationStatus" class="text-base">—</dd>
            </div>
            <div>
              <dt class="text-xs uppercase tracking-[0.3em] text-slate-500">File size</dt>
              <dd id="detailOperationSize" class="text-base">—</dd>
            </div>
            <div>
              <dt class="text-xs uppercase tracking-[0.3em] text-slate-500">Date</dt>
              <dd id="detailOperationDate" class="text-base">—</dd>
            </div>
            <div class="md:col-span-2">
              <dt class="text-xs uppercase tracking-[0.3em] text-slate-500">Created</dt>
              <dd id="detailOperationCreated" class="text-base">—</dd>
            </div>
          </dl>
        </div>
      </div>

      <footer class="mt-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-sm text-slate-400">
        <div id="operationSubtotal" class="text-xs uppercase tracking-[0.2em]">—</div>
        <div class="flex items-center gap-3 justify-center w-full sm:w-auto" id="operationsPagination">
          <button type="button" class="btn-accent disabled:opacity-40 disabled:pointer-events-none px-3 py-2 text-xs" data-action="prev">
            <i class="bi bi-chevron-left"></i>
          </button>
          <span id="operationsPaginationLabel">Page 1 of 1</span>
          <button type="button" class="btn-accent disabled:opacity-40 disabled:pointer-events-none px-3 py-2 text-xs" data-action="next">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </footer>
    </section>
  </div>
</section>

<script src="<?= asset_url('js/database.js') ?>"></script>

