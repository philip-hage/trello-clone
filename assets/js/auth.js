async function registerSubmit(event) {
  event.preventDefault(); // Prevents the default form submission behavior

  const name = document.getElementById("input-name").value;
  const email = document.getElementById("input-email").value;
  const password = document.getElementById("input-password").value;

  if (name === "" || email === "" || password === "") {
    localStorage.setItem("toast", "false");
    localStorage.setItem("toastMessage", "Please Fill in all the fields");
    localStorage.setItem("toasttitle", "Failed");
    location.reload();
  }

  if (email.indexOf("@") === -1) {
    localStorage.setItem("toast", "false");
    localStorage.setItem("toastMessage", "Please enter a valid email");
    localStorage.setItem("toasttitle", "Failed");
    location.reload();
  }

  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "auth",
      action: "register",
      name: name,
      email: email,
      password: password,
    }),
  });

  const response = await call.json();

  if (response.status === "200") {
    $userId = response.userId;
    window.location.href = `${SYSTEM_ADDRESS}workspaces/overview/${$userId}`;
  } else if (response.status === "400") {
    localStorage.setItem("toast", "false");
    localStorage.setItem("toastMessage", "Email already exists");
    localStorage.setItem("toasttitle", "Failed");
    window.location.href =  `${SYSTEM_ADDRESS}auth/register`;
  }
}

async function loginForm(event) {
  event.preventDefault(); // Prevents the default form submission behavior

  const email = document.getElementById("input-email").value;
  const password = document.getElementById("input-password").value;

  if (email === "" || password === "") {
    alert("Please fill in all the fields");
    return;
  }

  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "auth",
      action: "login",
      email: email,
      password: password,
    }),
  });

  const response = await call.json();

  if (response.status === "200") {
    $userId = response.userId;
    window.location.href = `${SYSTEM_ADDRESS}workspaces/overview/${$userId}`;
  } else if (response.status === "400") {
    localStorage.setItem("toast", "false");
    localStorage.setItem("toastMessage", "Email or Password is incorrect");
    localStorage.setItem("toasttitle", "Failed");
    window.location.href =  `${SYSTEM_ADDRESS}auth/login`;
  }
}
