/* Giftwise — single page app.
 *
 * Tabs: My lists · Friends' lists · Friends. Routes (hash): #/lists, #/list/<id>, #/gifts, #/gift/<id>,
 * #/friends (search by username, requests, who I follow, my followers), #/user/<id>, #/profile, #/invite/<code>.
 * Sharing a product link to the installed app (Web Share Target) opens "Add a wish" with the link.
 *
 * The surprise: on a friend's list you can mark a wish ("I'm thinking about it" / "I'll get it") and chat
 * with the other friends. The server never sends any of this to the owner of the list.
 */
(function () {
    'use strict';

    const VERSION = '1.2.2';
    const {
        h,
        icon,
        tile,
        chip,
        fab,
        btn,
        iconBtn,
        busy,
        field,
        input,
        textarea,
        select,
        sheet,
        actionSheet,
        confirm,
        toast,
        store
    } = UI;
    const t = (s, v) => I18N.t(s, v);
    const root = document.getElementById('app');
    const S = {
        user: null,
        loaded: false,
        config: {},
        following: [],
        followers: [],
        requests: {incoming: [], outgoing: []},
        myLists: [],
        friendLists: [],
        views: {},
        signature: ''
    };
    const CONTACT_URL = 'https://www.saveriomorelli.com/contact-me/email/';
    const DONATE_URL = 'https://www.saveriomorelli.com/donate/';

    const OCCASIONS = {
        birthday: ['cake', 'Birthday'],
        christmas: ['tree', 'Christmas'],
        wedding: ['rings', 'Wedding'],
        baby: ['baby', 'Baby'],
        graduation: ['grad', 'Graduation'],
        anniversary: ['heart', 'Anniversary'],
        housewarming: ['house', 'New home'],
        other: ['gift', 'Other'],
    };
    const occIcon = o => (OCCASIONS[o] || OCCASIONS.other)[0];
    const occLabel = o => t((OCCASIONS[o] || OCCASIONS.other)[1]);
    const plural = (n, one, many) => t(n === 1 ? '1 ' + one : '{n} ' + many, {n});

    // ===================================================== small helpers

    function applyState(d) {
        for (const k of ['user', 'following', 'followers', 'requests', 'myLists', 'friendLists']) if (d[k] !== undefined) S[k] = d[k];
        S.signature = JSON.stringify([S.following, S.followers, S.requests, S.myLists, S.friendLists.map(l => [l.id, l.updatedAt, l.takenCount, l.consideringCount, l.commentCount, l.itemCount])]);
    }

    async function act(route, body, button) {
        const d = await busy(button || null, () => Api.post(route, body));
        applyState(d);
        render(true);
        return d;
    }

    const myList = id => S.myLists.find(l => l.id === Number(id));

    function initials(name) {
        return String(name || '?').trim().split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase();
    }

    function avatar(name, cls) {
        let hash = 0;
        for (const ch of String(name || '')) hash = (hash * 31 + ch.charCodeAt(0)) % 360;
        return h('span.avatar' + (cls ? '.' + cls : ''), {style: {background: `linear-gradient(135deg, hsl(${hash} 70% 62%), hsl(${(hash + 40) % 360} 60% 42%))`}}, initials(name));
    }

    function domain(url) {
        try {
            return new URL(url).hostname.replace(/^(www|m)\./, '');
        } catch (e) {
            return '';
        }
    }

    function dateChip(d) {
        if (!d) return null;
        const n = UI.daysUntil(d);
        return chip(n < 0 ? UI.fmtDate(d) : (n === 0 ? t('Today!') : UI.fmtDate(d) + ' · ' + UI.relDays(n)), n >= 0 && n <= 14 ? 'warn' : 'neutral', 'calendar');
    }

    function listRow(o) {
        return h('div.list-row' + (o.big ? '.big' : '') + (o.cls ? '.' + o.cls : ''), o.lead || null,
            h('div.body', h('p.title', o.title), o.sub ? h('div.sub', o.sub) : null, o.meta && o.meta.filter(Boolean).length ? h('div.meta', o.meta) : null),
            o.trail ? h('div.trail', o.trail) : null);
    }

    function cardRow(o, onclick) {
        return h('div.card' + (onclick ? '.clickable' : ''), onclick ? {
            role: 'button',
            tabindex: 0,
            onclick,
            onkeydown: e => {
                if (e.key === 'Enter' && e.target === e.currentTarget) onclick();
            }
        } : null, listRow(o));
    }

    function groupRow(ic, title, sub, onclick, opts) {
        opts = opts || {};
        const tag = opts.href ? 'a' : onclick ? 'button' : 'div';
        return h(tag + '.list-row' + (opts.danger ? '.danger' : ''), {
                type: tag === 'button' ? 'button' : undefined,
                href: opts.href,
                target: opts.external ? '_blank' : undefined,
                rel: opts.external ? 'noopener' : undefined,
                onclick
            },
            opts.lead || tile('icon', ic, 'sm'), h('div.body', h('p.title', title), sub ? h('div.sub', sub) : null),
            opts.trail !== undefined ? opts.trail : (onclick || opts.href ? h('div.trail', icon(opts.external ? 'open' : 'right')) : null));
    }

    function sectionTitle(text, count, action) {
        return h('h2.section-title', h('span', text), count != null ? h('span.count', String(count)) : null, action ? h('span.grow') : null, action || null);
    }

    function banner(tone, ic, title, text, actions) {
        return h('div.banner.' + tone, icon(ic), h('div.b-body', h('h3', title), h('p', text), actions && actions.length ? h('div.b-actions', actions) : null));
    }

    function empty(ic, title, text, action) {
        return h('div.empty', tile('icon', ic, 'xl'), h('strong', title), text ? h('p', text) : null, action || null);
    }

    const moreBtn = (items, title) => iconBtn('more', t('More actions'), () => actionSheet(items, title));
    const cancelBtn = () => btn(t('Cancel'), {kind: 'text', onclick: UI.closeTop});

    function seg(options, value, onchange) {
        const wrap = h('div.seg', {role: 'tablist'});
        for (const [v, label, ic] of options) {
            const b = h('button' + (v === value ? '.sel' : ''), {
                type: 'button', role: 'tab', onclick: () => {
                    wrap.querySelectorAll('button').forEach(x => x.classList.toggle('sel', x === b));
                    onchange(v);
                }
            }, ic ? icon(ic) : null, label);
            wrap.appendChild(b);
        }
        return wrap;
    }

    function wishThumb(it, size) {
        if (it.image) {
            const img = h('img', {src: it.image, alt: '', loading: 'lazy', referrerpolicy: 'no-referrer'});
            const box = h('span.thumb' + (size ? '.' + size : ''), img);
            img.addEventListener('error', () => {
                box.replaceChildren(icon('gift'));
                box.classList.add('noimg');
            });
            return box;
        }
        return h('span.thumb.noimg' + (size ? '.' + size : ''), icon('gift'));
    }

    function wishMeta(it) {
        return [it.price != null ? h('span.price', UI.money(it.price, it.currency)) : null, it.quantity > 1 ? '× ' + it.quantity : null, it.url ? domain(it.url) : null].filter(Boolean);
    }

    const sep = arr => arr.flatMap((x, i) => i ? [' · ', x] : [x]);

    async function shareOrCopy(text, url) {
        if (navigator.share) {
            try {
                await navigator.share({title: 'Giftwise', text, url});
                return;
            } catch (e) {
                if (e.name === 'AbortError') return;
            }
        }
        try {
            await navigator.clipboard.writeText(url);
            toast(t('Link copied'));
        } catch (e) {
            prompt(t('Copy the link'), url);
        }
    }

    const inviteUrl = () => new URL('#/invite/' + S.user.inviteCode, location.href.split('#')[0]).href;

    // ===================================================== routing

    function route() {
        const parts = (location.hash.replace(/^#\/?/, '') || 'lists').split('/');
        return {name: parts[0], id: parts[1] ? decodeURIComponent(parts[1]) : null};
    }

    /** Navigates once a closing sheet has finished (closing a sheet goes back in the history). */
    const goLater = path => setTimeout(() => go(path), 260);

    function go(path) {
        const target = '#/' + path;
        if (UI.sheetsOpen()) {
            UI.closeAllSheets();
            setTimeout(() => {
                location.hash = target;
            }, 120);
        } else location.hash = target;
    }

    let curDepth = -1;

    function trackDepth() {
        const st = history.state;
        if (st && typeof st.gwDepth === 'number') {
            curDepth = st.gwDepth;
            return;
        }
        curDepth = curDepth < 0 ? 0 : curDepth + 1;
        history.replaceState({gwDepth: curDepth}, '');
    }

    const DESK = matchMedia('(min-width: 960px)');
    DESK.addEventListener('change', () => {
        if (S.user && S.loaded) {
            UI.closeAllSheets();
            render(true);
        }
    });
    const NAV_OF = {list: 'lists', gift: 'gifts', user: 'friends', invite: 'friends', history: 'history'};

    let lastKey = '';

    function render(keepScroll) {
        if (!S.user) return renderAuth();
        if (!S.loaded) {
            root.replaceChildren(DESK.matches ? h('div.content', UI.skeletonScreen()) : UI.skeletonScreen());
            return;
        }
        const r = route();
        const key = r.name + '/' + (r.id || '');
        const y = window.scrollY;
        const views = {
            lists: viewLists,
            list: viewList,
            gifts: viewGifts,
            gift: viewGift,
            friends: viewFriends,
            user: viewPerson,
            profile: viewProfile,
            invite: viewInvite,
            history: viewHistory
        };
        const v = (views[r.name] || viewLists)(r.id);
        if (!v) return;
        const showBack = v.back && !(DESK.matches && ['profile', 'history'].includes(r.name));
        const bar = showBack
            ? h('header.appbar.small', iconBtn('back', t('Back'), () => {
                if (curDepth > 0) history.back(); else location.replace('#/' + v.back);
            }), h('div.titles', h('h1', v.title)), v.actions || [])
            : h('header.appbar',
                h('div.titles', h('div.brand', h('img', {
                    src: 'assets/icons/icon-192.png',
                    alt: ''
                }), 'Giftwise'), h('h1', v.title)),
                v.actions || [],
                h('button.avatar-btn', {
                    type: 'button',
                    title: t('Profile'),
                    'aria-label': t('Profile and settings'),
                    onclick: () => go('profile')
                }, avatar(S.user.name)));
        const nav = v.nav !== false;
        const main = h('main.view' + (!nav ? '.no-nav' : '') + (nav && !v.fab ? '.no-fab' : '') + (v.cls ? '.' + v.cls : ''), v.body);
        const content = h('div.content', bar, main);
        if (nav) content.appendChild(bottomNav(v.tab));
        if (v.fab) {
            if (DESK.matches) bar.appendChild(v.fab);
            else {
                if (!nav) v.fab.classList.add('low');
                content.appendChild(v.fab);
            }
        }
        root.replaceChildren(sideNav(NAV_OF[r.name] || r.name), content);
        document.title = v.title + ' · Giftwise';
        if (keepScroll && key === lastKey) window.scrollTo(0, y); else window.scrollTo(0, 0);
        document.body.classList.toggle('scrolled', window.scrollY > 4);
        lastKey = key;
        if (v.after) v.after();
    }

    window.addEventListener('scroll', () => document.body.classList.toggle('scrolled', window.scrollY > 4), {passive: true});

    function bottomNav(active) {
        const tab = (name, label, ic, badge) => h('a' + (active === name ? '.active' : ''), {
                href: '#/' + name,
                'aria-current': active === name ? 'page' : null
            },
            h('span.pill', icon(ic), badge ? h('span.dot', String(badge)) : null), label);
        return h('nav.bottomnav', {'aria-label': t('Sections')}, tab('lists', t('My lists'), 'list'), tab('gifts', t('Friends’ lists'), 'gift'), tab('friends', t('Friends'), 'group', S.requests.incoming.length));
    }

    function sideNav(active) {
        const link = (name, label, ic, badge) => h('a.sn-link' + (active === name ? '.active' : ''), {
                href: '#/' + name,
                'aria-current': active === name ? 'page' : null
            },
            icon(ic), h('span', label), badge ? h('span.sn-badge.warn', String(badge)) : null);
        return h('nav.sidenav', {'aria-label': t('Sections')},
            h('a.sn-brand', {href: '#/lists'}, h('img', {
                src: 'assets/icons/icon-192.png',
                alt: ''
            }), h('strong', 'Giftwise')),
            h('button.btn.btn-filled.sn-cta', {
                type: 'button',
                onclick: () => openWishForm({})
            }, icon('add'), h('span', t('Add a wish'))),
            h('div.sn-links',
                link('lists', t('My lists'), 'list'),
                link('gifts', t('Friends’ lists'), 'gift'),
                link('friends', t('Friends'), 'group', S.requests.incoming.length),
                link('history', t('Activity history'), 'history')),
            h('div.grow'),
            h('button.sn-link.sn-help', {
                type: 'button',
                onclick: openHowItWorks
            }, icon('help'), h('span', t('How it works'))),
            h('a.sn-user' + (active === 'profile' ? '.active' : ''), {
                    href: '#/profile',
                    title: t('Profile and settings')
                },
                avatar(S.user.name), h('span.grow', h('strong', S.user.name), h('small', S.user.email)), icon('right')));
    }

    // ===================================================== MY LISTS

    function viewLists() {
        const body = [];
        const active = S.myLists.filter(l => !l.archived), archived = S.myLists.filter(l => l.archived);
        if (!S.following.length && !S.followers.length && !store.get('hide.nofriends', false)) {
            body.push(banner('info', 'personAdd', t('Invite your friends'), t('Only the people who follow you see your lists. Find your friends by username, or send them your invite link: whoever opens it and you will follow each other.'),
                [btn(t('Later'), {
                    kind: 'text', onclick: () => {
                        store.set('hide.nofriends', true);
                        render(true);
                    }
                }), btn(t('Find friends'), {icon: 'search', onclick: () => go('friends')})]));
        }
        if (!active.length) {
            body.push(empty('gift', t('No wish lists yet'), t('Create a list for your birthday, Christmas, your wedding… and add everything you would like to receive.'),
                btn(t('Create your first list'), {icon: 'add', onclick: () => openListForm({})})));
        }
        body.push(h('div.stack', active.map(listCard)));
        if (archived.length) {
            body.push(sectionTitle(t('Archived'), archived.length));
            body.push(h('div.stack', archived.map(listCard)));
        }
        return {
            tab: 'lists',
            title: t('My wish lists'),
            actions: [iconBtn('history', t('Activity history'), () => go('history'))],
            body,
            fab: fab(t('New list'), 'add', () => openListForm({}))
        };
    }

    function listCard(l) {
        const vis = l.visibility === 'friends' ? t('All followers') : plural(l.viewers.length, 'follower', 'followers');
        const covers = l.items.filter(i => i.image).slice(0, 3);
        return h('div.card.clickable.list-card' + (l.archived ? '.archived' : ''), {
                role: 'button',
                tabindex: 0,
                onclick: () => go('list/' + l.id),
                onkeydown: e => {
                    if (e.key === 'Enter' && e.target === e.currentTarget) go('list/' + l.id);
                }
            },
            listRow({
                big: true, lead: h('span.occ.occ-' + l.occasion, icon(occIcon(l.occasion))), title: l.title,
                sub: sep([occLabel(l.occasion), plural(l.items.length, 'wish', 'wishes'), h('span.vis', icon(l.visibility === 'friends' ? 'group' : 'lock'), vis)]),
                meta: [dateChip(l.eventDate), l.archived ? chip(t('Archived'), 'neutral', 'archive') : null],
                trail: moreBtn(listMenu(l), l.title),
            }),
            covers.length ? h('div.covers', covers.map(i => wishThumb(i, 'sm'))) : null);
    }

    function listMenu(l) {
        return [
            {icon: 'add', label: t('Add a wish'), onclick: () => openWishForm({listId: l.id})},
            {icon: 'edit', label: t('Edit list'), onclick: () => openListForm(l)},
            {
                icon: 'share',
                label: t('Tell my friends'),
                onclick: () => shareOrCopy(t('I made a wish list on Giftwise: “{title}”', {title: l.title}), new URL('#/gifts', location.href.split('#')[0]).href)
            },
            {icon: 'history', label: t('Activity history'), onclick: () => go('history')},
            {
                icon: 'archive',
                label: l.archived ? t('Restore from archive') : t('Archive (hide from friends)'),
                onclick: () => act('list.archive', {
                    id: l.id,
                    archived: !l.archived
                }).then(() => toast(l.archived ? t('List restored') : t('List archived')))
            },
            {
                icon: 'delete', label: t('Delete list'), danger: true, onclick: async () => {
                    if (await confirm(t('Delete “{title}”?', {title: l.title}), t('The list and all its wishes will be deleted. Your friends will no longer see it.'), t('Delete'), true)) {
                        await act('list.delete', {id: l.id});
                        goLater('lists');
                        toast(t('List deleted'));
                    }
                }
            },
        ];
    }

    function openListForm(l) {
        const isEdit = !!l.id;
        const title = input({value: l.title || '', maxlength: 80});
        let occasion = l.occasion || 'birthday';
        const date = input({type: 'date', value: l.eventDate || ''});
        const desc = textarea({value: l.description || '', maxlength: 500, rows: 2});
        let visibility = l.visibility || 'friends';
        const chosen = new Set(l.viewers || []);
        const occWrap = h('div.occ-picker', {role: 'radiogroup'});

        function paintOcc() {
            occWrap.replaceChildren(...Object.keys(OCCASIONS).map(o => h('button' + (o === occasion ? '.sel' : ''), {
                    type: 'button',
                    role: 'radio',
                    'aria-checked': String(o === occasion),
                    onclick: () => {
                        occasion = o;
                        if (!title.value.trim() || Object.keys(OCCASIONS).some(k => title.value === suggestTitle(k))) title.value = suggestTitle(o);
                        paintOcc();
                    }
                },
                h('span.occ.occ-' + o, icon(occIcon(o))), h('span', occLabel(o)))));
        }

        const suggestTitle = o => ({
            birthday: t('My birthday'),
            christmas: t('Christmas {y}', {y: new Date().getFullYear()}),
            wedding: t('Our wedding'),
            baby: t('Baby shower'),
            graduation: t('My graduation'),
            anniversary: t('Our anniversary'),
            housewarming: t('New home')
        }[o] || '');
        paintOcc();
        if (!isEdit && !title.value) title.value = suggestTitle(occasion);
        const viewersBox = h('div.pick-friends');

        function paintViewers() {
            viewersBox.hidden = visibility !== 'selected';
            viewersBox.replaceChildren(...(S.followers.length ? S.followers.map(f => {
                const cb = h('input', {
                    type: 'checkbox', checked: chosen.has(f.id), onchange: () => {
                        cb.checked ? chosen.add(f.id) : chosen.delete(f.id);
                    }
                });
                return h('label.check.friend-check', cb, avatar(f.name, 'sm'), h('span', f.name));
            }) : [h('p.helper', t('Nobody follows you yet: find your friends in the Friends tab.'))]));
        }

        paintViewers();
        const save = btn(isEdit ? t('Save') : t('Create list'), {
            icon: 'check', onclick: async () => {
                if (!title.value.trim()) {
                    toast(t('Give the list a name.'), 'error');
                    title.focus();
                    return;
                }
                const d = await act('list.save', {
                    id: l.id || 0,
                    title: title.value,
                    occasion,
                    eventDate: date.value || null,
                    description: desc.value,
                    visibility,
                    viewers: [...chosen]
                }, save);
                s.close();
                if (!isEdit) {
                    goLater('list/' + d.id);
                    setTimeout(() => toast(t('List created: now add your wishes')), 400);
                }
            }
        });
        const s = sheet({
            title: isEdit ? t('Edit list') : t('New wish list'),
            body: [
                h('div.label', t('Occasion')), occWrap,
                field(t('Name of the list'), title),
                field(t('Date of the event (optional)'), date, {hint: t('Friends see how many days are left')}),
                field(t('Message for your friends (optional)'), desc),
                h('div.label', t('Who can see it')),
                seg([['friends', t('All my followers'), 'group'], ['selected', t('Only some of them'), 'lock']], visibility, v => {
                    visibility = v;
                    paintViewers();
                }),
                viewersBox,
            ],
            actions: [cancelBtn(), save],
        });
    }

    function viewList(id) {
        const l = myList(id);
        if (!l) {
            go('lists');
            return null;
        }
        const body = [];
        body.push(h('div.list-head',
            h('span.occ.occ-lg.occ-' + l.occasion, icon(occIcon(l.occasion))),
            h('div', h('div.meta-row', chip(occLabel(l.occasion), 'neutral'), dateChip(l.eventDate), chip(l.visibility === 'friends' ? t('All followers') : plural(l.viewers.length, 'follower', 'followers'), 'neutral', l.visibility === 'friends' ? 'group' : 'lock')),
                l.description ? h('p.lead.muted', l.description) : null)));
        if (!store.get('hide.surprise', false)) {
            body.push(banner('info', 'visibilityOff', t('Your surprise is safe'), t('Friends can mark what they are going to give you and talk about it, but you will never see it. Keep your list up to date and leave the rest to them.'),
                [btn(t('Got it'), {
                    kind: 'text', onclick: () => {
                        store.set('hide.surprise', true);
                        render(true);
                    }
                })]));
        }
        if (!l.items.length) body.push(empty('gift', t('The list is empty'), t('Add what you would like: paste a link from any shop and Giftwise fills in name, picture and price.')));
        body.push(h('div.stack', l.items.map(it => wishCard(it, {
            onclick: () => openWishDetail(it, l),
            trail: moreBtn([
                it.url ? {
                    icon: 'open',
                    label: t('Open link'),
                    onclick: () => window.open(it.url, '_blank', 'noopener')
                } : null,
                {icon: 'edit', label: t('Edit'), onclick: () => openWishForm(it)},
                {icon: 'delete', label: t('Remove from the list'), danger: true, onclick: () => deleteWish(it)},
            ], it.title),
        }))));
        return {
            tab: 'lists',
            title: l.title,
            back: 'lists',
            body,
            actions: [moreBtn(listMenu(l), l.title)],
            fab: fab(t('Add a wish'), 'add', () => openWishForm({listId: l.id}))
        };
    }

    function wishCard(it, o) {
        o = o || {};
        return h('div.card.clickable.wish-card' + (it.priority ? '.top' : ''), {
                role: 'button',
                tabindex: 0,
                onclick: o.onclick,
                onkeydown: e => {
                    if (e.key === 'Enter' && e.target === e.currentTarget && o.onclick) o.onclick();
                }
            },
            listRow({
                lead: wishThumb(it),
                title: [it.priority ? h('span.star', {title: t('Most wanted')}, icon('star')) : null, it.title],
                sub: wishMeta(it).length ? sep(wishMeta(it)) : null,
                meta: o.meta || (it.notes ? [h('span.note', it.notes)] : null),
                trail: o.trail,
            }));
    }

    async function deleteWish(it) {
        if (!(await confirm(t('Remove “{title}”?', {title: it.title}), t('It will disappear from the list (also for your friends).'), t('Remove'), true))) return;
        await act('item.delete', {id: it.id});
        UI.closeAllSheets();
        toast(t('Wish removed'));
    }

    function openWishDetail(it, l) {
        const s = sheet({
            title: it.title,
            body: [
                h('div.wish-detail', it.image ? wishThumb(it, 'xl') : null,
                    h('div.wd-meta', it.price != null ? h('p.price.big', UI.money(it.price, it.currency)) : null,
                        it.priority ? chip(t('Most wanted'), 'warn', 'star') : null, it.quantity > 1 ? chip(t('Quantity: {n}', {n: it.quantity}), 'neutral') : null,
                        it.url ? h('p.small.muted', domain(it.url)) : null)),
                it.notes ? h('p.wd-notes', it.notes) : null,
                it.url ? h('a.btn.btn-tonal.btn-block', {
                    href: it.url,
                    target: '_blank',
                    rel: 'noopener'
                }, icon('open'), h('span', t('Open the product page'))) : null,
            ],
            actions: [btn(t('Remove'), {
                kind: 'danger-text',
                icon: 'delete',
                onclick: () => deleteWish(it)
            }), btn(t('Edit'), {
                kind: 'tonal', icon: 'edit', onclick: () => {
                    s.close();
                    setTimeout(() => openWishForm(it), 230);
                }
            })],
        });
    }

    // ---------- add / edit a wish (with link preview) ----------

    function extractUrl(text) {
        const m = String(text || '').match(/https?:\/\/[^\s<>"]+/i);
        return m ? m[0].replace(/[).,;!?»”]+$/, '') : '';
    }

    function openWishForm(it, shared) {
        const isEdit = !!it.id;
        const lists = S.myLists.filter(l => !l.archived);
        if (!lists.length && !isEdit) {
            toast(t('First create a list for your wishes.'));
            openListForm({});
            return;
        }
        const url = input({type: 'url', value: it.url || '', inputmode: 'url', maxlength: 2000});
        const title = input({value: it.title || '', maxlength: 160});
        const image = input({type: 'url', value: it.image || '', maxlength: 2000});
        const price = input({
            value: it.price != null ? String(it.price).replace('.', I18N.lang === 'it' ? ',' : '.') : '',
            inputmode: 'decimal',
            maxlength: 12
        });
        const currency = select(['EUR', 'GBP', 'USD', 'CHF'].map(c => ({
            value: c,
            label: c
        })), it.currency || store.get('currency', I18N.lang === 'it' ? 'EUR' : 'GBP'));
        const qty = input({type: 'number', min: 1, max: 99, value: it.quantity || 1, inputmode: 'numeric'});
        const notes = textarea({value: it.notes || '', maxlength: 1000, rows: 2});
        let priority = it.priority || 0;
        const defaultList = it.listId || (route().name === 'list' ? Number(route().id) : null) || store.get('lastList', lists[0] && lists[0].id);
        const listSel = select(S.myLists.filter(l => !l.archived || l.id === it.listId).map(l => ({
            value: l.id,
            label: l.title
        })), lists.some(l => l.id === defaultList) || isEdit ? defaultList : lists[0].id);
        const prev = h('div.link-preview', {hidden: true});
        const fetchBtn = btn(t('Get details'), {kind: 'tonal', icon: 'link', onclick: () => fetchPreview(true)});
        let lastFetched = '';

        function paintPrev() {
            const show = image.value || title.value;
            prev.hidden = !show;
            prev.replaceChildren(wishThumb({image: image.value}, 'lg'), h('div', h('strong', title.value || t('Untitled')), h('small', sep([price.value ? UI.money(Number(price.value.replace(',', '.')), currency.value) : null, domain(url.value)].filter(Boolean)))));
        }

        async function fetchPreview(force) {
            const u = extractUrl(url.value);
            if (!u) {
                if (force) toast(t('Paste a link first.'), 'error');
                return;
            }
            if (u !== url.value) url.value = u;
            if (!force && u === lastFetched) return;
            lastFetched = u;
            try {
                const d = await busy(fetchBtn, () => Api.post('link.preview', {url: u}));
                const p = d.preview;
                if (p.title && (!title.value || force)) title.value = p.title;
                if (p.image && (!image.value || force)) image.value = p.image;
                if (p.price != null && (!price.value || force)) price.value = String(p.price).replace('.', I18N.lang === 'it' ? ',' : '.');
                if (p.currency) currency.value = p.currency;
                if (p.url && p.url !== u && p.url.length < 600) url.value = p.url;
                if (!p.title && !p.image) toast(t('The shop did not share the details: fill them in yourself.'));
                paintPrev();
            } catch (e) { /* busy() already showed the error */
            }
        }

        url.addEventListener('paste', () => setTimeout(() => fetchPreview(false), 30));
        url.addEventListener('change', () => fetchPreview(false));
        [title, image, price].forEach(x => x.addEventListener('input', paintPrev));
        const prioSeg = seg([[0, t('Nice to have'), 'starOutline'], [1, t('Most wanted'), 'star']], priority ? 1 : 0, v => {
            priority = v;
        });
        const save = btn(isEdit ? t('Save') : t('Add to the list'), {
            icon: 'check', onclick: async () => {
                if (!title.value.trim()) {
                    toast(t('Give the wish a name.'), 'error');
                    title.focus();
                    return;
                }
                store.set('currency', currency.value);
                store.set('lastList', Number(listSel.value));
                await act('item.save', {
                    id: it.id || 0,
                    listId: Number(listSel.value),
                    title: title.value,
                    url: extractUrl(url.value),
                    image: image.value.trim(),
                    price: price.value.trim(),
                    currency: currency.value,
                    quantity: Number(qty.value) || 1,
                    priority,
                    notes: notes.value
                }, save);
                s.close();
                toast(isEdit ? t('Wish updated') : t('Added to “{title}”', {title: (myList(listSel.value) || {}).title || ''}), null, !isEdit && route().name !== 'list' ? {
                    label: t('Open'),
                    onclick: () => go('list/' + listSel.value)
                } : null);
            }
        });
        const s = sheet({
            title: isEdit ? t('Edit wish') : t('Add a wish'),
            body: [
                field(t('Link to the product (optional)'), url, {hint: t('Paste it from any shop: name, picture and price are filled in for you')}),
                h('div.url-actions', fetchBtn),
                prev,
                field(t('What would you like?'), title),
                h('div.field-row', field(t('Price (optional)'), price), field(t('Currency'), currency, {cls: 'narrow'}), field(t('Quantity'), qty, {cls: 'narrow'})),
                prioSeg,
                field(t('Notes for your friends'), notes, {hint: t('Size, colour, model… anything that helps')}),
                field(t('Picture (link to an image)'), image),
                field(t('List'), listSel),
            ],
            actions: [cancelBtn(), save],
            autofocus: false,
        });
        paintPrev();
        if (shared && extractUrl(url.value)) fetchPreview(false);
        else if (!isEdit) setTimeout(() => url.focus(), 260);
    }

    // ===================================================== FRIENDS' LISTS (the secret part)

    function viewGifts() {
        const body = [];
        const lists = S.friendLists;
        if (!S.following.length) {
            body.push(empty('group', t('You don’t follow anyone yet'), t('Follow your friends to see their wish lists and agree with the others on who gives what.'),
                btn(t('Find friends'), {icon: 'search', onclick: () => go('friends')})));
            return {tab: 'gifts', title: t('Friends’ lists'), body};
        }
        if (!lists.length) body.push(empty('gift', t('Nothing here yet'), t('When your friends create a wish list, you will find it here.')));
        const soon = lists.filter(l => l.eventDate && UI.daysUntil(l.eventDate) >= 0),
            other = lists.filter(l => !(l.eventDate && UI.daysUntil(l.eventDate) >= 0));
        if (soon.length) {
            body.push(sectionTitle(t('Coming up'), soon.length));
            body.push(h('div.stack', soon.map(giftCard)));
        }
        if (other.length) {
            body.push(sectionTitle(soon.length ? t('Other lists') : t('Their lists'), other.length));
            body.push(h('div.stack', other.map(giftCard)));
        }
        return {tab: 'gifts', title: t('Friends’ lists'), body};
    }

    function giftCard(l) {
        const free = Math.max(0, l.itemCount - l.takenCount);
        return cardRow({
            big: true,
            lead: h('span.occ-wrap', avatar(l.owner.name), h('span.occ.occ-mini.occ-' + l.occasion, icon(occIcon(l.occasion)))),
            title: l.title,
            sub: sep([l.owner.name, occLabel(l.occasion), plural(l.itemCount, 'wish', 'wishes')]),
            meta: [dateChip(l.eventDate), l.takenCount ? chip(t('{n} taken', {n: l.takenCount}), 'taken', 'check') : null, free && l.itemCount ? chip(t('{n} still free', {n: free}), 'neutral') : null,
                l.myClaims ? chip(t('You: {n}', {n: l.myClaims}), 'ok', 'gift') : null, l.commentCount ? chip(String(l.commentCount), 'neutral', 'chat') : null],
            trail: icon('right'),
        }, () => go('gift/' + l.id));
    }

    async function loadView(id, force) {
        if (S.views[id] && !force) return S.views[id];
        const d = await Api.get('list.view&id=' + id);
        S.views[id] = d;
        return d;
    }

    function viewGift(id) {
        const summary = S.friendLists.find(l => l.id === Number(id));
        const v = S.views[id];
        if (!v) {
            loadView(id).then(() => render(true)).catch(e => {
                toast(e.message, 'error');
                go('gifts');
            });
            return {
                tab: 'gifts',
                title: summary ? summary.title : t('Wish list'),
                back: 'gifts',
                body: [UI.skeletonScreen()]
            };
        }
        const l = v.list, me = S.user.id;
        const body = [];
        body.push(h('div.list-head',
            h('span.occ-wrap', avatar(l.owner.name, 'lg'), h('span.occ.occ-mini.occ-' + l.occasion, icon(occIcon(l.occasion)))),
            h('div', h('p.owner-line', t('{name}’s list', {name: l.owner.name})),
                h('div.meta-row', chip(occLabel(l.occasion), 'neutral'), dateChip(l.eventDate)),
                l.description ? h('p.lead.muted', '“' + l.description + '”') : null)));
        body.push(h('div.secret-note', icon('visibilityOff'), h('span', t('{name} can’t see what you mark or write here.', {name: l.owner.name.split(' ')[0]}))));
        if (!v.items.length) body.push(empty('gift', t('No wishes yet'), t('{name} hasn’t added anything to this list yet.', {name: l.owner.name.split(' ')[0]})));
        const order = it => {
            const mine = it.claims.find(c => c.userId === me);
            const taken = it.claims.some(c => c.status === 'taken');
            return mine ? 1 : taken ? 2 : 0;
        };
        const items = v.items.slice().sort((a, b) => order(a) - order(b) || b.priority - a.priority);
        body.push(h('div.stack', items.map(it => {
            const n = v.comments.filter(c => c.itemId === it.id).length;
            return wishCard(it, {
                onclick: () => openFriendWish(id, it.id),
                meta: [statusChip(it), n ? chip(String(n), 'neutral', 'chat') : null],
                trail: icon('right')
            });
        })));
        body.push(sectionTitle(t('Chat with the other friends'), v.comments.filter(c => c.itemId === null).length || null));
        body.push(chatBox(id, null, v));
        return {tab: 'gifts', title: l.title, back: 'gifts', body};
    }

    /** "Free" / "Anna is thinking about it" / "Taken by Bruno" (with "you" when it is me). */
    function statusChip(it) {
        const me = S.user.id;
        const taken = it.claims.filter(c => c.status === 'taken'),
            consid = it.claims.filter(c => c.status === 'considering');
        const who = list => list.map(c => c.userId === me ? t('you') : c.name.split(' ')[0]).join(', ');
        if (taken.length) return chip(taken.some(c => c.userId === me) && taken.length === 1 ? t('You are getting it') : t('Taken by {who}', {who: who(taken)}), 'taken', 'check');
        if (consid.length) return chip(consid.some(c => c.userId === me) && consid.length === 1 ? t('You are thinking about it') : t('Thinking about it: {who}', {who: who(consid)}), 'warn', 'think');
        return chip(t('Still free'), 'neutral');
    }

    function openFriendWish(listId, itemId) {
        let s = null;
        const bodyBox = h('div');

        function paint() {
            const v = S.views[listId];
            const it = v && v.items.find(x => x.id === itemId);
            if (!it) {
                if (s) s.close();
                toast(t('This wish has been removed from the list.'));
                return;
            }
            const mine = it.claims.find(c => c.userId === S.user.id);
            const cur = mine ? mine.status : 'none';
            const others = it.claims.filter(c => c.userId !== S.user.id);
            bodyBox.replaceChildren(...[
                h('div.wish-detail', it.image ? wishThumb(it, 'xl') : null,
                    h('div.wd-meta', it.price != null ? h('p.price.big', UI.money(it.price, it.currency)) : null,
                        it.priority ? chip(t('Most wanted'), 'warn', 'star') : null, it.quantity > 1 ? chip(t('Quantity: {n}', {n: it.quantity}), 'neutral') : null,
                        it.url ? h('p.small.muted', domain(it.url)) : null)),
                it.notes ? h('p.wd-notes', '“' + it.notes + '”') : null,
                it.url ? h('a.btn.btn-tonal.btn-block', {
                    href: it.url,
                    target: '_blank',
                    rel: 'noopener'
                }, icon('open'), h('span', t('Open the product page'))) : null,
                h('div.label', t('Your plan')),
                h('div.claim-seg', [['none', t('Not for me'), 'close'], ['considering', t('Thinking about it'), 'think'], ['taken', t('I’ll get it'), 'check']].map(([k, label, ic]) =>
                    h('button.claim-' + k + (k === cur ? '.sel' : ''), {
                        type: 'button', onclick: async e => {
                            if (k === cur) return;
                            const b = e.currentTarget;
                            try {
                                const d = await busy(b, () => Api.post('claim.set', {itemId: it.id, status: k}));
                                S.views[listId] = d;
                                paint();
                                render(true);
                                refreshSummaries();
                                toast(k === 'taken' ? t('Marked as taken: the others will know') : k === 'considering' ? t('The others will know you are thinking about it') : t('Removed your plan'));
                            } catch (x) {
                                if (x.status === 404) {
                                    delete S.views[listId];
                                    render(true);
                                }
                            }
                        }
                    }, icon(ic), h('span', label)))),
                others.length ? h('div.claims-list', others.map(c => h('div.claim-line', avatar(c.name, 'sm'), h('span', h('strong', c.name), ' · ', c.status === 'taken' ? t('is getting it') : t('is thinking about it')), h('small.muted', UI.ago(c.at))))) : null,
                h('div.label', t('Talk about this gift')),
                chatBox(listId, it.id, S.views[listId], paint)].filter(Boolean));
        }

        paint();
        s = sheet({
            title: (S.views[listId].items.find(x => x.id === itemId) || {}).title || '',
            body: [bodyBox],
            cls: 'tall',
            autofocus: false
        });
    }

    /** Messages between friends about a list (itemId null) or one wish, with the input box. */
    function chatBox(listId, itemId, v, after) {
        const list = h('div.chat');
        const msgs = v.comments.filter(c => c.itemId === itemId);
        if (!msgs.length) list.appendChild(h('p.chat-empty', itemId ? t('No messages yet. Ask the others: shall we split it?') : t('Agree on the presents here: who gives what, splitting the cost, a group gift…')));
        for (const c of msgs) {
            const mine = c.userId === S.user.id;
            list.appendChild(h('div.msg' + (mine ? '.mine' : ''), !mine ? avatar(c.name, 'sm') : null,
                h('div.bubble', !mine ? h('strong', c.name.split(' ')[0]) : null, h('p', c.body), h('small', UI.ago(c.at),
                    mine ? h('button.msg-del', {
                        type: 'button', title: t('Delete'), onclick: async () => {
                            const d = await Api.post('comment.delete', {id: c.id});
                            if (d.list) S.views[listId] = d;
                            refresh();
                        }
                    }, t('Delete')) : null))));
        }
        const box = textarea({
            rows: 1,
            maxlength: 1000,
            placeholder: t('Write to the other friends…'),
            'aria-label': t('Message')
        });
        const send = iconBtn('send', t('Send'), async () => {
            const text = box.value.trim();
            if (!text) return;
            send.disabled = true;
            try {
                S.views[listId] = await Api.post('comment.add', {listId: Number(listId), itemId, body: text});
                box.value = '';
                refresh();
                refreshSummaries();
            } catch (e) {
                toast(e.message, 'error');
            } finally {
                send.disabled = false;
            }
        }, 'tonal');
        box.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                send.click();
            }
        });
        box.addEventListener('input', () => {
            box.style.height = 'auto';
            box.style.height = Math.min(box.scrollHeight, 140) + 'px';
        });

        function refresh() {
            if (after) after();
            render(true);
        }

        return h('div.chat-wrap', list, h('div.chat-input', box, send));
    }

    async function refreshSummaries() {
        try {
            applyState(await Api.get('state'));
        } catch (e) { /* offline */
        }
    }

    // ===================================================== PEOPLE (follow / followers)

    const at = u => (u && u.username ? '@' + u.username : '');
    let searchQ = '', searchResults = null, searchTimer = null, searchSeq = 0;

    /** Follow button for a person, from the relation (following: none | pending | accepted). */
    function followBtn(p, small) {
        const kind = p.following === 'accepted' ? 'tonal' : p.following === 'pending' ? 'text' : 'filled';
        const label = p.following === 'accepted' ? t('Following') : p.following === 'pending' ? t('Requested') : (p.followsMe === 'accepted' ? t('Follow back') : t('Follow'));
        const b = btn(label, {
            kind,
            icon: p.following === 'accepted' ? 'check' : p.following === 'pending' ? 'timer' : 'personAdd',
            cls: small ? 'btn-sm' : '',
            onclick: async e => {
                e.stopPropagation();
                if (p.following === 'none') {
                    const d = await act('follow', {userId: p.id}, b);
                    p.following = d.result === 'following' ? 'accepted' : 'pending';
                    toast(d.result === 'following' ? t('You now follow {name}', {name: p.name.split(' ')[0]}) : t('Request sent: {name} has to accept it', {name: p.name.split(' ')[0]}));
                } else if (p.following === 'pending') {
                    await act('unfollow', {userId: p.id}, b);
                    p.following = 'none';
                    toast(t('Request cancelled'));
                } else if (await confirm(t('Stop following {name}?', {name: p.name}), t('You will no longer see their wish lists. Your plans and messages on their lists stay, but you will not see them unless you follow them again.'), t('Unfollow'), true)) {
                    await act('unfollow', {userId: p.id});
                    p.following = 'none';
                    toast(t('You no longer follow {name}', {name: p.name.split(' ')[0]}));
                }
                paintSearch();
            }
        });
        return b;
    }

    function personRow(p, trail, sub) {
        return h('div.list-row.person-row', {
                role: 'button',
                tabindex: 0,
                onclick: () => go('user/' + p.id),
                onkeydown: e => {
                    if (e.key === 'Enter' && e.target === e.currentTarget) go('user/' + p.id);
                }
            },
            avatar(p.name), h('div.body', h('p.title', p.name), h('div.sub', sub || at(p))), trail ? h('div.trail.gap', trail) : null);
    }

    const resultsBox = h('div.search-results');

    function paintSearch() {
        UI.clear(resultsBox);
        if (!searchQ.trim()) return;
        if (searchResults === null) {
            resultsBox.appendChild(h('p.helper', t('Searching…')));
            return;
        }
        if (!searchResults.length) {
            resultsBox.appendChild(h('div.card.empty-search', h('p', t('Nobody found with “{q}”.', {q: searchQ})), h('p.small.muted', t('Check the username, or invite them with your link.')),
                btn(t('Share invite link'), {
                    kind: 'tonal',
                    icon: 'share',
                    onclick: () => shareOrCopy(t('Join me on Giftwise: let’s make gift giving easy!'), inviteUrl())
                })));
            return;
        }
        resultsBox.appendChild(h('div.group', searchResults.map(p => personRow(p, followBtn(p, true), [at(p), p.followsMe === 'accepted' ? ' · ' + t('follows you') : ''].join('')))));
    }

    function viewFriends() {
        const body = [];
        const inc = S.requests.incoming, out = S.requests.outgoing;
        const q = input({
            type: 'search',
            value: searchQ,
            placeholder: t('Search by username or email'),
            enterkeyhint: 'search',
            autocapitalize: 'none',
            autocomplete: 'off',
            'aria-label': t('Search people')
        });
        q.addEventListener('input', () => {
            searchQ = q.value;
            searchResults = null;
            clearTimeout(searchTimer);
            paintSearch();
            const term = q.value.trim();
            if (term.replace(/^@/, '').length < 2) {
                searchResults = [];
                if (!term) UI.clear(resultsBox); else paintSearch();
                return;
            }
            const seq = ++searchSeq;
            searchTimer = setTimeout(async () => {
                try {
                    const d = await Api.get('user.search&q=' + encodeURIComponent(term));
                    if (seq === searchSeq) {
                        searchResults = d.results;
                        paintSearch();
                    }
                } catch (e) {
                    if (seq === searchSeq) {
                        searchResults = [];
                        paintSearch();
                    }
                }
            }, 250);
        });
        body.push(h('div.searchbar', icon('search'), q));
        body.push(resultsBox);
        paintSearch();
        if (inc.length) {
            body.push(sectionTitle(t('Requests to follow you'), inc.length));
            body.push(h('div.group', inc.map(p => personRow(p, [
                btn(t('Decline'), {
                    kind: 'text', cls: 'btn-sm', onclick: e => {
                        e.stopPropagation();
                        act('follow.decline', {userId: p.id}, e.currentTarget);
                    }
                }),
                btn(t('Accept'), {
                    icon: 'check', cls: 'btn-sm', onclick: e => {
                        e.stopPropagation();
                        act('follow.accept', {userId: p.id}, e.currentTarget).then(() => toast(t('{name} now follows you', {name: p.name.split(' ')[0]})));
                    }
                }),
            ]))));
        }
        body.push(sectionTitle(t('You follow'), S.following.length));
        if (!S.following.length) body.push(h('p.helper', t('You don’t follow anyone yet: search for your friends by username.')));
        else body.push(h('div.group', S.following.map(f => {
            const n = S.friendLists.filter(l => l.owner.id === f.id).length;
            return personRow(f, icon('right'), [at(f), n ? plural(n, 'wish list', 'wish lists') : t('No lists shared with you')].filter(Boolean).join(' · '));
        })));
        body.push(sectionTitle(t('Your followers'), S.followers.length));
        if (!S.followers.length) body.push(h('p.helper', t('Nobody follows you yet. Share your invite link: who opens it and you will follow each other.')));
        else body.push(h('div.group', S.followers.map(f => personRow(f, icon('right'), [at(f), S.following.some(x => x.id === f.id) ? t('you follow each other') : ''].filter(Boolean).join(' · ')))));
        if (out.length) {
            body.push(sectionTitle(t('Waiting for an answer'), out.length));
            body.push(h('div.group', out.map(p => personRow(p, btn(t('Cancel'), {
                kind: 'text',
                cls: 'btn-sm',
                onclick: e => {
                    e.stopPropagation();
                    act('unfollow', {userId: p.id}, e.currentTarget);
                }
            }), at(p) + ' · ' + t('Request sent {when}', {when: UI.ago(p.at)})))));
        }
        body.push(h('div.card.invite-card',
            h('div.ic-top', tile('icon', 'share', 'lg'), h('div', h('strong', t('Invite your friends')), h('p', t('Not on Giftwise yet? Send them your link: when they open it you will follow each other (they can sign up in a few seconds).')))),
            h('div.code-line', h('code', inviteUrl().replace(/^https?:\/\//, ''))),
            h('div.btn-row', btn(t('Share invite link'), {
                icon: 'share',
                onclick: () => shareOrCopy(t('Join me on Giftwise: let’s make gift giving easy!'), inviteUrl())
            }))));
        return {
            tab: 'friends',
            title: t('Friends'),
            body,
            fab: fab(t('Invite'), 'share', () => shareOrCopy(t('Join me on Giftwise: let’s make gift giving easy!'), inviteUrl())),
            after: () => {
                if (searchQ && document.activeElement === document.body) {
                }
            }
        };
    }

    const people = {};

    function viewPerson(id) {
        id = Number(id);
        if (id === S.user.id) {
            go('profile');
            return null;
        }
        const known = S.following.find(x => x.id === id) || S.followers.find(x => x.id === id) || S.requests.incoming.find(x => x.id === id) || S.requests.outgoing.find(x => x.id === id);
        const p = people[id];
        if (!p) {
            Api.get('user.get&id=' + id).then(d => {
                people[id] = d.person;
                render(true);
            }).catch(e => {
                toast(e.message, 'error');
                go('friends');
            });
            return {
                tab: 'friends',
                title: known ? known.name : t('Profile'),
                back: 'friends',
                body: [UI.skeletonScreen()]
            };
        }
        // Keep the relation in sync with the latest state
        p.following = S.following.some(x => x.id === id) ? 'accepted' : S.requests.outgoing.some(x => x.id === id) ? 'pending' : 'none';
        p.followsMe = S.followers.some(x => x.id === id) ? 'accepted' : S.requests.incoming.some(x => x.id === id) ? 'pending' : 'none';
        const lists = S.friendLists.filter(l => l.owner.id === id);
        const first = p.name.split(' ')[0];
        const body = [h('div.card.person-card', listRow({
                big: true, lead: avatar(p.name, 'lg'), title: p.name, sub: at(p),
                meta: [p.followsMe === 'accepted' ? chip(t('Follows you'), 'neutral', 'group') : null, p.following === 'accepted' && p.followsMe === 'accepted' ? chip(t('You follow each other'), 'ok', 'check') : null]
            }),
            h('div.btn-row', followBtn(p)))];
        if (p.followsMe === 'pending') {
            body.push(banner('info', 'personAdd', t('{name} wants to follow you', {name: first}), t('If you accept, {name} will see your wish lists.', {name: first}),
                [btn(t('Decline'), {
                    kind: 'text',
                    onclick: e => act('follow.decline', {userId: id}, e.currentTarget)
                }), btn(t('Accept'), {
                    icon: 'check',
                    onclick: e => act('follow.accept', {userId: id}, e.currentTarget)
                })]));
        }
        if (p.following === 'accepted') {
            body.push(sectionTitle(t('Wish lists'), lists.length));
            body.push(lists.length ? h('div.stack', lists.map(giftCard)) : h('p.helper', t('{name} has not shared any wish list with you yet.', {name: first})));
        } else {
            body.push(h('div.locked', icon('lock'), h('p', p.following === 'pending' ? t('Waiting for {name} to accept your request.', {name: first}) : t('Follow {name} to see their wish lists.', {name: first}))));
        }
        const menu = [];
        if (p.followsMe === 'accepted') menu.push({
            icon: 'delete', label: t('Remove from your followers'), danger: true, onclick: async () => {
                if (await confirm(t('Remove {name} from your followers?', {name: p.name}), t('{name} will no longer see your wish lists (they are not notified).', {name: first}), t('Remove'), true)) {
                    await act('follower.remove', {userId: id});
                    toast(t('Removed from your followers'));
                }
            }
        });
        return {
            tab: 'friends',
            title: p.name,
            back: 'friends',
            body,
            actions: menu.length ? [moreBtn(menu, p.name)] : []
        };
    }

    function viewInvite(code) {
        // Opened an invite link while signed in: become friends
        if (code === S.user.inviteCode) {
            setTimeout(() => {
                toast(t('This is your own invite link: share it with your friends.'));
                go('friends');
            }, 0);
            return null;
        }
        Api.post('invite.accept', {code}).then(d => {
            applyState(d);
            location.replace('#/friends');
            toast(d.already ? t('You and {name} already follow each other', {name: d.friendName.split(' ')[0]}) : t('You and {name} now follow each other!', {name: d.friendName.split(' ')[0]}));
        }).catch(e => {
            toast(e.message, 'error');
            location.replace('#/friends');
        });
        return {tab: 'friends', title: t('Invite'), back: 'friends', body: [UI.skeletonScreen()]};
    }

    // ===================================================== PROFILE

    /** "Developed with ♥ by Saverio Morelli" at the bottom of the profile, in the font of the website (heart as SVG). */
    function devCredit() {
        return h('a.dev-credit', {
                href: 'https://www.saveriomorelli.com/',
                target: '_blank',
                rel: 'noopener',
                'aria-label': t('Developed with love by Saverio Morelli')
            },
            h('span', t('Developed with')), h('span.dev-heart', {html: '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="#E5484D" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>'}), h('span', t('by')), h('span.dev-name', 'Saverio Morelli'));
    }

    function viewProfile() {
        const u = S.user;
        const theme = store.get('theme', 'auto');
        const body = [
            h('div.card', listRow({
                big: true, lead: avatar(u.name, 'lg'), title: u.name, sub: '@' + (u.username || '') + ' · ' + u.email,
                meta: [chip(plural(S.followers.length, 'follower', 'followers'), 'neutral', 'group'), chip(t('{n} following', {n: S.following.length}), 'neutral'), u.googleLinked ? chip('Google', 'neutral', 'link') : null]
            })),
            sectionTitle(t('Account')),
            h('div.group',
                groupRow('person', t('Username'), '@' + (u.username || '—'), openUsernameForm),
                groupRow('edit', t('Name and email'), u.name + ' · ' + u.email, openProfileForm),
                groupRow('history', t('Activity history'), t('View recent actions and undo mistakes'), () => go('history')),
                groupRow('lock', t('Who can follow you'), u.followPolicy === 'open' ? t('Everyone, without asking') : t('Only people you approve'), openPrivacy),
                groupRow('lock', u.hasPassword ? t('Change password') : t('Set a password'), u.hasPassword ? null : t('Sign in also without Google'), openPasswordForm),
                groupRow('personAdd', t('Your invite link'), t('Whoever opens it and you will follow each other'), () => shareOrCopy(t('Join me on Giftwise: let’s make gift giving easy!'), inviteUrl()))),
            sectionTitle(t('Preferences')),
            h('div.group',
                groupRow('globe', t('Language'), (I18N.langs.find(l => l[0] === I18N.lang) || [, ''])[1], () => actionSheet(I18N.langs.map(([k, label]) => ({
                    icon: 'globe',
                    label,
                    selected: k === I18N.lang,
                    onclick: () => I18N.set(k)
                })), t('Language'))),
                groupRow('theme', t('Theme'), {
                    auto: t('Automatic'),
                    light: t('Light'),
                    dark: t('Dark')
                }[theme], () => actionSheet([['auto', t('Automatic (like the system)')], ['light', t('Light')], ['dark', t('Dark')]].map(([k, label]) => ({
                    icon: 'theme',
                    label,
                    selected: k === theme,
                    onclick: () => {
                        store.set('theme', k);
                        UI.applyTheme();
                        render(true);
                    }
                })), t('Theme'))),
                groupRow('school', t('App tutorial'), t('A quick tour of how Giftwise works'), () => openTutorial()),
                groupRow('bulb', t('Show tips again'), null, () => {
                    ['hide.nofriends', 'hide.surprise'].forEach(k => store.set(k, false));
                    render(true);
                    toast(t('Tips restored'));
                })),
            sectionTitle(t('Other')),
            h('div.group',
                groupRow('info', t('Privacy and info'), 'Giftwise ' + VERSION, null, {
                    href: 'privacy.html',
                    external: true
                }),
                groupRow('chat', t('Contact us'), t('Suggestions or problems'), null, {
                    href: CONTACT_URL,
                    external: true
                }),
                groupRow('heart', t('Donation'), t('Free and open-source project, with no ads: a small donation helps cover server and maintenance costs.'), null, {
                    href: DONATE_URL,
                    external: true
                }),
                groupRow('logout', t('Sign out'), null, async () => {
                    try {
                        await Api.post('auth.logout', {});
                    } catch (e) { /* ignore */
                    }
                    S.user = null;
                    S.loaded = false;
                    render();
                }),
                groupRow('delete', t('Delete my account'), t('Lists, wishes and friendships are deleted'), openDeleteAccount, {danger: true})),
        ];
        body.push(devCredit());
        return {tab: null, title: t('Profile'), back: 'lists', body, cls: 'narrow'};
    }

    // ===================================================== ACTIVITY HISTORY

    function viewHistory() {
        let list = null;
        const box = h('div.history-box', UI.skeletonScreen());
        const body = [box];

        async function load() {
            try {
                const d = await Api.get('history.list');
                list = d.history || [];
                paint();
            } catch (e) {
                list = [];
                paint();
                toast(e.message, 'error');
            }
        }

        function paint() {
            UI.clear(box);
            if (!list || !list.length) {
                box.appendChild(empty('history', t('No actions recorded yet'), t('Actions such as adding, editing or deleting wishes and lists will appear here.')));
                return;
            }

            box.appendChild(h('div.stack', list.map(hItem => {
                let title = '';
                let ic = 'history';
                const itemTitle = hItem.itemTitle || t('Untitled');
                const listTitle = hItem.listTitle || '';
                const act = hItem.action;

                if (act === 'wish_add') {
                    title = t('Added wish “{title}”', {title: itemTitle});
                    ic = 'add';
                } else if (act === 'wish_edit') {
                    title = t('Edited wish “{title}”', {title: itemTitle});
                    ic = 'edit';
                } else if (act === 'wish_delete') {
                    title = t('Deleted wish “{title}”', {title: itemTitle});
                    ic = 'delete';
                } else if (act === 'list_create') {
                    title = t('Created list “{title}”', {title: listTitle});
                    ic = 'list';
                } else if (act === 'list_edit') {
                    title = t('Edited list “{title}”', {title: listTitle});
                    ic = 'edit';
                } else if (act === 'list_archive') {
                    title = hItem.details && hItem.details.archived ? t('Archived list “{title}”', {title: listTitle}) : t('Unarchived list “{title}”', {title: listTitle});
                    ic = 'archive';
                } else if (act === 'list_delete') {
                    title = t('Deleted list “{title}”', {title: listTitle});
                    ic = 'delete';
                } else if (act === 'claim_set') {
                    const st = (hItem.details && hItem.details.new_status) || '';
                    if (st === 'taken') title = t('Claimed gift “{title}”', {title: itemTitle});
                    else if (st === 'considering') title = t('Marked “{title}” as idea', {title: itemTitle});
                    else title = t('Released gift “{title}”', {title: itemTitle});
                    ic = 'gift';
                } else {
                    title = `${act}: ${itemTitle || listTitle}`;
                }

                const timeStr = UI.fmtDateTime(hItem.createdAt);
                const subParts = [timeStr, listTitle && act.startsWith('wish_') ? listTitle : ''].filter(Boolean);
                const sub = subParts.join(' · ');

                const meta = [];
                if (hItem.undone) {
                    meta.push(chip(t('Undone'), 'neutral'));
                }

                const trail = hItem.undone ? null : btn(t('Undo'), {
                    kind: 'tonal', icon: 'undo',
                    onclick: async () => {
                        const ok = await confirm(t('Undo this action?'), title, t('Undo'), false);
                        if (!ok) return;
                        try {
                            const res = await Api.post('history.undo', {id: hItem.id});
                            applyState(res);
                            toast(t('Action undone successfully'));
                            load();
                        } catch (err) {
                            toast(err.message, 'error');
                        }
                    }
                });

                return cardRow({
                    lead: tile('icon', ic),
                    title,
                    sub,
                    meta,
                    trail,
                });
            })));
        }

        load();
        return {
            tab: 'lists',
            title: t('Activity history'),
            back: 'lists',
            body,
        };
    }

    function openProfileForm() {
        const name = input({value: S.user.name, maxlength: 80, autocomplete: 'name'});
        // The email identifies the account: shown, never changed
        const email = input({type: 'email', value: S.user.email, readonly: true, disabled: true, title: t('The email can’t be changed.')});
        const save = btn(t('Save'), {
            icon: 'check', onclick: async () => {
                await act('auth.profile', {name: name.value}, save);
                s.close();
                toast(t('Profile updated'));
            }
        });
        const s = sheet({
            title: t('Name and email'),
            body: [field(t('Name'), name), field('Email', email)],
            actions: [cancelBtn(), save]
        });
    }

    function openUsernameForm() {
        const v = input({
            value: S.user.username || '',
            maxlength: 20,
            autocapitalize: 'none',
            spellcheck: 'false',
            placeholder: ' ',
            class: 'input'
        });
        const save = btn(t('Save'), {
            icon: 'check', onclick: async () => {
                await act('account.username', {username: v.value}, save);
                s.close();
                toast(t('Username updated'));
            }
        });
        const s = sheet({
            title: t('Username'),
            body: [h('p.sheet-text', t('It is public: your friends search for you with it. 3–20 letters, digits, dots and underscores.')), h('div.field.uname-field', h('span.at', '@'), v, h('label', {for: (v.id = 'f-uname2')}, t('Username')))],
            actions: [cancelBtn(), save]
        });
    }

    function openPrivacy() {
        const cur = S.user.followPolicy || 'approve';
        actionSheet([
            {
                icon: 'lock',
                label: t('Only people you approve'),
                selected: cur === 'approve',
                onclick: () => act('account.privacy', {followPolicy: 'approve'}).then(() => toast(t('New followers will need your approval')))
            },
            {
                icon: 'group',
                label: t('Everyone, without asking'),
                selected: cur === 'open',
                onclick: () => act('account.privacy', {followPolicy: 'open'}).then(() => toast(t('Anyone can now follow you')))
            },
        ], t('Who can follow you (and see your lists)'));
    }

    function openPasswordForm() {
        const cur = input({type: 'password', autocomplete: 'current-password'});
        const pw = input({type: 'password', autocomplete: 'new-password'});
        const save = btn(t('Save'), {
            icon: 'check', onclick: async () => {
                await busy(save, () => Api.post('auth.password', {current: cur.value, password: pw.value}));
                S.user.hasPassword = true;
                s.close();
                render(true);
                toast(t('Password saved'));
            }
        });
        const s = sheet({
            title: S.user.hasPassword ? t('Change password') : t('Set a password'),
            body: [S.user.hasPassword ? field(t('Current password'), cur) : null, field(t('New password (min. 8 characters)'), pw)],
            actions: [cancelBtn(), save]
        });
    }

    function openDeleteAccount() {
        const pw = input({type: 'password', autocomplete: 'current-password'});
        const del = btn(t('Delete for good'), {
            kind: 'danger', icon: 'delete', onclick: async () => {
                await busy(del, () => Api.post('auth.delete', {password: pw.value}));
                s.close();
                S.user = null;
                S.loaded = false;
                render();
                toast(t('Account deleted'));
            }
        });
        const s = sheet({
            title: t('Delete your account?'),
            body: [h('p.sheet-text', t('Your lists, wishes, friendships, plans and messages will be deleted for good. This cannot be undone.')), S.user.hasPassword ? field(t('Password'), pw) : null],
            actions: [cancelBtn(), del]
        });
    }

    function openHowItWorks() {
        openTutorial();
    }

    // ===================================================== tutorial

    const MAJOR = VERSION.split('.')[0];

    /** Shown at the first sign-in on a device and the first time a new major version is opened. */
    function maybeShowTutorial() {
        const key = 'tutorial.' + S.user.id;
        // After a language change inside the tutorial the page reloads: reopen it on the same slide.
        let resume = null;
        try {
            resume = sessionStorage.getItem('gw.tourResume');
            sessionStorage.removeItem('gw.tourResume');
        } catch (e) { /* no storage */
        }
        if (resume === null && store.get(key, '') === MAJOR) return;
        setTimeout(() => openTutorial(() => store.set(key, MAJOR), +resume || 0), resume === null ? 400 : 0);
    }

    /** Small illustrations built with the app's own components (occasion badges, wish cards, chips, chat bubbles). */
    function illo(kind) {
        const el = illoContent(kind);
        el.classList.add('ill-' + kind); // per-slide animations (app.css, "Tutorial animations")
        return el;
    }

    function illoContent(kind) {
        const occ = o => h('span.occ.occ-' + o, icon(occIcon(o)));
        const mini = (lead, title, sub, meta) => h('div.tour-card', lead, h('div', h('strong', title), sub ? h('small', sub) : null, meta ? h('div.meta', meta) : null));
        const cur = I18N.lang === 'it' ? 'EUR' : 'GBP';
        switch (kind) {
            case 'welcome':
                return h('div.illo', h('div.tour-logo', h('img', {src: 'assets/icons/icon-192.png', alt: ''})),
                    h('div.float.f1', occ('birthday')), h('div.float.f2', occ('christmas')), h('div.float.f3', occ('wedding')), h('div.float.f4', occ('graduation')));
            case 'lists':
                return h('div.illo.col',
                    mini(occ('birthday'), t('My birthday'), null, [chip(t('in {n} days', {n: 12}), 'warn', 'calendar'), chip(plural(6, 'wish', 'wishes'), 'neutral')]),
                    mini(occ('christmas'), t('Christmas {y}', {y: new Date().getFullYear()}), null, [chip(t('All followers'), 'neutral', 'group')]),
                    mini(occ('graduation'), t('My graduation'), null, [chip(plural(4, 'follower', 'followers'), 'neutral', 'lock')]));
            case 'wishes':
                return h('div.illo.col',
                    mini(h('span.thumb.noimg', icon('gift')), [h('span.star', icon('star')), t('Wireless headphones')], UI.money(89.9, cur), [chip(t('Most wanted'), 'warn', 'star')]),
                    mini(h('span.thumb.noimg', icon('bag')), t('Travel backpack'), UI.money(45, cur), null),
                    h('div.tour-pill', icon('link'), t('Paste a product link')));
            case 'friends':
                return h('div.illo.col',
                    h('div.tour-avatars', ['Anna L', 'Marco R', 'Sara B'].map(n => avatar(n))),
                    mini(avatar('Marco Rossi', 'sm'), 'Marco Rossi', '@marco.rossi', [chip(t('You follow each other'), 'ok', 'check')]),
                    h('div.tour-pill', icon('share'), t('Share invite link')));
            case 'claim':
                return h('div.illo.col',
                    mini(h('span.thumb.noimg', icon('gift')), t('Wireless headphones'), UI.money(89.9, cur), [chip(t('You are getting it'), 'taken', 'check')]),
                    mini(h('span.thumb.noimg', icon('bag')), t('Travel backpack'), UI.money(45, cur), [chip(t('Thinking about it: {who}', {who: 'Sara'}), 'warn', 'think')]),
                    h('div.tour-pill', icon('visibilityOff'), t('Hidden from the list owner')));
            case 'chat':
                return h('div.illo.col',
                    h('div.tour-chat',
                        h('div.msg', avatar('Sara B', 'sm'), h('div.bubble', h('strong', 'Sara'), h('p', t('Shall we split this gift?')))),
                        h('div.msg.mine', h('div.bubble', h('p', t('Great idea, count me in!'))))),
                    h('div.banner.info.tour-banner', icon('visibilityOff'), h('div.b-body', h('h3', t('Your surprise is safe')), h('p', t('{name} can’t see what you mark or write here.', {name: 'Marco'})))));
            case 'donate':
                return h('div.illo.col',
                    mini(tile('icon', 'heart'), t('100% free & open-source'), t('No ads, no subscriptions'), [chip(t('Open-source'), 'ok', 'check')]),
                    mini(tile('icon', 'shield'), t('Server and maintenance'), t('Kept alive thanks to your support'), null),
                    h('a.tour-pill', {
                        href: DONATE_URL,
                        target: '_blank',
                        rel: 'noopener'
                    }, icon('heart'), t('Donation')));
            default:
                return h('div.illo');
        }
    }

    function openTutorial(onDone, start) {
        if (document.querySelector('.tour-ovl')) return;
        const steps = [
            ['welcome', t('Welcome to Giftwise'), t('Create wish lists for every occasion and share them with your friends: they agree on who gives what, without spoiling the surprise.')],
            ['lists', t('1 · Make your wish lists'), t('Birthday, Christmas, wedding, graduation… Set the date of the event and choose whether to show the list to all your followers or only to some.')],
            ['wishes', t('2 · Add your wishes'), t('Paste a link from any online shop (or share it from the app): Giftwise fills in name, picture and price for you. Mark the ones you care about most.')],
            ['friends', t('3 · Follow your friends'), t('Search for your friends by username or share your invite link: you see the lists of the people you follow, and your followers see yours.')],
            ['claim', t('4 · Claim a gift in secret'), t('On a friend’s list, mark a wish as “Thinking about it” or “I’ll get it”: the other friends see it right away, so nobody buys the same present twice.')],
            ['chat', t('5 · Coordinate in the secret chat'), t('Every list and every wish has a chat visible only to friends: agree on who gets what or split the cost of a group gift. The surprise is 100% safe!')],
            ['donate', t('Support the project'), t('Giftwise is a free and open-source project. If you find it useful, consider making a small donation in the Profile to help cover server and maintenance costs.')],
        ];
        let i = Math.min(Math.max(0, start || 0), steps.length - 1);
        const stage = h('div.tour-stage');
        // Language choice, available on every slide (the page reloads in the new language and resumes here)
        const langSeg = h('div.seg.tour-lang', {
            role: 'group',
            'aria-label': t('Language')
        }, I18N.langs.map(([k, label]) =>
            h('button' + (k === I18N.lang ? '.sel' : ''), {
                type: 'button', lang: k, 'aria-pressed': String(k === I18N.lang), onclick: () => {
                    if (k === I18N.lang) return;
                    try {
                        sessionStorage.setItem('gw.tourResume', String(i));
                    } catch (e) { /* no storage */
                    }
                    I18N.set(k);
                }
            }, icon('globe'), label)));
        const dots = h('div.tour-dots');
        const next = btn('', {onclick: () => go(i + 1)});
        const back = btn(t('Back'), {kind: 'text', onclick: () => go(i - 1)});
        const skip = btn(t('Skip'), {kind: 'text', onclick: () => finish()});
        const box = h('div.tour', {
            role: 'dialog',
            'aria-modal': 'true',
            'aria-label': t('App tutorial')
        }, h('div.tour-top', langSeg, skip), stage, dots, h('div.tour-actions', back, next));
        const ovl = h('div.tour-ovl', box);

        function paintT() {
            const [kind, title, text] = steps[i];
            stage.replaceChildren(h('div.tour-slide', illo(kind), h('h2', title), h('p', text)));
            dots.replaceChildren(...steps.map((_, k) => h('button' + (k === i ? '.on' : ''), {
                type: 'button',
                'aria-label': String(k + 1),
                onclick: () => go(k)
            })));
            back.style.visibility = i ? 'visible' : 'hidden';
            skip.style.visibility = i < steps.length - 1 ? 'visible' : 'hidden';
            next.querySelector('span') ? next.querySelector('span').textContent = (i === steps.length - 1 ? t('Get started') : t('Next')) : next.appendChild(h('span', i === steps.length - 1 ? t('Get started') : t('Next')));
        }

        function go(k) {
            if (k >= steps.length) return finish();
            i = Math.max(0, k);
            paintT();
        }

        function finish() {
            document.removeEventListener('keydown', key);
            ovl.classList.remove('open');
            setTimeout(() => ovl.remove(), 200);
            if (S.user) store.set('tutorial.' + S.user.id, MAJOR);
            if (onDone) onDone();
        }

        const key = e => {
            if (e.key === 'ArrowRight') go(i + 1); else if (e.key === 'ArrowLeft') go(i - 1); else if (e.key === 'Escape') finish();
        };
        document.addEventListener('keydown', key);
        let x0 = null;
        stage.addEventListener('pointerdown', e => {
            x0 = e.clientX;
        });
        stage.addEventListener('pointerup', e => {
            if (x0 === null) return;
            const dx = e.clientX - x0;
            x0 = null;
            if (Math.abs(dx) > 50) go(i + (dx < 0 ? 1 : -1));
        });
        document.body.appendChild(ovl);
        paintT();
        requestAnimationFrame(() => ovl.classList.add('open'));
    }

    // ===================================================== AUTH

    function features(cls) {
        return h('ul.features.' + cls,
            h('li', tile('icon', 'list', 'sm'), t('Wish lists for every occasion, with links, pictures and prices')),
            h('li', tile('icon', 'group', 'sm'), t('Follow your friends with their username and see their lists')),
            h('li', tile('icon', 'visibilityOff', 'sm'), t('They agree on who gives what: you never see it')),
            h('li', tile('icon', 'gift', 'sm'), t('No more duplicate or unwanted presents')));
    }

    function pendingInvite() {
        return (location.hash.match(/^#\/invite\/([A-Za-z0-9]+)/) || [])[1] || '';
    }

    function renderAuth(mode) {
        const pending = pendingInvite();
        mode = mode || (pending ? 'register' : store.get('authMode', 'login'));
        const err = h('div.err-box', {hidden: true});
        const showErr = m => {
            UI.clear(err);
            err.append(icon('error'), h('span', m));
            err.hidden = false;
        };
        const email = input({type: 'email', autocomplete: 'email', inputmode: 'email', maxlength: 190});
        const pw = input({type: 'password', autocomplete: mode === 'login' ? 'current-password' : 'new-password'});
        const name = input({autocomplete: 'name', maxlength: 80});
        const uname = input({
            autocomplete: 'username',
            maxlength: 20,
            autocapitalize: 'none',
            spellcheck: 'false',
            placeholder: ' ',
            class: 'input'
        });
        const unameHint = h('p.hint');
        let unameTouched = false;
        const slug = v => v.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, '.').replace(/^\.+|\.+$/g, '').slice(0, 20);
        let checkTimer = null;

        function checkUsername() {
            clearTimeout(checkTimer);
            const v = uname.value.trim().replace(/^@/, '').toLowerCase();
            if (!v) {
                unameHint.textContent = t('Public: your friends find you with it. You can change it later.');
                unameHint.className = 'hint';
                return;
            }
            checkTimer = setTimeout(async () => {
                try {
                    const d = await Api.get('username.check&u=' + encodeURIComponent(v), {silentAuth: true});
                    // The username suggested from the name is taken: propose a free one straight away
                    if (!unameTouched && d.valid && !d.available && d.suggestion) {
                        uname.value = d.suggestion;
                        d.username = d.suggestion;
                        d.available = true;
                    }
                    unameHint.textContent = !d.valid ? t('3–20 letters, digits, dots and underscores') : d.available ? t('@{u} is available', {u: d.username}) : t('@{u} is already taken', {u: d.username});
                    unameHint.className = 'hint ' + (d.valid && d.available ? 'ok' : 'bad');
                } catch (e) { /* offline */
                }
            }, 300);
        }

        name.addEventListener('input', () => {
            if (!unameTouched) {
                uname.value = slug(name.value);
                checkUsername();
            }
        });
        uname.addEventListener('input', () => {
            unameTouched = true;
            checkUsername();
        });
        checkUsername();
        const privacy = h('input', {type: 'checkbox'});
        const gslot = h('div.google-slot');
        const invited = h('div');
        if (pending) Api.get('invite.info&code=' + pending, {silentAuth: true}).then(d => invited.replaceChildren(banner('info', 'gift', t('{name} invited you!', {name: d.name}), t('Create an account (or sign in): you and {name} will follow each other straight away.', {name: d.name})))).catch(() => {
        });
        const submit = btn(mode === 'login' ? t('Sign in') : t('Create account'), {type: 'submit', cls: 'btn-block'});
        const form = h('form', {
                novalidate: true, onsubmit: async e => {
                    e.preventDefault();
                    err.hidden = true;
                    try {
                        const d = await busy(submit, () => mode === 'login'
                            ? Api.post('auth.login', {
                                email: email.value,
                                password: pw.value,
                                inviteCode: pending
                            }, {silentAuth: true})
                            : Api.post('auth.register', {
                                name: name.value,
                                username: uname.value,
                                email: email.value,
                                password: pw.value,
                                inviteCode: pending,
                                acceptPrivacy: privacy.checked
                            }, {silentAuth: true}));
                        S.user = d.user;
                        afterLogin(pending);
                    } catch (ex) {
                        showErr(ex.message);
                    }
                }
            },
            mode === 'register' ? field(t('Name'), name) : null,
            mode === 'register' ? h('div.field.uname-field', h('span.at', '@'), uname, h('label', {for: (uname.id = 'f-uname')}, t('Username')), unameHint) : null,
            field('Email', email),
            field(mode === 'login' ? 'Password' : t('Password (min. 8 characters)'), pw),
            mode === 'register' ? h('label.check', privacy, h('span', t('I have read and accept the '), h('a', {
                href: 'privacy.html',
                target: '_blank',
                rel: 'noopener'
            }, t('privacy policy')))) : null,
            err, h('div.spacer'), submit,
            mode === 'login' ? h('div.center', {style: {marginTop: '6px'}}, btn(t('Forgot your password?'), {
                kind: 'text', onclick: () => {
                    const s = sheet({
                        title: t('Forgot password'),
                        body: [h('p.sheet-text', t('Write to us through the contact form, from the email address of your account: we will help you get back in.')), h('div.btn-row', h('a.btn.btn-tonal', {
                            href: CONTACT_URL,
                            target: '_blank',
                            rel: 'noopener'
                        }, icon('open'), h('span', t('Open the contact form'))))],
                        actions: [btn(t('Got it'), {onclick: () => s.close()})]
                    });
                }
            })) : null);
        root.replaceChildren(h('div.auth',
            h('div.auth-top', h('div.logo', h('img', {
                src: 'assets/icons/icon-192.png',
                alt: ''
            })), h('h1', 'Giftwise'), h('p', t('The right gift, without spoiling the surprise')), features('desk-only')),
            h('div.auth-body', invited,
                seg([['login', t('Sign in')], ['register', t('Create account')]], mode, v => {
                    store.set('authMode', v);
                    renderAuth(v);
                }),
                S.config.googleClientId ? [gslot, h('div.divider-text', t('or with your email'))] : null,
                form, features('mob-only'))));
        document.title = 'Giftwise';
        if (S.config.googleClientId) UI.googleButton(gslot, S.config.googleClientId, cred => googleSignIn(cred, pending), mode === 'login' ? 'continue_with' : 'signup_with');
    }

    async function googleSignIn(credential, pending) {
        try {
            const d = await Api.post('auth.google', {credential, inviteCode: pending || ''}, {silentAuth: true});
            S.user = d.user;
            afterLogin(pending);
        } catch (e) {
            if (e.code !== 'needs_signup') {
                toast(e.message, 'error');
                return;
            }
            const privacy = h('input', {type: 'checkbox'});
            const ok = btn(t('Create account'), {
                icon: 'check', onclick: async () => {
                    if (!privacy.checked) {
                        toast(t('To continue, accept the privacy policy.'), 'error');
                        return;
                    }
                    const d = await busy(ok, () => Api.post('auth.google', {
                        credential,
                        acceptPrivacy: true,
                        inviteCode: pending || ''
                    }, {silentAuth: true}));
                    s.close();
                    S.user = d.user;
                    setTimeout(() => afterLogin(pending), 150);
                }
            });
            const s = sheet({
                title: t('Create your account'),
                body: [h('div.card', {style: {marginBottom: '8px'}}, listRow({
                    lead: avatar(e.data.name || e.data.email),
                    title: e.data.name || t('New account'),
                    sub: e.data.email
                })),
                    h('label.check', privacy, h('span', t('I have read and accept the '), h('a', {
                        href: 'privacy.html',
                        target: '_blank',
                        rel: 'noopener'
                    }, t('privacy policy'))))],
                actions: [cancelBtn(), ok],
            });
        }
    }

    function afterLogin(pending) {
        if (pending) location.hash = '#/friends'; // the invite has been applied by the server
        start();
    }

    function fatal(title, text, action) {
        root.replaceChildren(h('div.auth', h('div.auth-top', h('div.logo', h('img', {
                src: 'assets/icons/icon-192.png',
                alt: ''
            })), h('h1', 'Giftwise'), h('p', title)),
            h('div.auth-body.center', h('p', text), action || btn(t('Try again'), {icon: 'swap', onclick: start}))));
    }

    // ===================================================== shared links (Web Share Target)

    let sharedPayload = null;
    (function readShare() {
        const q = new URLSearchParams(location.search);
        if (!q.has('share')) return;
        const text = [q.get('title'), q.get('text'), q.get('url')].filter(Boolean).join(' ');
        const url = extractUrl(q.get('url') || '') || extractUrl(text);
        const title = (q.get('title') || '').replace(url, '').trim() || (q.get('text') || '').replace(/https?:\/\/\S+/g, '').trim();
        sharedPayload = {url, title: title.slice(0, 160)};
        history.replaceState(history.state, '', location.pathname + '#/lists');
    })();

    function consumeShare() {
        if (!sharedPayload || !S.loaded) return false;
        const p = sharedPayload;
        sharedPayload = null;
        setTimeout(() => openWishForm({url: p.url, title: p.title}, true), 300);
        return true;
    }

    // ===================================================== sync & start

    async function sync() {
        if (!S.user || !S.loaded || document.visibilityState !== 'visible' || UI.sheetsOpen() || document.querySelector('.tour-ovl')) return;
        try {
            const before = S.signature;
            applyState(await Api.get('state', {silentAuth: true}));
            const r = route();
            if (r.name === 'gift' && r.id) {
                await loadView(r.id, true);
                render(true);
                return;
            }
            if (before !== S.signature) render(true);
        } catch (e) { /* offline: try again later */
        }
    }

    // ===================================================== email check (see auth.resend and auth.verify)

    /** Until the address is confirmed the account can do nothing else: the server refuses every call. */
    function verifyGate() {
        const status = h('p.sheet-text.center', {role: 'status'});
        const check = btn(t('I’ve confirmed it'), {
            cls: 'btn-block', onclick: async () => {
                try {
                    const d = await busy(check, () => Api.get('auth.me', {silentAuth: true}));
                    S.user = d.user;
                    if (S.user && !S.user.mustVerify) return start();
                    status.textContent = t('Not confirmed yet: open the link in the email.');
                } catch (e) {
                    status.textContent = e.message;
                }
            }
        });
        const resend = btn(t('Send the link again'), {
            kind: 'tonal', cls: 'btn-block', onclick: async () => {
                try {
                    const d = await busy(resend, () => Api.post('auth.resend', {}, {silentAuth: true}));
                    if (d.user && !d.user.mustVerify) return start();
                    status.textContent = t('New link sent to {email}. It may take a minute: check spam too.', {email: S.user.email});
                } catch (e) {
                    status.textContent = e.message;
                }
            }
        });
        const out = btn(t('Sign out'), {
            kind: 'text', cls: 'btn-block', onclick: async () => {
                try {
                    await Api.post('auth.logout', {}, {silentAuth: true});
                } catch (e) { /* the cookie expires by itself */
                }
                S.user = null;
                S.loaded = false;
                render();
            }
        });
        root.replaceChildren(h('div.auth', h('div.auth-top', h('div.logo', h('img', {src: 'assets/icons/icon-192.png', alt: ''})), h('h1', 'Giftwise'), h('p', t('Confirm your email'))),
            h('div.auth-body.center', h('p', t('We sent a link to {email}. Open it to start using your account: until then the account stays locked.', {email: S.user.email})),
                status, h('div.verify-actions', check, resend, out))));
    }

    /** The link of the email: a button confirms it, so that mail scanners opening links change nothing. */
    function confirmEmailLink(token) {
        return new Promise(resolve => {
            const ok = btn(t('Confirm email'), {
                icon: 'check', cls: 'btn-block', onclick: async () => {
                    try {
                        const d = await busy(ok, () => Api.post('auth.verify', {token}, {silentAuth: true}));
                        S.user = d.user;
                        s.close();
                        toast(t('Email confirmed. Let’s go!'));
                    } catch (e) {
                        toast(e.message, 'error');
                    }
                }
            });
            const s = sheet({title: t('Confirm your email'), body: [h('p.sheet-text', t('Tap the button to confirm the address of your Giftwise account.'))], actions: [ok], onclose: resolve});
        });
    }

    async function start() {
        const token = new URLSearchParams(location.search).get('verify');
        if (token) {
            history.replaceState(null, '', location.pathname + location.hash);
            await confirmEmailLink(token);
        }
        try {
            const [me, cfg] = await Promise.all([Api.get('auth.me', {silentAuth: true}), Api.get('auth.config', {silentAuth: true}).catch(() => null)]);
            S.user = me.user;
            if (cfg) S.config = cfg;
            if (S.user && S.user.mustVerify) return verifyGate();
        } catch (e) {
            if (e.code === 'not_installed') return fatal(t('Installation not completed'), t('The database is not configured yet.'), h('a.btn.btn-filled', {href: 'install-db.php'}, t('Open install-db.php')));
            return fatal(t('Could not connect'), e.message);
        }
        if (!S.user) {
            S.loaded = false;
            render();
            return;
        }
        render();
        try {
            applyState(await Api.get('state'));
            S.loaded = true;
            render();
            if (!consumeShare()) maybeShowTutorial();
        } catch (e) {
            if (e.code === 'email_unverified') return verifyGate();
            fatal(t('Could not load the data'), e.message);
        }
    }

    Api.onUnauthenticated(() => {
        S.user = null;
        S.loaded = false;
        UI.closeAllSheets();
        render();
    });
    window.addEventListener('hashchange', () => {
        trackDepth();
        const r = route();
        if (r.name === 'gift' && r.id && S.views[r.id]) loadView(r.id, true).then(() => render(true)).catch(() => {
        });
        if (!UI.sheetsOpen()) render(); else {
            UI.closeAllSheets();
            setTimeout(render, 130);
        }
    });
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') sync();
    });
    setInterval(sync, 30000);
    matchMedia('(prefers-color-scheme: dark)').addEventListener('change', UI.applyTheme);

    UI.applyTheme();
    trackDepth();
    if ('serviceWorker' in navigator) window.addEventListener('load', () => navigator.serviceWorker.register('sw.js').catch(() => {
    }));
    start();
})();
