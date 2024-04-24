-- delete the database
DROP DATABASE IF EXISTS fhwien;

-- Create the database
CREATE DATABASE fhwien;

-- Select the newly created database
USE fhwien;

-- Datenomdell für den Tee Shop:
CREATE TABLE
    users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL unique,
        createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
        createdUser INT not null default 0,
        modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        modUser INT not null default 0,
        lockstate int default 0
    );

CREATE TABLE
    categories (
        category_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT null,
        createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
        createdUser INT not null default 0,
        modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        modUser INT not null default 0,
        lockstate int default 0
    );

CREATE TABLE
    products (
        product_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        image varchar(255),
        tax DECIMAL(10, 2) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        stock INT NOT NULL,
        createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
        createdUser INT not null default 0,
        modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        modUser INT not null default 0,
        lockstate int default 0
    );

CREATE TABLE
    product_categories (
        product_id INT,
        category_id INT,
        createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
        createdUser INT not null default 0,
        modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        modUser INT not null default 0,
        lockstate int default 0,
        PRIMARY KEY (product_id, category_id),
        FOREIGN KEY (product_id) REFERENCES products (product_id),
        FOREIGN KEY (category_id) REFERENCES categories (category_id)
    );

CREATE TABLE
    carts (
        cart_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        session_id varchar(255),
        createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
        createdUser INT not null default 0,
        modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        modUser INT not null default 0,
        lockstate int default 0,
        FOREIGN KEY (user_id) REFERENCES users (user_id)
    );

CREATE TABLE
    cart_items (
        cart_id INT,
        product_id INT,
        quantity INT,
        createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
        createdUser INT not null default 0,
        modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        modUser INT not null default 0,
        lockstate int default 0,
        PRIMARY KEY (cart_id, product_id),
        FOREIGN KEY (cart_id) REFERENCES carts (cart_id),
        FOREIGN KEY (product_id) REFERENCES products (product_id)
    );

CREATE TABLE
    addresses (
        address_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        address_type varchar(255) not null,
        street VARCHAR(255) NOT NULL,
        city VARCHAR(255) NOT NULL,
        state VARCHAR(255),
        postal_code VARCHAR(20) NOT NULL,
        country VARCHAR(255) NOT null,
        createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
        createdUser INT not null default 0,
        modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        modUser INT not null default 0,
        lockstate int default 0,
        FOREIGN KEY (user_id) REFERENCES users (user_id)
    );

CREATE TABLE
    orders (
        order_id INT AUTO_INCREMENT PRIMARY KEY,
        address_id_delivery INT,
        address_id_billing INT,
        user_id INT,
        order_date DATETIME,
        total_price DECIMAL(10, 2),
        status int,
        createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
        createdUser INT not null default 0,
        modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        modUser INT not null default 0,
        lockstate int default 0,
        FOREIGN KEY (user_id) REFERENCES users (user_id),
        FOREIGN KEY (address_id_delivery) REFERENCES addresses (address_id),
        FOREIGN KEY (address_id_billing) REFERENCES addresses (address_id)
    );

CREATE TABLE
    order_items (
        order_id INT,
        product_id INT,
        quantity INT,
        price DECIMAL(10, 2),
        tax Decimal(10, 2),
        createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
        createdUser INT not null default 0,
        modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        modUser INT not null default 0,
        lockstate int default 0,
        PRIMARY KEY (order_id, product_id),
        FOREIGN KEY (order_id) REFERENCES orders (order_id),
        FOREIGN KEY (product_id) REFERENCES products (product_id)
    );

CREATE TABLE
    payments (
        payment_id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        payment_type VARCHAR(50),
        payment_status VARCHAR(50),
        payment_date DATETIME,
        createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
        createdUser INT not null default 0,
        modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        modUser INT not null default 0,
        lockstate int default 0,
        FOREIGN KEY (order_id) REFERENCES orders (order_id)
    );

-- Testdaten
INSERT INTO
    users (username, password, email)
