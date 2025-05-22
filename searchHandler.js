function handleSearch(event) {
    if (event.key === 'Enter') {
        const query = document.getElementById('searchInput').value.trim();
        if (query) {
            const url = window.location.pathname;
            if(url.includes('usersearch.html')){
                window.location.href = `usersearch.html?q=${encodeURIComponent(query)}`;
            }else{
                window.location.href = `recipesearch.html?q=${encodeURIComponent(query)}`;
            }
        }
    }
}