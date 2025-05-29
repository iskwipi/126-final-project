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
}