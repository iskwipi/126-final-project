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
    if (ingredientRow.parentNode.children.length > 2) {
        ingredientRow.parentNode.removeChild(ingredientRow);
    } else {
        ingredientRow.querySelectorAll('input').forEach(input => input.value = '');
        const select = ingredientRow.querySelector('select');
        if (select) select.selectedIndex = 0;
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

function addInstructionRow(element) {
    const instructionRow = element.closest('.instructions-field .input-wrapper');
    const newRow = instructionRow.cloneNode(true);

    newRow.querySelectorAll('textarea').forEach(input => {
        input.addEventListener('input', () => {
            input.style.height = 'auto'; // Reset to auto
            input.style.height = input.scrollHeight + 'px'; // Adjust height
        });
    });

    const plusIcon = newRow.querySelector('.fa-plus');
    if (plusIcon) plusIcon.setAttribute('onclick', 'addInstructionRow(this)');

    instructionRow.parentNode.insertBefore(newRow, instructionRow.nextSibling);
    
    newRow.querySelectorAll('textarea').forEach(input => {
        input.value = ''
        input.style.height = 'auto';
        input.style.height = input.scrollHeight + 'px';
    });
}

function removeInstructionRow(element) {
    const instructionRow = element.closest('.instructions-field .input-wrapper');
    if (instructionRow.parentNode.children.length > 2) {
        instructionRow.parentNode.removeChild(instructionRow);
    } else {
        instructionRow.querySelectorAll('textarea').forEach(input => {
            input.value = ''
            input.style.height = 'auto';
            input.style.height = input.scrollHeight + 'px';
        });
    }
}

const instructionFields = document.querySelectorAll('.instructions-field textarea, .description-field textarea');
instructionFields.forEach(textarea => {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
    textarea.addEventListener('input', () => {
        textarea.style.height = 'auto'; // Reset to auto
        textarea.style.height = textarea.scrollHeight + 'px'; // Adjust height
    });
});