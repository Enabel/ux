import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

export default class extends Controller {
    constructor() {
        super(...arguments);
        this.onClick = this.onClick.bind(this);
        this.onSubmit = this.onSubmit.bind(this);
        this.onHideBsModal = this.onHideBsModal.bind(this);
        this.onPopState = this.onPopState.bind(this);
        this.currentUrl = null;
        this.closingFromHistory = false;
    }

    initialize() {
        window.history.replaceState({ page: document.location.toString() }, '', document.location.toString());

        if (!this.element.matches('[data-modal-background-uri]')) {
            return;
        }

        window.history.replaceState({ page: this.element.dataset.modalBackgroundUri }, '', document.location.toString());
        this.currentUrl = document.location.toString();
        Modal.getOrCreateInstance(this.element).show();
    }

    connect() {
        document.addEventListener('click', this.onClick);
        document.addEventListener('submit', this.onSubmit);
        document.addEventListener('hide.bs.modal', this.onHideBsModal);
        window.addEventListener('popstate', this.onPopState);
    }

    disconnect() {
        document.removeEventListener('click', this.onClick);
        document.removeEventListener('submit', this.onSubmit);
        document.removeEventListener('hide.bs.modal', this.onHideBsModal);
        window.removeEventListener('popstate', this.onPopState);
    }

    onClick(event) {
        const link = event.target.closest('a[data-target="modal"]');

        if (!link) {
            return;
        }

        event.preventDefault();
        this.load(link.href);
    }

    onSubmit(event) {
        if (!event.target.matches('form[data-target="modal"]')) {
            return;
        }

        event.preventDefault();
        // A form without an action attribute posts to the URL the modal was loaded from: the document URL
        // cannot be trusted for that, as it changes on history navigation while the modal stays displayed.
        const url = event.target.getAttribute('action') ? event.target.action : (this.currentUrl ?? event.target.action);
        const { method } = event.target;
        const body = new FormData(event.target);
        this.load(url, method, body);
    }

    onHideBsModal(event) {
        // Bootstrap aborts the hide when a listener cancels the event: the modal stays displayed.
        if (event.target !== this.element || event.defaultPrevented) {
            return;
        }

        // The modal content is gone: action-less forms of the background page post to the document URL again.
        this.currentUrl = null;

        if (this.closingFromHistory) {
            // The history already moved back to the background page: do not push a new entry.
            this.closingFromHistory = false;

            return;
        }

        window.history.pushState({ page: window.history.state.page }, '', window.history.state.page);
    }

    onPopState() {
        if (!this.element.classList.contains('show')) {
            return;
        }

        // popstate also fires for fragment navigation and for moves between two modal pages: only close
        // when the document is back on the background page.
        const backgroundUrl = window.history.state?.page;

        if (!backgroundUrl || this.withoutHash(backgroundUrl) !== this.withoutHash(document.location.toString())) {
            return;
        }

        this.closingFromHistory = true;

        try {
            Modal.getOrCreateInstance(this.element).hide();
        } finally {
            this.closingFromHistory = false;
        }
    }

    withoutHash(url) {
        const parsed = new URL(url, document.location.origin);
        parsed.hash = '';

        return parsed.toString();
    }

    load(url, method, body) {
        method = method ? method.toUpperCase() : 'GET';

        const xhrUrl = this.buildUrl(url, method, body);
        const xhrOptions = this.buildOptions(method, body);

        fetch(xhrUrl, xhrOptions).then((response) => {
            if (response.headers.has('X-Modal-Redirect')) {
                window.location = response.headers.get('X-Modal-Redirect');

                return;
            }

            if (response.headers.has('X-Modal-Redirect-Self')) {
                return this.load(response.headers.get('X-Modal-Redirect-Self'));
            }

            if (response.headers.has('X-Modal-Callback')) {
                const event = new CustomEvent('modal:callback', {
                    detail: response.headers.get('X-Modal-Callback')
                });

                this.element.dispatchEvent(event);
                Modal.getOrCreateInstance(this.element).close();

                return;
            }

            response.text().then((html) => {
                this.element.innerHTML = html;
                Modal.getOrCreateInstance(this.element).show();
            });

            window.history.pushState(window.history.state, '', url);
            this.currentUrl = url;
        });
    }

    buildUrl(url, method, body) {
        url = this.addSearchParams(url, new URLSearchParams('_enabel_ux_modal=1'));

        if (method !== 'GET') {
            return url;
        }

        if (!body) {
            return url;
        }

        url = this.addSearchParams(url, body);

        return url;
    }

    buildOptions(method, body) {
        const options = { method };

        if (method === 'GET') {
            return options;
        }

        if (!body) {
            return options;
        }

        options.body = body;

        return options;
    }

    addSearchParams(url, params) {
        url = new URL(url, document.location.origin);

        for (const [key, value] of params.entries()) {
            url.searchParams.set(key, value);
        }

        return url.toString();
    }
}
