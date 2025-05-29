var sortOrder = "id";
displayOwned();

document.getElementById('display-mode').addEventListener('change', function () {
    sortOrder = this.value;
    displayOwned();
});

async function getOwned(){
    const request = typeof profileID !== 'undefined' ? "getOwned.php?id=" + profileID : "getOwned.php"
    const response = await fetch(request);
    return response.json();
}

async function displayOwned(){
    const data = await getOwned();
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
                    <img src="${recipe.pictureLink}" alt="recipe image">
                </div>
                <div id="recipe-name"> 
                    <p>${recipe.recipeTitle}</p>
                </div>
                <div id="tags">
                    <p>${recipe.tags}</p>
                </div>
                <div class="ratings">
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                    </button> 
                    <p>${recipe.avgRating != null ? recipe.avgRating : 0} stars (${recipe.countRating != null ? recipe.countRating : 0} ratings)</p>
                </div>
            </div>
        `;
    }
    const posts = document.getElementById('profile-featured-section');
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