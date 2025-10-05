export function hexToRgba(hex, opacity = 1) {
    hex = hex.replace(/^#/, '');

    if (hex.length === 3) {
        hex = hex.split('').map(x => x + x).join('');
    }

    let r = parseInt(hex.substring(0, 2), 16);
    let g = parseInt(hex.substring(2, 4), 16);
    let b = parseInt(hex.substring(4, 6), 16);

    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
}

/**
 * Get a CSS variable's value from the document or a specific element.
 * @param {string} variable - CSS variable name (e.g., '--background')
 * @param {HTMLElement} [element=document.documentElement]
 * @returns {string} The resolved CSS value, trimmed
 */
export function useCssVar(variable, element = document.documentElement) {
    return getComputedStyle(element).getPropertyValue(variable).trim();
}

export function hslToHsla(hsl, alpha = 1) {
    const match = hsl.match(/hsl\(([^)]+)\)/);
    if (!match) return hsl;

    return `hsla(${match[1]}, ${alpha})`;
}
