## Order Management Assignment
 
Steps to set up

- Clone The project
- Run commands
    ```
    composer install
    php artisan key:generate
    copy .env.example .env
    ```
- Set Database configurations in .env
- Run commands
    ```
    php artisan migrate
    php artisan db:seed
    php artisan passport:install(Run oauth key and set in .env)
    ```
Finally, you can test requests in postman with request collection file in apitest folder

For Filtering you can test with get method keys 
- Examples
    ```
    localhost:8000/api/categories?name=blah_blah
    localhost:8000/api/products?category=blah&page=2&per_page=4
    ```
       

