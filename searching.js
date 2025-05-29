function addIngredientRow(element) {
    const ingredientRow = element.closest('.ingredients-field .input-wrapper');
    const newRow = ingredientRow.cloneNode(true);

    newRow.querySelectorAll('input').forEach(input => input.value = '');

    const plusIcon = newRow.querySelector('.fa-plus');
    if (plusIcon) plusIcon.setAttribute('onclick', 'addIngredientRow(this)');

    ingredientRow.parentNode.insertBefore(newRow, ingredientRow.nextSibling);
}

function removeIngredientRow(element) {
    const ingredientRow = element.closest('.ingredients-field .input-wrapper');
    if (ingredientRow.parentNode.children.length > 2) {
        ingredientRow.parentNode.removeChild(ingredientRow);
    } else {
        ingredientRow.querySelectorAll('input').forEach(input => input.value = '');
    }
}

function addTagRow(element) {
    const tagRow = element.closest('.posting-tags-field .input-wrapper');
    const newRow = tagRow.cloneNode(true);

    newRow.querySelectorAll('input').forEach(input => input.value = '');

    const plusIcon = newRow.querySelector('.fa-plus');
    if (plusIcon) plusIcon.setAttribute('onclick', 'addTagRow(this)');

    tagRow.parentNode.insertBefore(newRow, tagRow.nextSibling);
}

function removeTagRow(element) {
    const tagRow = element.closest('.posting-tags-field .input-wrapper');
    if (tagRow.parentNode.children.length > 2) {
        tagRow.parentNode.removeChild(tagRow);
    } else {
        tagRow.querySelectorAll('input').forEach(input => input.value = '');
    }
}