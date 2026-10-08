export function makeQuillDelta(text = 'initial\n') {
    return { ops: [{ insert: text }] };
}

export function makeChecklistItem(overrides = {}) {
    const id = overrides.id || '01TESTITEM';
    const storeUrl = overrides.storeUrl || 'http://localhost/bff/notes/01TESTNOTE/checklist-items';
    const itemUrl = `${storeUrl}/${id}`;

    return {
        id,
        content: 'Buy milk',
        isCompleted: false,
        updateUrl: itemUrl,
        deleteUrl: itemUrl,
        position: 0,
        ...overrides,
    };
}

export function makeJsonResponse(status, payload) {
    return {
        ok: status >= 200 && status < 300,
        status,
        json: async () => payload,
    };
}
