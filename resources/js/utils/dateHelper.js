export function ymdToReadableDate(dateStr) {
    if (!dateStr) return "";

    const [year, month, day] = dateStr.split("-");
    const date = new Date(Number(year), Number(month) - 1, Number(day));

    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
}

export function todayInYmd() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

export function isMarketOpen() {
    const now = new Date();

    // Convert to New York time (ET)
    const nyTime = new Date(
        now.toLocaleString('en-US', { timeZone: 'America/New_York' })
    );

    const day = nyTime.getDay(); // 0 = Sunday, 6 = Saturday
    const hours = nyTime.getHours();
    const minutes = nyTime.getMinutes();

    // Closed on weekends
    if (day === 0 || day === 6) return false;

    // Market open from 9:30 to 16:00 ET
    const timeInMinutes = hours * 60 + minutes;
    return timeInMinutes >= 570 && timeInMinutes < 960; // 570 = 9:30 AM, 960 = 4:00 PM
}
