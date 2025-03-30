CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title ENUM('admin', 'customer') NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(100)  NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id int ,
    FOREIGN key (role_id) references roles(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE customers (
    id INT PRIMARY KEY,
    address VARCHAR(255) NULL, 
    phone VARCHAR(20) NULL, 
    user_id int, 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);


CREATE TABLE user_permissions (
    user_id INT,
    permission_id INT,
    PRIMARY KEY (user_id, permission_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

CREATE TABLE categorys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    icon VARCHAR(255) 
);

CREATE TABLE Products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    stock int,
    category_id INT,
    base_price DECIMAL(10, 2) NOT NULL,
    isAvailable bool ,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP null,
    FOREIGN KEY (category_id) REFERENCES categorys(id)
);

CREATE TABLE Product_sizes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    size_name VARCHAR(50) NOT NULL,
    price_adjustment DECIMAL(10, 2) DEFAULT 0.00,
    stock_quantity INT NOT NULL DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES Products(id) ON DELETE CASCADE
);

CREATE TABLE Product_colors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    color_name VARCHAR(50) NOT NULL,
    color_code VARCHAR(50) not null ,
    price_adjustment DECIMAL(10, 2) DEFAULT 0.00,
    stock_quantity INT NOT NULL DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES Products(id) ON DELETE CASCADE
);

CREATE TABLE Product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT false,
    size_id INT,
    FOREIGN KEY (product_id) REFERENCES Products(id) ON DELETE CASCADE,
    FOREIGN KEY (size_id) REFERENCES Product_sizes(id) ON DELETE SET NULL
);



CREATE TABLE `carts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL, 
  `session_id` varchar(255) DEFAULT NULL, 
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `session_id` (`session_id`),
    CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL

)
CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `selected_color` varchar(50) DEFAULT NULL,
  `selected_size` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL, 
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cart_id` (`cart_id`),
  KEY `product_id` (`product_id`),
  FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `Products` (`id`)
) 