# Simple Logistics Application

To get started with this project, you'll need to follow these steps:

1. Clone the repository:

   ```bash
   git clone https://github.com/MarkKulUa/simple-logistics-application.git
   cd simple-logistics-application
   ```

2. Install dependencies:
   ```bash
   npm install
   composer install
   ```

3. Env configuration:
   ```bash
   cp .env.example .env
   touch database/database.sqlite
   php artisan migrate --seed
   php artisan key:generate
   ```
   
4. Start the development server:
   ```bash
   npm run dev
   php artisan serve
   ```

5. Open link in browser http://localhost:5173/

P.s. After migration, the seeder created a test user with an email "test@example.com" and password "password".
You can use them to check if authorization works.
