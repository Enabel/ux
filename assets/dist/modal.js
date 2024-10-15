import { Controller } from '@hotwired/stimulus';
import Modal from 'bootstrap/js/dist/modal';

export default class extends Controller {
    constructor() {
        super(...arguments);
        this.onClick = this.onClick.bind(this);
        this.onSubmit = this.onSubmit.bind(this);
        this.onHideBsModal = this.onHideBsModal.bind(this);
    }

    initialize() {
        window.history.replaceState({ page: document.location.toString() }, '', document.location.toString());

        if (!this.element.matches('[data-modal-background-uri]')) {
            return;
        }

        window.history.replaceState({ page: this.element.dataset.modalBackgroundUri }, '', document.location.toString());
        Modal.getOrCreateInstance(this.element).show();
    }

    connect() {
        document.addEventListener('click', this.onClick);
        document.addEventListener('submit', this.onSubmit);
        document.addEventListener('hide.bs.modal', this.onHideBsModal);
    }

    disconnect() {
        document.removeEventListener('click', this.onClick);
        document.removeEventListener('submit', this.onSubmit);
        document.removeEventListener('hide.bs.modal', this.onHideBsModal);
    }

    onClick(event) {
        if (!event.target.matches('a[data-target="modal"]')) {
            return;
        }

        event.preventDefault();
        this.load(event.target.href);
    }

    onSubmit(event) {
        if (!event.target.matches('form[data-target="modal"]')) {
            return;
        }

        event.preventDefault();
        const url = event.target.action;
        const { method } = event.target;
        const body = new FormData(event.target);
        this.load(url, method, body);
    }

    onHideBsModal(event) {
        if (event.target !== this.element) {
            return;
        }

        window.history.pushState({ page: window.history.state.page }, '', window.history.state.page);
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

            response.text().then((html) => {
                this.element.innerHTML = html;
                Modal.getOrCreateInstance(this.element).show();
            });

            window.history.pushState(window.history.state, '', url);
        });
    }

    buildUrl(url, method, body) {
        url = this.addSearchParams(url, new URLSearchParams('_modal=1'));

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
