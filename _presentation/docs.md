
# React Frontend — Articles Viewer

Overview

    Small React app that fetches posts from Laravel API (/posts), shows original and updated articles, supports responsive layout, search, and view details.
    Uses Vite + React, Tailwind CSS for styling, Axios for HTTP.

Quick setup

    Create project:
        npm create vite@latest articles-ui -- --template react
        cd articles-ui
    Install:
        npm install axios react-router-dom
        npm install -D tailwindcss postcss autoprefixer
        npx tailwindcss init -p
    Tailwind config (tailwind.config.cjs):
        content: ["./index.html","./src/**/*.{js,jsx}"]
    Add Tailwind directives to src/index.css:
        @tailwind base; @tailwind components; @tailwind utilities;

Config

    Create .env in project root:
        VITE_API_BASE_URL=http://localhost:8000/api

Project structure

    src/
        api/
            postsApi.js
        components/
            Header.jsx
            PostCard.jsx
            PostList.jsx
            PostDetail.jsx
            Spinner.jsx
        pages/
            Home.jsx
            PostPage.jsx
        App.jsx
        main.jsx
        index.css
