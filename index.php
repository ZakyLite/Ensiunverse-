<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ensiuniverse</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Montserrat:wght@700&family=Poppins:wght@600&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="index.css">
   <link rel="stylesheet" href="login.css">
   <link rel="stylesheet" href="Register.css">
</head>
<body>
    <header class="header">
        <ul>
          <li id="ChatPic"><i class="fa-regular fa-comment"></i></li>
          <h1 class="HeaderTitle">Ensi<span>universe</span></h1>
          <li class="Home"><a href="#Attractive">Home</a></li>
          <li class="AboutUs"><a href="#About">About</a></li>
          <li class="Contact"><a href="#Contacts">Contact</a></li>
          <li class="Light" id="Light"><i><i class="fa-regular fa-sun"></i></i></li>
          <li class="Dark" id="Dark"><i><i class="fa-regular fa-moon"></i></i></li>
          <script src="ColorMods.js"></script>
          <li class="Login" id="Login"><a href="javascript:void(0)">Login</a></li>
          <li class="Register" id="Register"><a href="javascript:void(0)">Register</a></li>
        </ul>
    </header>
    <script src="index.js"></script>
    <section class="Attractive" id="Attractive">
      <div class="Writing">
        <p><h1>Connect with <span>ENSIA</span><br>like never before</h1></p>
        <p>Ensiuniverse is the first social platform dedicated to ENSIA students. Discover new peers, share your ideas,
           and connect within a safe and engaging community</p>
      </div>
     <div class="ChatPic2"><i class="fa-regular fa-comment"></i></div>
     <div class="pic"><img src="imgz/hero-image.jpg" alt="Community"></div>
     <div class="ButtonStartNow" id="StartBtn"><h2>Start Now</h2></div>
     <div class="PointsWrapper">
       <ul>
          <li class="Secure">Secure</li>
          <li class="Free">Free</li>
          <li class="FES">For Ensia students</li>
        </ul>
     </div>
    </section>
    
    <section class="About" id="About">
      <div class="MiddleTitle">
        <h1>Why Ensi<span>universe</span> ?</h1>
        <p>
          We understand that making new connections can be challenging.
          Our platform provides a safe and welcoming environment for all ENSIA students.
        </p>
      </div>

      <div class="Features" id="Features">
        <div class="FeatureBlock">
          <span>Anonymous</span>
          <div class="icon"><i class="fa-solid fa-question"></i></div>
          <p>Start chatting anonymously. You can reveal your identity only when you want, keeping full control.</p>
        </div>
        <div class="FeatureBlock">
          <span>Matchmaking</span>
          <div class="icon"><i class="fa-solid fa-people-arrows"></i></div>
          <p>Start chatting anonymously. You can reveal your identity only when you want, keeping full control.</p>
        </div>
        <div class="FeatureBlock">
          <span>Security</span>
          <div class="icon"><i class="fa-solid fa-shield-halved"></i></div>
          <p>Exclusive access with your @ensia.edu.dz email. Active moderation and reporting ensure a respectful environment.</p>
        </div>
        <div class="FeatureBlock">
          <span>Identity Control</span>
          <div class="icon"><i class="fa-solid fa-person"></i></div>
          <p>Decide when and with whom to share your identity. Anonymous or visible profile—it's your choice.</p>
        </div>
        <div class="FeatureBlock">
          <span>Amazing Community</span>
          <div class="icon"><i class="fa-solid fa-heart"></i></div>
          <p>Join a helpful community where everyone can find their place, whether shy or outgoing.</p>
        </div>
        <div class="FeatureBlock">
          <span>Events</span>
          <div class="icon"><i class="fa-solid fa-calendar-days"></i></div>
          <p>Organize or join events, study sessions, and activities with your new ENSIA friends.</p>
        </div>
      </div>
      
      <script>
        document.addEventListener("DOMContentLoaded", () => {
          const FeatureBlocks = document.querySelectorAll(".FeatureBlock");
          FeatureBlocks.forEach(block => {
            const icon = block.querySelector("i.fa-solid"); 
            block.addEventListener("mouseover", () => {
              icon.style.transform = "scale(1.2)";
              block.style.transform = "translateY(-5px)" 
              block.style.transition = "0.3s ease-in-out";
            });
            block.addEventListener("mouseout", () => {
              icon.style.transform = "scale(1)";
              block.style.transform = "translateY(0)";
            });
          });
        });
      </script>
    </section>
    
    <section class="JoinUs">
      <div class="ShittyText"><h1>Join <span>Ensiuniverse</span></h1></div>
      <div class="Paragraphs">
        <p class="p1">More you connect</p>
        <p class="p1">More you chat</p>
        <p class="p1">More you make friendships</p>
        <p class="p2">Be active</p>
        <p class="p2">Be sociable</p>
        <p class="p2">Be cool</p>
      </div>
    </section>

    <section class="Contacts" id="Contacts">
      <h1>Contact Us</h1>
      <div class="Writi">
        <p>A question , A suggestion , We are waiting for your comments</p>
      </div>
      <div class="BlocksWrapper">
        <div class="Blocks"><i class="fa-solid fa-envelope"></i><a href="https://mail.google.com/mail/u/0/#inbox?compose=CllgCJNvNGSSKMRPKLDXcdZjwxHWfScNVNsQBqzgdvxQRdFcfptlnCRtwwxQjTzNwmcjsnDDTNB">zakaria.mansourbahar@ensia.edu.dz</a></div>
        <div class="Blocks"><div class="phnum"><i class="fa-solid fa-phone"></i>+213 770 87 21 14 </div></div>
        <div class="Blocks"><div class="Loca"><i class="fa-solid fa-location-dot"></i>Algeria,Algiers,Sidi Abdellah</div> </div>
      </div>
    </section>
    
    <section class="ReadyToJoin">
      <div class="wrapper">
        <h1>Are You ready to join Ensiuniverse ?</h1>
        <p>Sign up now with your @ensia.edu.dz email and start exploring your
           student community. Connect with classmates, join discussions, and discover 
          new friends—all in a safe and welcoming environment.</p>
        <h1><a href="#" id="RegisterBottom">Register Now</a></h1>
      </div>
      <br><br><br><br><br><br><br><br><br>
    </section>
    
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.querySelector('.mobile-menu-btn');
        const mobileNav = document.querySelector('.mobile-nav');
        
        if (menuBtn) {
          menuBtn.addEventListener('click', function() {
            mobileNav.classList.toggle('active');
          });
        }
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
          if (!event.target.closest('.mobile-nav') && !event.target.closest('.mobile-menu-btn')) {
            mobileNav.classList.remove('active');
          }
        });
      });
    </script>
    
    <!-- SINGLE OVERLAY CONTAINER -->
    <div id="loginOverlay" class="overlay">
      <section class="FormContainer">
        <!-- LOGIN FORM -->
        <form class="LoginForm" id="loginForm" method="post" action="includes/LoginHandler.php">
          <button type="button" id="closeLogin" class="close-btn">&times;</button>
          
          <div class="icon-container">
            <i class="fa-regular fa-comment"></i>
          </div>
          <h1 class="Title">Welcome to Ensiuniverse</h1>

          <div class="Choice" id="Choice">
            <ul>
              <li class="FChoice" id="FChoice"><a href="#" id="showLogin">Login</a></li>
              <li class="SChoice"><a href="#" id="showRegister">Register</a></li>
            </ul>
          </div>

          <div class="InputsHolder">
            <h3>Ensia Email</h3>
            <div class="input-group">
              <i class="fa-regular fa-envelope"></i>
              <input type="email" name="Email" placeholder="firstName.familyName@ensia.edu.dz">
            </div>

            <h3>Password</h3>
            <div class="input-group">
              <i class="fa-solid fa-lock"></i>
              <input type="password" name="Pwd" placeholder="Your password">
              <i class="fa-regular fa-eye togglePassword"></i>
            </div>
          </div>
          <div class="FormLinks">
            <div class="LoginButton" id="LoginButton"><a href="#">Login</a></div>
            <script>
                document.getElementById("LoginButton").addEventListener("click", function(e) {
                e.preventDefault(); 
                document.getElementById("loginForm").submit();
                });
            </script>
            <div class="ForgottenPassword"><a href="Forgottenpwd.php">Forgot your password?</a></div>
          </div>

          <div class="Warning">
            <div class="ParagraphHolder">
              <h1><i class="fa-solid fa-triangle-exclamation"></i>Only Ensia students can access</h1>
              <p>Only emails ending with <b>@ensia.edu.dz</b> are allowed.</p>
            </div>
          </div>
          <script>
  // Select all eye icons
          document.querySelectorAll(".togglePassword").forEach(icon => {
            icon.addEventListener("click", () => {
              // The password input is the previous sibling of the eye icon
              const input = icon.previousElementSibling;
              if (!input) return; // safety check

              if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
              } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
              }
            });
          });
        </script>



        </form>
        
        <!-- REGISTER FORM -->
        <form class="RegisterForm form" id="registerForm" style="display:none;" method="post" action="includes/RegisterHandler.php">
          
          <button type="button" id="closeRegister" class="close-btn">&times;</button>

          <div class="icon-container">
            <i class="fa-regular fa-comment"></i>
          </div>
          <h1 class="Title">Create your account</h1>
          <p style="position:absolute;top:110px;left:30px; font-size:16px;color:red"><strong>Important:</strong>(If you did not recieve any email , check your spam section)</p>
          <br><br>

          <div class="Choice">
            <ul>
              <li class="FChoice"><a href="#" id="showLogin2">Login</a></li>
              <li class="SChoice"><a href="#" id="showRegister2">Register</a></li>
            </ul>
          </div>

          <div class="InputsHolder">
            <h3>First Name</h3>
            <div class="input-group">
              <i class="fa-solid fa-user"></i>
              <input type="text" name="FirstName" placeholder="First Name" id="FirstName">
            </div>

            <h3>Last Name</h3>
            <div class="input-group">
              <i class="fa-solid fa-user"></i>
              <input type="text" name="LastName" placeholder="Name" id="LastName">
            </div>

            <h3>ENSIA Email</h3>
            <div class="input-group">
              <i class="fa-regular fa-envelope"></i>
              <input type="email" name="Email" placeholder="your @ensia.edu.dz" id="Email">
            </div>

            <h3>Password</h3>
            <div class="input-group">
              <i class="fa-solid fa-lock"></i>
              <input type="password" name="Pwd" placeholder="Choose a password" id="Password">
              <i class="fa-regular fa-eye" id="togglePassword" style="cursor:pointer; margin-left:8px;"></i>
            </div>
            <h3>Password Confirmation</h3>
            <div class="input-group">
              <i class="fa-solid fa-lock"></i>
              <input type="password" name="ConfirmPwd" placeholder="Confirm your password" id="ConfirmedPassword">
              <i class="fa-regular fa-eye" id="toggleConfirmPassword" style="cursor:pointer; margin-left:8px;"></i>
            </div>
            <p id="passwordError" style="color:red; font-size:14px; display:none;"></p>
                  <script>
                        const password = document.getElementById("Password");
                        const confirmPassword = document.getElementById("ConfirmedPassword");
                        const errorMsg = document.getElementById("passwordError");

                        confirmPassword.addEventListener("input", () => {
                          if(password.value=='' && confirmPassword.value==''){
                            errorMsg.textContent = "";
                            errorMsg.style.display = "none";
                          }
                          else if (password.value !== confirmPassword.value) {
                            errorMsg.textContent = "Passwords do not match";
                            errorMsg.style.display = "block";
                          } else {
                            errorMsg.textContent = "";
                            errorMsg.style.display = "none";
                          }
                        });
                  </script>
                  <script>
                      function toggleVisibility(inputId, iconId) {
                      const input = document.getElementById(inputId);
                      const icon = document.getElementById(iconId);

                      if (input.type === "password") {
                        input.type = "text";
                        icon.classList.remove("fa-eye");
                        icon.classList.add("fa-eye-slash");
                      } else {
                        input.type = "password";
                        icon.classList.remove("fa-eye-slash");
                        icon.classList.add("fa-eye");
                      }
                    }

                    document.getElementById("togglePassword").addEventListener("click", () => {
                      toggleVisibility("Password", "togglePassword");
                    });

                    document.getElementById("toggleConfirmPassword").addEventListener("click", () => {
                      toggleVisibility("ConfirmedPassword", "toggleConfirmPassword");
                    });
          </script>
          </div>
          <div class="FormLinks">
            <div class="LoginButton">
                <a href="#" id="registerBtn">Create my account</a>
            </div>
             
          </div>

          <div class="Warning">
            <div class="ParagraphHolder">
              <p>
                By subscribing , you accept our <a href="#">conditions</a><br>
                <b>Access only for Ensia Students</b>
              </p>
            </div>
          </div>
        </form>
      </section>
    </div>
    <script>
      document.getElementById("registerBtn").addEventListener("click", function(e) {
      e.preventDefault(); // prevent link navigation
      document.getElementById("registerForm").submit(); // submit the form
        });
    </script>
    <script>
      // Get elements
      const overlay = document.getElementById("loginOverlay");
      const loginForm = document.getElementById("loginForm");
      const registerForm = document.getElementById("registerForm");
      const registerBtn = document.getElementById("Register");
      const registerBottomBtn = document.getElementById("RegisterBottom");
      const closeBtn = document.getElementById("closeLogin");
      const closeRegisterBtn = document.getElementById("closeRegister");
      const Open=document.getElementById("Login");
      const open=document.getElementById("StartBtn")

      // Show overlay when register button is clicked
      function showOverlay() {
        overlay.style.display = "flex";
        loginForm.style.display = "block";
        registerForm.style.display = "none";
        document.body.classList.add('no-scroll-x');
      }
      Open.addEventListener("click" , showOverlay);
      
      // Register button event listeners
      if (registerBtn) {
        registerBtn.addEventListener("click", (e) => {
          e.preventDefault();
          showOverlay();
          loginForm.style.display = "none";
          registerForm.style.display = "block";
        });
      }
      open.addEventListener("click" , (e)=>{
        e.preventDefault();
        showOverlay(); 
        loginForm.style.display = "none";
        registerForm.style.display = "block";
      });
      if (registerBottomBtn) {
        registerBottomBtn.addEventListener("click", (e) => {
          e.preventDefault();
          showOverlay();
          loginForm.style.display = "none";
          registerForm.style.display = "block";
          
          
        });
      }
      
      // Close overlay
      if (closeBtn) {
        closeBtn.addEventListener("click", () => {
          overlay.style.display = "none";
        });
      }
      
      if (closeRegisterBtn) {
        closeRegisterBtn.addEventListener("click", () => {
          overlay.style.display = "none";
        });
      }
      
      // Close overlay when clicking outside
      overlay.addEventListener("click", (e) => {
        if (e.target === overlay) {
          overlay.style.display = "none";
        }
      });
      
      // Form switching
      document.getElementById("showRegister").addEventListener("click", (e) => {
        e.preventDefault();
        loginForm.style.display = "none";
        registerForm.style.display = "block";
      });

      document.getElementById("showLogin").addEventListener("click", (e) => {
        e.preventDefault();
        registerForm.style.display = "none";
        loginForm.style.display = "block";
      });

      document.getElementById("showRegister2").addEventListener("click", (e) => {
        e.preventDefault();
      });

      document.getElementById("showLogin2").addEventListener("click", (e) => {
        e.preventDefault();
        registerForm.style.display = "none";
        loginForm.style.display = "block";
      });
    </script>
  
    <form method="post" action="includes/RegisterHandler.php" style="display:none;">
        <input type="text" name="FirstName" value="<?= htmlspecialchars($old['FirstName'] ?? '') ?>" placeholder="First Name">
        <input type="text" name="LastName" value="<?= htmlspecialchars($old['LastName'] ?? '') ?>" placeholder="Last Name">
        <input type="email" name="Email" value="<?= htmlspecialchars($old['Email'] ?? '') ?>" placeholder="Email">
        <input type="password" name="Pwd" placeholder="Password">
        <input type="password" name="ConfirmPwd" placeholder="Confirm Password">
        <button type="submit">Register</button>
    </form>
<script>
document.getElementById('RegisterBottom').addEventListener('click', function () {
  if (window.innerWidth <= 768) {
    // scroll the root (<html>) element
    document.documentElement.scrollTo({
      top: 0,
      behavior: 'smooth'
    });

    // scroll the <body> element (some browsers use body for scrolling)
    document.body.scrollTo({
      top: 400,
      behavior: 'smooth'
    });
  }
});
</script>
<script>
document.getElementById('RegisterBottom').addEventListener('click', function () {
  if(window.innerWidth > 768){
    document.documentElement.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  }
    });
</script>
<script>
document.getElementById('StartBtn').addEventListener('click', function () {
    // Only run on mobile-sized screens (<=768 px)
    if (window.innerWidth <= 768) {
        window.scrollTo({
            top: 600,           // change to 0 if you want very top
            behavior: 'smooth' // smooth scrolling animation
        });
    }
});
</script>
<script>


</body>
</html>