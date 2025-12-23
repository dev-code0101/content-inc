
# Project Documentation

## Overview

This project is a Python script designed to scrape blog articles from a specified website and send the collected data to a local API. The script uses the requests library for HTTP requests and BeautifulSoup for parsing HTML content.
Features

    Scrapes blog articles from multiple pages (1 to 15).
    Extracts article titles, content, tags, and links.
    Sends the collected articles to a specified API endpoint.
    Includes testing functionality that can be run via command line.

Requirements

The project requires the following Python libraries:

    requests
    beautifulsoup4

To install these dependencies, use the provided requirements.txt file:

bash

pip install -r requirements.txt

Project Structure

Code

/ingest_engine
│
├── run.py   # Main script for scraping and API interaction
├── tests.py         # Unit tests for the script
└── requirements.txt  # Required dependencies
└── docs.md          # Project documentation

Usage

    Run the Script: To start scraping and send articles to the API, run the following command:

bash

python run.py

Run Tests: To execute unit tests, use the --test flag:

bash

    python run.py --test

Script Details
scrape_blog_articles()

    Description: Scrapes blog articles from the specified website.
    Returns: A list of dictionaries, each containing article data (title, content, tags, etc.).

send_articles_to_api(articles, api_url)

    Parameters:
        articles: A list of article dictionaries to be sent to the API.
        api_url: The API endpoint (default: http://127.0.0.1:8000/api/posts).
    Returns: Two lists, one for successful posts and another for failed posts.

Command Line Arguments

    --test: A flag to run unit tests located in tests.py.
