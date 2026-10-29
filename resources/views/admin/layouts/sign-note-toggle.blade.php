{{--
    "No signature required" on/off for every report's print.
    Next to a page's Print button this adds a checkbox; unticked, every print opened from the page
    gets sign_note=0, and the print layouts (printMaster, printMaster2 and the HR salary / bonus
    sheets) leave out their "This is a system generated report, no signature required." line.
    The choice is remembered per page in this browser.
--}}
<script>
(function () {
    var PARAM = 'sign_note';
    var storeKey = 'signNote:' + location.pathname;

    function triggers() {
        return Array.prototype.filter.call(
            document.querySelectorAll('a[target="_blank"], button[formtarget="_blank"], input[formtarget="_blank"], form[target="_blank"] button, form[target="_blank"] input[type="submit"]'),
            function (el) { return el.querySelector('.fa-print'); }
        );
    }

    function remembered() {
        try { return localStorage.getItem(storeKey) !== '0'; } catch (e) { return true; }
    }

    function remember(on) {
        try { on ? localStorage.removeItem(storeKey) : localStorage.setItem(storeKey, '0'); } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', function () {
        var list = triggers();
        // The page's own Print button (with a text label), not a per-row icon.
        var anchor = list.filter(function (el) { return el.textContent.trim() !== ''; })[0];
        if (!anchor) return;

        var wrap = document.createElement('label');
        wrap.className = 'sign-note-toggle mb-0';
        wrap.title = 'Show "This is a system generated report, no signature required." at the bottom of the print';
        wrap.style.cssText = 'display:inline-flex;align-items:center;gap:4px;margin:0 0 0 8px;font-size:12px;font-weight:500;cursor:pointer;white-space:nowrap;vertical-align:middle;';
        wrap.innerHTML = '<input type="checkbox" style="margin:0;cursor:pointer"> No-signature note';
        var box = wrap.querySelector('input');
        box.checked = remembered();
        box.addEventListener('change', function () { remember(box.checked); });

        var after = anchor.parentElement && anchor.parentElement.classList.contains('btn-group') ? anchor.parentElement : anchor;
        after.insertAdjacentElement('afterend', wrap);

        // Applied at click time, so links rebuilt by the page's own scripts are covered too.
        document.addEventListener('click', function (e) {
            var el = e.target.closest && e.target.closest('a, button, input');
            if (!el || list.indexOf(el) === -1) return;
            var on = box.checked;

            if (el.tagName === 'A') {
                var url = new URL(el.href, location.href);
                on ? url.searchParams.delete(PARAM) : url.searchParams.set(PARAM, '0');
                el.href = url.toString();
                return;
            }

            var form = el.form;
            if (!form) return;
            var input = form.querySelector('input[type="hidden"][name="' + PARAM + '"]');
            if (on) {
                if (input) input.remove();
            } else {
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = PARAM;
                    form.appendChild(input);
                }
                input.value = '0';
            }
        }, true);
    });
})();
</script>
