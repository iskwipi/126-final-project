const params = new URLSearchParams(window.location.search);
const searchTerm = params.get('q');
const url = window.location.pathname;
var sortOrder = "id";

if(searchTerm){
    document.getElementById('query-title').textContent = `Search results for: "${searchTerm}"`;
    console.log(searchTerm);
    if(url=='/recipesearch.html'){
        document.getElementById('display-mode').addEventListener('change', function () {
            sortOrder = this.value;
            console.log(sortOrder);
            filterRecipes(searchTerm.toLowerCase());
        });
        filterRecipes(searchTerm.toLowerCase());
    }else if(url=='/usersearch.html'){
        filterUsers(searchTerm.toLowerCase());
    }
}

async function getData(){
    const response = await fetch("data.json");
    return response.json();
}

async function filterRecipes(searchTerm){
    const data = await getData();
    var recipes = [];
    for(let x = 0; x < 5; x++){
        for(let y = 0; y < data.recipes.length; y++){
            recipes[x * data.recipes.length + y] = data.recipes[y];
        }
    }
    recipes = recipes.filter(recipe => recipe.title.toLowerCase().includes(searchTerm));
    const users = data.users;

    for(let i=0; i<recipes.length; i++){
        console.log("calculating...")
        total = 0;
        count = 0;
        for(let rating of recipes[i].ratings){
            total += rating.value;
            count++;
        }
        recipes[i].avgRating = total / count;
    }

    if(sortOrder=="id"){
        console.log('id');
        recipes.sort((a, b) => (parseInt(b.recipeID) - parseInt(a.recipeID)))
    }else if(sortOrder=="rating"){
        console.log('rating');
        recipes.sort((a, b) => (parseInt(b.avgRating) - parseInt(a.avgRating)))
    }

    // console.log(recipes);
    // console.log(users);
    var content = "";
    for(let recipe of recipes){
        // console.log(recipe);
        content += `
            <div class="recipe-posts"> 
                <div class="image">
                    <div class="bookmark">
                        <button type="button">
                            <i class="fa-regular fa-bookmark"></i>
                            <!-- <i class="fa-solid fa-bookmark solid-icon"></i> -->
                        </button>
                    </div>
                    <div id="user">
                        <a href="profile.html">${users[recipe.ownerID - 1].username}</a>
                    </div>
                    <img src="${recipe.picture}" alt="recipe image">
                </div>
                <div id="recipe-name"> 
                    <p>${recipe.title}</p>
                </div>
                <div id="tags">
                    <p>${recipe.tags.map(tag => `#${tag}`).join(' ')}</p>
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
                    <p>(${recipe.ratings.length} ratings)</p>
                </div>
            </div>
        `;
    }
    const posts = document.getElementById('search-posts');
    posts.innerHTML = content;
}

async function filterUsers(searchTerm){
    const data = await getData();
    var users = [];
    for(let x = 0; x < 5; x++){
        for(let y = 0; y < data.users.length; y++){
            users[x * data.users.length + y] = data.users[y];
        }
    }
    users = users.filter(user => user.username.toLowerCase().includes(searchTerm));

    console.log(users);
    var content = "";
    for(let user of users){
        console.log(user);
        content += `
            <div class="profile-container"> 
                <div class="user-details">
                    <a href="profile.html"><strong>${user.username}</strong></a>
                    <div class="user-info">
                        <p>${2025 - parseInt(user.dateOfBirth.slice(0, 4))}, ${user.occupation}</p>
                        <p>${user.followersID.length} follower/s</p>
                    </div>
                    <div class="user-avg-rating">
                        <p>Average recipe rating:</p>
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
}