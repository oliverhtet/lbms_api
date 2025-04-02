
```markdown type="code" file="FLOWCHART.md"
# Library Management System Flow

## System Overview

The Library Book Management System is designed to manage books, authors, categories, and borrowings in a library. The system has three main user roles: Admin, Librarian, and Member, each with different permissions.

## User Roles and Permissions

### Admin
- Full access to all resources
- Can manage users, roles, and permissions

### Librarian
- Can manage books, authors, categories, and borrowings
- Cannot manage users or system settings

### Member
- Can view books and their details
- Can view their borrowing history
- Cannot add, edit, or delete resources

## Main Processes

### Authentication Flow

1. User registers or logs in
2. System validates credentials
3. System generates access token
4. User uses token for subsequent requests

### Book Management Flow

1. Admin/Librarian adds new book with details
2. System validates book information
3. Book is added to the database
4. Book becomes available for borrowing

### Borrowing Process

1. Member requests to borrow a book
2. Librarian creates a borrowing record
3. System checks book availability
4. System updates book available copies
5. System sets due date for return

### Return Process

1. Member returns book to library
2. Librarian marks book as returned
3. System updates borrowing status
4. System updates book available copies
5. System calculates any late fees (if applicable)

## Database Relationships

- A Book can have multiple Authors
- A Book can belong to multiple Categories
- A User can have multiple Borrowings
- A Book can have multiple Borrowings (at different times)

## API Flow

1. Client sends request with authentication token
2. Middleware validates token and permissions
3. Controller processes the request
4. Repository/Model interacts with database
5. Response is formatted and returned to client

## Error Handling

1. Validation errors are returned with 422 status code
2. Authentication errors are returned with 401 status code
3. Authorization errors are returned with 403 status code
4. Resource not found errors are returned with 404 status code
5. Server errors are returned with 500 status code