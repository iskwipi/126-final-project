const params = new URLSearchParams(window.location.search);
const searchTerm = params.get('q');
const url = window.location.pathname;
var sortOrder = "id";

if(searchTerm){
    document.getElementById('query-title').textContent = `Search results for: "${searchTerm}"`;
    if(url.includes('recipesearch.php')){
        document.getElementById('display-mode').addEventListener('change', function () {
            sortOrder = this.value;
            filterRecipes(searchTerm.toLowerCase());
        });
        filterRecipes(searchTerm.toLowerCase());
    }else if(url.includes('usersearch.php')){
        filterUsers(searchTerm.toLowerCase());
    }
}

async function getRecipes(){
    const response = await fetch("getRecipes.php");
    return response.json();
}

async function filterRecipes(searchTerm){
    const data = await getRecipes();
    var recipes = data;
    recipes = recipes.filter(recipe => recipe.recipeTitle.toLowerCase().includes(searchTerm));
    if(sortOrder=="id"){
        recipes.sort((a, b) => (parseInt(b.recipeID) - parseInt(a.recipeID)))
    }else if(sortOrder=="rating"){
        recipes.sort((a, b) => (parseInt(b.avgRating) - parseInt(a.avgRating)))
    }
    var content = "";
    for(let recipe of recipes){
        content += `
            <div class="recipe-posts" data-recipe-id="${recipe.recipeID}"> 
                <div class="image">
                    <div class="bookmark">
                        <button type="button">
                            <i class="fa-regular fa-bookmark"></i>
                            <!-- <i class="fa-solid fa-bookmark solid-icon"></i> -->
                        </button>
                    </div>
                    <div id="user">
                        <a href="profile.php?id=${recipe.userID}">${recipe.username}</a>
                    </div>
                    <img src="${recipe.pictureLink}" alt="recipe image">
                </div>
                <div id="recipe-name"> 
                    <p>${recipe.recipeTitle}</p>
                </div>
                <div id="tags">
                    <p>#${recipe.tags.join(" #")}</p>
                </div>
                <div class="ratings">
                    <button type="button"> 
                        <i class="fa-regular fa-star"></i>
                    </button>
                    <button type="button"> 
                        <i class="fa-regular fa-star"></i>
                    </button>
                    <button type="button"> 
                        <i class="fa-regular fa-star"></i>
                    </button>
                    <button type="button"> 
                        <i class="fa-regular fa-star"></i>
                    </button>
                    <button type="button"> 
                        <i class="fa-regular fa-star"></i>
                    </button> 
                    <p>${recipe.avgRating != null ? recipe.avgRating : 0} stars (${recipe.countRating != null ? recipe.countRating : 0} ratings)</p>
                </div>
            </div>
        `;
    }
    const posts = document.getElementById('search-posts');
    posts.innerHTML = content;
    const recipePosts = document.querySelectorAll('.recipe-posts');
    recipePosts.forEach(recipePost => {
        console.log("link");
        recipePost.addEventListener('click', () => {
        const recipeID = recipePost.getAttribute('data-recipe-id');
        window.location.href = `displaypost.php?id=${recipeID}`;
        });
    });
}

async function getUsers(){
    const response = await fetch("getUsers.php");
    return response.json();
}

async function filterUsers(searchTerm){
    const data = await getUsers();
    var users = data;
    users = users.filter(user => user.username.toLowerCase().includes(searchTerm));
    const minute = 1000 * 60;
    const hour = minute * 60;
    const day = hour * 24;
    const year = day * 365;
    const currentYear = Date.now();
    var content = "";
    for(let user of users){
        const birthYear = new Date(user.dateOfBirth);
        content += `
            <div class="profile-container" data-profile-id="${user.userID}"> 
                <div class="user-details">
                    <a><strong>${user.username}</strong></a>
                    <div class="user-info">
                        <p>${Math.round((currentYear - birthYear)/ year)}, ${user.occupation}</p>
                        <p>${user.followerCount != null ? user.followerCount + ' follower' + (user.followerCount > 1 ? 's' : '') : '0 followers'}</p>
                    </div>
                    <div class="user-avg-rating">
                        <p>Average recipe rating: ${user.avgRating != null ? user.avgRating : 0}</p>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                    </div>
                </div>
                <button type="button" class="follow-button">Follow</button>
            </div>
        `;
    }
    const posts = document.getElementById('search-feed-content');
    posts.innerHTML = content;
    const profiles = document.querySelectorAll('.profile-container');
    profiles.forEach(profile => {
        console.log("link");
        profile.addEventListener('click', () => {
        const profileID = profile.getAttribute('data-profile-id');
        window.location.href = `profile.php?id=${profileID}`;
        });
    });
}