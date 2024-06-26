// Globale Variable für Teesorten
let teas = [];

// Local Storage ID
const CART_KEY = 'warenkorb';



/**
 * Sends a POST request to a predefined URL with the provided data and handles responses.
 * 
 * @param {Object} postData - The data to be sent with the request.
 * @param {Function} successCallback - The callback function to execute if the request is successful.
 * @param {Function} errorCallback - The callback function to execute if the request fails.
 */
async function postData(postData, successCallback, errorCallback) {
    const apiURL = 'api/';  // Hardcoded URL for all requests

    try {
        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(postData)
        };

        const response = await fetch(apiURL, requestOptions);
        const resJson = await response.json();

        if (resJson.success === true) {
            successCallback(resJson.data);
        } else {
            throw new Error(resJson.error || 'Unknown error');
        }
    } catch (error) {
        console.error('Error:', error);
        errorCallback(error);
    }
}

// Asynchrones Laden der Teesorten
function handleTeaLoadSuccess(data) {
    console.log('Teesorten erfolgreich geladen:', data);
    teas = data;
}
function handleTeaLoadError(error) {
    console.error('Fehler beim Laden der Teesorten:', error);
    alert('Fehler beim Laden der Teesorten!');
}

// Asynchrones Laden der Teesorten using the new generic function
async function loadTeas() {
    await postData(
        { entity: 'ShopApi', action: 'getProducts' },
        handleTeaLoadSuccess,
        handleTeaLoadError
    );
}

// Asynchrones Laden der Teesorten using the new generic function
async function addressDelete(addressId) {
    await postData(
        { entity: 'ShopApi', action: 'addressDelete', addressId: addressId },
        (data) => {
            console.log("addressDelete", data);
            window.location.reload(true);
        },
        (error) => {
            alert(error.message);
        }
    );
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
    const tea = teas.find(t => t.id === parseInt(id)); // Finde den entsprechenden Tee anhand der ID

    if (!tea) {
        alert('Tee nicht gefunden!');
        return;
    }

    if (cart[id]) {
        // Berechne die neue Gesamtmenge unter Berücksichtigung der bereits im Warenkorb befindlichen Menge
        let newQuantity = cart[id].quantity + quantity;

        // Überprüfe, ob die neue Menge den Lagerstand überschreitet
        if (newQuantity > tea.lagerstand) {
            alert('Die hinzugefügte Menge zusammen mit der bereits im Warenkorb befindlichen Menge überschreitet den Lagerstand von ' + tea.lagerstand + ' Einheiten. Die Menge wird auf den maximalen Lagerstand gesetzt.');
            cart[id].quantity = tea.lagerstand; // Setze die Menge auf den maximal verfügbaren Lagerstand
        } else {
            cart[id].quantity = newQuantity; // Aktualisiere die Menge im Warenkorb
        }
    } else {
        // Wenn der Artikel noch nicht im Warenkorb ist, überprüfe die Menge im Vergleich zum Lagerstand
        if (quantity > tea.lagerstand) {
            alert('Die gewünschte Menge überschreitet den verfügbaren Lagerstand von ' + tea.lagerstand + ' Einheiten. Die Menge wird auf den maximalen Lagerstand gesetzt.');
            cart[id] = { name: name, quantity: tea.lagerstand }; // Setze die Menge auf den maximalen Lagerstand
        } else {
            cart[id] = { name: name, quantity: quantity }; // Füge den neuen Artikel zum Warenkorb hinzu
        }
    }

    saveCart(cart);
    displayCart();
}



