let currentIndex = 0;
const carousel = document.getElementById('carousel');
const recipes = document.querySelectorAll('.recipe-card');
const totalRecipes = recipes.length;

// carousel navigation
function moveCarousel(direction) {
  const cardWidth = recipes[0]?.offsetWidth + 20 || 220;
  const visibleCards = 4;
  const maxIndex = Math.max(0, totalRecipes - visibleCards);
  currentIndex = Math.max(0, Math.min(currentIndex + direction, maxIndex));
  carousel.scrollTo({
    left: currentIndex * cardWidth,
    behavior: 'smooth'
  });
}

// save/unsave recipe functionality
function saveRecipe(recipeName, iconElement) {
  let savedRecipes = JSON.parse(localStorage.getItem('cookbook')) || [];
  if (!savedRecipes.includes(recipeName)) {
    savedRecipes.push(recipeName);
    localStorage.setItem('cookbook', JSON.stringify(savedRecipes));
    iconElement.classList.add('saved');
    alert(`${recipeName} has been saved to your cookbook!`);
  } else {
    savedRecipes = savedRecipes.filter(name => name !== recipeName);
    localStorage.setItem('cookbook', JSON.stringify(savedRecipes));
    iconElement.classList.remove('saved');
    alert(`${recipeName} has been removed from your cookbook!`);
  }
  if (window.location.pathname.includes('cookbook.html')) {
    loadSavedRecipes();
  }
}

// search recipes functionality
function searchRecipes() {
  const searchInput = document.getElementById('search-input').value.toLowerCase();
  const savedRecipes = JSON.parse(localStorage.getItem('cookbook')) || [];
  const popup = document.getElementById('search-popup');
  const searchResults = document.getElementById('search-results');

  // clear previous results
  searchResults.innerHTML = '';

  if (!searchInput) {
    searchResults.innerHTML = '<p>Please enter a search term.</p>';
    popup.style.display = 'block';
    return;
  }

  // recipe data for search
  const recipeData = {
    'Caldereta': { img: 'caldereta.jpg', tags: '#caldereta #chicken', ratings: '(16 ratings)', details: 'A rich Filipino stew made with beef or chicken, tomatoes, and liver paste.' },
    'Sinigang na Baboy': { img: 'sinigang.jpg', tags: '#sinigang #pork', ratings: '(45 ratings)', details: 'A sour pork soup with tamarind base, vegetables, and pork.' },
    'Adobong Manok': { img: 'adobo.png', tags: '#adobo #chicken #stew', ratings: '(10 ratings)', details: 'A classic Filipino dish of chicken braised in soy sauce, vinegar, and garlic.' },
    'Bangus Bicol Express': { img: 'bicol-express.jpg', tags: '#bangus #bicolexpress', ratings: '(19 ratings)', details: 'Milkfish cooked with coconut milk, chilies, and shrimp paste.' },
    'Bistek': { img: 'bistek.jpg', tags: '#beefsteak #beef', ratings: '(10 ratings)', details: 'Beef steak marinated in soy sauce and calamansi, served with onions.' },
    'Beef Kare-Kare': { img: 'kare-kare.jpg', tags: '#kare-kare #beef', ratings: '(20 ratings)', details: 'A peanut-based stew with oxtail, vegetables, and a thick sauce.' },
    'Paksiw na Lechon': { img: 'paksiw-na-lechon.jpg', tags: '#paksiw #lechon', ratings: '(13 ratings)', details: 'Leftover lechon cooked with vinegar, soy sauce, and spices.' },
    'Kinamatisang Manok': { img: 'kinamatisang-manok.jpg', tags: '#kinamatisan #chicken', ratings: '(21 ratings)', details: 'Chicken stewed with tomatoes, onions, and garlic.' }
  };

  // filter saved recipes based on search term
  const matchingRecipes = savedRecipes.filter(recipeName => {
    const data = recipeData[recipeName] || { tags: '' };
    return recipeName.toLowerCase().includes(searchInput) || data.tags.toLowerCase().includes(searchInput);
  });

  if (matchingRecipes.length === 0) {
    searchResults.innerHTML = '<p>No matching recipes found.</p>';
  } else {
    matchingRecipes.forEach(recipeName => {
      const data = recipeData[recipeName] || { img: 'default.jpg', tags: '#recipe', ratings: '(0 ratings)', details: 'No details available.' };
      const resultItem = document.createElement('div');
      resultItem.className = 'recipe-card';
      resultItem.innerHTML = `
        <a href="recipe-details.html?recipe=${encodeURIComponent(recipeName)}" style="text-decoration: none; color: inherit;">
          <img src="${data.img}" alt="${recipeName}">
          <h3>${recipeName}</h3>
          <p>${data.tags}</p>
          <p>${data.ratings}</p>
        </a>
      `;
      searchResults.appendChild(resultItem);
    });
  }

  popup.style.display = 'block';
}

// close popup
function closePopup() {
  const popup = document.getElementById('search-popup');
  popup.style.display = 'none';
}

