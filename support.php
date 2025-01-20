<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #616cbb, #748ac7);
            color: #fff;

            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Header Styles */
        .header {
            width: 100%;
            max-width: 1200px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #4C57A7;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: white;
            border-radius: 10px;
        }

        .header img {
            max-width: 100px;
        }
        
        .date-time {
            font-size: 16px;
            font-weight: bold;
            color: white;
            margin-top: 5px; /* Added margin to move it down a bit */
        }

        .logout-settings-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-button {
            padding: 10px 20px;
            background-color: white;
            color: #4C57A7;
            border: 1px solid #4C57A7;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #E2E8F0;
        }
	
	
	
	/* Button Styling */
	.settings-button {
	    background: none;
	    border: none;
	    cursor: pointer;
	    display: flex;
	    flex-direction: column;
	    justify-content: space-between;
	    align-items: center;
	    width: 50px;
	    height: 50px; 
	    padding: 12px;
	    border-radius: 50%;
	    transition: background-color 0.3s ease;
	    background-color: white;

	}



	/* Hover Effect for the Button */
	.settings-button:hover {
	    background-color: rgba(0, 0, 0, 0.1);
	}


        /* Fullscreen overlay */
    .settings-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        display: none;
        justify-content: space-between; /* Divides left and right sections */
        padding: 20px;
        z-index: 1000;
        color: white;
    }

 /* Settings Menu (Left Section) */
	.settings-menu {
	    flex: 1; /* Left section takes 30% */
	    max-width: 20%; /* Optional: Restrict max width */
	    /*background: linear-gradient(to left, #748ac7, #4C57A7);*/
	    background: linear-gradient(to right, #616cbb, #748ac7);
	    padding: 30px;
	    border-radius: 10px;
	    font-size: 24px;
	    box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.5);
	}

	.settings-menu h2 {
	    color: #D1D9F1;
	    margin-top: 0;
	    font-weight: bold;
	    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
	}

	.settings-menu ul {
	    list-style: none;
	    padding: 0;
	    margin: 0;
	}

	.settings-menu li {
	    margin-bottom: 15px;
	}

	.settings-menu a {
	    text-decoration: none;
	    color: #E2E8F0;
	    font-size: 20px;
	    padding: 5px;
	    transition: color 0.3s, background-color 0.3s;
	    border-radius: 5px;
	}

	.settings-menu a:hover {
	    color: #2C3E99;
	    background-color: #D1D9F1;
	}
	
	
	
	/* Team Section */
	.team-section {
	    flex: 2;
	    background: linear-gradient(to right, #616cbb, #748ac7);
	    padding: 20px;
	    border-radius: 10px;
	    box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.5);
	    color: #ffffff;
	    overflow-y: auto;
	    display: grid; /* Use grid to align team members */
	    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Create responsive grid */
	    gap: 20px; /* Space between the team members */
	}

        .team-section h1 {
            font-size: 52px;
            margin-bottom: 100px;
            color: white;
            font-family: 'Yatra One', cursive;
        }

	.team-member {
	    transition: transform 0.3s ease-in-out;
	    text-decoration: none;
	    color: inherit;
	    background: white;
	    padding: 20px;
	    border-radius: 10px;
	    position: relative;
	    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
	    display: flex;
	    flex-direction: column;
	    justify-content: center;
	    align-items: center;
	}

        .team-member:hover {
            transform: scale(1.05);
        }

        .team-member img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }

        .team-member h3 {
            color: #4C57A7;
            font-size: 26px;
            margin-top: 20px;
        }

        .team-member p {
            color: #4C57A7;
            font-size: 14px;
        }
        
        
        

	/* Close Button */
	.close-settings {
	    background: none;
	    border: none;
	    font-size: 18px;
	    color: #E2E8F0;
	    cursor: pointer;
	    margin-bottom: 20px;
	    border-radius: 5px;
	    transition: color 0.3s, background-color 0.3s;
	}

	.close-settings:hover {

	    color: #2C3E99;
	    background-color: #D1D9F1;
	}
	
	
	
	/* Go Back Button Styles */
.go-back-button {
    padding: 10px 20px;
    background-color: #4C57A7;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 20px;
}

