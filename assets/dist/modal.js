import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap/js/dist/modal';

export default class extends Controller {
    constructor() {
        super(...arguments);
        this.modal = null;
    }

    connect() {
        document.addEventListener('click', this.onClick);
        document.addEventListener('submit', this.onSubmit);
        document.addEventListener('hide.bs.modal', this.onHideBsModal);

        window.history.replaceState({ page: document.location.toString() }, '', document.location.toString());

        if (this.element.matches('[data-background]')) {
            window.history.replaceState({ page: this.element.dataset.background }, '', document.location.toString());
            this.modal = new Modal(this.element.querySelector('.modal'));
            this.modal.show();
        }
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
        load(url, method, body);
    }

    onHideBsModal() {
        window.history.pushState({ page: window.history.state.page }, '', window.history.state.page);
    }

    load(url, method, body) {
        method = method ? method.toUppercase() : 'GET';

        const xhrUrl = this.buildUrl(url, method, body);
        const xhrOptions = this.buildOptions(method, body);

        fetch(xhrUrl, xhrOptions).then((response) => {
            if (response.headers.has('X-Modal-Redirect')) {
                window.location = response.headers.get('X-Modal-Redirect');

                return;
            }

            response.text().then((html) => {
                if (this.modal instanceof Modal) {
                    this.modal.dispose();
                }

                this.element.innerHTML = html;
                this.modal = new Modal(this.element.querySelector('.modal'));
                this.modal.show();
            });

            window.history.pushState(window.history.state, '', url);
        });
    }

    buildUrl(url, method, body) {
        url = this.addSearchParams(url, new URLSearchParams('_modal'));

        if (method !== 'GET') {
            return url;
        }

        if (!body) {
            return url;
        }

        url = this.addSearchParams(url, new URLSearchParams(body));

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
        url = new URL(url, `${document.location.protocol}//${document.location.hostname}`);

        for (const [key, value] of Object.entries(params)) {
            url.searchParams.set(key, value);
        }

        return url.toString();
    }
}
