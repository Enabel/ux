// Run with: node --test tests/js
import { register } from 'node:module';
import { test, beforeEach } from 'node:test';
import assert from 'node:assert/strict';

register('./loader.mjs', import.meta.url);

const { default: ModalController } = await import('../../assets/dist/modal.js');

const ORIGIN = 'https://example.test';
const BACKGROUND = `${ORIGIN}/finance/1`;
const MODAL = `${ORIGIN}/finance/1/budget-line/create?backUrl=%2Ffinance%2F1`;

let env;

function createEnv() {
    const documentListeners = {};
    const windowListeners = {};
    const fetchCalls = [];
    const pushStates = [];
    const location = { href: BACKGROUND, origin: ORIGIN, toString() { return this.href; } };
    const history = {
        state: null,
        replaceState(state, _title, url) {
            this.state = state;
            location.href = new URL(url, location.href).toString();
        },
        pushState(state, _title, url) {
            pushStates.push(url);
            this.state = state;
            location.href = new URL(url, location.href).toString();
        },
    };

    const add = (listeners) => (type, listener) => {
        (listeners[type] ??= new Set()).add(listener);
    };
    const remove = (listeners) => (type, listener) => {
        listeners[type]?.delete(listener);
    };
    const dispatch = (listeners, type, event = {}) => {
        for (const listener of [...(listeners[type] ?? [])]) {
            listener(event);
        }
    };

    const classes = new Set();
    const element = {
        classList: { contains: (name) => classes.has(name) },
        matches: () => false,
        dataset: {},
        innerHTML: '',
        dispatchEvent() {},
    };

    const hideCalls = [];
    const modal = {
        show() {
            classes.add('show');
        },
        hide() {
            hideCalls.push(true);
            dispatch(documentListeners, 'hide.bs.modal', { target: element });
            classes.delete('show');
        },
    };

    globalThis.__modalStub = { getOrCreateInstance: () => modal };
    globalThis.window = {
        history,
        location,
        addEventListener: add(windowListeners),
        removeEventListener: remove(windowListeners),
    };
    globalThis.document = {
        location,
        addEventListener: add(documentListeners),
        removeEventListener: remove(documentListeners),
    };
    globalThis.FormData = class FakeFormData {
        constructor(form) {
            this.form = form;
        }

        entries() {
            return [][Symbol.iterator]();
        }
    };
    globalThis.fetch = (url, options) => {
        fetchCalls.push({ url, method: options.method });

        return Promise.resolve({
            headers: { has: () => false, get: () => null },
            text: () => Promise.resolve('<form></form>'),
        });
    };

    return {
        location,
        history,
        element,
        modal,
        fetchCalls,
        pushStates,
        hideCalls,
        documentListeners,
        windowListeners,
        dispatchDocument: (type, event) => dispatch(documentListeners, type, event),
        dispatchWindow: (type, event) => dispatch(windowListeners, type, event),
    };
}

function createController() {
    const controller = new ModalController();
    controller.element = env.element;
    controller.initialize();
    controller.connect();

    return controller;
}

// A form as the browser exposes it: without an action attribute, `form.action` is the document URL.
function createForm(actionAttribute) {
    return {
        matches: (selector) => selector === 'form[data-target="modal"]',
        getAttribute: (name) => (name === 'action' ? (actionAttribute ?? null) : null),
        get action() {
            return new URL(actionAttribute ?? env.location.href, env.location.href).toString();
        },
        method: 'post',
    };
}

function click(href) {
    env.dispatchDocument('click', {
        target: { closest: () => ({ href }) },
        preventDefault() {},
    });
}

function submit(form) {
    env.dispatchDocument('submit', { target: form, preventDefault() {} });
}

const settle = () => new Promise((resolve) => setImmediate(resolve));

beforeEach(() => {
    env = createEnv();
});