.go-back-button:hover {
    background-color: #3B4A8A;
}

	
	
        /* Container */
        .container {
            margin: 100px auto;
            padding: 20px;
            max-width: 1200px;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        /* Info Section */
        .info-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Add transition */
        }

        .info-section:hover {
            transform: scale(1.05); /* Slightly increase size on hover */
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* Increase shadow on hover */
        }
        .combined-box:hover {
            transform: scale(1.05); /* Slightly increase size on hover */
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* Increase shadow on hover */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Add smooth transition */
        }

        .info-section.reverse {
            flex-direction: row-reverse;
        }

        /* Text Box */
        .text-box {
            flex: 1;
            color: #4C57A7;
            font-size: 18px;
        }

        .text-box h2 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #4C57A7;
        }

        .text-box p, .text-box ul {
            line-height: 1.8;
        }

        /* Combined White Box */
        .combined-box {
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Blue Box Inside the Combined White Box */
        .blue-box {
            background-color: #2E4A7D; /* Darker Blue */
            color: white;
            border-radius: 10px;
            padding: 80px;
            padding-bottom: px;
            margin: 40px 0px; /* Space around the blue box */

             /* Box width */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 18px;
            line-height: 1.9;
            overflow: hidden; /* Ensure smooth expansion */
            word-wrap: break-word; /* Allow long words to break */

        }
        .blue-box h2 {
            font-size: 24px;
            margin-bottom: 15px;
            }

        .blue-box h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .blue-box ul {
            list-style: none;
            padding: 0;
        }

        .blue-box ul li {
            margin: 10px 0;
        }

        /* Connections Section */
        .connections-section {
            margin-top: 60px;
             /* Larger gap before Connections heading */
        }

        .connections-section h2 {
            margin-bottom: 20px;
        }

        .connection-graphic {
            display:flex;
            justify-content:center
            align-items:center
            text-align: center;
            margin: 0px auto;
        }

        .connection-graphic img {
            max-width: 100%;
            height: auto;
            display:block;
        }
        
        
        /* Footer Styles */
        .footer {
            font-size: 14px;
            text-align: center;
            margin-top: auto;
        }

        footer hr {
            border: 0;
            border-top: 1px solid white;
            margin-bottom: 10px;
        }
        
        
        
        @media (max-width: 768px) {
	    .header {
		flex-direction: column;
		align-items: flex-start;
	    }

	    .header img {
		max-width: 80px;
	    }

	    .settings-overlay {
		flex-direction: column; /* Stack the settings and about sections */
		gap: 20px;
	    }
	    
	    .team-section {
		grid-template-columns: repeat(2, 1fr); /* Two items per row on larger screens */
	    }

	    .settings-menu, .about-us {
		max-width: 100%; /* Use full width for smaller screens */
		flex: none;
	    }

	    .bar-chart {
		height: 150px; /* Adjust chart height */
	    }

	    .nav-link {
		font-size: 16px;
		padding: 8px 10px;
	    }
	}

        
        
        /* Active overlay display */
    .settings-overlay.active {
        display: flex; /* Flex layout is only applied when active */
    }
        /* FAQ Section */
    .faq-section {
        background-color: white;
        color: #4C57A7;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: 40px;
        text-align: left;
        align-items: center;
        justify-content: center;
    }

    .faq-section h2 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #4C57A7;
    }

    .question {
        font-size: 18px;
        font-weight: bold;
        margin: 15px 0;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .question:hover {
        color: #2C3E99;
    }

    .answer {
        display: none;
        font-size: 16px;
        margin: 5px 0 20px 0;
        padding-left: 20px;
        color: #333;
        line-height: 1.5;
    }

    .github-button {
        background-color: #4C57A7;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 20px;
        align-self: center;
    }

    .github-button:hover {
        background-color: #3B4A8A;
    }     
    </style>
</head>
<body>
    <div class="header">
        <img src="images/sleep.png" alt="Sleep Med Logo">
        <div class="date-time" id="currentDateTime"></div>
        <div class="logout-settings-container">
            <button id="logout-btn" class="logout-button">Logout</button>
            <button id="settings-btn" class="settings-button">⋮</button>
        </div>
    </div>

    <script>
        // Update the date and time dynamically
        function updateDateTime() {
            const date = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const formattedDateTime = date.toLocaleString('en-US', options);
            document.getElementById('currentDateTime').textContent = formattedDateTime;
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>

    <!-- Settings Overlay -->
    <div class="settings-overlay" id="settings-overlay">
        <div class="settings-menu">
            <button class="close-settings" id="close-settings">Close ✕</button>
            <h2>Settings</h2>
            <ul>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="delete-account.php">Delete Account</a></li>
            <li><a href="#">Language</a></li>
            <li><a href="support.html">Support</a></li>
            <li><a href="app-information.php">App Information</a></li>
            </ul>
        </div>

        <div class="team-section">
            <h1>Our Team</h1>
                <!-- Team Member 1 -->
                <a href="https://github.com/safrinfaizz" target="_blank" class="team-member">
                    <img src="images/safreena.jpg" alt="Safreena">
                    <h3>Safreena</h3>
                    <p>Front-End Developer</p>
                    <p>"As a health informatics student interested in building websites and working with data, I contributed to the Sleep Monitor project by developing the front-end. For me, front-end development is where creativity and technology meet to solve problems and inspire users."</p>
                </a>

                <!-- Team Member 2 -->
                <a href="https://github.com/SenaDok" target="_blank" class="team-member">
                    <img src="images/sena.jpg" alt="Sena">
                    <h3>Sena</h3>
                    <p>Front-End Developer</p>
                    <p>“A healthy body holds a healthy mind and soul, and that's what we should strive to have and share”</p>
                </a>

                <!-- Team Member 3 -->
                <a href="https://github.com/AngelinaNSS" target="_blank" class="team-member">
                    <img src="images/angelina.jpg" alt="Angelina">
                    <h3>Angelina</h3>
                    <p>Front-End Developer</p>
                    <p>"I’m a health informatics student with a passion for using tech to improve healthcare. With this Sleep Monitor project, I aim to help people track and improve their sleep, especially for those working late shifts, so they can feel better and perform their best."</p>
                </a>

                <!-- Team Member 4 -->
                <a href="https://github.com/kseniiavi" target="_blank" class="team-member">
                    <img src="images/kseniia.jpg" alt="Kseniia">
                    <h3>Kseniia</h3>
                    <p>Back-End Developer</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Est quaerat tempora.</p>
                </a>

                <!-- Team Member 5 -->
                <a href="https://github.com/Maryem29" target="_blank" class="team-member">
                    <img src="images/maryem.jpg" alt="Maryem">
                    <h3>Maryem</h3>
                    <p>Back-End Developer</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Est quaerat tempora.</p>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Open settings overlay
        const settingsBtn = document.getElementById("settings-btn");
        const settingsOverlay = document.getElementById("settings-overlay");
        const closeSettings = document.getElementById("close-settings");

        settingsBtn.addEventListener("click", () => {
            settingsOverlay.classList.add("active");
        });

        closeSettings.addEventListener("click", () => {
            settingsOverlay.classList.remove("active");
        });
    </script>

    <!-- Main Content for Support -->
     <div>
        <button onclick="goBack()" class="go-back-button">Go Back</button>
    </div>
    <div class="container">
        <div class="info-section">
            <div class="text-box">
                <h2>Support</h2>
                <p>Welcome to the support section! Here, you'll find helpful information about the app and how to make the most of it.</p>
                <p>If you have any issues, feel free to reach out to our team for assistance.</p>
            </div>
        </div>

        <div class="faq-section">
            <h2>Frequently Asked Questions</h2>
        
            <div class="question" onclick="toggleAnswer(1)">
                Q: How can I switch accounts?
            </div>
            <div class="answer" id="answer1">
                A: You can switch accounts by going to the settings page and clicking on "Switch Account."
            </div>
        
            <div class="question" onclick="toggleAnswer(2)">
                Q: How can I reset my password or update personal information?
            </div>
            <div class="answer" id="answer2">
                A: Go to the profile page, where you can update your personal information or reset your password.
            </div>
        
            <div class="question" onclick="toggleAnswer(3)">
                Q: How can I delete my account?
            </div>
            <div class="answer" id="answer3">
                A: Navigate to the settings page and select "Delete Account."
            </div>
        
            <div class="question" onclick="toggleAnswer(4)">
                Q: Where can I view real-time data?
            </div>
            <div class="answer" id="answer4">
                A: Real-time data is available on the home page.
            </div>
        
            <div class="question" onclick="toggleAnswer(5)">
                Q: Where can I find my weekly or monthly reports?
            </div>
            <div class="answer" id="answer5">
                A: Reports are available in the "Reports" page.
            </div>
        
            <div class="question" onclick="toggleAnswer(6)">
                Q: How can I learn to use the app effectively?
            </div>
            <div class="answer" id="answer6">
                A: Refer to the "App Information" page for guidance on using the app effectively.
            </div>
        
            <div class="question" onclick="toggleAnswer(7)">
                Q: How do I set alerts?
            </div>
            <div class="answer" id="answer7">
                A: Alerts can be set from the "Alerts" page in the app.
            </div>
        
            <button class="github-button" onclick="window.open('https://github.com/kseniiavi/Sleep-Monitor')">
                GitHub
            </button>
        </div>

        <div class="blue-box">
            <h2>Contact Support</h2>
            <p>If you need assistance, please contact our support team at <strong>support@sleepmed.com</strong></p>
        </div>

    <script>
        // Function to go back to the previous page
        function goBack() {
            if (document.referrer) {
                window.history.back();
            } else {
                window.location.href = "login.php";
            }
        }
        function toggleAnswer(answerId) {
            // Hide all answers
            const allAnswers = document.querySelectorAll('.answer');
            allAnswers.forEach(answer => answer.style.display = 'none');

            // Show selected answer
            const selectedAnswer = document.getElementById('answer' + answerId);
            selectedAnswer.style.display = 'block';
        }

    </script>
    <footer>
        <hr>
        <p>Sleep Med - All Rights Reserved</p>
    </footer>

</body>
</html>