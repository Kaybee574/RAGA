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
    // We prevent default to run our custom validation
    if (!validateInputs()) {
        e.preventDefault();
    }
  });
}

//validates input for the sign in page
if (form2) {
  form2.addEventListener("submit", (e) => {
    if (!checkInputs()) {
        e.preventDefault();
    }
  });
}

//validates input for the forgot password page
if (form3) {
  form3.addEventListener("submit", (e) => {
    if (!checkEmail()) {
        e.preventDefault();
    }
  });
}

//the function that validates the input for the sign up page
function validateInputs() {
  const type = document.getElementById('user_type').value;
  let isValid = true;

  //get the values and remove any whitespaces
  const fnameValue = fname.value.trim();
  const passwordValue = password.value.trim();
  const password2Value = password2.value.trim();

  //if name is not entered
  if (fnameValue === "") {
    setErrorFor(fname, "Name cannot be blank");
    isValid = false;
  } else {
    setSuccessFor(fname);
  }

  // Conditional validation based on role
  if (type === 'seller' || type === 'both') {
    const emailValue = email.value.trim();
    if (emailValue === "") {
      setErrorFor(email, "Email cannot be blank");
      isValid = false;
    } else if (!isEmail(emailValue)) {
      setErrorFor(email, "Email is invalid (use campus email)");
      isValid = false;
    } else {
      setSuccessFor(email);
    }
  }

  if (type === 'buyer' || type === 'both') {
    const studentNumber = document.getElementById('student_number');
    if (studentNumber && studentNumber.value.trim() === "") {
        setErrorFor(studentNumber, "Student number is required");
        isValid = false;
    } else if (studentNumber) {
        setSuccessFor(studentNumber);
    }
  }

  if (passwordValue === "") {
    setErrorFor(password, "Password cannot be blank");
    isValid = false;
  } else {
    setSuccessFor(password);
  }

  if (password2Value === "") {
    setErrorFor(password2, "Confirm Password cannot be blank");
    isValid = false;
  } else if (passwordValue !== password2Value) {
    setErrorFor(password2, "Passwords do not match");
    isValid = false;
  } else {
    setSuccessFor(password2);
  }

  return isValid;
}

function checkInputs() {
  const emailValue = email.value.trim();
  const passwordValue = password.value.trim();
  let isValid = true;

  if (emailValue === "") {
    setErrorFor(email, "Email cannot be blank");
    isValid = false;
  } else if (!isEmail(emailValue)) {
    setErrorFor(email, "Email is invalid");
    isValid = false;
  } else {
    setSuccessFor(email);
  }

  if (passwordValue === "") {
    setErrorFor(password, "Password cannot be blank");
    isValid = false;
  } else {
    setSuccessFor(password);
  }
  
  return isValid;
}

function checkEmail() {
  const emailValue = email.value.trim();
  let isValid = true;

  if (emailValue === "") {
    setErrorFor(email, "Email cannot be blank");
    isValid = false;
  } else if (!isEmail(emailValue)) {
    setErrorFor(email, "Email is invalid");
    isValid = false;
  } else {
    setSuccessFor(email);
  }
  return isValid;
}

//returns error message
function setErrorFor(input, message) {
  const formrow = input.parentElement;
  const small = formrow.querySelector("small");
  if (small) {
    small.innerText = message;
  } else {
    // Fallback if small tag is missing
    input.setCustomValidity(message);
    input.reportValidity();
  }
  formrow.className = "formrow error";
}

//returns success message
function setSuccessFor(input) {
  const formrow = input.parentElement;
  formrow.className = "formrow success";
  input.setCustomValidity("");
}

//checks if the email is of the correct syntax
function isEmail(email) {
  // Relaxed regex to allow standard emails as well as the specific campus ones
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}
