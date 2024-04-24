// Globale Variable für Teesorten
let teas = [];

// Local Storage ID
const CART_KEY = 'warenkorb';

// Asynchrones Laden der Teesorten
async function loadTeas() {
    try {
        const response = await fetch('teas.json');
        teas = await response.json();
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
        const bruttoPreis= tea.preis+mwSt;
        const itemElement = document.createElement('li');
        itemElement.innerHTML = `
        <article>
            <img src="${tea.bild}" alt="${tea.name}">
            <div class="product_inner">
            <h3>${tea.name} - ${bruttoPreis.toFixed(2)}€</h3>
            <p>${tea.beschreibung}</p>
            <p><a href="${tea.link}" target="_blank">Mehr erfahren</a></p>
            </div>
        </article>
        `;
        const button = document.createElement('button');
        button.textContent = 'In den Warenkorb';
        button.addEventListener('click', () => addToCart(tea.name, 1));
        itemElement.appendChild(button);
        fragment.appendChild(itemElement);
    });
  
    //wenn das Element exisitert ergänzen
    if(listElement){
      listElement.appendChild(fragment);
    }    
}

// Hinzufügen zum Warenkorb
function addToCart(name, quantity) {
    const cart = getCart();
    var quan = Number(cart[name])
    cart[name] = (quan || 0) + quantity;
    saveCart(cart);
    displayCart();
}

// Menge aktualisieren
function updateQuantity(name, quantity) {
    const cart = getCart();
    quantity > 0 ? cart[name] = quantity : delete cart[name];
    saveCart(cart);
    displayCart();
}

// Artikel entfernen
function removeFromCart(name) {
    updateQuantity(name, 0);
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
        const tea = teas.find(t => t.name === key);
        if (!tea) return; // Wenn der Artikel nicht gefunden wird, überspringen
        const netto = tea.preis * cart[key];
        const mwSt = netto * (tea.mehrwertsteuer / 100);
        totalNetto += netto;
        totalMwSt += mwSt;

        tableHtml += `
            <tr>
                <td>${key}</td>
                <td>
                    <input type="number" value="${cart[key]}" min="1" onchange="updateQuantity('${key}', this.value)">
                </td>
                <td class="right">${(netto + mwSt).toFixed(2)}€ <em>(inkl. MwSt.)</em></td>
                <td><button onclick="removeFromCart('${key}')"><i class="fa fa-trash hand" aria-hidden="true"></i></button></td>
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
