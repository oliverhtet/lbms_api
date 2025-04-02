- Admin: [admin@example.com](mailto:admin@example.com) / password
- Librarian: [librarian@example.com](mailto:librarian@example.com) / password
- Member: [member@example.com](mailto:member@example.com) / password


## API Documentation

### Authentication Endpoints

| Method | Endpoint | Description
|-----|-----|-----
| POST | /api/register | Register a new user
| POST | /api/login | Login and get access token
| POST | /api/logout | Logout and invalidate token
| GET | /api/user | Get authenticated user details


### Book Endpoints

| Method | Endpoint | Description
|-----|-----|-----
| GET | /api/books | Get all books
| POST | /api/books | Create a new book
| GET | /api/books/id | Get book details
| PUT | /api/books/id | Update a book
| DELETE | /api/books/id | Delete a book


### Author Endpoints

| Method | Endpoint | Description
|-----|-----|-----
| GET | /api/authors | Get all authors
| POST | /api/authors | Create a new author
| GET | /api/authors/id | Get author details
| PUT | /api/authors/id | Update an author
| DELETE | /api/authors/id | Delete an author


### Category Endpoints

| Method | Endpoint | Description
|-----|-----|-----
| GET | /api/categories | Get all categories
| POST | /api/categories | Create a new category
| GET | /api/categories/id | Get category details
| PUT | /api/categories/id | Update a category
| DELETE | /api/categories/id | Delete a category


### Borrowing Endpoints

| Method | Endpoint | Description
|-----|-----|-----
| GET | /api/borrowings | Get all borrowings
| POST | /api/borrowings | Create a new borrowing
| GET | /api/borrowings/id | Get borrowing details
| PUT | /api/borrowings/id | Update a borrowing
| DELETE | /api/borrowings/id | Delete a borrowing
| POST | /api/borrowings/id/return | Return a borrowed book
| GET | /api/my-borrowings | Get borrowings for authenticated user


### User Endpoints (Admin only)

| Method | Endpoint | Description
|-----|-----|-----
| GET | /api/users | Get all users
| POST | /api/users | Create a new user
| GET | /api/users/id | Get user details
| PUT | /api/users/id | Update a user
| DELETE | /api/users/id | Delete a user
| GET | /api/users/roles | Get all available roles