-- delete the database
DROP DATABASE IF EXISTS fhwien;

-- Create the database
CREATE DATABASE fhwien;

-- Select the newly created database
USE fhwien;

-- Datenomdell für den Tee Shop:
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL unique,
    createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    createdUser INT not null default 0,
    modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    modUser INT not null default 0,
    lockstate int default 0
);

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT null,
    createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    createdUser INT not null default 0,
    modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    modUser INT not null default 0,
    lockstate int default 0
);

CREATE TABLE products (
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

CREATE TABLE product_categories (
    product_category_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    category_id INT,
    createdDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    createdUser INT not null default 0,
    modDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    modUser INT not null default 0,
    lockstate int default 0,
    FOREIGN KEY (product_id) REFERENCES products (product_id),
    FOREIGN KEY (category_id) REFERENCES categories (category_id)
);

CREATE TABLE addresses (
    address_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name varchar(255),
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    company VARCHAR(255),
    address_type varchar(255),
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

CREATE TABLE orders (
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

CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
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
    FOREIGN KEY (order_id) REFERENCES orders (order_id),
    FOREIGN KEY (product_id) REFERENCES products (product_id)
);

CREATE TABLE payments (
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
        FLOOR(RAND () * (200 - 1 + 1)) + 1,
        'assets/gruener-tee.jpeg'
    ),
    (
        'Schwarzer Tee',
        'Kräftiger und aromatischer schwarzer Tee.',
        20,
        5.99,
        FLOOR(RAND () * (200 - 1 + 1)) + 1,
        'assets/schwarzer-tee.jpeg'
    ),
    (
        'Weißer Tee',
        'Milder und fein aromatischer weißer Tee.',
        20,
        6.50,
        FLOOR(RAND () * (200 - 1 + 1)) + 1,
        'assets/weisser-tee.jpeg'
    ),
    (
        'Oolong Tee',
        'Traditioneller, halbfermentierter Tee mit einem einzigartigen Geschmack.',
        20,
        7.00,
        FLOOR(RAND () * (200 - 1 + 1)) + 1,
        'assets/oolong-tee.jpeg'
    ),
    (
        'Pfefferminztee',
        'Erfrischender Tee mit dem kühlenden Geschmack von Pfefferminze.',
        7,
        3.99,
        FLOOR(RAND () * (200 - 1 + 1)) + 1,
        'assets/pfefferminztee.jpeg'
    ),
    (
        'Kamillentee',
        'Beruhigender und entspannender Tee mit Kamillenblüten.',
        7,
        3.50,
        FLOOR(RAND () * (200 - 1 + 1)) + 1,
        'assets/kamillentee.jpeg'
    ),
    (
        'Früchtetee',
        'Süßer Tee aus einer Mischung verschiedener Früchte.',
        20,
        4.25,
        FLOOR(RAND () * (200 - 1 + 1)) + 1,
        'assets/fruechtetee.jpeg'
    ),
    (
        'Chai Tee',
        'Würziger Tee mit einer Mischung aus Schwarztee und verschiedenen Gewürzen.',
        20,
        5.75,
        FLOOR(RAND () * (200 - 1 + 1)) + 1,
        'assets/chai-tee.jpeg'
    ),
    (
        'Mate Tee',
        'Belebender Tee aus den Blättern des Mate-Strauchs.',
        20,
        4.95,
        FLOOR(RAND () * (200 - 1 + 1)) + 1,
        'assets/mate-tee.jpeg'
    ),
    (
        'Earl Grey',
        'Berühmter Schwarztee aromatisiert mit Bergamotte-Öl.',
        20,
        5.50,
        FLOOR(RAND () * (200 - 1 + 1)) + 1,
        'assets/earl-grey.jpeg'
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
    users -- password 1teamCpu@test.at
    (
        firstname,
        lastname,
        password,
        email,
        createdDate,
        createdUser,
        modDate,
        modUser,
        lockstate
    )
VALUES
    (
        'Team',
        'CPU',
        '$2y$10$yl/BRnKACeucvSh4BF1aquxlSpOwZudEibr2OmEEL/d3VF1gPkGN2',
        'teamCpu@test.at',
        '2024-04-28 12:18:34.000',
        0,
        '2024-04-28 12:18:34.000',
        0,
        0
    );

INSERT INTO
    fhwien.addresses (
        name,
        firstname,
        lastname,
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
        'Witwe Polde',
        'Intel',
        'CPU',
        1,
        'shipping',
        'Mohrenbräustraße 1',
        'Dornbirn',
        'Vorarlberg',
        '6850',
        'AT'
    );

INSERT INTO
    fhwien.addresses (
        name,
        firstname,
        lastname,
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
        'Hauptadresse',
        'AMD',
        'CPU',
        1,
        'billing',
        'FH-Wien-Strasse 12',
        'Wien',
        'Wien',
        '1010',
        'AT'
    );