<!DOCTYPE html>
<head>
  <title>Home Page</title>
  <link rel="stylesheet" href="login_style.css">
  <link rel="icon" href="logo.png" type="image/x-icon"> 
</head>
<body>

  <div id="myDiv">
    <div class="copyright">
      Expense Tracker
    </div>
  </div>

  <div id="container" class="container">
    <div class="row">

      <div class="col align-items-center flex-col sign-up">
        <div class="form-wrapper align-items-center">
          <div class="form sign-up">
            <form action="signup.php" method="POST">
              <div class="input-group">
                <i class='bx bxs-user'></i>
                <input type="email" name="email" placeholder="Email *" required>
              </div>
              <div class="input-group">
                <i class='bx bx-mail-send'></i>
                <input type="text" name="username" placeholder="Username *" required>
              </div>
              <div class="input-group">
                <i class='bx bxs-lock-alt'></i>
                <input type="password" name="password" placeholder="Password *" required>
              </div>
              <div class="input-group">
                <i class='bx bxs-lock-alt'></i>
                <input type="password" name="confirm_password" placeholder="Confirm Password *" required>
              </div>
              <button type="submit">Create Account</button>
            </form>
            <p>
              <span>Already have an account?</span>
              <b onclick="toggle()" class="pointer">Sign in here</b>
            </p>
          </div>
        </div>
      </div>


      <div class="col align-items-center flex-col sign-in">
        <div class="form-wrapper align-items-center">
          <div class="form sign-in">
            <form action="login.php" method="POST">
              <div class="input-group">
                <i class='bx bxs-user'></i>
                <input type="email" name="email" placeholder="Email *" required>
              </div>
              <div class="input-group">
                <i class='bx bxs-lock-alt'></i>
                <input type="password" name="password" placeholder="Password *" required>
              </div>
              <button type="submit">Submit</button>
            </form>
            <p>
              <b class="forget-btn pointer">Forgot password?</b>
            </p>
            <p>
              <span>Don't have an account?</span>
              <b onclick="toggle()" class="pointer">Sign up here</b>
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="row content-row">
      <div class="col align-items-center flex-col">
        <div class="text sign-in">
          <h2>Happy to see you again</h2>
        </div>
      </div>
      <div class="col align-items-center flex-col">
        <div class="text sign-up">
          <h2>Welcome</h2>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.querySelector(".container").classList.add("hidden");
    setTimeout(function(){
      document.getElementById("myDiv").style.display = "none";
      document.querySelector(".container").classList.remove("hidden");
    }, 5000);
  
    let container = document.getElementById('container');
    function toggle() {
      container.classList.toggle('sign-in');
      container.classList.toggle('sign-up');
    }
  
    setTimeout(() => {
      container.classList.add('sign-in');
    }, 5200);
  </script>

</body>
</html>