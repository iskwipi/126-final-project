function switchSearchMode() {
    const params = new URLSearchParams(window.location.search);
    const searchTerm = params.get('q');
    const url = window.location.pathname;
    if(url=='/recipesearch.html'){
        window.location.href = `usersearch.html?q=${encodeURIComponent(searchTerm)}`;
    }else if(url=='/usersearch.html'){
        window.location.href = `recipesearch.html?q=${encodeURIComponent(searchTerm)}`;
    }
}