test('a form without action posts to the modal URL after a history back', async () => {
    createController();

    click(MODAL);
    await settle();
    assert.equal(env.location.href, MODAL);

    // Browser back: the document URL returns to the background page, the modal stays displayed.
    env.location.href = BACKGROUND;

    submit(createForm());
    await settle();

    const post = env.fetchCalls.at(-1);
    const url = new URL(post.url);
    assert.equal(post.method, 'POST');
    assert.equal(url.pathname, '/finance/1/budget-line/create');
    assert.equal(url.searchParams.get('backUrl'), '/finance/1');
    assert.equal(url.searchParams.get('_enabel_ux_modal'), '1');
});

test('a form without action posts to the modal URL without history navigation', async () => {
    createController();

    click(MODAL);
    await settle();
    submit(createForm());
    await settle();

    const url = new URL(env.fetchCalls.at(-1).url);
    assert.equal(url.pathname, '/finance/1/budget-line/create');
});

test('a form with an explicit action uses it', async () => {
    createController();

    click(MODAL);
    await settle();
    env.location.href = BACKGROUND;

    submit(createForm('/other/path'));
    await settle();

    assert.equal(new URL(env.fetchCalls.at(-1).url).pathname, '/other/path');
});

test('a directly linked modal posts action-less forms to the document URL', async () => {
    env.location.href = MODAL;
    env.element.matches = (selector) => selector === '[data-modal-background-uri]';
    env.element.dataset.modalBackgroundUri = BACKGROUND;
    createController();

    env.location.href = BACKGROUND;
    submit(createForm());
    await settle();

    assert.equal(new URL(env.fetchCalls.at(-1).url).pathname, '/finance/1/budget-line/create');
});

test('popstate with the modal open hides it without pushing a history entry', async () => {
    createController();

    click(MODAL);
    await settle();
    assert.equal(env.element.classList.contains('show'), true);
    const pushesBefore = env.pushStates.length;

    env.history.replaceState({ page: BACKGROUND }, '', BACKGROUND);
    env.dispatchWindow('popstate', {});

    assert.equal(env.hideCalls.length, 1);
    assert.equal(env.element.classList.contains('show'), false);
    assert.equal(env.pushStates.length, pushesBefore);
    assert.equal(env.location.href, BACKGROUND);
});

test('popstate that stays on a modal page (fragment or back to another modal page) keeps it open', async () => {
    createController();

    click(MODAL);
    await settle();

    env.location.href = `${MODAL}#section`;
    env.dispatchWindow('popstate', {});
    env.location.href = `${ORIGIN}/finance/1/other-modal`;
    env.dispatchWindow('popstate', {});

    assert.equal(env.hideCalls.length, 0);
    assert.equal(env.element.classList.contains('show'), true);
});

test('popstate back to the background page with a fragment closes the modal', async () => {
    createController();

    click(MODAL);
    await settle();
    env.location.href = `${BACKGROUND}#top`;
    env.dispatchWindow('popstate', {});

    assert.equal(env.hideCalls.length, 1);
});

test('a normal hide still pushes the background page', async () => {
    createController();

    click(MODAL);
    await settle();
    const pushesBefore = env.pushStates.length;

    env.modal.hide();

    assert.equal(env.pushStates.length, pushesBefore + 1);
    assert.equal(env.pushStates.at(-1), BACKGROUND);
});

test('a hide after a popstate close pushes the background page again', async () => {
    createController();

    click(MODAL);
    await settle();
    env.location.href = BACKGROUND;
    env.dispatchWindow('popstate', {});
    const pushesBefore = env.pushStates.length;

    click(MODAL);
    await settle();
    env.modal.hide();

    // One push for the reload of the modal, one for the normal close: the flag is not stuck.
    assert.equal(env.pushStates.length, pushesBefore + 2);
});

test('popstate with the modal closed is a no-op', () => {
    createController();

    env.dispatchWindow('popstate', {});

    assert.equal(env.hideCalls.length, 0);
    assert.equal(env.pushStates.length, 0);
});

test('disconnect removes the popstate listener', () => {
    const controller = createController();
    assert.equal(env.windowListeners.popstate.size, 1);

    controller.disconnect();

    assert.equal(env.windowListeners.popstate.size, 0);
});
