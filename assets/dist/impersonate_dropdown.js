import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'results'];

    static values = {
        searchUrl: String,
        noResultsLabel: { type: String, default: 'No results' },
        exitParameter: { type: String, default: '_switch_user' },
        debounce: { type: Number, default: 250 },
        minLength: { type: Number, default: 2 },
    };

    initialize() {
        this.timer = null;
        this.abortController = null;
    }

    disconnect() {
        if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
        }
        if (this.abortController) {
            this.abortController.abort();
            this.abortController = null;
        }
    }

    search() {
        const query = this.inputTarget.value.trim();

        if (this.timer) {
            clearTimeout(this.timer);
        }

        if (query.length < this.minLengthValue) {
            this.clearResults();
            return;
        }

        this.timer = setTimeout(() => this.fetchResults(query), this.debounceValue);
    }

    fetchResults(query) {
        if (this.abortController) {
            this.abortController.abort();
        }
        this.abortController = new AbortController();

        const url = new URL(this.searchUrlValue, document.location.origin);
        url.searchParams.set('q', query);

        fetch(url.toString(), {
            signal: this.abortController.signal,
            headers: { Accept: 'application/json' },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then((items) => this.renderResults(Array.isArray(items) ? items : []))
            .catch((error) => {
                if (error.name === 'AbortError') {
                    return;
                }
                this.renderError();
            });
    }

    renderResults(items) {
        this.resultsTarget.replaceChildren();

        if (items.length === 0) {
            const empty = document.createElement('li');
            empty.className = 'dropdown-item-text text-muted small';
            empty.textContent = this.noResultsLabelValue;
            this.resultsTarget.appendChild(empty);
            return;
        }

        const fragment = document.createDocumentFragment();
        for (const item of items) {
            fragment.appendChild(this.buildItem(item));
        }
        this.resultsTarget.appendChild(fragment);
    }

    renderError() {
        this.resultsTarget.replaceChildren();
        const error = document.createElement('li');
        error.className = 'dropdown-item-text text-danger small';
        error.textContent = this.noResultsLabelValue;
        this.resultsTarget.appendChild(error);
    }

    clearResults() {
        if (this.resultsTarget) {
            this.resultsTarget.replaceChildren();
        }
    }

    buildItem(item) {
        const li = document.createElement('li');
        const link = document.createElement('a');
        link.className = 'dropdown-item d-flex align-items-center gap-2';

        const url = new URL(document.location.href);
        url.searchParams.set(this.exitParameterValue, item.email ?? '');
        link.href = url.toString();

        if (item.initials) {
            const initials = document.createElement('span');
            initials.className = 'enabel-ux-impersonate-initials';
            initials.textContent = item.initials;
            link.appendChild(initials);
        }

        const label = document.createElement('span');
        label.className = 'flex-grow-1';
        label.textContent = item.displayName ?? item.email ?? '';
        link.appendChild(label);

        if (item.email && item.displayName) {
            const email = document.createElement('small');
            email.className = 'text-muted';
            email.textContent = item.email;
            link.appendChild(email);
        }

        li.appendChild(link);
        return li;
    }
}
