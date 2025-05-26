function switchSearchMode() {
    const params = new URLSearchParams(window.location.search);
    const searchTerm = params.get('q');
    const url = window.location.pathname;
    if(url.includes('recipesearch.php')){
        window.location.href = `usersearch.php?q=${encodeURIComponent(searchTerm)}`;
    }else if(url.includes('usersearch.php')){
        window.location.href = `recipesearch.php?q=${encodeURIComponent(searchTerm)}`;
    }
}