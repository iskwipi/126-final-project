function addIngredientRow(element) {
    const container = document.getElementById('ingredients');
    const newRow = document.createElement('div');
    newRow.className = 'ingredient-row';
    newRow.innerHTML = `
        <input type="number" id="ingredient-measurement" name="ingredient-measurement[]" placeholder="Measure" required>
        <select id="unit-type" name="unit-type[]" required>
            <option value="">Unit:</option>
            <option value="teaspoon">Teaspoon (tsp)</option>
            <option value="tablespoon">Tablespoon (tbsp)</option>
            <option value="cup">Cup</option>
            <option value="milliliter">Milliliter (ml)</option>
            <option value="liter">Liter (l)</option>
            <option value="gram">Gram (g)</option>
            <option value="kilogram">Kilogram (kg)</option>
            <option value="ounce">Ounce (oz)</option>
            <option value="pound">Pound (lb)</option>
            <option value="pinch">Pinch</option>
            <option value="dash">Dash</option>
            <option value="piece">Piece</option>
        </select>
        <div class="input-wrapper">
            <input type="text" id="recipe-ingredients" name="recipe-ingredients[]" placeholder="Ingredient Name" required>
            <i class="fa-solid fa-plus" onclick="addIngredientRow(this)"></i>
            <i class="fa-regular fa-circle-xmark" onclick="removeIngredientRow(this)"></i>
        </div>
    `;
    container.appendChild(newRow);
}

function removeIngredientRow(element) {
    const row = element.parentElement.parentElement;
    row.remove();
}

function addInstructionRow(element) {
    const container = document.getElementById('instructions');
    const newRow = document.createElement('div');
    newRow.className = 'input-wrapper';
    newRow.innerHTML = `
        <textarea id="recipe-instructions" name="recipe-instructions[]" placeholder="Step-by-step instructions" required></textarea>
        <i class="fa-solid fa-plus" onclick="addInstructionRow(this)"></i>
        <i class="fa-regular fa-circle-xmark" onclick="removeInstructionRow(this)"></i>
    `;
    container.appendChild(newRow);
}

function removeInstructionRow(element) {
    const row = element.parentElement;
    row.remove();
}

function addTagRow(element) {
    const container = document.getElementById('tags');
    const newRow = document.createElement('div');
    newRow.className = 'input-wrapper';
    newRow.innerHTML = `
        <input type="text" id="tag-Input" name="tag-Input[]" placeholder="Insert tag..." required>
        <i class="fa-solid fa-plus" onclick="addTagRow(this)"></i>
        <i class="fa-regular fa-circle-xmark" onclick="removeTagRow(this)"></i>
    `;
    container.appendChild(newRow);
}

function removeTagRow(element) {
    const row = element.parentElement;
    row.remove();
}