const scrollContent = document.querySelector('.featured-posts');
const btnLeft = document.querySelector('.left-nav-button');
const btnRight = document.querySelector('.right-nav-button');

btnLeft.addEventListener('click', () => {
    scrollContent.scrollBy({
    left: -300, // pixels to scroll left
    behavior: 'smooth'
    });
});
console.log("done");

btnRight.addEventListener('click', () => {
    scrollContent.scrollBy({
    left: 300, // pixels to scroll right
    behavior: 'smooth'
    });
});
console.log("done");