// function to load and render saved recipes in cookbook.html
function loadSavedRecipes() {
  const savedRecipes = JSON.parse(localStorage.getItem('cookbook')) || [];
  const carousel = document.getElementById('carousel');
  carousel.innerHTML = '';

  if (savedRecipes.length === 0) {
    carousel.innerHTML = '<p style="margin-left: 20px; color: #666;">No saved recipes yet.</p>';
    return;
  }

  const recipeData = {
    'Caldereta': { img: 'caldereta.jpg', tags: '#caldereta #chicken', ratings: '(16 ratings)' },
    'Sinigang na Baboy': { img: 'sinigang.jpg', tags: '#sinigang #pork', ratings: '(45 ratings)' },
    'Adobong Manok': { img: 'adobo.png', tags: '#adobo #chicken #stew', ratings: '(10 ratings)' },
    'Bangus Bicol Express': { img: 'bicol-express.jpg', tags: '#bangus #bicolexpress', ratings: '(19 ratings)' },
    'Bistek': { img: 'bistek.jpg', tags: '#beefsteak #beef', ratings: '(10 ratings)' },
    'Beef Kare-Kare': { img: 'kare-kare.jpg', tags: '#kare-kare #beef', ratings: '(20 ratings)' },
    'Paksiw na Lechon': { img: 'paksiw-na-lechon.jpg', tags: '#paksiw #lechon', ratings: '(13 ratings)' },
    'Kinamatisang Manok': { img: 'kinamatisang-manok.jpg', tags: '#kinamatisan #chicken', ratings: '(21 ratings)' }
  };

  savedRecipes.forEach(recipeName => {
    const data = recipeData[recipeName] || { img: 'default.jpg', tags: '#recipe', ratings: '(0 ratings)' };
    const recipeCard = document.createElement('div');
    recipeCard.className = 'recipe-card';
    recipeCard.innerHTML = `
      <img src="${data.img}" alt="${recipeName}">
      <h3>${recipeName}</h3>
      <p>${data.tags}</p>
      <p>${data.ratings}</p>
      <div class="rating" data-recipe="${recipeName}">
        <span class="star" data-value="1">★</span>
        <span class="star" data-value="2">★</span>
        <span class="star" data-value="3">★</span>
        <span class="star" data-value="4">★</span>
        <span class="star" data-value="5">★</span>
      </div>
      <img class="save-icon ${savedRecipes.includes(recipeName) ? 'saved' : ''}" src="bookmark-icon.jpg" alt="Save Recipe" onclick="saveRecipe('${recipeName}', this)">
    `;
    carousel.appendChild(recipeCard);
  });

  document.querySelectorAll('.rating').forEach(rating => {
    const recipeName = rating.dataset.recipe;
    const stars = rating.querySelectorAll('.star');
    
    const savedRating = localStorage.getItem(`rating-${recipeName}`);
    if (savedRating) {
      stars.forEach(star => {
        if (star.dataset.value <= savedRating) {
          star.classList.add('selected');
        }
      });
    }

    stars.forEach(star => {
      star.addEventListener('click', () => {
        const value = star.dataset.value;
        const currentRating = localStorage.getItem(`rating-${recipeName}`);
        
        if (currentRating === value) {
          localStorage.removeItem(`rating-${recipeName}`);
          stars.forEach(s => s.classList.remove('selected'));
          alert(`${recipeName} rating has been removed!`);
        } else {
          localStorage.setItem(`rating-${recipeName}`, value);
          stars.forEach(s => {
            s.classList.remove('selected');
            if (s.dataset.value <= value) {
              s.classList.add('selected');
            }
          });
          alert(`${recipeName} rated ${value} stars!`);
        }
      });

      star.addEventListener('mouseover', () => {
        const value = star.dataset.value;
        stars.forEach(s => {
          s.classList.remove('hovered');
          if (s.dataset.value <= value) {
            s.classList.add('hovered');
          }
        });
      });

      star.addEventListener('mouseout', () => {
        stars.forEach(s => s.classList.remove('hovered'));
      });
    });
  });

  window.totalRecipes = savedRecipes.length;
}

// rating functionality for homepage.html and profile.html
document.querySelectorAll('.rating').forEach(rating => {
  const recipeName = rating.dataset.recipe;
  const stars = rating.querySelectorAll('.star');
  
  const savedRating = localStorage.getItem(`rating-${recipeName}`);
  if (savedRating) {
    stars.forEach(star => {
      if (star.dataset.value <= savedRating) {
        star.classList.add('selected');
      }
    });
  }

  stars.forEach(star => {
    star.addEventListener('click', () => {
      const value = star.dataset.value;
      const currentRating = localStorage.getItem(`rating-${recipeName}`);
      
      if (currentRating === value) {
        localStorage.removeItem(`rating-${recipeName}`);
        stars.forEach(s => s.classList.remove('selected'));
        alert(`${recipeName} rating has been removed!`);
      } else {
        localStorage.setItem(`rating-${recipeName}`, value);
        stars.forEach(s => {
          s.classList.remove('selected');
          if (s.dataset.value <= value) {
            s.classList.add('selected');
          }
        });
        alert(`${recipeName} rated ${value} stars!`);
      }
    });

    star.addEventListener('mouseover', () => {
      const value = star.dataset.value;
      stars.forEach(s => {
        s.classList.remove('hovered');
        if (s.dataset.value <= value) {
          s.classList.add('hovered');
        }
      });
    });

    star.addEventListener('mouseout', () => {
      stars.forEach(s => s.classList.remove('hovered'));
    });
  });
});

// ensure buttons work on page load
document.querySelector('.carousel-btn.left')?.addEventListener('click', () => moveCarousel(-1));
document.querySelector('.carousel-btn.right')?.addEventListener('click', () => moveCarousel(1));

// load saved recipes on cookbook.html
if (window.location.pathname.includes('cookbook.html')) {
  window.addEventListener('load', loadSavedRecipes);
}