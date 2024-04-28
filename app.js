// Globale Variable für Teesorten
let teas = [];

// Local Storage ID
const CART_KEY = 'warenkorb';

// Asynchrones Laden der Teesorten
async function loadTeas() {
    try {
        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ entity: 'ShopProducts', action: 'getProducts' })
        };
        const response = await fetch('api/', requestOptions);
        let resJson = await response.json();

        if (resJson.success === true) {
            teas = resJson.data;
        } else {
            throw new Error(resJson.error);
        }
    } catch (error) {
        console.error('Fehler beim Laden der Teesorten:', error);
        alert('Fehler beim Laden der Teesorten!')
    }
}

// Warenkorb laden
function getCart() {
    const cartJSON = localStorage.getItem(CART_KEY);
    return cartJSON ? JSON.parse(cartJSON) : {};
}

// Warenkorb speichern
function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

// Anzeigen der Teesorten
function displayTeas() {
    const listElement = document.getElementById('teelist');
    const fragment = document.createDocumentFragment();

    teas.forEach(tea => {
        const mwSt = tea.preis * (tea.mehrwertsteuer / 100);
        const bruttoPreis = tea.preis + mwSt;
        const itemElement = document.createElement('li');
        itemElement.innerHTML = `
        <article>
            <img src="${tea.bild}" alt="${tea.name}">
            <div class="product_inner">
            <h3>${tea.name} - ${bruttoPreis.toFixed(2)}€</h3>
            <p>${tea.beschreibung}</p>
            <p><center>Lagerstand: ${tea.lagerstand}</center></p>
            </div>
        </article>
        `;
        const button = document.createElement('button');
        button.textContent = 'In den Warenkorb';
        button.addEventListener('click', () => addToCart(tea.id, tea.name, 1));
        itemElement.appendChild(button);
        fragment.appendChild(itemElement);
    });

    if (listElement) {
        listElement.appendChild(fragment);
    }
}

// Hinzufügen zum Warenkorb
function addToCart(id, name, quantity) {
    const cart = getCart();
    if (cart[id]) {
        cart[id].quantity += quantity;
    } else {
        cart[id] = { name: name, quantity: quantity };
    }
    saveCart(cart);
    displayCart();
}

// Menge aktualisieren
function updateQuantity(id, quantity) {
    const cart = getCart();
    if (quantity > 0) {
        cart[id].quantity = quantity;
    } else {
        delete cart[id];
    }
    saveCart(cart);
    displayCart();
}

// Artikel entfernen
function removeFromCart(id) {
    updateQuantity(id, 0);
}

// Warenkorb anzeigen
function displayCart() {
    const cart = getCart();
    const cartElement = document.getElementById('warenkorb');
    cartElement.innerHTML = Object.keys(cart).length ? createCartTable(cart) : '<p>Dein Warenkorb ist leer.</p>';
}

// Hilfsfunktion zur Erstellung der Warenkorbtabelle
function createCartTable(cart) {
    let totalNetto = 0, totalMwSt = 0;

    let tableHtml = `<table>
        <tr>
            <th>Artikel</th>
            <th>Menge</th>
            <th>Preis</th>
            <th></th>
        </tr>`;

    Object.keys(cart).forEach(key => {
        const tea = teas.find(t => t.id === parseInt(key));
        if (!tea) return;
        const netto = tea.preis * cart[key].quantity;
        const mwSt = netto * (tea.mehrwertsteuer / 100);
        totalNetto += netto;
        totalMwSt += mwSt;

        tableHtml += `
            <tr>
                <td>${cart[key].name}</td>
                <td>
                    <input type="number" value="${cart[key].quantity}" min="1" onchange="updateQuantity('${key}', this.value)">
                </td>
                <td class="right">${(netto + mwSt).toFixed(2)}€ <em>(inkl. MwSt.)</em></td>
                <td><button onclick="removeFromCart('${key}')">-</button></td>
            </tr>`;
    });

    tableHtml += `
        <tr class="cart_sum">
            <td colspan="3"><strong>Gesamtsumme</strong></td>
            <td class="right"><strong>${(totalNetto + totalMwSt).toFixed(2)}€</strong></td>
        </tr>
    </table>`;

    return tableHtml;
}

// Event-Listener zum Laden der Teesorten und Initialisierung der Anzeige
document.addEventListener('DOMContentLoaded', async () => {
    await loadTeas();
    displayTeas();
    displayCart();
});
