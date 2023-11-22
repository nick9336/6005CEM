<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/6c+X8j5pIo4Cz5Cf3fH8GjHaXG3QJb" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <title>Data Privacy & Terms of Service</title>
    <style>
        
        .container {
            width: 80%;
            margin: 20px auto;
        }
        .accordion {
            background-color: #eee;
            color: #444;
            cursor: pointer;
            padding: 18px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 15px;
            transition: 0.4s;
        }
        .active, .accordion:hover {
            background-color: skyblue;
        }
        .accordion:after {
            content: '\002B';
            color: black;
            font-weight: bold;
            float: right;
            margin-left: 5px;
        }
        .active:after {
            content: "\2212";
        }
        .panel {
            padding: 0 18px;
            background-color: white;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.2s ease-out;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Data Privacy & Terms of Service</h2>
    
    <button class="accordion">Introduction</button>
    <div class="panel">
        <p>Welcome to The Savoury Spoon.We have provided ordering food service and reservation service .The terms and conditions outline the rules have clearly 
            provided that once you have click to agree the term and 
            conditions means to you have agreed  </p>
    </div>

    <button class="accordion"> Data Collection</button>
    <div class="panel">
        <p>When users register on our website, we collect important information to create and manage their accounts effectively. This includes:</p>
        <ul>
        <li><strong>Personal Identification Information:</strong> Name, email address, and contact number to register as user by using our website .</li>
        <li><strong>Password:</strong> For account security .Your Password are encrypted and stored securely.</li>
    </ul>
    <p>All collected data is treated with the utmost confidentiality and is used solely for the purposes of enhancing user experience, account management, and service improvement. 
       </p>
    </div>

    <button class="accordion">Data Sharing and Disclosure</button>
    <div class="panel">
        <p>Our commitment to your privacy provided to how we secured your personal data. We are transparent about the sharing and disclosure of your information.</p>
        <ul>
            <li><strong>Legal Requirements:</strong>We may be required to disclose your information if it is necessary to comply with a legal obligation, such as responding to a court order, a government request, or to establish or protect our legal rights.</li>
        </ul>
    </div>

    <button class="accordion">Data Security</button>
    <div class="panel">
        <p>Our commitments to keeping your data secure and the measures we take to do so to ensure your persnal information in secured and encrypted.</p>
        <ul>
            <li><strong>Encryption:</strong> We have implemented industry-standard encryption technologies when transferring and receiving user data exchanged with our site to protect your personal data.</li>
            <li><strong>Secure Server:</strong> All sensitive data is stored on secure servers that are protected against unauthorized access.</li>
            <li><strong>Data Breach :</strong> In the unlikely event of a data breach, we have procedures in place to act rapidly and comply with all applicable laws regarding reporting.</li>
        </ul>
    </div>

    <button class="accordion">Your Rights</button>
    <div class="panel">
        <p>As a valued user of our service, you have  rights concerning the personal data we collect from you:</p>
        <ul>
            <li><strong>Right to Access:</strong> You have the right to access the personal information we hold about you and to request details on how we process it.</li>
            <li><strong>Right to Update:</strong> You have the right to access the personal information we hold about you and to request update the personal details</li>
        </ul>    
    </div>

    <button class="accordion">Reservation Policy & Ordering Policy</button>
    <div class="panel">
        <p>Our reservation and ordering policies are designed to ensure a seamless and enjoyable experience for our customers:</p>
        <ul>
            <li><strong>Reservation :</strong> When you access to reservation , you will able to check the history reservation .</li>
            <li><strong>Ordering:</strong> For orders placed online, ensure that all checkout details, including dish selections are in accurate.</li>
            <li><strong>Payment:</strong> Payment policies for reservations and orders will be clearly stated during the payment process. The payment method have been list for choose.</li>
        </ul>
    </div>
    

    <a href="register.php" class="btn btn-primary" style="text-decoration: none;padding:10px;color:white;background-color:#444;">Back</a>
   

    

</div>

<script>
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.maxHeight){
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            } 
        });
    }
</script>

</body>
</html>