'use strict';
// names ending in [] become a PHP array.
const ingredientList = document.querySelector('[data-ingredients]');
const addIngredient = document.querySelector('[data-add-ingredient]');
function updateIngredientButtons() {
    if (!ingredientList) return;
    const rows = ingredientList.querySelectorAll('.ingredient-row');
    rows.forEach(row => { row.querySelector('button').disabled = rows.length === 1; });
    addIngredient.disabled = rows.length >= 30;
}
if (ingredientList) {
    addIngredient.addEventListener('click', () => {
        if (ingredientList.children.length >= 30) return;
        const row = ingredientList.firstElementChild.cloneNode(true);
        row.querySelector('input').value = '';
        ingredientList.append(row);
        updateIngredientButtons();
        row.querySelector('input').focus();
    });
    ingredientList.addEventListener('click', event => {
        const button = event.target.closest('[data-remove-ingredient]');
        if (button && ingredientList.children.length > 1) {
            button.closest('.ingredient-row').remove();
            updateIngredientButtons();
        }
    });
    updateIngredientButtons();
}
// Fetch updates the button without reloading the page.
document.querySelectorAll('[data-favorite]').forEach(button => {
    button.addEventListener('click', async () => {
        const saved = button.getAttribute('aria-pressed') === 'true';
        const card = button.closest('[data-recipe-card], .detail');
        const status = card.querySelector('.favorite-status');
        button.disabled = true; button.textContent = 'Saving…'; status.textContent = '';
        try {
            const response = await fetch('api/toggle_favorite.php', {
                method: 'POST', credentials: 'same-origin',
                body: new URLSearchParams({recipe_id: button.dataset.id, csrf_token: button.dataset.csrf, is_favorited: saved ? '0' : '1'})
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Unable to save this recipe.');
            button.setAttribute('aria-pressed', String(result.is_favorited));
            button.textContent = result.is_favorited ? 'Unsave' : 'Save';
            status.textContent = result.message;
            if (!result.is_favorited && card.closest('[data-favorites-page]')) {
                card.remove();
                if (!document.querySelector('[data-recipe-card]')) document.querySelector('[data-empty-favorites]').hidden = false;
            }
        } catch (error) {
            button.textContent = saved ? 'Unsave' : 'Save';
            status.textContent = error instanceof SyntaxError ? 'Unexpected response. Reload and try again.' : error.message;
        } finally { button.disabled = false; }
    });
});
document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', event => {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
});
