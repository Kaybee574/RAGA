//using navigator objects to get the language, userAgent, geolocation, permissions and online status of the browser

//checks if the browser is connected to the internet
console.log(navigator.onLine);

//checks if access to certain things is provided
console.log(navigator.permissions);

//checks the location of the user
console.log(
  navigator.geolocation.getCurrentPosition((position) => {
    // This code only runs AFTER the user clicks "Allow"
    console.log("Latitude:", position.coords.latitude);
    console.log("Longitude:", position.coords.longitude);
  }),
);

//returns the browser name, version and operating system
console.log(navigator.userAgent);

//return the language used by the user
console.log(navigator.language);

//checks if cookies are enabled
console.log(navigator.cookieEnabled);

console.log(navigator.clipboard);

//access the forms through the form id
const form1 = document.querySelector(".flex-form.signup-form");
const form2 = document.querySelector(".flex-form.signin-form");
const form3 = document.querySelector(".forgot_password");
const email = document.getElementById("email");
const password = document.getElementById("password");
const fname = document.getElementById("name");
const password2 = document.getElementById("confirm_password");

//validates input for the sign up page
if (form1) {
  form1.addEventListener("submit", (e) => {
    e.preventDefault();
    validateInputs();
  });
}

//validates input for the sign in page
if (form2) {
  form2.addEventListener("submit", (e) => {
    e.preventDefault();
    checkInputs();
  });
}

//validates input for the forgot password page
if (form3) {
  form3.addEventListener("submit", (e) => {
    e.preventDefault();
    checkEmail();
  });
}
//the function that validates the input for the sign up page
function validateInputs() {
  //get the values and remove any whitespaces
  const fnameValue = fname.value.trim();
  const emailValue = email.value.trim();
  const passwordValue = password.value.trim();
  const password2Value = password2.value.trim();

  //if name is not entered
  if (fnameValue === "") {
    //calls a function that returns an error message
    setErrorFor(fname, "Name cannot be blank");
  } else {
    //calls a function that returns a success message
    setSuccessFor(fname);
  }

  if (emailValue === "") {
    //calls a function that returns an error message
    setErrorFor(email, "Email cannot be blank");
  } else if (!isEmail(emailValue)) {
    setErrorFor(email, "Email is invalid");
  } else {
    //calls the function if the email is valid
    setSuccessFor(email);
  }

  if (passwordValue === "") {
    //calls a function that returns an error message
    setErrorFor(password, "Password cannot be blank");
  } else {
    //calls the function if the password is valid
    setSuccessFor(password);
  }

  if (password2Value === "") {
    //calls a function that returns an error message
    setErrorFor(password2, "Confirm Password cannot be blank");
  } else if (passwordValue !== password2Value) {
    //checks if passwords match
    setErrorFor(password2, "Passwords do not match");
  } else {
    //calls the function if the password is valid
    setSuccessFor(password2);
  }
}

function checkInputs() {
  const emailValue = email.value.trim();
  const passwordValue = password.value.trim();

  if (emailValue === "") {
    //calls a function that returns an error message
    setErrorFor(email, "Email cannot be blank");
  } else if (!isEmail(emailValue)) {
    //checks the validity of the email through a function that checks for the syntax
    setErrorFor(email, "Email is invalid");
  } else {
    //calls the function if the email is valid
    setSuccessFor(email);
  }

  if (passwordValue === "") {
    //calls a function that returns an error message
    setErrorFor(password, "Password cannot be blank");
  } else {
    setSuccessFor(password);
    window.location.href = "Explore.html"; // Page Redirect
  }
}

function checkEmail() {
  const emailValue = email.value.trim();

  if (emailValue === "") {
    //calls a function that returns an error message
    setErrorFor(email, "Email cannot be blank");
  } else if (!isEmail(emailValue)) {
    //checks if the email is of the correct syntax through a function
    setErrorFor(email, "Email is invalid");
  } else {
    setSuccessFor(email);
    alert("Password reset email sent successfully!");
  }
}

//returns error message
function setErrorFor(input, message) {
  const formrow = input.parentElement;
  const small = formrow.querySelector("small");
  small.innerText = message;
  formrow.className = "formrow error";
}

//returns success message
function setSuccessFor(input) {
  const formrow = input.parentElement;
  formrow.className = "formrow success";
}

//checks if the email is of the correct syntax
function isEmail(email) {
  const regex =
    /^[gG][0-9][0-9][aA-zZ][0-9][0-9][0-9][0-9]@campus\.ru\.ac\.za$/;
  return regex.test(email);
}
