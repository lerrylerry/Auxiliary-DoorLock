<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success | Assessment Submitted</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f2f2f2; /* Light gray background */
            padding-top: 80px;
            font-family: 'Arial', sans-serif;
        }

        .success-container {
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 2px solid #4CAF50; /* Green border for success theme */
        }

        .success-container h1 {
            font-size: 5rem;
            color: #4CAF50; /* Green color for success */
        }

        .success-container p {
            font-size: 1.3rem;
            color: #555;
            margin-bottom: 20px;
        }

        .success-container a {
            font-size: 1.1rem;
            text-decoration: none;
            color: #fff;
            background-color: #4CAF50; /* Green color for button */
            padding: 10px 30px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .success-container a:hover {
            background-color: #388E3C; /* Darker green on hover */
        }

        .success-container .icon {
            font-size: 6rem;
            color: #4CAF50; /* Green icon */
            margin-bottom: 20px;
        }

        .success-container small {
            display: block;
            margin-top: 30px;
            color: #888;
            font-size: 0.9rem;
        }

        /* Style for the back button */
        .back-btn {
            font-size: 1.2rem;
            text-decoration: none;
            color: #fff;
            background-color: #2196F3; /* Blue color for back button */
            padding: 12px 30px;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #1976D2; /* Darker blue on hover */
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="success-container">
            <div class="icon">
                <i class="bi bi-check-circle"></i> <!-- Check icon for success -->
            </div>
            <h1>Success!</h1>
            <p>Your assessment has been successfully submitted and the admin has been notified.</p>
            <br>

            <!-- Back to Login Button -->
            <a href="login.php" class="back-btn">Back to Login</a>

            <br><br>
            <small>&copy; 2024 Auxiliary System</small>
        </div>
    </div>

</body>
</html>
