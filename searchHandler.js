function handleSearch(event) {
    if (event.key === 'Enter') {
        const query = document.getElementById('searchInput').value.trim();
        if (query) {
            const url = window.location.pathname;
            if(url.includes('usersearch.php')){
                window.location.href = `usersearch.php?q=${encodeURIComponent(query)}`;
            }else{
                window.location.href = `recipesearch.php?q=${encodeURIComponent(query)}`;
            }
        }
    }
}