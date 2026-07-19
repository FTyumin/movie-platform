# Movie Platform

A social platform for discovering, reviewing, and tracking movies.

## About

Movie Platform is a web application where users can browse a catalog of movies, write and read reviews, and get personalized recommendations based on their taste. Users can track which movies they've seen, mark favorites, build a watchlist, and follow other users to see their activity in a shared feed. New users complete a short quiz about their favorite genres, which helps power tailored recommendations from day one. Administrators can import and manage the movie catalog, sourced from [The Movie Database (TMDB)](https://www.themoviedb.org/).

## Key Features

- **Browse & search** a catalog of movies, actors, and directors
- **Ratings & reviews**, with nested comments on each review
- **Personalized recommendations** based on favorites, ratings, seen movies, and preferred genres
- **Favorites, "Seen," and "Want to Watch"** tracking for every movie
- **Activity feed** showing reviews and updates from users you follow
- **Genre quiz** for new users to set initial recommendation preferences
- **Admin tools** for importing movies from TMDB and managing the catalog

## Built With

- **Backend:** Laravel (PHP), with Livewire for interactive components
- **Database:** MariaDB
- **Frontend:** Blade templates styled with Tailwind CSS and daisyUI, plus Alpine.js for lightweight interactivity
- **Movie data:** [TMDB API](https://www.themoviedb.org/documentation/api)

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
