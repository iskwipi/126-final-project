async function getData(){
    const response = await fetch("data.json");
    return response.json();
}

async function displayFeed(){
    const data = await getData();
    const recipes = [];
    for(let x = 0; x < 5; x++){
        for(let y = 0; y < data.recipes.length; y++){
            recipes[x * data.recipes.length + y] = data.recipes[y];
        }
    }
    const users = data.users;

    console.log(recipes);
    console.log(users);
    var content = "";
    for(let recipe of recipes){
        console.log(recipe);
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
    const posts = document.getElementById('posts-section');
    posts.innerHTML = content;
}

async function displayFeatured(){
    const data = await getData();
    const recipes = [];
    for(let x = 0; x < 5; x++){
        for(let y = 0; y < data.recipes.length; y++){
            recipes[x * data.recipes.length + y] = data.recipes[y];
        }
    }
    const users = data.users;

    console.log(recipes);
    console.log(users);
    var content = `
        <button class="nav-button left-button">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
    `;
    for(let recipe of recipes){
        console.log(recipe);
        content += `
            <div class="featured-item"> 
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
}

async function displaySaved(){
    const data = await getData();
    const recipes = [];
    for(let x = 0; x < 5; x++){
        for(let y = 0; y < data.recipes.length; y++){
            recipes[x * data.recipes.length + y] = data.recipes[y];
        }
    }
    const users = data.users;

    console.log(recipes);
    console.log(users);
    var content = "";
    for(let recipe of recipes){
        console.log(recipe);
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
    const posts = document.getElementById('saved-section');
    posts.innerHTML = content;
}

displayFeed();
displayFeatured();
displaySaved();