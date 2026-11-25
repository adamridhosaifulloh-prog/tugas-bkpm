async function fetchWithRetry(url, options) {
    let percobaan = 0;

    while (true) {
        try {
            return await fetch(url, options);
        } catch (error) {
            if (!error || error.name !== 'TypeError' || percobaan >= 1) {
                throw error;
            }
            percobaan += 1;
        }
    }
}
