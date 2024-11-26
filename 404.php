<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f2f2f2; /* Light gray background */
            padding-top: 80px;
            font-family: 'Arial', sans-serif;
        }

        .error-container {
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 2px solid #ed8383; /* Border in the theme color */
        }

        .error-container h1 {
            font-size: 5rem;
            color: #ed8383; /* Use theme color */
        }

        .error-container p {
            font-size: 1.3rem;
            color: #555;
            margin-bottom: 20px;
        }

        .error-container a {
            font-size: 1.1rem;
            text-decoration: none;
            color: #fff;
            background-color: #ed8383; /* Theme color for the button */
            padding: 10px 30px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .error-container a:hover {
            background-color: #d06c6c; /* Slightly darker shade on hover */
        }

        .error-container .icon {
            font-size: 6rem;
            color: #ed8383; /* Icon color in the theme color */
            margin-bottom: 20px;
        }

        /* Custom styling for the page when an error occurs */
        .error-container small {
            display: block;
            margin-top: 30px;
            color: #888;
            font-size: 0.9rem;
        }

        /* Go back button styling */
        .go-back-btn {
            font-size: 1.1rem;
            text-decoration: none;
            color: #fff;
            background-color: #f44336; /* Red color for 'Go Back' button */
            padding: 10px 30px;
            border-radius: 5px;
            margin-top: 20px;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .go-back-btn:hover {
            background-color: #e53935; /* Darker red on hover */
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="error-container">
            <div class="icon">
                <i class="bi bi-exclamation-circle"></i>
            </div>
            <h1>404</h1>
            <p>Oops! The page you are looking for could not be found.</p>
            <p>The link may be broken, or the requested token has already been used.</p>

            <!-- Go Back button to login.php -->
            <a href="login.php" class="go-back-btn">Go Back to Login</a>
            
            <br>
            <small>&copy; 2024 Auxiliary System</small>
        </div>
    </div>

</body>
</html>