// Menge aktualisieren
function updateQuantity(id, quantity) {
    const cart = getCart();
    const tea = teas.find(t => t.id === parseInt(id)); // Finde den Tee basierend auf der ID

    if (!tea) {
        alert('Tee nicht gefunden!');
        return;
    }

    if (quantity > 0) {
        if (quantity <= tea.lagerstand) {
            cart[id].quantity = quantity;
        } else {
            alert('Die gewünschte Menge überschreitet den verfügbaren Lagerstand von ' + tea.lagerstand + ' Einheiten.');
            cart[id].quantity = tea.lagerstand;
        }
    } else {
        delete cart[id]; // Artikel aus dem Warenkorb entfernen, wenn die Menge 0 oder weniger ist
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

    // Element für die Darstellung der Einkaufsdaten in einem Formularfeld abrufen
    const cartData = document.getElementById('cartData');
    if (cartData) {
        // Einkaufswagenobjekt in JSON-String umwandeln und dem Eingabewert zuweisen
        cartData.value = JSON.stringify(cart);
    }

    // Hauptelement für die Anzeige des Einkaufswagens abrufen
    const cartElement = document.getElementById('warenkorb');
    if (cartElement) {
        cartElement.innerHTML = Object.keys(cart).length ? createCartTable(cart) : '<p>Dein Warenkorb ist leer.</p>';
    }

    // Element für die Anzeige des Einkaufswagens beim Checkout abrufen
    const cartElementCheckout = document.getElementById('checkout_warenkorb');
    if (cartElementCheckout) {
        cartElementCheckout.innerHTML = Object.keys(cart).length ? createCartTable(cart, true) : '<p>Dein Warenkorb ist leer.</p>';
    }

    // Button für den Übergang zur Kasse abrufen und verbergen, wenn der Warenkorb leer ist
    const checkoutButton = document.getElementById('zurKassa');
    if (checkoutButton) {
        checkoutButton.style.display = Object.keys(cart).length ? 'block' : 'none';
    }
}

// Hilfsfunktion zur Erstellung der Warenkorbtabelle

function createCartTable(cart, checkout = false) {
    let totalNetto = 0, totalMwSt = 0;
    let versandKosten = 5.90; // Versandkosten als Beispiel

    // Tabelle mit einer bedingten Klasse im Checkout-Fall
    let tableHtml = `<table${checkout ? ' class="checkout_order_table"' : ''}>
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
                    <input type="number" value="${cart[key].quantity}" min="1" max="${tea.lagerstand}" onchange="updateQuantity('${key}', this.value)">
                </td>
                <td class="right">${(netto + mwSt).toFixed(2)}€ <em>(inkl. MwSt.)</em></td>
                <td class="delButton"><button onclick="removeFromCart('${key}')"><i class="fa fa-trash-o" aria-hidden="true"></i>
                </button></td>
            </tr>`;
    });

    // Berechnungen und zusätzliche Zeilen für Checkout
    if (checkout) {
        const gesamtsumme = totalNetto + totalMwSt + versandKosten;
        tableHtml += `
            <tr class="cart_sum">
                <td colspan="3"><strong>Zwischensumme</strong></td>
                <td class="right"><strong>${(totalNetto + totalMwSt).toFixed(2)}€</strong></td>
            </tr>
            <tr>
                <td colspan="3">Versandkosten</td>
                <td class="right">${versandKosten.toFixed(2)}€</td>
            </tr>            
            <tr>
                <td colspan="3"><strong>Gesamtsumme</strong></td>
                <td class="right"><strong>${gesamtsumme.toFixed(2)}€</strong></td>
            </tr>
            <tr>
                <td colspan="3">enthaltene MwSt.</td>
                <td class="right">${totalMwSt.toFixed(2)}€</td>
            </tr>
           `;
    } else {
        tableHtml += `
            <tr class="cart_sum">
                <td colspan="3"><strong>Gesamtsumme</strong></td>
                <td class="right"><strong>${(totalNetto + totalMwSt).toFixed(2)}€</strong></td>
            </tr>`;
    }

    tableHtml += '</table>';

    return tableHtml;
}


// Event-Listener zum Laden der Teesorten und Initialisierung der Anzeige
document.addEventListener('DOMContentLoaded', async () => {
    await loadTeas();
    displayTeas();
    displayCart();
});