VALUES
    (
        'alice',
        'gafitanu',
        'alice.gafitanu@edu.fh-wien.ac.at'
    ),
    (
        'stefanie',
        'bilgeri',
        'stefanie.bilgeri@edu.fh-wien.ac.at'
    ),
    (
        'georg',
        'hollenstein',
        'georg.hollenstein@edu.fh-wien.ac.at'
    ),
    (
        'karolina',
        'sarota',
        'karolina.sarota@edu.fh-wien.ac.at'
    ),
    (
        'roland',
        'pregernig',
        'roland.pregernig@edu.fh-wien.ac.at'
    ),
    (
        'tobias',
        'luger',
        'tobias.luger@edu.fh-wien.ac.at'
    );

INSERT INTO
    categories (name)
VALUES
    ('Tee'),
    ('Zubehör');

INSERT INTO
    products (name, description, tax, price, stock, image)
VALUES
    (
        'Grüner Tee',
        'Erfrischender und belebender grüner Tee.',
        20,
        4.99,
        100,
        '/assets/gruener-tee.jpeg'
    ),
    (
        'Schwarzer Tee',
        'Kräftiger und aromatischer schwarzer Tee.',
        20,
        5.99,
        100,
        '/assets/schwarzer-tee.jpeg'
    ),
    (
        'Weißer Tee',
        'Milder und fein aromatischer weißer Tee.',
        20,
        6.50,
        100,
        '/assets/weisser-tee.jpeg'
    ),
    (
        'Oolong Tee',
        'Traditioneller, halbfermentierter Tee mit einem einzigartigen Geschmack.',
        20,
        7.00,
        100,
        '/assets/oolong-tee.jpeg'
    ),
    (
        'Pfefferminztee',
        'Erfrischender Tee mit dem kühlenden Geschmack von Pfefferminze.',
        7,
        3.99,
        100,
        '/assets/pfefferminztee.jpeg'
    ),
    (
        'Kamillentee',
        'Beruhigender und entspannender Tee mit Kamillenblüten.',
        7,
        3.50,
        100,
        '/assets/kamillentee.jpeg'
    ),
    (
        'Früchtetee',
        'Süßer Tee aus einer Mischung verschiedener Früchte.',
        20,
        4.25,
        100,
        '/assets/fruechtetee.jpeg'
    ),
    (
        'Chai Tee',
        'Würziger Tee mit einer Mischung aus Schwarztee und verschiedenen Gewürzen.',
        20,
        5.75,
        100,
        '/assets/chai-tee.jpeg'
    ),
    (
        'Mate Tee',
        'Belebender Tee aus den Blättern des Mate-Strauchs.',
        20,
        4.95,
        100,
        '/assets/mate-tee.jpeg'
    ),
    (
        'Earl Grey',
        'Berühmter Schwarztee aromatisiert mit Bergamotte-Öl.',
        20,
        5.50,
        100,
        '/assets/earl-grey.jpeg'
    );

INSERT INTO
    product_categories (product_id, category_id)
VALUES
    (1, 1),
    (2, 1),
    (3, 1),
    (4, 1),
    (5, 1),
    (6, 1),
    (7, 1),
    (8, 1),
    (9, 1),
    (10, 1);

INSERT INTO
    carts (user_id)
VALUES
    (1),
    (2);

INSERT INTO
    cart_items (cart_id, product_id, quantity)
VALUES
    (1, 1, 2),
    (1, 3, 1),
    (2, 2, 1);

INSERT INTO
    addresses (
        user_id,
        address_type,
        street,
        city,
        state,
        postal_code,
        country
    )
VALUES
    (
        1,
        'shipping',
        '123 Tea Lane',
        'Teatown',
        'Teastate',
        '12345',
        'Tealand'
    ),
    (
        1,
        'billing',
        '456 Tea Blvd',
        'Teacity',
        'Teastate',
        '54321',
        'Tealand'
    );

INSERT INTO
    orders (
        address_id_delivery,
        address_id_billing,
        user_id,
        order_date,
        total_price
    )
VALUES
    (1, 2, 1, NOW (), 28.96);

INSERT INTO
    order_items (order_id, product_id, quantity, price, tax)
VALUES
    (1, 1, 1, 5.99, 0.20),
    (1, 2, 1, 6.49, 0.20),
    (1, 3, 1, 4.99, 0.10);

INSERT INTO
    payments (
        order_id,
        payment_type,
        payment_status,
        payment_date
    )
VALUES
    (1, 'Credit Card', 'Completed', NOW ());