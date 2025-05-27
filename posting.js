function addIngredientRow(element) {
    const ingredientRow = element.closest('.ingredient-row');
    const newRow = ingredientRow.cloneNode(true);

    newRow.querySelectorAll('input').forEach(input => input.value = '');
    const select = newRow.querySelector('select');
    if (select) select.selectedIndex = 0;

    const plusIcon = newRow.querySelector('.fa-plus');
    if (plusIcon) plusIcon.setAttribute('onclick', 'addIngredientRow(this)');

    ingredientRow.parentNode.insertBefore(newRow, ingredientRow.nextSibling);
}

function removeIngredientRow(element) {
    const ingredientRow = element.closest('.ingredient-row');
    if (ingredientRow.parentNode.children.length > 1) {
        ingredientRow.parentNode.removeChild(ingredientRow);
    } else {
        ingredientRow.querySelectorAll('input').forEach(input => input.value = '');
        const select = ingredientRow.querySelector('select');
        if (select) select.selectedIndex = 0;
    }
}
