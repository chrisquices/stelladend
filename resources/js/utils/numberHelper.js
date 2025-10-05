// Format number with comma separators (no dollar sign)
export function formatNumberWithSeparators(number, decimals = 2) {
    if (number === null || number === undefined || isNaN(number)) {
        return (0).toFixed(decimals);
    }

    return Number(number)
        .toFixed(decimals)
        .replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Format number to dollar currency
export function formatNumberToDollar(number, maxSignificantDecimal = 2, includePlusSign = false) {
    if (number === null || number === undefined || isNaN(number)) {
        return '$0.00';
    }

    const numeric = Number(number);
    const isNegative = numeric < 0;
    const absNumber = Math.abs(numeric);

    const formatted = absNumber
        .toFixed(maxSignificantDecimal)
        .replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    const prefix = isNegative
        ? '-$'
        : includePlusSign && numeric > 0
            ? '+$'
            : '$';

    return `${prefix}${formatted}`;
}

// Format number to percentage string
export function formatNumberToPercentage(number, maxSignificantDecimal = 2, includePlusSign = false) {
    const safeNumber = number || 0;
    const formatted = safeNumber.toFixed(maxSignificantDecimal);
    const prefix = includePlusSign && safeNumber > 0 ? '+' : '';
    return `${prefix}${formatted}%`;
}

//
export function fx(val, dp = 2) {
    return Number(Number(val).toFixed(dp));
}
