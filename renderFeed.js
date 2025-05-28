var sortOrder = "id";
renderFeed();

document.getElementById('display-mode').addEventListener('change', function () {
    sortOrder = this.value;
    renderFeed();
});

async function renderFeed(){
    const url = window.location.pathname;
    if(url.includes('homepage.php')){
        displayFeatured();
        displayFeed();
    }else if(url.includes('cookbook.php')){
        displaySaved();
    }
}

async function getRecipes(){
    const response = await fetch("getRecipes.php");
    return response.json();
}

async function displayFeatured(){
    const data = await getRecipes();
    const recipes = data;
    var content = `
        <button class="nav-button left-button">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
    `;
    for(let recipe of recipes){
        content += `
            <div class="featured-item" data-recipe-id="${recipe.recipeID}"> 
                <div class="image">
                    <div class="bookmark">
                        <button type="button">
                            <i class="fa-regular fa-bookmark"></i>
                            <!-- <i class="fa-solid fa-bookmark solid-icon"></i> -->
                        </button>
                    </div>
                    <div id="user">
                        <a href="profile.php">${recipe.username}</a>
                    </div>
                    <img src="${recipe.pictureLink}" alt="recipe image">
                </div>
                <div id="recipe-name"> 
                    <p>${recipe.recipeTitle}</p>
                </div>
            </div>
        `;
    }
    content += `
        <button class="nav-button right-button">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    `;
    const posts = document.getElementById('featured-section');
    posts.innerHTML = content;
    const recipePosts = document.querySelectorAll('.featured-item');
    recipePosts.forEach(recipePost => {
        console.log("link");
        recipePost.addEventListener('click', () => {
        const recipeID = recipePost.getAttribute('data-recipe-id');
        window.location.href = `displaypost.php?id=${recipeID}`;
        });
    });
}

async function displayFeed(){
    const data = await getRecipes();
    const recipes = data;
    console.log(recipes);
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
                        <a href="profile.php">${recipe.username}</a>
                    </div>
                    <img src="${recipe.pictureLink}" alt="recipe image">
                </div>
                <div id="recipe-name"> 
                    <p>${recipe.recipeTitle}</p>
                </div>
                <div id="tags">
                    <p>${recipe.tags}</p>
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
                    <p>${recipe.avgRating} stars (${recipe.countRating} ratings)</p>
                </div>
            </div>
        `;
    }
    const posts = document.getElementById('posts-section');
    posts.innerHTML = content;
    console.log("done");
    const recipePosts = document.querySelectorAll('.recipe-posts');
    recipePosts.forEach(recipePost => {
        console.log("link");
        recipePost.addEventListener('click', () => {
        const recipeID = recipePost.getAttribute('data-recipe-id');
        window.location.href = `displaypost.php?id=${recipeID}`;
        });
    });
}


async function getSaves(){
    const response = await fetch("getSaved.php");
    return response.json();
}

async function displaySaved(){
    const data = await getSaves();
    const recipes = data;
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
                        <a href="profile.php">${recipe.username}</a>
                    </div>
                    <img src="${recipe.pictureLink}" alt="recipe image">
                </div>
                <div id="recipe-name"> 
                    <p>${recipe.recipeTitle}</p>
                </div>
                <div id="tags">
                    <p>${recipe.tags}</p>
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
                    <p>${recipe.avgRating} stars (${recipe.countRating} ratings)</p>
                </div>
            </div>
        `;
    }
    const posts = document.getElementById('saved-section');
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