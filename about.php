<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - The Gallery Cafe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <style>
        .hero {
            background-image: url('test1.jpg');
            background-size: cover;
            background-position: center;
            height: 40vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
            margin-top: 20px;
            border-radius: 5px;
            position: relative;
            overflow: hidden;
        }
        .hero h1 {
            font-size: 2.5rem;
            margin: 0;
            text-align: center;
        }
        .hero::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .section {
            padding: 40px 0;
            background-color: #f8f9fa;
        }
        .section h2 {
            font-size: 2rem;
            margin-bottom: 15px;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
        }
        .section .content {
            margin: 0 auto;
            max-width: 800px;
            text-align: center;
        }
        .section .content p {
            font-size: 1rem;
            line-height: 1.6;
            color: #555;
        }
        .about-section, .mission-section, .history-section, .community-section, .testimonials-section, .choose-us-section {
            background-color: #ffffff;
        }
        .choose-us-section {
            background-color: #f5f5f5;
        }
        .choose-us-section .content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .choose-us-section .item {
            flex: 1 1 30%;
            margin: 10px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            background-color: #ffffff;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }
        .choose-us-section .item img {
            width: 100px;
            height: 100px;
            margin-bottom: 10px;
        }
        .team-section {
            padding: 40px 0;
            background-color: #ffffff;
        }
        .team-section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
        }
        .team-section .member {
            margin-bottom: 20px;
            text-align: center;
        }
        .team-section .member img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }
        .team-section .member h5 {
            margin-top: 10px;
            font-size: 1.1rem;
            color: #333;
        }
        .team-section .member p {
            font-size: 0.9rem;
            color: #666;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 15px 0;
            text-align: center;
        }
        .footer p {
            margin: 0;
        }
        .contact-us {
            background-color: #f8f9fa;
            padding: 40px 0;
            text-align: center;
        }
        .contact-us h2 {
            font-size: 2rem;
            margin-bottom: 15px;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
        }
        .contact-us .contact-info {
            max-width: 800px;
            margin: 0 auto;
        }
        .contact-us .contact-info p {
            font-size: 1rem;
            line-height: 1.6;
            color: #555;
        }
        .contact-us .contact-info a {
            color: #007bff;
            text-decoration: none;
        }
        .contact-us .contact-info a:hover {
            text-decoration: underline;
        }
        .contact-us .social-links {
            margin-top: 20px;
        }
        .contact-us .social-links a {
            font-size: 1.5rem;
            color: #333;
            margin: 0 10px;
            text-decoration: none;
        }
        .contact-us .social-links a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <?php include("nav.php"); ?> <!-- Include navigation -->

    <div class="hero">
        <div class="hero-content">
            <h1>About Us</h1>
        </div>
    </div>

    <div class="section about-section">
        <div class="container">
            <div class="content">
                <h2>Welcome to The Gallery Cafe</h2>
                <p>Since 2010, we’ve been serving delicious food in a cozy, art-filled environment. Enjoy our fresh, locally sourced dishes while appreciating the work of local artists.</p>
                <p>From breakfast to dinner, our menu offers something for everyone. Join us and experience great food and community spirit.</p>
            </div>
        </div>
    </div>

    <div class="section mission-section">
        <div class="container">
            <div class="content">
                <h2>Our Mission</h2>
                <p>We aim to provide a warm, welcoming space with exceptional food made from fresh, local ingredients. We support local artists and engage with our community through various events.</p>
            </div>
        </div>
    </div>

    <div class="section history-section">
        <div class="container">
            <div class="content">
                <h2>Our Story</h2>
                <p>Founded in 2010, The Gallery Cafe started as a small neighborhood spot and has grown into a beloved destination known for its unique blend of food and art.</p>
            </div>
        </div>
    </div>

    <div class="section community-section">
        <div class="container">
            <div class="content">
                <h2>Community Focus</h2>
                <p>We’re committed to supporting local causes and fostering community connections through events and partnerships.</p>
            </div>
        </div>
    </div>

    <div class="section testimonials-section">
        <div class="container">
            <div class="content">
                <h2>What Guests Say</h2>
                <div class="row">
                    <div class="col-md-4">
                        <blockquote class="blockquote">
                            <p class="mb-0">"A fantastic spot with great food and local art!"</p>
                            <footer class="blockquote-footer">Sarah Lee <cite title="Source Title">Local Artist</cite></footer>
                        </blockquote>
                    </div>
                    <div class="col-md-4">
                        <blockquote class="blockquote">
                            <p class="mb-0">"Love the brunch here. Friendly staff and a cozy atmosphere."</p>
                            <footer class="blockquote-footer">Mark Johnson <cite title="Source Title">Regular Customer</cite></footer>
                        </blockquote>
                    </div>
                    <div class="col-md-4">
                        <blockquote class="blockquote">
                            <p class="mb-0">"The Gallery Cafe is my go-to for a relaxing meal."</p>
                            <footer class="blockquote-footer">Emily Carter <cite title="Source Title">Local Art Enthusiast</cite></footer>
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section choose-us-section">
        <div class="container">
            <h2>Why Choose Us?</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="item">
                        <img src="fresh_item1.jpg" alt="Fresh Ingredients">
                        <h5>Fresh Ingredients</h5>
                        <p>High-quality, locally-sourced ingredients.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item">
                        <img src="art_gallery.jpg" alt="Art Gallery">
                        <h5>Local Art</h5>
                        <p>Featuring artwork from local artists.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item">
                        <img src="cozy_atmosphere.jpg" alt="Cozy Atmosphere">
                        <h5>Cozy Atmosphere</h5>
                        <p>A welcoming environment for all.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="section team-section">
        <div class="container">
            <h2>Meet Our Team</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="member">
                        <img src="8ffud63c.png" alt="John Doe">
                        <h5>Ratnapala</h5>
                        <p>Owner</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="member">
                        <img src="sa8oy6tp.png" alt="Jane Smith">
                        <h5>Jane Smith</h5>
                        <p>Manager</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="member">
                        <img src="qnqkd8jk.png" alt="Michael Brown">
                        <h5>Michael Brown</h5>
                        <p>Head Chef</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-us">
        <div class="container">
            <h2>Contact Us</h2>
            <div class="contact-info">
                <p><strong>Email:</strong> <a href="mailto:info@thegallerycafe.com">info@thegallerycafe.com</a></p>
                <p><strong>Phone:</strong> +94 123 456 789</p>
                <p><strong>Follow Us:</strong></p>
                <div class="social-links">
                    <a href="https://www.facebook.com/TheGalleryCafe" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/TheGalleryCafe" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://twitter.com/TheGalleryCafe" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date("Y"); ?> The Gallery Cafe. All rights reserved.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-oBqDVmMz4fnFO9ktdAd6XbzzClGd0l8MNz2RnC65hdPCTt5/ZK5DZpJQjU36pP2j" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-pj2MoOa2jW9v8eG5BdoxE6P02b2LzKjQjOMVf0fQz8eD5nF/jRt7bBR6aFRaFcuZ" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
