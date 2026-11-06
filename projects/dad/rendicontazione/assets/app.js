/**
 * Rendicontazione Ore — Main Application Controller (Vanilla JS SPA).
 * Handles authentication, real-time hours calculation, multi-shift management,
 * month/year navigation, admin user approvals, and export.
 */

'use strict';

const App = {
    state: {
        currentUser: null,
        activeView: 'month', // 'month', 'year', 'users'
        currentYear: 2026,
        currentMonth: 11,
        targetUserId: 0,
        targetUserName: '',
        monthData: null,
        yearData: null,
        usersList: [],
        presets: [],
        pendingCount: 0,
        googleClientId: '',
        theme: 'dark',
    },

    async init() {
        // 1. Initialize Theme
        const savedTheme = localStorage.getItem('dad_rend_theme') || 'dark';
        this.setTheme(savedTheme);

        // 2. Load API Config
        try {
            const cfg = await this.api('config');
            if (cfg.ok) {
                this.state.googleClientId = cfg.googleClientId;
            }
        } catch (e) {
            console.error('Config fetch failed:', e);
        }

        // 3. Set initial year & month (default to 2026 Nov or current)
        const now = new Date();
        this.state.currentYear = now.getFullYear();
        this.state.currentMonth = now.getMonth() + 1;

        // 4. Check Authentication
        await this.checkAuth();

        // 5. Setup Event Listeners
        this.setupEventListeners();
    },

    setTheme(theme) {
        this.state.theme = theme;
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('dad_rend_theme', theme);
        const icon = document.getElementById('theme-icon');
        if (icon) {
            icon.textContent = theme === 'dark' ? '☀️' : '🌙';
        }
    },

    toggleTheme() {
        this.setTheme(this.state.theme === 'dark' ? 'light' : 'dark');
    },

    // --- API HELPER ---
    async api(action, method = 'GET', data = null) {
        const url = `api/index.php?action=${encodeURIComponent(action)}`;
        const opts = {
            method,
            headers: {
                'Accept': 'application/json',
            },
        };

        if (data && method !== 'GET') {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(data);
        }

        const res = await fetch(url, opts);
        const json = await res.json();
        if (!res.ok && !json.ok) {
            throw new Error(json.error || 'Errore di comunicazione con il server');
        }
        return json;
    },

    // --- AUTHENTICATION ---
    async checkAuth() {
        try {
            const res = await this.api('auth.me');
            if (res.ok && res.authenticated && res.user) {
                this.state.currentUser = res.user;
                this.state.pendingCount = res.pending_users_count || 0;
                this.renderAppShell();
                this.loadActiveView();
            } else {
                this.state.currentUser = null;
                this.renderLoginView();
            }
        } catch (e) {
            this.state.currentUser = null;
            this.renderLoginView();
        }
    },

    async handleGoogleCredential(credential) {
        try {
            this.showToast('Accesso in corso...');
            const res = await this.api('auth.google', 'POST', { credential });
            if (res.ok) {
                this.state.currentUser = res.user;
                this.state.pendingCount = res.pending_users_count || 0;
                this.showToast(`Benvenuto, ${res.user.name}!`);
                this.renderAppShell();
                this.loadActiveView();
            }
        } catch (e) {
            this.showToast(e.message, 'danger');
        }
    },

    async devLogin(role = 10) {
        try {
            this.showToast('Accesso di sviluppo...');
            const res = await this.api('auth.dev_login', 'POST', { role });
            if (res.ok) {
                this.state.currentUser = res.user;
                this.showToast(`Connesso come ${res.user.name}`);
                this.renderAppShell();
                this.loadActiveView();
            }
        } catch (e) {
            this.showToast(e.message, 'danger');
        }
    },

    async logout() {
        try {
            await this.api('auth.logout', 'POST');
        } catch (e) {}
        this.state.currentUser = null;
        this.state.targetUserId = 0;
        this.renderLoginView();
    },

    // --- VIEW SWITCHING ---
    switchView(viewName) {
        this.state.activeView = viewName;
        document.querySelectorAll('.nav-tab').forEach(tab => {
            tab.classList.toggle('active', tab.dataset.view === viewName);
        });
        this.loadActiveView();
    },

    async loadActiveView() {
        if (!this.state.currentUser) return;

        // If user is pending manual approval:
        if (this.state.currentUser.status === 'pending') {
            this.renderPendingApprovalView();
            return;
        }

        // If user is rejected or disabled:
        if (this.state.currentUser.status !== 'active') {
            this.renderInactiveView();
            return;
        }

        if (this.state.activeView === 'month') {
            await this.loadMonthData();
        } else if (this.state.activeView === 'year') {
            await this.loadYearData();
        } else if (this.state.activeView === 'users') {
            await this.loadUsersData();
        } else if (this.state.activeView === 'history') {
            await this.loadHistoryData();
        }
    },

    // --- MONTH VIEW ---
    async loadMonthData() {
        const main = document.getElementById('view-container');
        if (!main) return;
        main.innerHTML = '<div style="padding:40px; text-align:center; color:var(--text-muted)">Caricamento orari...</div>';

        try {
            const targetParam = this.state.targetUserId > 0 ? `&target_user_id=${this.state.targetUserId}` : '';
            const res = await this.api(`hours.month&year=${this.state.currentYear}&month=${this.state.currentMonth}${targetParam}`);
            if (res.ok) {
                this.state.monthData = res.data;
                this.state.targetUserName = res.target_user.name;
                this.renderMonthView();
            }
        } catch (e) {
            main.innerHTML = `<div style="padding:40px; text-align:center; color:var(--danger)">Errore: ${e.message}</div>`;
        }
    },

    renderMonthView() {
        const m = this.state.monthData;
        if (!m) return;
        const main = document.getElementById('view-container');
        if (!main) return;

        const isAdmin = (this.state.currentUser.role >= 9);
        const isViewingOther = (this.state.targetUserId > 0 && this.state.targetUserId !== this.state.currentUser.id);

        let html = '';

        // Admin context switch notice
        if (isAdmin && isViewingOther) {
            html += `
                <div class="admin-context-bar">
                    <div class="admin-context-label">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span>Stai consultando e gestendo le ore di: <strong>${this.escape(this.state.targetUserName)}</strong></span>
                    </div>
                    <button class="btn btn-secondary btn-sm" onclick="App.setTargetUser(0)">
                        Torna alle mie ore
                    </button>
                </div>
            `;
        }

        // Controls bar: Month switcher & quick buttons
        html += `
            <div class="control-bar">
                <div class="period-switcher">
                    <button class="btn btn-secondary btn-icon" onclick="App.prevMonth()" title="Mese precedente">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <div class="current-period-title">${m.month_name} ${m.year}</div>
                    <button class="btn btn-secondary btn-icon" onclick="App.nextMonth()" title="Mese successivo">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="App.goToToday()" style="margin-left:8px;">Oggi</button>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="App.openAddDayModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Aggiungi Giornata
                    </button>
                    <button class="btn btn-secondary" onclick="App.applyQuickPresetToday()" title="Segna il turno standard per la giornata odierna">
                        ⚡ Turno Standard Oggi
                    </button>
                    <button class="btn btn-secondary" onclick="App.exportCsv()" title="Esporta in CSV">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Esporta CSV
                    </button>
                    <button class="btn btn-secondary btn-icon" onclick="window.print()" title="Stampa o Salva PDF">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    </button>
                </div>
            </div>
        `;

        // Monthly Summary Stats Cards
        html += `
            <div class="stats-grid">
                <div class="stat-card accent-card">
                    <span class="stat-label">Ore Ordinarie</span>
                    <span class="stat-value">${m.totals.ordinary_formatted}</span>
                    <span style="font-size:0.8rem; color:var(--text-muted)">${m.totals.ordinary_hours.toFixed(1)} ore decimali</span>
                </div>
                <div class="stat-card warning-card">
                    <span class="stat-label">Ore Straordinarie</span>
                    <span class="stat-value">${m.totals.overtime_formatted}</span>
                    <span style="font-size:0.8rem; color:var(--text-muted)">${m.totals.overtime_hours.toFixed(1)} ore decimali</span>
                </div>
                <div class="stat-card success-card">
                    <span class="stat-label">Totale Ore Mese</span>
                    <span class="stat-value">${m.totals.total_formatted}</span>
                    <span style="font-size:0.8rem; color:var(--text-muted)">${m.totals.total_human} complessive</span>
                </div>
                <div class="stat-card info-card">
                    <span class="stat-label">Giorni Lavorati</span>
                    <span class="stat-value">${m.totals.days_worked}</span>
                    <span style="font-size:0.8rem; color:var(--text-muted)">su ${m.days.length} giorni di calendario</span>
                </div>
            </div>
        `;

        // Printable header
        html += `
            <div class="print-header">
                <h2>Rendicontazione Ore &middot; ${m.month_name} ${m.year}</h2>
                <p>Lavoratore: <strong>${this.escape(this.state.targetUserName || this.state.currentUser.name)}</strong> &middot; Totale Ore: <strong>${m.totals.total_formatted}</strong> (${m.totals.ordinary_formatted} ord. + ${m.totals.overtime_formatted} straord.)</p>
            </div>
        `;

        // Table
        html += `
            <div class="timesheet-card">
                <div class="table-responsive">
                    <table class="timesheet-table">
                        <thead>
                            <tr>
                                <th style="width:160px">Data</th>
                                <th style="width:105px">Orario Inizio</th>
                                <th style="width:105px">Orario Fine</th>
                                <th style="width:120px">Tipo</th>
                                <th style="width:110px">Ordinarie</th>
                                <th style="width:110px">Straordinarie</th>
                                <th style="width:130px">Progressivo</th>
                                <th>Note ed Eventi</th>
                                <th style="width:90px; text-align:right">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        const todayStr = new Date().toISOString().split('T')[0];

        m.days.forEach(d => {
            const isToday = (d.date === todayStr);
            const rowClass = [
                d.is_weekend ? 'weekend-row' : '',
                isToday ? 'today-row' : '',
                d.day_type !== 'work' ? 'holiday-row' : '',
            ].filter(Boolean).join(' ');

            const shifts = d.shifts || [];

            if (shifts.length === 0) {
                // Single day row without shifts (e.g. rest day, sickness, or holiday)
                let noteTags = '';
                if (d.notes) {
                    noteTags = this.renderNoteBadges(d.notes, d.day_type);
                } else if (d.is_weekend) {
                    noteTags = '<span style="color:var(--text-muted); font-size:0.8rem">Riposo settimanale</span>';
                }

                html += `
                    <tr class="${rowClass}">
                        <td>
                            <div class="day-cell">
                                <span class="day-cell-main">${d.day_name} ${d.day_number}</span>
                                <span class="day-cell-sub">${d.date}</span>
                            </div>
                        </td>
                        <td colspan="3" style="color:var(--text-muted); font-size:0.85rem">
                            ${d.day_type !== 'work' ? `<span class="badge badge-pending">${d.day_type.toUpperCase()}</span>` : '—'}
                        </td>
                        <td class="time-badge">${d.has_entry ? d.hours_ordinary_formatted : '—'}</td>
                        <td class="time-badge">${d.has_entry ? d.hours_overtime_formatted : '—'}</td>
                        <td class="time-badge" style="color:var(--text-secondary)">${d.cumulative_hours > 0 ? d.cumulative_hours_formatted : '—'}</td>
                        <td>${noteTags}</td>
                        <td style="text-align:right">
                            <button class="btn btn-secondary btn-sm" onclick="App.openEditDayModal('${d.date}')" title="Modifica giornata">
                                ✏️
                            </button>
                        </td>
                    </tr>
                `;
            } else {
                // Multi-shift rows grouped under this date
                shifts.forEach((s, idx) => {
                    const isFirst = (idx === 0);
                    html += `
                        <tr class="${rowClass}">
                            <td>
                                ${isFirst ? `
                                    <div class="day-cell">
                                        <span class="day-cell-main">${d.day_name} ${d.day_number}</span>
                                        <span class="day-cell-sub">${d.date}</span>
                                    </div>
                                ` : ''}
                            </td>
                            <td class="time-badge">${s.start_time || '—'}</td>
                            <td class="time-badge">${s.end_time || '—'}</td>
                            <td>
                                ${s.is_overtime ? '<span class="shift-tag tag-str">Straordinario</span>' : '<span class="shift-tag tag-ord">Ordinario</span>'}
                            </td>
                            <td class="time-badge">${s.hours_ordinary > 0 ? App.formatHoursHms(s.hours_ordinary) : ''}</td>
                            <td class="time-badge" style="color:var(--warning)">${s.hours_overtime > 0 ? App.formatHoursHms(s.hours_overtime) : ''}</td>
                            <td class="time-badge" style="font-weight:700">
                                ${isFirst ? d.cumulative_hours_formatted : ''}
                            </td>
                            <td>
                                ${this.renderNoteBadges(s.notes || (isFirst ? d.notes : ''), d.day_type)}
                            </td>
                            <td style="text-align:right">
                                ${isFirst ? `
                                    <button class="btn btn-secondary btn-sm" onclick="App.openEditDayModal('${d.date}')" title="Modifica giornata">
                                        ✏️
                                    </button>
                                ` : ''}
                            </td>
                        </tr>
                    `;
                });
            }
        });

        // Table footer with sums
        html += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align:right; font-weight:700">TOTALE MESE:</td>
                                <td class="time-badge" style="color:var(--accent)">${m.totals.ordinary_formatted}</td>
                                <td class="time-badge" style="color:var(--warning)">${m.totals.overtime_formatted}</td>
                                <td class="time-badge" style="color:var(--success)">${m.totals.total_formatted}</td>
                                <td colspan="2" style="font-size:0.85rem; color:var(--text-secondary)">
                                    ${m.totals.total_human} complessive su ${m.totals.days_worked} giornate
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        `;

        main.innerHTML = html;
    },

    renderNoteBadges(text, dayType) {
        if (!text && !dayType) return '';
        const lower = (text || '').toLowerCase();
        const pills = [];

        if (lower.includes('pioggia')) {
            pills.push('<span class="tag-event tag-rain">☔ Pioggia / Intemperie</span>');
        }
        if (lower.includes('malattia') || dayType === 'malattia') {
            pills.push('<span class="tag-event tag-illness">🏥 Malattia</span>');
        }
        if (lower.includes('ferie') || dayType === 'ferie') {
            pills.push('<span class="tag-event tag-leave">🏖️ Ferie</span>');
        }
        if (lower.includes('permesso') || dayType === 'permesso') {
            pills.push('<span class="tag-event tag-doctor">⏱️ Permesso</span>');
        }
        if (lower.includes('medico') || lower.includes('ospedale') || lower.includes('sangue')) {
            pills.push('<span class="tag-event tag-doctor">🩺 Visita Medica</span>');
        }

        let result = pills.join(' ');
        if (text) {
            result += ` <span style="font-size:0.85rem; color:var(--text-secondary); margin-left:4px;">${this.escape(text)}</span>`;
        }
        return result;
    },

    // --- YEAR VIEW ---
    async loadYearData() {
        const main = document.getElementById('view-container');
        if (!main) return;
        main.innerHTML = '<div style="padding:40px; text-align:center; color:var(--text-muted)">Caricamento riepilogo annuale...</div>';

        try {
            const targetParam = this.state.targetUserId > 0 ? `&target_user_id=${this.state.targetUserId}` : '';
            const res = await this.api(`hours.year&year=${this.state.currentYear}${targetParam}`);
            if (res.ok) {
                this.state.yearData = res.data;
                this.renderYearView();
            }
        } catch (e) {
            main.innerHTML = `<div style="padding:40px; text-align:center; color:var(--danger)">Errore: ${e.message}</div>`;
        }
    },

    renderYearView() {
        const y = this.state.yearData;
        if (!y) return;
        const main = document.getElementById('view-container');
        if (!main) return;

        let html = `
            <div class="control-bar">
                <div class="period-switcher">
                    <button class="btn btn-secondary btn-icon" onclick="App.prevYear()" title="Anno precedente">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <div class="current-period-title">Anno ${y.year}</div>
                    <button class="btn btn-secondary btn-icon" onclick="App.nextYear()" title="Anno successivo">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-secondary" onclick="App.exportCsv(null)" title="Esporta l'intero anno in CSV">
                        📥 Esporta Anno Intero (CSV)
                    </button>
                    <button class="btn btn-secondary btn-icon" onclick="window.print()" title="Stampa Riepilogo">
                        🖨️
                    </button>
                </div>
            </div>

            <!-- Big Annual Stat Cards -->
            <div class="stats-grid">
                <div class="stat-card accent-card">
                    <span class="stat-label">Ore Ordinarie Totali</span>
                    <span class="stat-value">${y.totals.ordinary_formatted}</span>
                    <span style="font-size:0.8rem; color:var(--text-muted)">${y.totals.ordinary_hours.toFixed(1)} ore decimali</span>
                </div>
                <div class="stat-card warning-card">
                    <span class="stat-label">Ore Straordinarie Totali</span>
                    <span class="stat-value">${y.totals.overtime_formatted}</span>
                    <span style="font-size:0.8rem; color:var(--text-muted)">${y.totals.overtime_hours.toFixed(1)} ore decimali</span>
                </div>
                <div class="stat-card success-card">
                    <span class="stat-label">Totale Ore Lavorate</span>
                    <span class="stat-value">${y.totals.total_formatted}</span>
                    <span style="font-size:0.8rem; color:var(--text-muted)">${y.totals.total_human} nel ${y.year}</span>
                </div>
                <div class="stat-card info-card">
                    <span class="stat-label">Giorni Lavorati</span>
                    <span class="stat-value">${y.totals.days_worked}</span>
                    <span style="font-size:0.8rem; color:var(--text-muted)">Media: ${y.totals.avg_hours_per_worked_day} ore/giorno &middot; ☔ ${y.totals.rain_days} giorni pioggia</span>
                </div>
            </div>

            <h3 style="font-size:1.15rem; margin: 30px 0 16px;">Riepilogo Mese per Mese</h3>
            <div class="year-grid">
        `;

        y.months.forEach(m => {
            const hasHours = (m.total_hours > 0);
            html += `
                <div class="month-card" onclick="App.selectMonth(${m.month})">
                    <div class="month-card-header">
                        <span class="month-card-title">${m.month_name}</span>
                        <span class="badge ${hasHours ? 'badge-user' : 'badge-rejected'}">
                            ${hasHours ? `${m.days_worked} gg` : 'Vuoto'}
                        </span>
                    </div>
                    <div class="month-card-stats">
                        <div class="month-stat-item">
                            <span class="month-stat-label">ORDINARIE</span>
                            <span class="month-stat-val" style="color:var(--accent)">${m.ordinary_formatted}</span>
                        </div>
                        <div class="month-stat-item">
                            <span class="month-stat-label">STRAORDINARIE</span>
                            <span class="month-stat-val" style="color:var(--warning)">${m.overtime_formatted}</span>
                        </div>
                    </div>
                    <div style="margin-top:auto; padding-top:10px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:0.85rem; color:var(--text-muted)">Totale:</span>
                        <span style="font-weight:800; font-size:1.15rem; color:var(--success)">${m.total_formatted}</span>
                    </div>
                </div>
            `;
        });

        html += `</div>`;
        main.innerHTML = html;
    },

    selectMonth(m) {
        this.state.currentMonth = m;
        this.switchView('month');
    },

    prevMonth() {
        if (this.state.currentMonth === 1) {
            this.state.currentMonth = 12;
            this.state.currentYear--;
        } else {
            this.state.currentMonth--;
        }
        this.loadMonthData();
    },

    nextMonth() {
        if (this.state.currentMonth === 12) {
            this.state.currentMonth = 1;
            this.state.currentYear++;
        } else {
            this.state.currentMonth++;
        }
        this.loadMonthData();
    },

    goToToday() {
        const now = new Date();
        this.state.currentYear = now.getFullYear();
        this.state.currentMonth = now.getMonth() + 1;
        this.loadMonthData();
    },

    prevYear() {
        this.state.currentYear--;
        this.loadYearData();
    },

    nextYear() {
        this.state.currentYear++;
        this.loadYearData();
    },

    // --- USERS & APPROVALS (ADMIN) ---
    async loadUsersData() {
        const main = document.getElementById('view-container');
        if (!main) return;
        main.innerHTML = '<div style="padding:40px; text-align:center; color:var(--text-muted)">Caricamento utenti...</div>';

        try {
            const res = await this.api('users.list');
            if (res.ok) {
                this.state.usersList = res.users;
                this.state.pendingCount = res.pending_count;
                this.renderUsersView();
            }
        } catch (e) {
            main.innerHTML = `<div style="padding:40px; text-align:center; color:var(--danger)">Errore: ${e.message}</div>`;
        }
    },

    renderUsersView() {
        const users = this.state.usersList || [];
        const main = document.getElementById('view-container');
        if (!main) return;

        const isMaster = (this.state.currentUser.role >= 10);
        const pendingUsers = users.filter(u => u.status === 'pending');

        let html = `
            <div style="margin-bottom:24px;">
                <h2 style="font-size:1.4rem; font-weight:700; margin-bottom:6px;">Gestione Utenti e Accessi</h2>
                <p style="color:var(--text-secondary); font-size:0.95rem;">
                    Come amministratore puoi approvare manualmente i nuovi account registrati con Google, promuovere o declassare ruoli e consultare le ore dei lavoratori.
                </p>
            </div>
        `;

        if (pendingUsers.length > 0) {
            html += `
                <div class="pending-callout">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--warning); flex-shrink:0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <div>
                        <strong style="color:var(--warning); font-size:1.05rem;">Ci sono ${pendingUsers.length} utente/i in attesa di approvazione!</strong>
                        <p style="font-size:0.88rem; color:var(--text-secondary); margin-top:2px;">
                            Gli utenti non approvati non possono vedere né registrare ore finché non clicchi su &laquo;Approva&raquo;.
                        </p>
                    </div>
                </div>
            `;
        }

        html += `
            <div class="timesheet-card">
                <div class="table-responsive">
                    <table class="timesheet-table users-table">
                        <thead>
                            <tr>
                                <th style="width:50px"></th>
                                <th>Nome e Cognome</th>
                                <th>Email</th>
                                <th style="width:140px">Ruolo</th>
                                <th style="width:160px">Stato Accesso</th>
                                <th style="width:130px">Registrazione</th>
                                <th style="text-align:right">Azioni Amministratore</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        users.forEach(u => {
            const avatarHtml = u.avatar_url
                ? `<img src="${this.escape(u.avatar_url)}" class="user-avatar" alt="">`
                : `<div class="user-avatar" style="display:flex;align-items:center;justify-content:center;font-weight:700;background:var(--bg-elevated);">${this.escape(u.name.charAt(0))}</div>`;

            let roleBadge = `<span class="badge badge-user">Utente</span>`;
            if (u.role >= 10) roleBadge = `<span class="badge badge-master">Master Admin</span>`;
            else if (u.role >= 9) roleBadge = `<span class="badge badge-admin">Amministratore</span>`;

            let statusBadge = `<span class="badge badge-user">Attivo</span>`;
            if (u.status === 'pending') statusBadge = `<span class="badge badge-pending">In Attesa</span>`;
            else if (u.status === 'rejected') statusBadge = `<span class="badge badge-rejected">Rifiutato</span>`;
            else if (u.status === 'disabled') statusBadge = `<span class="badge badge-rejected">Disabilitato</span>`;

            const isSelf = (u.id === this.state.currentUser.id);

            html += `
                <tr>
                    <td>${avatarHtml}</td>
                    <td>
                        <strong style="color:var(--text)">${this.escape(u.name)}</strong>
                        ${isSelf ? '<span style="font-size:0.75rem; color:var(--accent); margin-left:6px;">(Tu)</span>' : ''}
                    </td>
                    <td style="color:var(--text-secondary)">${this.escape(u.email)}</td>
                    <td>${roleBadge}</td>
                    <td>${statusBadge}</td>
                    <td style="font-size:0.8rem; color:var(--text-muted)">${u.created_at ? u.created_at.split(' ')[0] : '—'}</td>
                    <td style="text-align:right">
                        <div style="display:inline-flex; gap:6px; align-items:center;">
                            ${u.status === 'pending' ? `
                                <button class="btn btn-success btn-sm" onclick="App.approveUser(${u.id})">
                                    ✓ Approva
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="App.rejectUser(${u.id})">
                                    ✕ Rifiuta
                                </button>
                            ` : ''}

                            ${u.status === 'active' && !isSelf ? `
                                <button class="btn btn-secondary btn-sm" onclick="App.setTargetUser(${u.id})" title="Visualizza e gestisci le ore di questo utente">
                                    👁️ Vedi Ore
                                </button>
                            ` : ''}

                            ${isMaster && !isSelf ? `
                                ${u.role < 9 ? `
                                    <button class="btn btn-secondary btn-sm" onclick="App.setUserRole(${u.id}, 9)" title="Promuovi ad Amministratore">
                                        Rendi Admin
                                    </button>
                                ` : `
                                    <button class="btn btn-secondary btn-sm" onclick="App.setUserRole(${u.id}, 1)" title="Imposta come semplice Utente">
                                        Rendi Utente
                                    </button>
                                `}
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        });

        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        main.innerHTML = html;
    },

    async approveUser(userId) {
        try {
            const res = await this.api('users.approve', 'POST', { user_id: userId });
            if (res.ok) {
                this.showToast('Utente approvato con successo!', 'success');
                this.loadUsersData();
            }
        } catch (e) {
            this.showToast(e.message, 'danger');
        }
    },

    async rejectUser(userId) {
        if (!confirm('Sei sicuro di voler rifiutare l\'accesso a questo utente?')) return;
        try {
            const res = await this.api('users.reject', 'POST', { user_id: userId });
            if (res.ok) {
                this.showToast('Utente rifiutato.', 'warning');
                this.loadUsersData();
            }
        } catch (e) {
            this.showToast(e.message, 'danger');
        }
    },

    async setUserRole(userId, newRole) {
        try {
            const res = await this.api('users.set_role', 'POST', { user_id: userId, role: newRole });
            if (res.ok) {
                this.showToast(`Ruolo aggiornato a ${res.role_label}!`, 'success');
                this.loadUsersData();
            }
        } catch (e) {
            this.showToast(e.message, 'danger');
        }
    },

    setTargetUser(targetId) {
        this.state.targetUserId = targetId;
        this.switchView('month');
    },

    // --- CRONOLOGIA & AUDIT LOG ---
    async loadHistoryData() {
        const main = document.getElementById('view-container');
        if (!main) return;
        main.innerHTML = '<div style="padding:40px; text-align:center; color:var(--text-muted)">Caricamento cronologia modifiche...</div>';

        try {
            const targetParam = this.state.targetUserId > 0 ? `&target_user_id=${this.state.targetUserId}` : '';
            const res = await this.api(`history.list${targetParam}`);
            if (res.ok) {
                this.state.historyList = res.history || [];
                this.state.canUndo = !!res.can_undo;
                this.renderHistoryView();
            }
        } catch (e) {
            main.innerHTML = `<div style="padding:40px; text-align:center; color:var(--danger)">Errore: ${e.message}</div>`;
        }
    },

    renderHistoryView() {
        const list = this.state.historyList || [];
        const main = document.getElementById('view-container');
        if (!main) return;

        const isAdmin = (this.state.currentUser.role >= 9);

        let html = `
            <div style="margin-bottom:24px; display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">
                <div>
                    <h2 style="font-size:1.4rem; font-weight:700; margin-bottom:6px;">Cronologia Modifiche</h2>
                    <p style="color:var(--text-secondary); font-size:0.95rem;">
                        ${isAdmin 
                            ? 'Registro completo delle modifiche ai fogli ore. In qualità di <strong>amministratore</strong> puoi annullare qualsiasi modifica per ripristinare lo stato precedente.'
                            : 'Registro di tutte le attività e modifiche relative al tuo foglio ore. <em>(L\'annullamento delle modifiche è riservato agli amministratori).</em>'
                        }
                    </p>
                </div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <button class="btn btn-secondary btn-sm" onclick="App.loadHistoryData()">
                        🔄 Aggiorna
                    </button>
                </div>
            </div>
        `;

        if (list.length === 0) {
            html += `
                <div class="timesheet-card" style="padding:48px; text-align:center; color:var(--text-muted)">
                    <div style="font-size:2.5rem; margin-bottom:12px;">📜</div>
                    <p style="font-size:1.05rem; font-weight:600">Nessuna modifica registrata finora</p>
                    <p style="font-size:0.88rem; margin-top:4px">Tutte le aggiunte, modifiche o eliminazioni di turni verranno tracciate qui in automatico.</p>
                </div>
            `;
            main.innerHTML = html;
            return;
        }

        html += `<div class="history-timeline">`;

        list.forEach(item => {
            let iconClass = 'icon-update';
            let iconChar = '✏️';

            if (item.action === 'day.create') {
                iconClass = 'icon-create';
                iconChar = '➕';
            } else if (item.action === 'day.delete') {
                iconClass = 'icon-delete';
                iconChar = '🗑️';
            } else if (item.action === 'day.rollback') {
                iconClass = 'icon-rollback';
                iconChar = '↩️';
            }

            const isUndone = item.is_undone;
            const canUndo = item.can_undo; // only true if admin and not undone

            html += `
                <div class="history-card ${isUndone ? 'is-undone' : ''}">
                    <div class="history-left">
                        <div class="history-icon ${iconClass}">${iconChar}</div>
                        <div class="history-content">
                            <div class="history-title">${this.escape(item.summary)}</div>
                            <div class="history-meta">
                                <span>Modificato da: <strong>${this.escape(item.actor_name)}</strong> <small>(${this.escape(item.actor_role_label)})</small></span>
                                <span>&bull;</span>
                                <span>Lavoratore: <strong>${this.escape(item.target_name)}</strong></span>
                                <span>&bull;</span>
                                <span>${this.escape(item.created_at)}</span>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                        ${isUndone ? `
                            <span class="undone-badge">
                                ✓ Annullata ${item.undone_at ? item.undone_at.split(' ')[0] : ''} da ${this.escape(item.undone_by_name || 'Admin')}
                            </span>
                        ` : ''}

                        ${canUndo ? `
                            <button class="btn btn-danger btn-sm" onclick="App.undoHistory(${item.id})">
                                ↩️ Annulla Modifica
                            </button>
                        ` : ''}

                        ${!isAdmin && !isUndone ? `
                            <span style="font-size:0.78rem; color:var(--text-muted); font-style:italic;">
                                Solo admin può annullare
                            </span>
                        ` : ''}
                    </div>
                </div>
            `;
        });

        html += `</div>`;
        main.innerHTML = html;
    },

    async undoHistory(logId) {
        if (!this.state.canUndo && this.state.currentUser.role < 9) {
            this.showToast('Solo gli amministratori possono annullare le modifiche.', 'danger');
            return;
        }

        if (!confirm('Sei sicuro di voler annullare questa modifica? Lo stato precedente della giornata verrà ripristinato automaticamente.')) {
            return;
        }

        try {
            this.showToast('Annullamento in corso...');
            const res = await this.api('history.undo', 'POST', { id: logId });
            if (res.ok) {
                this.showToast('Modifica annullata e stato precedente ripristinato!', 'success');
                // Refresh history and month data in state
                await this.loadHistoryData();
                if (res.month_data && this.state.currentYear === res.month_data.year && this.state.currentMonth === res.month_data.month) {
                    this.state.monthData = res.month_data;
                }
            }
        } catch (e) {
            this.showToast(e.message, 'danger');
        }
    },

    // --- DAY & SHIFTS EDITOR MODAL ---
    openAddDayModal(dateStr = null) {
        if (!dateStr) {
            const y = this.state.currentYear;
            const m = String(this.state.currentMonth).padStart(2, '0');
            dateStr = `${y}-${m}-01`;
        }
        this.renderDayModal(dateStr, 'work', '', [
            { start_time: '07:00', end_time: '12:00', is_overtime: false, notes: '' },
            { start_time: '13:00', end_time: '16:00', is_overtime: false, notes: '' },
            { start_time: '16:00', end_time: '17:00', is_overtime: true, notes: '' },
        ]);
    },

    openEditDayModal(dateStr) {
        const m = this.state.monthData;
        const day = m ? m.days.find(d => d.date === dateStr) : null;
        if (!day) {
            this.openAddDayModal(dateStr);
            return;
        }

        const shifts = (day.shifts && day.shifts.length > 0) ? day.shifts : [
            { start_time: '07:00', end_time: '12:00', is_overtime: false, notes: '' }
        ];

        this.renderDayModal(dateStr, day.day_type || 'work', day.notes || '', shifts);
    },

    renderDayModal(dateStr, dayType, notes, shifts) {
        let modal = document.getElementById('day-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'day-modal';
            modal.className = 'modal-backdrop';
            document.body.appendChild(modal);
        }

        let shiftsHtml = '';
        shifts.forEach((s, idx) => {
            shiftsHtml += this.renderShiftRowHtml(idx, s.start_time || '', s.end_time || '', s.is_overtime || false, s.notes || '');
        });

        modal.innerHTML = `
            <div class="modal-card">
                <div class="modal-header">
                    <h3 class="modal-title">Gestione Orario Giornata</h3>
                    <button class="btn btn-secondary btn-icon" onclick="App.closeDayModal()">✕</button>
                </div>
                <div class="modal-body">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Data</label>
                            <input type="date" id="modal-date" class="form-input" value="${dateStr}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tipo Giornata / Assenza</label>
                            <select id="modal-day-type" class="form-select" onchange="App.onDayTypeChange()">
                                <option value="work" ${dayType === 'work' ? 'selected' : ''}>Lavoro normale</option>
                                <option value="pioggia" ${dayType === 'pioggia' ? 'selected' : ''}>Uscita / Stop per Pioggia</option>
                                <option value="malattia" ${dayType === 'malattia' ? 'selected' : ''}>Malattia</option>
                                <option value="ferie" ${dayType === 'ferie' ? 'selected' : ''}>Ferie</option>
                                <option value="permesso" ${dayType === 'permesso' ? 'selected' : ''}>Permesso orario</option>
                                <option value="festivo" ${dayType === 'festivo' ? 'selected' : ''}>Festività</option>
                                <option value="riposo" ${dayType === 'riposo' ? 'selected' : ''}>Riposo</option>
                            </select>
                        </div>
                    </div>

                    <!-- Shift Presets Selector -->
                    <div>
                        <label class="form-label">Applica Turno Predefinito Rapido</label>
                        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:4px;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="App.applyPresetToModal(1)">
                                Invernale (7-12 / 13-16 / 16-17)
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="App.applyPresetToModal(2)">
                                Estivo (6-14 / 14-15)
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="App.applyPresetToModal(3)">
                                Sabato (7-12 Straord.)
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Shifts List -->
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <label class="form-label" style="margin:0">Fasce Orarie Lavorate</label>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="App.addShiftRowToModal()">
                                + Aggiungi Fascia
                            </button>
                        </div>
                        <div id="modal-shifts-container" class="shifts-builder">
                            ${shiftsHtml}
                        </div>
                    </div>

                    <!-- General Day Notes -->
                    <div class="form-group">
                        <label class="form-label">Note della Giornata</label>
                        <input type="text" id="modal-notes" class="form-input" placeholder="Es. causa pioggia siamo andati via alle 10.00, timbrato..." value="${this.escape(notes)}">
                        <div class="note-pills">
                            <span class="note-pill" onclick="App.appendNoteToModal('causa pioggia siamo andati a casa alle 10.00')">☔ Causa Pioggia</span>
                            <span class="note-pill" onclick="App.appendNoteToModal('timbrato ore 8.00 per visita medica')">🩺 Visita Medica</span>
                            <span class="note-pill" onclick="App.appendNoteToModal('preso 3 ore di permesso')">⏱️ Permesso</span>
                            <span class="note-pill" onclick="App.appendNoteToModal('rientrato da casa')">🏠 Rientrato</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" onclick="App.deleteCurrentModalDay()" style="margin-right:auto">
                        Elimina Giornata
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="App.closeDayModal()">Annulla</button>
                    <button type="button" class="btn btn-primary" onclick="App.saveCurrentModalDay()">Salva Giornata</button>
                </div>
            </div>
        `;

        modal.classList.add('active');
    },

    renderShiftRowHtml(index, start, end, isOvertime, shiftNotes = '') {
        return `
            <div class="shift-row" data-index="${index}">
                <div>
                    <label style="font-size:0.75rem; color:var(--text-muted); display:block;">Dalle</label>
                    <input type="time" class="form-input shift-start" value="${start}" onchange="App.recalcShiftRow(this)">
                </div>
                <div>
                    <label style="font-size:0.75rem; color:var(--text-muted); display:block;">Alle</label>
                    <input type="time" class="form-input shift-end" value="${end}" onchange="App.recalcShiftRow(this)">
                </div>
                <div>
                    <label style="font-size:0.75rem; color:var(--text-muted); display:block;">Straordinario</label>
                    <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; margin-top:8px;">
                        <input type="checkbox" class="shift-overtime" ${isOvertime ? 'checked' : ''} onchange="App.recalcShiftRow(this)">
                        <span style="font-size:0.85rem">Sì</span>
                    </label>
                </div>
                <div style="display:flex; align-items:flex-end;">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.shift-row').remove()" title="Rimuovi fascia">
                        ✕
                    </button>
                </div>
            </div>
        `;
    },

    addShiftRowToModal() {
        const container = document.getElementById('modal-shifts-container');
        if (!container) return;
        const newIndex = container.querySelectorAll('.shift-row').length;
        const div = document.createElement('div');
        div.innerHTML = this.renderShiftRowHtml(newIndex, '07:00', '12:00', false, '');
        container.appendChild(div.firstElementChild);
    },

    applyPresetToModal(presetId) {
        const container = document.getElementById('modal-shifts-container');
        if (!container) return;
        container.innerHTML = '';

        if (presetId === 1) {
            // Invernale (7-12, 13-16, 16-17 straord)
            container.innerHTML += this.renderShiftRowHtml(0, '07:00', '12:00', false);
            container.innerHTML += this.renderShiftRowHtml(1, '13:00', '16:00', false);
            container.innerHTML += this.renderShiftRowHtml(2, '16:00', '17:00', true);
        } else if (presetId === 2) {
            // Estivo (6-14, 14-15 straord)
            container.innerHTML += this.renderShiftRowHtml(0, '06:00', '14:00', false);
            container.innerHTML += this.renderShiftRowHtml(1, '14:00', '15:00', true);
        } else if (presetId === 3) {
            // Sabato (7-12 straord)
            container.innerHTML += this.renderShiftRowHtml(0, '07:00', '12:00', true);
        }
    },

    appendNoteToModal(text) {
        const input = document.getElementById('modal-notes');
        if (!input) return;
        input.value = input.value ? (input.value + ' | ' + text) : text;
    },

    onDayTypeChange() {
        const type = document.getElementById('modal-day-type').value;
        const container = document.getElementById('modal-shifts-container');
        if (!container) return;
        // If illness, vacation, etc., clear shifts
        if (type === 'malattia' || type === 'ferie' || type === 'festivo' || type === 'riposo') {
            container.innerHTML = `<div style="padding:12px; color:var(--text-muted); font-size:0.88rem">Nessuna fascia oraria richiesta per giornata di ${type}.</div>`;
        }
    },

    recalcShiftRow(el) {
        // live calculation if needed
    },

    closeDayModal() {
        const modal = document.getElementById('day-modal');
        if (modal) modal.classList.remove('active');
    },

    async saveCurrentModalDay() {
        const date = document.getElementById('modal-date').value;
        const dayType = document.getElementById('modal-day-type').value;
        const notes = document.getElementById('modal-notes').value;

        const shifts = [];
        document.querySelectorAll('#modal-shifts-container .shift-row').forEach(row => {
            const start = row.querySelector('.shift-start').value;
            const end = row.querySelector('.shift-end').value;
            const isOvertime = row.querySelector('.shift-overtime').checked;
            if (start && end) {
                shifts.push({
                    start_time: start,
                    end_time: end,
                    is_overtime: isOvertime,
                });
            }
        });

        try {
            const payload = {
                date,
                day_type: dayType,
                notes,
                shifts,
                target_user_id: this.state.targetUserId,
            };
            const res = await this.api('hours.save_day', 'POST', payload);
            if (res.ok) {
                this.showToast('Giornata salvata con successo!', 'success');
                this.closeDayModal();
                if (this.state.activeView === 'month') {
                    this.state.monthData = res.month_data;
                    this.renderMonthView();
                } else {
                    this.loadActiveView();
                }
            }
        } catch (e) {
            this.showToast(e.message, 'danger');
        }
    },

    async deleteCurrentModalDay() {
        const date = document.getElementById('modal-date').value;
        if (!confirm(`Sei sicuro di voler eliminare la giornata del ${date}?`)) return;

        try {
            const res = await this.api('hours.delete_day', 'POST', {
                date,
                target_user_id: this.state.targetUserId,
            });
            if (res.ok) {
                this.showToast('Giornata eliminata.', 'info');
                this.closeDayModal();
                if (this.state.activeView === 'month') {
                    this.state.monthData = res.month_data;
                    this.renderMonthView();
                }
            }
        } catch (e) {
            this.showToast(e.message, 'danger');
        }
    },

    async applyQuickPresetToday() {
        const todayStr = new Date().toISOString().split('T')[0];
        try {
            const res = await this.api('hours.quick_preset', 'POST', {
                date: todayStr,
                preset_id: 1,
                target_user_id: this.state.targetUserId,
            });
            if (res.ok) {
                this.showToast(`Turno standard applicato per oggi (${todayStr})!`, 'success');
                this.state.monthData = res.month_data;
                this.renderMonthView();
            }
        } catch (e) {
            this.showToast(e.message, 'danger');
        }
    },

    exportCsv(month = this.state.currentMonth) {
        const targetParam = this.state.targetUserId > 0 ? `&target_user_id=${this.state.targetUserId}` : '';
        const monthParam = (month !== null && month !== undefined) ? `&month=${month}` : '';
        window.location.href = `api/index.php?action=export.csv&year=${this.state.currentYear}${monthParam}${targetParam}`;
    },

    // --- APP SHELL & LOGIN VIEWS ---
    renderAppShell() {
        const app = document.getElementById('app');
        if (!app) return;

        const u = this.state.currentUser;
        const isAdmin = (u.role >= 9);

        let roleBadge = `<span class="badge badge-user">Utente</span>`;
        if (u.role >= 10) roleBadge = `<span class="badge badge-master">Master Admin</span>`;
        else if (u.role >= 9) roleBadge = `<span class="badge badge-admin">Admin</span>`;

        const avatarHtml = u.avatar_url
            ? `<img src="${this.escape(u.avatar_url)}" class="user-avatar" alt="">`
            : `<div class="user-avatar" style="display:flex;align-items:center;justify-content:center;font-weight:700;">${this.escape(u.name.charAt(0))}</div>`;

        app.innerHTML = `
            <header class="app-header">
                <div class="header-container">
                    <div class="logo-area">
                        <div class="logo-icon">🕒</div>
                        <div>
                            <div class="logo-title">Rendicontazione Ore</div>
                            <div class="logo-subtitle">Registro Lavoro e Straordinari</div>
                        </div>
                    </div>

                    <nav class="nav-tabs">
                        <button class="nav-tab ${this.state.activeView === 'month' ? 'active' : ''}" data-view="month" onclick="App.switchView('month')">
                            📅 Mese
                        </button>
                        <button class="nav-tab ${this.state.activeView === 'year' ? 'active' : ''}" data-view="year" onclick="App.switchView('year')">
                            📊 Anno
                        </button>
                        <button class="nav-tab ${this.state.activeView === 'history' ? 'active' : ''}" data-view="history" onclick="App.switchView('history')">
                            📜 Cronologia
                        </button>
                        ${isAdmin ? `
                            <button class="nav-tab ${this.state.activeView === 'users' ? 'active' : ''}" data-view="users" onclick="App.switchView('users')">
                                👥 Utenti ${this.state.pendingCount > 0 ? `<span class="badge badge-pending" style="margin-left:4px">${this.state.pendingCount}</span>` : ''}
                            </button>
                        ` : ''}
                    </nav>

                    <div class="header-user">
                        <button class="btn btn-secondary btn-icon" id="theme-btn" onclick="App.toggleTheme()" title="Cambia tema">
                            <span id="theme-icon">${this.state.theme === 'dark' ? '☀️' : '🌙'}</span>
                        </button>
                        ${avatarHtml}
                        <div class="user-info">
                            <span class="user-name">${this.escape(u.name)}</span>
                            <div>${roleBadge}</div>
                        </div>
                        <button class="btn btn-secondary btn-sm" onclick="App.logout()" title="Esci dall'account" style="margin-left:4px;">
                            Esci
                        </button>
                    </div>
                </div>
            </header>

            <main class="app-main" id="view-container">
                <!-- Dynamic view content -->
            </main>
        `;
    },

    renderLoginView() {
        const app = document.getElementById('app');
        if (!app) return;

        app.innerHTML = `
            <div class="auth-container">
                <div class="auth-logo">🕒</div>
                <h1 style="font-size:1.6rem; font-weight:800; margin-bottom:8px;">Rendicontazione Ore</h1>
                <p style="color:var(--text-secondary); font-size:0.95rem; margin-bottom:32px;">
                    Portale riservato per il conteggio e la rendicontazione delle ore lavorative ordinarie e straordinarie.
                </p>

                <!-- Google Sign-In Button Container -->
                <div style="display:flex; justify-content:center; margin-bottom:24px;">
                    <div id="g_id_signin"></div>
                </div>

                <p style="font-size:0.8rem; color:var(--text-muted); line-height:1.5;">
                    🔒 L'accesso è riservato. I nuovi utenti dovranno essere approvati manualmente da un amministratore prima di poter accedere ai fogli ore.
                </p>

                <!-- Dev local quick buttons if in development mode -->
                <div style="margin-top:40px; padding-top:20px; border-top:1px dashed var(--border); font-size:0.85rem; color:var(--text-muted)">
                    <p style="margin-bottom:10px; font-weight:600">Accesso Rapido di Sviluppo:</p>
                    <div style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                        <button class="btn btn-secondary btn-sm" onclick="App.devLogin(10)">Master Admin (Saverio)</button>
                        <button class="btn btn-secondary btn-sm" onclick="App.devLogin(1)">Utente (Papà)</button>
                    </div>
                </div>
            </div>
        `;

        this.renderGoogleButton();
    },

    renderPendingApprovalView() {
        const app = document.getElementById('app');
        if (!app) return;
        const u = this.state.currentUser;

        app.innerHTML = `
            <div class="auth-container" style="max-width:540px;">
                <div style="font-size:3rem; margin-bottom:16px;">⏳</div>
                <h1 style="font-size:1.5rem; font-weight:800; color:var(--warning); margin-bottom:12px;">In attesa di approvazione</h1>
                <p style="color:var(--text); font-size:1rem; margin-bottom:16px; line-height:1.6;">
                    Ciao <strong>${this.escape(u.name)}</strong>, il tuo account Google (<code>${this.escape(u.email)}</code>) è stato registrato correttamente.
                </p>
                <div style="background:var(--bg-elevated); padding:16px; border-radius:8px; border:1px solid var(--border); margin-bottom:24px; text-align:left; font-size:0.9rem; color:var(--text-secondary);">
                    ℹ️ Come previsto dalle regole di sicurezza, <strong>un amministratore deve verificare e approvare manualmente il tuo profilo</strong> prima di permetterti di visualizzare o registrare le ore di lavoro.
                </div>
                <div style="display:flex; gap:12px; justify-content:center;">
                    <button class="btn btn-secondary" onclick="App.checkAuth()">🔄 Verifica Approvazione</button>
                    <button class="btn btn-secondary" onclick="App.logout()">Esci</button>
                </div>
            </div>
        `;
    },

    renderInactiveView() {
        const app = document.getElementById('app');
        if (!app) return;
        const u = this.state.currentUser;

        app.innerHTML = `
            <div class="auth-container" style="max-width:500px;">
                <div style="font-size:3rem; margin-bottom:16px;">🚫</div>
                <h1 style="font-size:1.5rem; font-weight:800; color:var(--danger); margin-bottom:12px;">Accesso Non Consentito</h1>
                <p style="color:var(--text-secondary); font-size:0.95rem; margin-bottom:24px;">
                    Il tuo account (<code>${this.escape(u.email)}</code>) risulta nello stato: <strong>${this.escape(u.status_label)}</strong>. Contatta l'amministratore per maggiori informazioni.
                </p>
                <button class="btn btn-secondary" onclick="App.logout()">Esci</button>
            </div>
        `;
    },

    renderGoogleButton() {
        if (!window.google || !window.google.accounts) {
            // Wait for GSI library to load
            setTimeout(() => this.renderGoogleButton(), 300);
            return;
        }

        const clientId = this.state.googleClientId || '126260557873-mss9iuldd7uttsj9pqd0oqbtntvvo6hl.apps.googleusercontent.com';

        google.accounts.id.initialize({
            client_id: clientId,
            callback: (res) => this.handleGoogleCredential(res.credential),
        });

        const target = document.getElementById('g_id_signin');
        if (target) {
            google.accounts.id.renderButton(target, {
                theme: this.state.theme === 'dark' ? 'filled_black' : 'outline',
                size: 'large',
                text: 'continue_with',
                shape: 'rectangular',
                logo_alignment: 'left',
                width: 280,
            });
        }
    },

    setupEventListeners() {
        // Keyboard shortcut: Escape closes modal
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.closeDayModal();
        });
    },

    // --- UTILITIES ---
    showToast(message, type = 'info') {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'toast';
        if (type === 'danger') toast.style.borderColor = 'var(--danger)';
        if (type === 'success') toast.style.borderColor = 'var(--success)';
        toast.textContent = message;

        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    },

    formatHoursHms(hours) {
        const totalSeconds = Math.round(hours * 3600);
        const h = Math.floor(totalSeconds / 3600);
        const rem = totalSeconds % 3600;
        const m = Math.floor(rem / 60);
        const s = rem % 60;
        return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    },

    escape(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    },
};

window.App = App;
document.addEventListener('DOMContentLoaded', () => App.init());
