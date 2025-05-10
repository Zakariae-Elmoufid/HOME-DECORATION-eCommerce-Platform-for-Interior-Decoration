# HOME-DECORATION-eCommerce-Platform-for-Interior-Decoration

Home Decoration is an eCommerce platform designed for selling interior decoration products. It offers a seamless shopping experience, allowing users to browse, purchase, and explore a wide range of decorative items to enhance their living spaces.

---

## Table of Contents
1. [Features](#features)
2. [Installation](#installation)
3. [Environment Setup](#environment-setup)
4. [Running the Project](#running-the-project)
5. [Project Structure](#project-structure)
6. [Main Functionalities](#main-functionalities)

---

## Features
- User authentication (registration, login, logout)
- Product browsing by category
- Shopping cart and checkout system
- Admin dashboard for managing products, categories, and orders
- Order management and tracking
- Payment integration with Stripe
- Wishlist functionality
- Role-based access control (Admin and Customer)

---

## Installation

### Prerequisites
Ensure you have the following installed on your system:
- Docker
- Docker Compose

### Steps
1. Clone the repository:
   ```bash
   git clone https://github.com/your-repo/HOME-DECORATION-eCommerce-Platform.git
   cd HOME-DECORATION-eCommerce-Platform

2. Build and start the Docker containers:
   ````docker-compose up -d --build

3. To stop the containers:
   http://localhost:8080
    ````docker-compose down
Project Structure
project/
├── app/
│   ├── Config/          # Database configuration
│   ├── Controllers/     # Application controllers
│   ├── Core/            # Core framework components
│   ├── Models/          # Database models
│   ├── Repositories/    # Data access layer
│   ├── Services/        # Business logic
│   ├── Views/           # Twig templates
│   └── router/          # Application routes
├── public/
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript files
│   └── index.php        # Entry point
├── [docker-compose.yml](http://_vscodecontentref_/0)   # Docker configuration
├── Dockerfile           # Dockerfile for building the app
└── [README.md](http://_vscodecontentref_/1)            # Project documentation

GitHub Copilot
Build and start the Docker containers:
Environment Setup
Copy the .env.example file to .env:

Update the .env file with your environment variables (e.g., database credentials, Stripe API keys, etc.): <vscode_annotation details='%5B%7B%22title%22%3A%22hardcoded-credentials%22%2C%22description%22%3A%22Embedding%20credentials%20in%20source%20code%20risks%20unauthorized%20access%22%7D%5D'> ``` </vscode_annotation> DB_HOST=db DB_USER=root DB_PASSWORD=yourpassword DB_NAME=decoration STRIPE_API_KEY=your_stripe_api_key

Running the Project
Start the Docker containers:

Access the application:

Frontend: http://localhost:8080
Backend API (if applicable): http://localhost:8000
To stop the containers:

Project Structure
Main Functionalities
Customer Features
Product Browsing: Customers can browse products by category or search for specific items.
Shopping Cart: Add, update, or remove items from the cart.
Checkout: Place orders with shipping and payment details.
Wishlist: Save favorite products for later.
Order Tracking: View order history and details.
Admin Features
Dashboard: View sales data and popular products.
Product Management: Add, edit, or delete products.
Category Management: Manage product categories.
Order Management: View and update order statuses.
Access Control: Manage admin roles and permissions.
Additional Notes
Payment Integration: The project uses Stripe for payment processing. Ensure you configure the Stripe API key in the .env file.
Session Management: Sessions are handled using PHP's native session management.
Twig Templates: The frontend uses Twig for rendering views.
For any issues or contributions, feel free to open a pull request or contact the repository maintainer. ```