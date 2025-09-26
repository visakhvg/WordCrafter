Word Crafter
Project Overview
Word Crafter is a web-based word puzzle game built with the Symfony framework. The objective of the game is for players to form as many valid English words as possible from a given set of scrambled letters. Each time a player successfully submits a word,
their score is updated, and the used letters are removed from the available pool. The homepage features a  leaderboard of the 10 top-scoring leaders and words submitted by all players.

Technical Approach
This project was built using Symfony, for the backend logic and routing. The core components include:

Controllers: Handle user requests, from starting a new game to submitting a word. They act as the bridge between the user interface and the application's business logic.

Services: Centralize key functionalities, such as generating the puzzle, validating words against a dictionary, and querying high scores. This approach promotes modular, reusable code.

Entity: Serves as the database layer, abstracting away raw SQL queries. I used Doctrine's repository pattern to manage the  UserSubmission entities, which provides a simple and powerful way to interact with the database.

Twig: The templating engine responsible for rendering the HTML pages.

Logic Implemented: A dictionary text file has been added in the asset , the random word pattern is selected from the dictionary words itself and the validatingis alos done using the same itself,Thereby redcuing the chances of error due to checking with multiple sources


Setup Instructions
Follow these steps to get the Word Crafter project up and running on your local machine.

Step 1: Install Dependencies
Make sure you have PHP and Composer installed on your system.

composer install

Step 2: Configure the Database
Update your .env file with your database credentials (e.g., username, password, and database name).

Step 3: Run Database Migrations
Use the Doctrine command-line tool to create your database and run the migrations to set up the necessary tables for UserSubmission.

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

Step 4: Start the Local Server
Use the Symfony CLI to start the local web server. The application will be accessible at the URL provided in the output.

symfony server:start

Once the server is running, open your web browser and navigate to the local URL to play the game!
http://127.0.0.1:8000/wordcrafter/homepage
