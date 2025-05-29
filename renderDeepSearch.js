var sortOrder = "score";
filterRecipes();

document.getElementById('display-mode').addEventListener('change', function () {
    sortOrder = this.value;
    filterRecipes();
});

function filterRecipes(){
    var recipes = deepsearchResult;
    if(sortOrder=="score"){
        recipes.sort((a, b) => (parseInt(b.score) - parseInt(a.score)))
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

    document.querySelectorAll('.recipe-posts .bookmark button').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const recipeID = btn.closest('.recipe-posts')?.getAttribute('data-recipe-id');
            saveRecipe(recipeID, btn);
        });
    });
}

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