// Minimal module loader hooks for running assets/dist/modal.js under `node --test`, without npm dependencies.
//
// The bare imports `@hotwired/stimulus` and `bootstrap` are replaced by tiny stubs. The Bootstrap stub delegates
// to `globalThis.__modalStub.getOrCreateInstance(element)`, which each test provides.

const STUBS = {
    '@hotwired/stimulus': 'export class Controller { constructor() {} }',
    'bootstrap': 'export class Modal { static getOrCreateInstance(element) { return globalThis.__modalStub.getOrCreateInstance(element); } }',
};

export async function resolve(specifier, context, nextResolve) {
    if (Object.hasOwn(STUBS, specifier)) {
        return { url: `stub:${specifier}`, shortCircuit: true };
    }

    return nextResolve(specifier, context);
}

export async function load(url, context, nextLoad) {
    if (url.startsWith('stub:')) {
        return { format: 'module', source: STUBS[url.slice('stub:'.length)], shortCircuit: true };
    }

    // assets/ has no "type": "module" in its package.json, so state the format explicitly.
    if (url.endsWith('/assets/dist/modal.js')) {
        return nextLoad(url, { ...context, format: 'module' });
    }

    return nextLoad(url, context);
}
