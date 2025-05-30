var sortOrder = "id";
displayOwned();

const displayMode = document.getElementById('display-mode');
if (displayMode) {
    displayMode.addEventListener('change', function () {
        sortOrder = this.value;
        displayOwned();
    });
}

async function getOwned() {
    const request = typeof profileID !== 'undefined' ? "getOwned.php?id=" + profileID : "getOwned.php";
    const response = await fetch(request);
    return response.json();
}

async function displayOwned() {
    const data = await getOwned();
    const recipes = data;
    console.log(recipes);
    if (sortOrder == "id") {
        recipes.sort((a, b) => (parseInt(b.recipeID) - parseInt(a.recipeID)));
    } else if (sortOrder == "rating") {
        recipes.sort((a, b) => (parseInt(b.avgRating || 0) - parseInt(a.avgRating || 0)));
    }
    var content = "";
    for (let recipe of recipes) {
        // Check if the logged-in user owns the recipe
        const isOwner = recipe.userID === currentUserId;
        const buttons = isOwner ? `
            <div class="recipe-actions">
                <button class="edit-btn" onclick="editRecipe(${recipe.recipeID}, event)">Edit</button>
                <button class="delete-btn" onclick="deleteRecipe(${recipe.recipeID}, event)">Delete</button>
            </div>
        ` : '';
        
        content += `
            <div class="recipe-posts" data-recipe-id="${recipe.recipeID}"> 
                <div class="image">
                    <img src="${recipe.pictureLink}" alt="recipe image">
                </div>
                <div id="recipe-name"> 
                    <p>${recipe.recipeTitle}</p>
                </div>
                <div id="tags">
                    <p>#${recipe.tags.join(" #")}</p>
                </div>
                <div class="ratings">
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <p>${recipe.avgRating != null ? recipe.avgRating : 0} stars (${recipe.countRating != null ? recipe.countRating : 0} ratings)</p>
                </div>
                ${buttons}
            </div>
        `;
    }
    const posts = document.getElementById('profile-featured-section');
    posts.innerHTML = content;
    const recipePosts = document.querySelectorAll('.recipe-posts');
    recipePosts.forEach(recipePost => {
        recipePost.addEventListener('click', (event) => {
            // Prevent navigation if clicking Edit/Delete buttons
            if (event.target.classList.contains('edit-btn') || event.target.classList.contains('delete-btn')) {
                return;
            }
            const recipeID = recipePost.getAttribute('data-recipe-id');
            window.location.href = `displaypost.php?id=${recipeID}`;
        });
    });
}

function editRecipe(recipeId, event) {
    event.stopPropagation(); // Prevent triggering the recipe card click
    window.location.href = `editrecipe.php?recipe_id=${recipeId}`;
}

function deleteRecipe(recipeId, event) {
    event.stopPropagation(); // Prevent triggering the recipe card click
    if (confirm('Are you sure you want to delete this recipe?')) {
        fetch('deleterecipe.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `recipe_id=${recipeId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Recipe deleted successfully!');
                location.reload();
            } else {
                alert('Error deleting recipe: ' + data.message);
            }
        })
        .catch(error => alert('Error: ' + error.message));
    }
}