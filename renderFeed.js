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

async function displayFeatured() {
    try {
        const [recipes, following] = await Promise.all([
            getRecipes(),
            getFollowing()
        ]);

        if (!Array.isArray(following) || following.length === 0) {
            document.getElementById('featured-posts').innerHTML = `
                <div class="no-following">
                    <p>You’re not following anyone yet :(</p>
                </div>`;
            return;
        }

        // filter only recipes from followed users
        const filteredRecipes = recipes.filter(recipe => following.includes(parseInt(recipe.userID)));

        let content = ``;
        for (let recipe of filteredRecipes) {
            content += `
                <div class="featured-item" data-recipe-id="${recipe.recipeID}"> 
                    <div class="image">
                        <div class="bookmark">
                            <button type="button">
                                <i class="fa-regular fa-bookmark"></i>
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
                </div>
            `;
        }

        const posts = document.getElementById('featured-posts');
        posts.innerHTML = content;

        // click post
        document.querySelectorAll('.featured-item').forEach(recipePost => {
            recipePost.addEventListener('click', () => {
                const recipeID = recipePost.getAttribute('data-recipe-id');
                window.location.href = `displaypost.php?id=${recipeID}`;
            });
        });

        // bookmark
        document.querySelectorAll('.featured-item .bookmark button').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const recipeID = btn.closest('.featured-item')?.getAttribute('data-recipe-id');
                saveRecipe(recipeID, btn);
            });
        });

    } catch (error) {
        console.error("error displaying featured posts:", error);
        document.getElementById('featured-posts').innerHTML = `
            <div class="error">
                <p>couldn’t load featured posts</p>
            </div>`;
    }
}

async function getFollowing() {
    const response = await fetch("getFollowing.php");
    return response.json();
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
                        <button type="button"">
                            <i class="fa-regular fa-bookmark "></i>
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
    const posts = document.getElementById('posts-section');
    posts.innerHTML = content;

    const recipePosts = document.querySelectorAll('.recipe-posts');
        recipePosts.forEach(recipePost => {
            recipePost.addEventListener('click', () => {
                const recipeID = recipePost.getAttribute('data-recipe-id');
                window.location.href = `displaypost.php?id=${recipeID}`;
            });
        });

    document.querySelectorAll('.recipe-posts .bookmark button').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const recipeID = btn.closest('.recipe-posts')?.getAttribute('data-recipe-id');
            saveRecipe(recipeID, btn);
        });
    });
}

// // avoid displaying post when user clicks on the bookmark button
// document.querySelectorAll('.bookmark button').forEach(btn => {
//     btn.addEventListener('click', function (e) {
//         e.stopPropagation(); 
//         const recipeID = btn.closest('.recipe-posts, .featured-item')?.getAttribute('data-recipe-id');
//         saveRecipe(recipeID, btn);
//     });
// });

// document.querySelectorAll('.recipe-posts, .featured-item').forEach(post => {
//     post.addEventListener('click', function () {
//         const recipeID = post.getAttribute('data-recipe-id');
//         window.location.href = `displaypost.php?id=${recipeID}`;
//     });
// });

async function saveRecipe(recipeID, btn) {
    const icon = btn.querySelector('i');
    const post = btn.closest('.recipe-posts, .featured-item'); 

    try {
        const response = await fetch('saveRecipe.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ recipeID })
        });

        const result = await response.json();
        if (response.ok) {
            if (result.status === "saved") {
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
            } else if (result.status === "unsaved") {
                icon.classList.remove('fa-solid');
                icon.classList.add('fa-regular');

                if (window.location.pathname.includes("cookbook.php")) {
                    post.remove(); // tanggal agad
                }
            }
        } else {
            console.error("Failed to toggle save");
        }
    } catch (error) {
        console.error("Error saving:", error);
    }
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
                            <i class="fa-solid fa-bookmark"></i>
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
    const posts = document.getElementById('saved-section');
    posts.innerHTML = content;

    const recipePosts = document.querySelectorAll('.recipe-posts');
    recipePosts.forEach(recipePost => {
        recipePost.addEventListener('click', () => {
            const recipeID = recipePost.getAttribute('data-recipe-id');
            window.location.href = `displaypost.php?id=${recipeID}`;
        });
    });

    document.querySelectorAll('.recipe-posts .bookmark button').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const recipeID = btn.closest('.recipe-posts')?.getAttribute('data-recipe-id');
            saveRecipe(recipeID, btn);
        });
    });

}

