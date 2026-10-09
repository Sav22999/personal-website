document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.querySelector('.nav-toggle');
    var nav = document.querySelector('nav');
    var links = document.querySelector('.nav-links');

    if (toggle && nav && links) {
        toggle.addEventListener('click', function () {
            nav.classList.toggle('menu-open');
            links.classList.toggle('open');
            document.body.classList.toggle('menu-open');
        });

        links.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                nav.classList.remove('menu-open');
                links.classList.remove('open');
                document.body.classList.remove('menu-open');
            });
        });
    }

    if (nav) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 10) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    }

    var contactForm = document.getElementById('contact-form');
    if (contactForm) {
        var submit = document.getElementById('form-submit');
        var altchaVerified = false;
        var reasonSelect = contactForm.querySelector('#reason');
        var fieldsProjectRequest = document.getElementById('fields-project-request');
        var fieldsExistingProject = document.getElementById('fields-existing-project');
        var fieldsTechnical = document.getElementById('fields-technical');
        var projectSelect = contactForm.querySelector('#project');
        var projectOtherGroup = document.getElementById('project-other-group');
        var projectTypeSelect = contactForm.querySelector('#project_type');
        var projectTypeOtherGroup = document.getElementById('project-type-other-group');

        function updateFieldsForReason(reason) {
            fieldsProjectRequest.hidden = true;
            fieldsExistingProject.hidden = true;
            fieldsTechnical.hidden = true;
            projectOtherGroup.hidden = true;
            projectTypeOtherGroup.hidden = true;

            if (projectSelect) projectSelect.selectedIndex = 0;
            if (projectTypeSelect) projectTypeSelect.selectedIndex = 0;

            if (reason === 'Project request') {
                fieldsProjectRequest.hidden = false;
            } else if (reason === 'Bug report' || reason === 'Feature request') {
                fieldsExistingProject.hidden = false;
                fieldsTechnical.hidden = false;
            }
        }

        reasonSelect.addEventListener('change', function () {
            updateFieldsForReason(this.value);
            checkFormValid();
        });

        if (projectSelect) {
            projectSelect.addEventListener('change', function () {
                projectOtherGroup.hidden = this.value !== 'Other';
                checkFormValid();
            });
        }

        if (projectTypeSelect) {
            projectTypeSelect.addEventListener('change', function () {
                projectTypeOtherGroup.hidden = this.value !== 'Other';
                checkFormValid();
            });
        }

        var params = new URLSearchParams(window.location.search);
        var presetReason = params.get('reason');
        if (presetReason) {
            var opt = reasonSelect.querySelector('option[value="' + presetReason + '"]');
            if (opt) {
                reasonSelect.value = presetReason;
                updateFieldsForReason(presetReason);
            }
        }

        function checkFormValid() {
            var name = contactForm.querySelector('#name').value.trim();
            var email = contactForm.querySelector('#email').value.trim();
            var reason = reasonSelect.value;
            var message = contactForm.querySelector('#message').value.trim();
            var valid = name && email && reason && message && altchaVerified;

            if (reason === 'Project request') {
                var ptype = projectTypeSelect.value;
                valid = valid && ptype;
                if (ptype === 'Other') {
                    var typeOther = contactForm.querySelector('#project_type_other').value.trim();
                    valid = valid && typeOther;
                }
            } else if (reason === 'Bug report' || reason === 'Feature request') {
                var proj = projectSelect.value;
                valid = valid && proj;
                if (proj === 'Other') {
                    var other = contactForm.querySelector('#project_other').value.trim();
                    valid = valid && other;
                }
            }

            submit.disabled = !valid;
        }

        contactForm.querySelectorAll('input, select, textarea').forEach(function (el) {
            el.addEventListener('input', checkFormValid);
            el.addEventListener('change', checkFormValid);
        });

        var altchaWidget = contactForm.querySelector('altcha-widget');
        if (altchaWidget) {
            altchaWidget.addEventListener('statechange', function (e) {
                altchaVerified = (e.detail && e.detail.state === 'verified');
                checkFormValid();
            });
        }

        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var status = document.getElementById('form-status');
            var altchaInput = contactForm.querySelector('input[name="altcha"]');
            var altchaValue = altchaInput ? altchaInput.value : '';

            if (!altchaValue) {
                status.textContent = 'Please complete the verification.';
                status.className = 'form-status error';
                status.hidden = false;
                return;
            }

            submit.disabled = true;
            submit.classList.add('loading');
            status.hidden = true;

            var reason = reasonSelect.value;
            var projectVal = '';
            if (reason === 'Project request') {
                projectVal = projectTypeSelect.value === 'Other'
                    ? contactForm.querySelector('#project_type_other').value
                    : projectTypeSelect.value;
            } else if (reason === 'Bug report' || reason === 'Feature request') {
                projectVal = projectSelect.value === 'Other'
                    ? contactForm.querySelector('#project_other').value
                    : projectSelect.value;
            }

            var data = {
                name: contactForm.querySelector('#name').value,
                email: contactForm.querySelector('#email').value,
                reason: reason,
                message: contactForm.querySelector('#message').value,
                project: projectVal,
                project_version: !fieldsTechnical.hidden ? (contactForm.querySelector('#project_version').value || '') : '',
                os: !fieldsTechnical.hidden ? (contactForm.querySelector('#os').value || '') : '',
                browser: !fieldsTechnical.hidden ? (contactForm.querySelector('#browser').value || '') : '',
                language: contactForm.querySelector('#language').value,
                company: contactForm.querySelector('#company') ? contactForm.querySelector('#company').value : '',
                ts: contactForm.querySelector('#ts') ? contactForm.querySelector('#ts').value : '',
                altcha: altchaValue,
            };

            fetch('/contact-me/send.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data),
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (result) {
                    status.textContent = result.message;
                    status.className = 'form-status ' + (result.success ? 'success' : 'error');
                    status.hidden = false;
                    if (result.success) {
                        contactForm.reset();
                        altchaVerified = false;
                        if (altchaWidget) altchaWidget.reset();
                        submit.disabled = true;
                        fieldsProjectRequest.hidden = true;
                        fieldsExistingProject.hidden = true;
                        fieldsTechnical.hidden = true;
                        projectOtherGroup.hidden = true;
                        projectTypeOtherGroup.hidden = true;
                    }
                })
                .catch(function () {
                    status.textContent = 'An error occurred. Please try again.';
                    status.className = 'form-status error';
                    status.hidden = false;
                })
                .finally(function () {
                    submit.classList.remove('loading');
                    checkFormValid();
                });
        });
    }

    var bookCover = document.getElementById('book-cover');
    var bookOverlay = document.getElementById('book-overlay');
    if (bookCover && bookOverlay) {
        bookCover.addEventListener('click', function () {
            bookOverlay.classList.add('active');
        });
        bookOverlay.addEventListener('click', function () {
            bookOverlay.classList.remove('active');
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') bookOverlay.classList.remove('active');
        });
    }

    document.querySelectorAll('.screenshot-grid').forEach(function (grid) {
        var scroller = document.createElement('div');
        scroller.className = 'screenshot-scroller';
        grid.parentNode.insertBefore(scroller, grid);
        scroller.appendChild(grid);

        function createScrollButton(direction) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'screenshot-scroll screenshot-scroll-' + direction;
            button.setAttribute('aria-label', direction === 'prev' ? 'Scroll screenshots left' : 'Scroll screenshots right');
            button.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" ' +
                'stroke-linecap="round" stroke-linejoin="round"><polyline points="' +
                (direction === 'prev' ? '15 18 9 12 15 6' : '9 18 15 12 9 6') + '"/></svg>';
            button.addEventListener('click', function () {
                grid.scrollBy({left: (direction === 'prev' ? -1 : 1) * grid.clientWidth * 0.8, behavior: 'smooth'});
            });
            scroller.appendChild(button);
        }

        function updateScrollHints() {
            var maxScroll = grid.scrollWidth - grid.clientWidth;
            scroller.classList.toggle('can-scroll-left', grid.scrollLeft > 4);
            scroller.classList.toggle('can-scroll-right', grid.scrollLeft < maxScroll - 4);
        }

        createScrollButton('prev');
        createScrollButton('next');
        grid.addEventListener('scroll', updateScrollHints, {passive: true});
        window.addEventListener('resize', updateScrollHints);
        grid.querySelectorAll('img').forEach(function (img) {
            img.addEventListener('load', updateScrollHints);
        });
        updateScrollHints();
    });

    var screenshotOverlay = document.getElementById('screenshot-overlay');
    if (screenshotOverlay) {
        var screenshotOverlayImg = screenshotOverlay.querySelector('img');
        var screenshotPrev = screenshotOverlay.querySelector('.screenshot-prev');
        var screenshotNext = screenshotOverlay.querySelector('.screenshot-next');
        var screenshotImgs = Array.prototype.map.call(document.querySelectorAll('.screenshot'), function (screenshot) {
            return screenshot.querySelector('img');
        });
        var screenshotIndex = 0;
        var touchStartX = null;

        function showScreenshot(index) {
            if (index < 0 || index >= screenshotImgs.length) return;
            screenshotIndex = index;
            screenshotOverlayImg.src = screenshotImgs[index].src;
            screenshotOverlayImg.alt = screenshotImgs[index].alt;
            screenshotPrev.hidden = index === 0;
            screenshotNext.hidden = index === screenshotImgs.length - 1;
        }

        function closeScreenshot() {
            screenshotOverlay.classList.remove('active');
        }

        document.querySelectorAll('.screenshot').forEach(function (screenshot, index) {
            screenshot.addEventListener('click', function () {
                showScreenshot(index);
                screenshotOverlay.classList.add('active');
            });
        });
        screenshotPrev.addEventListener('click', function (e) {
            e.stopPropagation();
            showScreenshot(screenshotIndex - 1);
        });
        screenshotNext.addEventListener('click', function (e) {
            e.stopPropagation();
            showScreenshot(screenshotIndex + 1);
        });
        screenshotOverlay.addEventListener('click', closeScreenshot);
        screenshotOverlay.addEventListener('touchstart', function (e) {
            touchStartX = e.touches[0].clientX;
        }, {passive: true});
        screenshotOverlay.addEventListener('touchend', function (e) {
            if (touchStartX === null) return;
            var deltaX = e.changedTouches[0].clientX - touchStartX;
            touchStartX = null;
            if (Math.abs(deltaX) < 50) return;
            e.preventDefault();
            showScreenshot(screenshotIndex + (deltaX < 0 ? 1 : -1));
        });
        document.addEventListener('keydown', function (e) {
            if (!screenshotOverlay.classList.contains('active')) return;
            if (e.key === 'Escape') closeScreenshot();
            if (e.key === 'ArrowLeft') showScreenshot(screenshotIndex - 1);
            if (e.key === 'ArrowRight') showScreenshot(screenshotIndex + 1);
        });
    }
});
